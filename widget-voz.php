<!-- ════════════════════════════════════════════════════════════════
     WIDGET DE VOZ — CodeChoco Denuncias
     Pega este bloque completo en nueva-denuncia.php
     justo antes del </body> de cierre.
════════════════════════════════════════════════════════════════ -->

<style>
/* ── Variables ─────────────────────────────────────────────── */
:root {
  --voz-green: #2D5016;
  --voz-green2: #4A7C28;
  --voz-blue: #1B4F72;
  --voz-red: #DC2626;
  --voz-yellow: #F4D03F;
  --voz-shadow: 0 8px 40px rgba(45,80,22,.2);
}

/* ── Botón flotante voz ────────────────────────────────────── */
#vozToggleBtn {
  position: fixed;
  bottom: 24px;
  left: 24px;
  z-index: 1100;
  width: 62px;
  height: 62px;
  border-radius: 50%;
  border: none;
  cursor: pointer;
  background: linear-gradient(135deg, var(--voz-green), var(--voz-blue));
  box-shadow: var(--voz-shadow);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
  gap: 2px;
  transition: transform .25s, box-shadow .25s;
}
#vozToggleBtn:hover {
  transform: scale(1.08);
  box-shadow: 0 12px 50px rgba(45,80,22,.35);
}
#vozToggleBtn svg {
  width: 26px;
  height: 26px;
  fill: none;
  stroke: #fff;
  stroke-width: 2;
  stroke-linecap: round;
  stroke-linejoin: round;
}
#vozBadge {
  position: absolute;
  top: -4px;
  left: -4px;
  background: var(--voz-yellow);
  color: #2C3E50;
  font-size: .6rem;
  font-weight: 800;
  border-radius: 20px;
  padding: 2px 6px;
  white-space: nowrap;
  letter-spacing: .3px;
}

/* ── Panel principal ───────────────────────────────────────── */
#vozPanel {
  position: fixed;
  bottom: 100px;
  left: 24px;
  z-index: 1099;
  width: min(360px, calc(100vw - 48px));
  border-radius: 20px;
  background: #fff;
  box-shadow: var(--voz-shadow);
  overflow: hidden;
  transform: scale(.92) translateY(20px);
  opacity: 0;
  pointer-events: none;
  transition: transform .28s cubic-bezier(.34,1.56,.64,1), opacity .22s ease;
}
#vozPanel.open {
  transform: scale(1) translateY(0);
  opacity: 1;
  pointer-events: all;
}

/* Header */
#vozHeader {
  background: linear-gradient(135deg, var(--voz-green), var(--voz-blue));
  color: #fff;
  padding: 14px 16px;
  display: flex;
  align-items: center;
  gap: 10px;
}
#vozHeaderIcon {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: rgba(255,255,255,.2);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.3rem;
  flex-shrink: 0;
}
#vozHeaderInfo { flex: 1; }
#vozHeaderInfo strong { display: block; font-size: .95rem; }
#vozHeaderInfo small { font-size: .73rem; opacity: .85; }
#vozCloseBtn {
  background: none;
  border: none;
  color: #fff;
  font-size: 1.2rem;
  cursor: pointer;
  padding: 4px 8px;
  border-radius: 6px;
  transition: background .2s;
}
#vozCloseBtn:hover { background: rgba(255,255,255,.15); }

/* Selector de idioma */
#vozLangSelector {
  display: flex;
  gap: 6px;
  padding: 12px 14px 0;
  flex-wrap: wrap;
}
.voz-lang-btn {
  flex: 1;
  min-width: 80px;
  padding: 7px 4px;
  border-radius: 10px;
  border: 2px solid #e0e7e0;
  background: #f4f7f1;
  color: #2C3E50;
  font-size: .75rem;
  font-weight: 600;
  cursor: pointer;
  transition: all .2s;
  text-align: center;
}
.voz-lang-btn:hover { border-color: var(--voz-green); color: var(--voz-green); }
.voz-lang-btn.active {
  background: var(--voz-green);
  border-color: var(--voz-green);
  color: #fff;
}

/* Selector de campo destino */
#vozTargetSelector {
  padding: 10px 14px 0;
}
#vozTargetSelector label {
  font-size: .75rem;
  font-weight: 600;
  color: #5D6D7E;
  display: block;
  margin-bottom: 4px;
}
#vozTargetSelect {
  width: 100%;
  border: 2px solid #e0e7e0;
  border-radius: 10px;
  padding: 7px 10px;
  font-size: .8rem;
  color: #2C3E50;
  outline: none;
  transition: border-color .2s;
}
#vozTargetSelect:focus { border-color: var(--voz-green); }

