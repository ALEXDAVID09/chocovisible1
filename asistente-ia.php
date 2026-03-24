<?php
// ══════════════════════════════════════════════════════
//  ChocoVisible — Asistente IA con Google Gemini
//  Doble función: endpoint AJAX + widget HTML
// ══════════════════════════════════════════════════════

define('OPENAI_API_KEY', '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');
define('OPENAI_MODEL',   'gpt-4o-mini');
define('OPENAI_URL',     'https://api.openai.com/v1/chat/completions');

// ── Modo AJAX ──────────────────────────────────────────
$esAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

// Detectar si viene JSON o FormData
$inputRaw  = file_get_contents('php://input');
$inputJson = json_decode($inputRaw, true);

$mensaje   = '';
$historial = [];

if ($inputJson && isset($inputJson['mensaje'])) {
    // Petición JSON (nueva-denuncia.php integrado)
    $mensaje   = trim(strip_tags($inputJson['mensaje']));
    $historial = $inputJson['historial'] ?? [];
    $esAjax    = true;
} elseif (isset($_POST['mensaje'])) {
    // Petición FormData (widget standalone)
    $mensaje = trim(strip_tags($_POST['mensaje']));
    $esAjax  = true;
}

if ($esAjax && $mensaje !== '') {
    header('Content-Type: application/json; charset=utf-8');

    if (empty($mensaje)) {
        echo json_encode(['error' => 'Mensaje vacío']);
        exit;
    }

    $system = "Eres 'Asis', el asistente virtual de ChocoVisible, un sistema ciudadano de denuncias del departamento del Chocó, Colombia. Tu misión es ayudar al ciudadano a redactar su denuncia de forma clara y completa.

Cuando el ciudadano te describa un problema o incidente, debes:
1. Responder con empatía y en español sencillo.
2. Hacer preguntas específicas para obtener: tipo de denuncia, descripción detallada, ubicación (municipio/barrio/dirección), fecha aproximada, y nivel de urgencia (Alta/Media/Baja).
3. Cuando ya tengas suficiente información, incluir al FINAL de tu respuesta exactamente este bloque (sin modificarlo):

<<<DATOS_DENUNCIA>>>
{\"tipo\":\"[tipo de denuncia]\",\"descripcion\":\"[descripcion completa]\",\"municipio\":\"[municipio]\",\"barrio\":\"[barrio o sector]\",\"fecha\":\"[fecha aproximada]\",\"urgencia\":\"[Alta/Media/Baja]\"}
<<<FIN_DATOS>>>

Tipos de denuncia válidos: Corrupción, Acoso, Discriminación, Problema ambiental, Servicio público, Obras incompletas, Otro.
No inventes datos. Si el ciudadano no ha dado suficiente información, sigue preguntando antes de generar el bloque.";

    // Construir historial para OpenAI
    $messages = [['role' => 'system', 'content' => $system]];
    foreach ($historial as $h) {
        if (isset($h['role'], $h['content'])) {
            $messages[] = ['role' => $h['role'], 'content' => $h['content']];
        }
    }
    $messages[] = ['role' => 'user', 'content' => $mensaje];

    $body = json_encode([
        'model'       => OPENAI_MODEL,
        'messages'    => $messages,
        'temperature' => 0.7,
        'max_tokens'  => 1024,
    ]);

    $ch = curl_init(OPENAI_URL);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $body,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . OPENAI_API_KEY,
        ],
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);

    $response = curl_exec($ch);
    $err      = curl_error($ch);
    curl_close($ch);

    if ($err) {
        echo json_encode(['error' => 'Error de conexión: ' . $err]);
        exit;
    }

    $data = json_decode($response, true);

    if (isset($data['choices'][0]['message']['content'])) {
        $texto = $data['choices'][0]['message']['content'];

        // Extraer datos de denuncia si están presentes
        $datosDenuncia = null;
        if (preg_match('/<<<DATOS_DENUNCIA>>>(.*?)<<<FIN_DATOS>>>/s', $texto, $m)) {
            $json    = trim($m[1]);
            $decoded = json_decode($json, true);
            if ($decoded) {
                $datosDenuncia = $decoded;
            }
            $texto = trim(preg_replace('/<<<DATOS_DENUNCIA>>>.*?<<<FIN_DATOS>>>/s', '', $texto));
        }

        // Mapear tipo de denuncia al valor del select del formulario
        $tipoMap = [
            'corrupción'=>'corrupcion','corrupcion'=>'corrupcion',
            'acoso'=>'acoso','acoso o intimidación'=>'acoso',
            'discriminación'=>'discriminacion','discriminacion'=>'discriminacion',
            'problema ambiental'=>'ambiental','ambiental'=>'ambiental',
            'servicio público'=>'servicios','servicios'=>'servicios',
            'obras incompletas'=>'laboral','laboral'=>'laboral',
            'seguridad'=>'seguridad','ético'=>'etico','etico'=>'etico',
            'otro'=>'otro',
        ];
        if ($datosDenuncia && isset($datosDenuncia['tipo'])) {
            $tipoLower = mb_strtolower(trim($datosDenuncia['tipo']));
            $datosDenuncia['tipo'] = $tipoMap[$tipoLower] ?? 'otro';
            // urgencia en minúsculas para el select
            $datosDenuncia['urgencia'] = mb_strtolower(trim($datosDenuncia['urgencia'] ?? 'media'));
        }

        echo json_encode([
            'respuesta'     => $texto,
            'datos'         => $datosDenuncia,   // clave que espera nueva-denuncia.php
            'datosDenuncia' => $datosDenuncia,   // clave del widget standalone
        ]);
    } else {
        $errorMsg = $data['error']['message'] ?? 'Sin respuesta de Gemini';
        echo json_encode(['error' => $errorMsg]);
    }
    exit;
}
?>