/* Área de visualización */
#vozDisplay {
  margin: 12px 14px 0;
  border-radius: 14px;
  border: 2px solid #e0e7e0;
  background: #f9fafb;
  min-height: 90px;
  padding: 12px;
  font-size: .85rem;
  color: #374151;
  line-height: 1.6;
  position: relative;
  transition: border-color .3s;
}
#vozDisplay.escuchando {
  border-color: var(--voz-red);
  background: #fff5f5;
}
#vozDisplay.listo {
  border-color: var(--voz-green2);
  background: #f0faf0;
}
#vozPlaceholder {
  color: #9ca3af;
  font-style: italic;
  font-size: .82rem;
}
#vozTexto {
  display: none;
}
#vozTextoInterim {
  color: #9ca3af;
  font-style: italic;
}

/* Indicador de onda */
#vozWave {
  display: none;
  align-items: center;
  justify-content: center;
  gap: 3px;
  margin: 6px 0 2px;
}
#vozWave.show { display: flex; }
#vozWave span {
  display: inline-block;
  width: 4px;
  border-radius: 4px;
  background: var(--voz-red);
  animation: vozWaveAnim .6s ease-in-out infinite alternate;
}
#vozWave span:nth-child(1) { height: 8px; animation-delay: 0s; }
#vozWave span:nth-child(2) { height: 18px; animation-delay: .1s; }
#vozWave span:nth-child(3) { height: 26px; animation-delay: .2s; }
#vozWave span:nth-child(4) { height: 18px; animation-delay: .3s; }
#vozWave span:nth-child(5) { height: 8px; animation-delay: .4s; }
@keyframes vozWaveAnim {
  from { transform: scaleY(1); }
  to   { transform: scaleY(1.6); }
}

/* Estado */
#vozStatus {
  text-align: center;
  font-size: .75rem;
  padding: 6px 14px 0;
  font-weight: 600;
  min-height: 22px;
}
#vozStatus.grabando { color: var(--voz-red); }
#vozStatus.listo    { color: var(--voz-green2); }
#vozStatus.error    { color: #d97706; }

/* Controles */
#vozControls {
  display: flex;
  gap: 8px;
  padding: 12px 14px 14px;
}
#vozStartBtn {
  flex: 1;
  height: 44px;
  border-radius: 12px;
  border: none;
  cursor: pointer;
  font-size: .85rem;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  transition: all .2s;
  background: linear-gradient(135deg, var(--voz-green), var(--voz-blue));
  color: #fff;
}
#vozStartBtn:hover { opacity: .88; transform: translateY(-1px); }
#vozStartBtn.grabando {
  background: linear-gradient(135deg, var(--voz-red), #991b1b);
  animation: vozPulseBtn 1.2s infinite;
}
@keyframes vozPulseBtn {
  0%,100% { box-shadow: 0 0 0 0 rgba(220,38,38,.4); }
  50%      { box-shadow: 0 0 0 8px rgba(220,38,38,0); }
}
#vozApplyBtn {
  height: 44px;
  padding: 0 14px;
  border-radius: 12px;
  border: 2px solid var(--voz-green);
  background: #fff;
  color: var(--voz-green);
  font-size: .82rem;
  font-weight: 700;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 5px;
  transition: all .2s;
  white-space: nowrap;
}
#vozApplyBtn:hover {
  background: var(--voz-green);
  color: #fff;
}
#vozApplyBtn:disabled {
  opacity: .4;
  cursor: default;
}
#vozClearBtn {
  height: 44px;
  width: 44px;
  border-radius: 12px;
  border: 2px solid #e0e7e0;
  background: #f4f7f1;
  color: #6b7280;
  font-size: 1rem;
  cursor: pointer;
  transition: all .2s;
  display: flex;
  align-items: center;
  justify-content: center;
}
#vozClearBtn:hover { border-color: var(--voz-red); color: var(--voz-red); }

/* Tip navegador */
#vozTip {
  font-size: .68rem;
  color: #9ca3af;
  text-align: center;
  padding: 0 14px 10px;
}
#vozNoSupport {
  display: none;
  background: #fef3c7;
  border-left: 4px solid #f59e0b;
  padding: 10px 14px;
  font-size: .8rem;
  color: #92400e;
  margin: 10px 14px;
  border-radius: 8px;
}
</style>

<!-- Botón flotante -->
<button id="vozToggleBtn" title="Dictar denuncia por voz">
  <span id="vozBadge">VOZ</span>
  <svg viewBox="0 0 24 24">
    <path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/>
    <path d="M19 10v2a7 7 0 0 1-14 0v-2"/>
    <line x1="12" y1="19" x2="12" y2="23"/>
    <line x1="8"  y1="23" x2="16" y2="23"/>
  </svg>
</button>