<!-- ══════════════════════════════════════════════════════
     WIDGET HTML — incluir antes de </body>
     ══════════════════════════════════════════════════════ -->
<style>
#aiBtn {
    position: fixed;
    bottom: 24px;
    left: 100px;
    z-index: 1050;
    width: 52px;
    height: 52px;
    border-radius: 50%;
    background: linear-gradient(135deg, #2D5016, #1B4F72);
    border: none;
    box-shadow: 0 4px 16px rgba(0,0,0,0.3);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform .2s, box-shadow .2s;
}
#aiBtn:hover { transform: scale(1.1); box-shadow: 0 6px 20px rgba(0,0,0,0.4); }
#aiBtn svg   { width: 26px; height: 26px; fill: #ffffff; }

#aiPanel {
    position: fixed;
    bottom: 90px;
    left: 24px;
    z-index: 1049;
    width: 340px;
    max-width: calc(100vw - 32px);
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.18);
    display: none;
    flex-direction: column;
    overflow: hidden;
    font-family: Arial, sans-serif;
}
#aiPanel.open { display: flex; }

#aiHeader {
    background: linear-gradient(135deg, #2D5016, #1B4F72);
    padding: 14px 16px;
    display: flex;
    align-items: center;
    gap: 10px;
    color: #fff;
}
#aiHeader .ai-avatar {
    width: 36px; height: 36px; border-radius: 50%;
    background: rgba(255,255,255,0.2);
    display: flex; align-items: center; justify-content: center;
    font-size: 18px;
}
#aiHeader .ai-info strong { font-size: 14px; display: block; }
#aiHeader .ai-info small  { font-size: 11px; opacity: .8; }
#aiClose {
    margin-left: auto; background: none; border: none;
    color: #fff; font-size: 20px; cursor: pointer; line-height: 1;
}

#aiMessages {
    flex: 1;
    padding: 14px;
    overflow-y: auto;
    max-height: 320px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    background: #f8f9fa;
}

.ai-msg {
    max-width: 85%;
    padding: 10px 13px;
    border-radius: 14px;
    font-size: 13px;
    line-height: 1.5;
    word-break: break-word;
}
.ai-msg.bot {
    background: #e8f5e8;
    color: #1a2e0a;
    border-bottom-left-radius: 4px;
    align-self: flex-start;
}
.ai-msg.user {
    background: #1B4F72;
    color: #fff;
    border-bottom-right-radius: 4px;
    align-self: flex-end;
}
.ai-msg.typing {
    background: #e8f5e8;
    color: #5D6D7E;
    align-self: flex-start;
    font-style: italic;
}

#aiFillBtn {
    display: none;
    margin: 0 14px 10px;
    padding: 9px;
    background: linear-gradient(135deg, #2D5016, #1B4F72);
    color: #fff;
    border: none;
    border-radius: 10px;
    font-size: 13px;
    font-weight: bold;
    cursor: pointer;
    text-align: center;
    transition: opacity .2s;
}
#aiFillBtn:hover { opacity: .9; }

#aiInputArea {
    display: flex;
    gap: 8px;
    padding: 12px 14px;
    border-top: 1px solid #e0e0e0;
    background: #fff;
}
#aiInput {
    flex: 1;
    border: 1px solid #ccc;
    border-radius: 20px;
    padding: 8px 14px;
    font-size: 13px;
    outline: none;
    resize: none;
    max-height: 80px;
    font-family: Arial, sans-serif;
}
#aiInput:focus { border-color: #2D5016; }
#aiSend {
    width: 38px; height: 38px;
    background: linear-gradient(135deg, #2D5016, #1B4F72);
    border: none; border-radius: 50%;
    cursor: pointer; display: flex;
    align-items: center; justify-content: center;
    flex-shrink: 0; transition: opacity .2s;
}
#aiSend:hover { opacity: .85; }
#aiSend svg { width: 18px; height: 18px; fill: #fff; }
</style>

<!-- Botón flotante IA -->
<button id="aiBtn" title="Asistente IA">
    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
    </svg>
</button>

<!-- Panel del chat -->
<div id="aiPanel">
    <div id="aiHeader">
        <div class="ai-avatar">🤖</div>
        <div class="ai-info">
            <strong>Asis — Asistente IA</strong>
            <small>ChocoVisible · Powered by OpenAI</small>
        </div>
        <button id="aiClose">×</button>
    </div>

    <div id="aiMessages">
        <div class="ai-msg bot">
            ¡Hola! Soy <strong>Asis</strong>, tu asistente para redactar denuncias. 👋<br><br>
            Cuéntame qué está pasando y te ayudo a documentarlo correctamente.
        </div>
    </div>

    <button id="aiFillBtn">⚡ Rellenar formulario automáticamente</button>

    <div id="aiInputArea">
        <textarea id="aiInput" rows="1" placeholder="Escribe tu mensaje..."></textarea>
        <button id="aiSend">
            <svg viewBox="0 0 24 24"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/></svg>
        </button>
    </div>
</div>

<script>
(function () {
    const btn      = document.getElementById('aiBtn');
    const panel    = document.getElementById('aiPanel');
    const closeBtn = document.getElementById('aiClose');
    const messages = document.getElementById('aiMessages');
    const input    = document.getElementById('aiInput');
    const sendBtn  = document.getElementById('aiSend');
    const fillBtn  = document.getElementById('aiFillBtn');

    let datosPendientes = null;

    btn.addEventListener('click', () => panel.classList.toggle('open'));
    closeBtn.addEventListener('click', () => panel.classList.remove('open'));

    input.addEventListener('input', function () {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 80) + 'px';
    });

    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            enviar();
        }
    });

    sendBtn.addEventListener('click', enviar);

    function addMsg(texto, tipo) {
        const div = document.createElement('div');
        div.className = 'ai-msg ' + tipo;
        div.innerHTML = texto.replace(/\n/g, '<br>');
        messages.appendChild(div);
        messages.scrollTop = messages.scrollHeight;
        return div;
    }

    function enviar() {
        const texto = input.value.trim();
        if (!texto) return;

        addMsg(texto, 'user');
        input.value = '';
        input.style.height = 'auto';
        sendBtn.disabled = true;

        const typing = addMsg('Asis está escribiendo...', 'typing');

        const fd = new FormData();
        fd.append('mensaje', texto);

        fetch('asistente-ia.php', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: fd
        })
        .then(r => r.json())
        .then(data => {
            typing.remove();
            if (data.error) {
                addMsg('⚠ Error: ' + data.error, 'bot');
            } else {
                addMsg(data.respuesta, 'bot');
                if (data.datosDenuncia) {
                    datosPendientes = data.datosDenuncia;
                    fillBtn.style.display = 'block';
                }
            }
        })
        .catch(() => {
            typing.remove();
            addMsg('⚠ No se pudo conectar. Intenta de nuevo.', 'bot');
        })
        .finally(() => { sendBtn.disabled = false; });
    }

    fillBtn.addEventListener('click', function () {
        if (!datosPendientes) return;
        const d = datosPendientes;

        const campos = {
            'tipo_denuncia':   d.tipo,
            'descripcion':     d.descripcion,
            'municipio':       d.municipio,
            'barrio':          d.barrio,
            'fecha_incidente': d.fecha,
            'urgencia':        d.urgencia,
        };

        let rellenos = 0;
        for (const [id, valor] of Object.entries(campos)) {
            if (!valor) continue;
            const el = document.getElementById(id) || document.querySelector('[name="' + id + '"]');
            if (el) {
                if (el.tagName === 'SELECT') {
                    for (const opt of el.options) {
                        if (opt.text.toLowerCase().includes(valor.toLowerCase()) ||
                            opt.value.toLowerCase().includes(valor.toLowerCase())) {
                            el.value = opt.value;
                            break;
                        }
                    }
                } else {
                    el.value = valor;
                }
                el.dispatchEvent(new Event('input',  { bubbles: true }));
                el.dispatchEvent(new Event('change', { bubbles: true }));
                rellenos++;
            }
        }

        fillBtn.style.display = 'none';
        datosPendientes = null;
        panel.classList.remove('open');

        addMsg('✅ ¡Formulario rellenado con ' + rellenos + ' campos! Revisa los datos y ajusta si es necesario.', 'bot');
        panel.classList.add('open');

        const form = document.querySelector('form, .card, #step1');
        if (form) form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
})();
</script>