<!-- Panel -->
<div id="vozPanel">

  <!-- Header -->
  <div id="vozHeader">
    <div id="vozHeaderIcon">🎤</div>
    <div id="vozHeaderInfo">
      <strong>Dictado por Voz</strong>
      <small>Habla y tu denuncia se escribe sola</small>
    </div>
    <button id="vozCloseBtn" title="Cerrar">✕</button>
  </div>

  <!-- Sin soporte -->
  <div id="vozNoSupport">
    ⚠️ Tu navegador no soporta dictado por voz. Usa <strong>Google Chrome</strong> o <strong>Microsoft Edge</strong> para esta función.
  </div>

  <!-- Selector de idioma -->
  <div id="vozLangSelector">
    <button class="voz-lang-btn active" data-lang="es-CO">🇨🇴 Español</button>
    <button class="voz-lang-btn" data-lang="es-ES">🇪🇸 Castellano</button>
    <button class="voz-lang-btn" data-lang="en-US">🇺🇸 English</button>
    <button class="voz-lang-btn" data-lang="fr-FR">🇫🇷 Français</button>
  </div>

  <!-- Selector de campo destino -->
  <div id="vozTargetSelector">
    <label>¿Dónde quieres escribir?</label>
    <select id="vozTargetSelect">
      <option value="descripcion">📝 Descripción del incidente</option>
      <option value="nombre">👤 Tu nombre</option>
      <option value="direccion">📍 Dirección del lugar</option>
      <option value="contacto">📞 Teléfono de contacto</option>
    </select>
  </div>

  <!-- Display de texto -->
  <div id="vozDisplay">
    <span id="vozPlaceholder">Presiona "Iniciar" y comienza a hablar...</span>
    <span id="vozTexto"></span>
    <span id="vozTextoInterim"></span>
  </div>

  <!-- Onda de audio -->
  <div id="vozWave">
    <span></span><span></span><span></span><span></span><span></span>
  </div>

  <!-- Estado -->
  <div id="vozStatus">Listo para escuchar</div>

  <!-- Controles -->
  <div id="vozControls">
    <button id="vozStartBtn">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/>
        <path d="M19 10v2a7 7 0 0 1-14 0v-2"/>
      </svg>
      Iniciar dictado
    </button>
    <button id="vozApplyBtn" disabled title="Aplicar texto al formulario">
      ✅ Aplicar
    </button>
    <button id="vozClearBtn" title="Limpiar">🗑️</button>
  </div>

  <div id="vozTip">🌐 Funciona mejor con Chrome · Requiere micrófono</div>
</div>

<script>
(function () {

  /* ── Elementos ── */
  const toggleBtn  = document.getElementById('vozToggleBtn');
  const panel      = document.getElementById('vozPanel');
  const closeBtn   = document.getElementById('vozCloseBtn');
  const startBtn   = document.getElementById('vozStartBtn');
  const applyBtn   = document.getElementById('vozApplyBtn');
  const clearBtn   = document.getElementById('vozClearBtn');
  const display    = document.getElementById('vozDisplay');
  const placeholder= document.getElementById('vozPlaceholder');
  const textoEl    = document.getElementById('vozTexto');
  const interimEl  = document.getElementById('vozTextoInterim');
  const statusEl   = document.getElementById('vozStatus');
  const waveEl     = document.getElementById('vozWave');
  const noSupport  = document.getElementById('vozNoSupport');
  const targetSel  = document.getElementById('vozTargetSelect');
  const langBtns   = document.querySelectorAll('.voz-lang-btn');

  /* ── Estado ── */
  let recognition  = null;
  let grabando     = false;
  let textoFinal   = '';
  let langActual   = 'es-CO';

  /* ── Verificar soporte ── */
  const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
  if (!SpeechRecognition) {
    noSupport.style.display = 'block';
    startBtn.disabled = true;
    setStatus('Tu navegador no soporta dictado por voz', 'error');
  }

  /* ── Abrir/cerrar panel ── */
  toggleBtn.addEventListener('click', () => {
    panel.classList.toggle('open');
    if (panel.classList.contains('open')) {
      document.getElementById('vozBadge').style.display = 'none';
    }
  });
  closeBtn.addEventListener('click', () => {
    panel.classList.remove('open');
    if (grabando) detener();
  });

  /* ── Selector de idioma ── */
  langBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      langBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      langActual = btn.dataset.lang;
      if (grabando) {
        detener();
        setTimeout(iniciar, 300);
      }
    });
  });

  /* ── Iniciar / Detener ── */
  startBtn.addEventListener('click', () => {
    if (grabando) detener();
    else iniciar();
  });

  function iniciar() {
    if (!SpeechRecognition) return;

    recognition = new SpeechRecognition();
    recognition.lang = langActual;
    recognition.continuous = true;
    recognition.interimResults = true;
    recognition.maxAlternatives = 1;

    recognition.onstart = () => {
      grabando = true;
      startBtn.classList.add('grabando');
      startBtn.innerHTML = `
        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="none">
          <rect x="6" y="6" width="12" height="12" rx="2"/>
        </svg>
        Detener
      `;
      waveEl.classList.add('show');
      display.classList.add('escuchando');
      display.classList.remove('listo');
      setStatus('🔴 Escuchando... habla ahora', 'grabando');
      placeholder.style.display = 'none';
      textoEl.style.display = 'inline';
    };

    recognition.onresult = (e) => {
      let interim = '';
      let final   = '';

      for (let i = e.resultIndex; i < e.results.length; i++) {
        const t = e.results[i][0].transcript;
        if (e.results[i].isFinal) final += t + ' ';
        else interim += t;
      }

      if (final) {
        textoFinal += final;
        textoEl.textContent = textoFinal;
        applyBtn.disabled = false;
      }
      interimEl.textContent = interim;
    };

    recognition.onerror = (e) => {
      const errores = {
        'no-speech'       : '⚠️ No detecté voz. Intenta de nuevo.',
        'audio-capture'   : '⚠️ No se puede acceder al micrófono.',
        'not-allowed'     : '🔒 Permiso de micrófono denegado. Habilítalo en tu navegador.',
        'network'         : '🌐 Error de red. Revisa tu conexión.',
        'aborted'         : '',
      };
      const msg = errores[e.error] || ('Error: ' + e.error);
      if (msg) setStatus(msg, 'error');
      detener(false);
    };

    recognition.onend = () => {
      if (grabando) {
        // Reiniciar automáticamente si sigue grabando (Chrome corta cada ~60s)
        try { recognition.start(); } catch(err) { detener(); }
      }
    };

    try {
      recognition.start();
    } catch(err) {
      setStatus('Error al iniciar el micrófono', 'error');
    }
  }

  function detener(limpiarInterim = true) {
    grabando = false;
    if (recognition) { try { recognition.stop(); } catch(e){} }

    startBtn.classList.remove('grabando');
    startBtn.innerHTML = `
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/>
        <path d="M19 10v2a7 7 0 0 1-14 0v-2"/>
      </svg>
      Iniciar dictado
    `;
    waveEl.classList.remove('show');

    if (limpiarInterim) interimEl.textContent = '';

    if (textoFinal.trim()) {
      display.classList.remove('escuchando');
      display.classList.add('listo');
      setStatus('✅ Dictado listo — presiona "Aplicar"', 'listo');
    } else {
      display.classList.remove('escuchando', 'listo');
      setStatus('Listo para escuchar', '');
      placeholder.style.display = 'inline';
      textoEl.style.display = 'none';
    }
  }

  /* ── Aplicar al formulario ── */
  applyBtn.addEventListener('click', () => {
    const campo = document.getElementById(targetSel.value);
    if (!campo || !textoFinal.trim()) return;

    const textoLimpio = textoFinal.trim();

    // Si es textarea, agregar al texto existente
    if (campo.tagName === 'TEXTAREA') {
      const sep = campo.value.trim() ? ' ' : '';
      campo.value += sep + textoLimpio;
    } else {
      campo.value = textoLimpio;
    }

    // Disparar evento input para contadores y validaciones
    campo.dispatchEvent(new Event('input', { bubbles: true }));

    // Scroll al campo
    campo.scrollIntoView({ behavior: 'smooth', block: 'center' });
    campo.focus();

    // Notificación
    if (typeof showNotification === 'function') {
      showNotification('🎤 Texto dictado aplicado al formulario', 'success');
    }

    // Feedback visual
    applyBtn.innerHTML = '✔ Aplicado';
    applyBtn.style.background = 'var(--voz-green)';
    applyBtn.style.color = '#fff';
    setTimeout(() => {
      applyBtn.innerHTML = '✅ Aplicar';
      applyBtn.style.background = '';
      applyBtn.style.color = '';
    }, 2000);

    // Cerrar panel
    setTimeout(() => panel.classList.remove('open'), 800);
  });

  /* ── Limpiar ── */
  clearBtn.addEventListener('click', () => {
    textoFinal = '';
    textoEl.textContent = '';
    interimEl.textContent = '';
    textoEl.style.display = 'none';
    placeholder.style.display = 'inline';
    display.classList.remove('escuchando', 'listo');
    applyBtn.disabled = true;
    setStatus('Listo para escuchar', '');
    if (grabando) detener();
  });

  /* ── Helper estado ── */
  function setStatus(msg, tipo) {
    statusEl.textContent = msg;
    statusEl.className = tipo || '';
  }

})();
</script>

<!-- ══════════════════════════════════════════════
     FIN DEL WIDGET DE VOZ
══════════════════════════════════════════════ -->