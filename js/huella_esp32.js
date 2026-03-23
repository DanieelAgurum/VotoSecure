/**
 * HuellaESP32 — Web Serial API
 * Conexión automática al lector AS608 vía ESP32
 * Sin prompt al usuario si el puerto ya fue autorizado antes
 */
class HuellaESP32 {
    constructor() {
        this.port        = null;
        this.reader      = null;
        this.writer      = null;
        this.connecting  = false;
        this.buffer      = '';          // Buffer de líneas parciales
        this.pendingVerify = null;
    }

    // ─── Conectar ────────────────────────────────────────────
    async connect() {
        try {
            if (!navigator.serial) {
                throw new Error('Web Serial API no soportada. Usa Chrome o Edge.');
            }

            const port = await this.getAvailablePort();
            if (!port) {
                console.log('ESP32: ningún puerto autorizado aún.');
                return false;
            }

            // Si el reader anterior sigue vivo, cancelarlo primero
            if (this.reader) {
                try { await this.reader.cancel(); } catch {}
                this.reader = null;
            }

            // Si el puerto ya tiene readable activo, reutilizarlo directo
            // Si no, cerrarlo (por si quedó en estado roto) y volver a abrir
            if (port.readable) {
                console.log('Puerto ya abierto, reutilizando...');
                this.port = port;
            } else {
                // Intentar cerrar primero por si quedó en estado inconsistente
                try { await port.close(); } catch {}

                try {
                    await port.open({ baudRate: 115200 });
                } catch (openErr) {
                    // "Failed to open" casi siempre = otra app tiene el puerto
                    if (openErr.message.includes('Failed to open')) {
                        console.error('Puerto ocupado por otra aplicación (Arduino IDE, screen, etc). Ciérrala y recarga.');
                    }
                    throw openErr;
                }
                this.port = port;
            }

            this.reader = this.port.readable.getReader();
            console.log('ESP32 conectado.');

            // Read loop en segundo plano — NO hace await aquí
            this._startReadLoop();
            return true;

        } catch (error) {
            console.error('ESP32 connect error:', error.message);
            return false;
        }
    }

    // ─── Read loop (no bloqueante) ───────────────────────────
    _startReadLoop() {
        const reader = this.reader;

        const loop = async () => {
            try {
                while (true) {
                    const { value, done } = await reader.read();
                    if (done) break;

                    // Acumular en buffer y procesar líneas completas
                    this.buffer += new TextDecoder().decode(value);
                    this._processBuffer();
                }
            } catch (err) {
                // Desconexión o cancel() llamado desde disconnect()
                if (err.name !== 'AbortError') {
                    console.warn('ESP32 read loop terminado:', err.message);
                }
            }
        };

        loop(); // Fire and forget — no await
    }

    // ─── Procesar buffer línea por línea ────────────────────
    _processBuffer() {
        const lines = this.buffer.split('\n');
        // La última parte puede ser incompleta — guardarla de vuelta
        this.buffer = lines.pop();

        for (const line of lines) {
            const trimmed = line.trim();
            if (trimmed) this._handleLine(trimmed);
        }
    }

    // ─── Manejar cada línea del ESP32 (texto plano) ─────────
    _handleLine(line) {
        console.log('ESP32 →', line);

        if (line.startsWith('FINGER_ID:')) {
            this._tmpFingerId = parseInt(line.split(':')[1]);
            return;
        }

        if (line.startsWith('CONFIDENCE:')) {
            this._tmpConfidence = parseInt(line.split(':')[1]);
            return;
        }

        if (line === 'FINGER_OK') {
            // Cancelar el timer de no_match si estaba pendiente
            if (this._noMatchTimer) {
                clearTimeout(this._noMatchTimer);
                this._noMatchTimer = null;
            }

            const data = {
                valida:     true,
                votante_id: this._tmpFingerId   || 0,
                confianza:  this._tmpConfidence || 0
            };
            this._tmpFingerId   = null;
            this._tmpConfidence = null;

            if (this.pendingVerify) {
                this.pendingVerify.resolve(data);
                this.pendingVerify = null;
            } else {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: '✅ Huella reconocida',
                        text: `Votante ID: ${data.votante_id}`,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            }
            return;
        }

        if (line === 'FINGER_NO_MATCH') {
            console.warn('Huella no reconocida en sensor.');
            if (this.pendingVerify) {
                // ⚠️ No resolver de inmediato: el sensor AS608 a veces manda FINGER_NO_MATCH
                // en un primer intento y luego FINGER_OK si el dedo sigue puesto.
                // Esperamos 2.5s — si en ese tiempo llega FINGER_OK, él resuelve la Promise.
                // Si no llega nada, ahí sí resolvemos con no_match.
                if (this._noMatchTimer) clearTimeout(this._noMatchTimer);
                const pendingAtThisMoment = this.pendingVerify;
                this._noMatchTimer = setTimeout(() => {
                    // Solo resolver si la Promise sigue siendo la misma (no fue resuelta por FINGER_OK)
                    if (this.pendingVerify === pendingAtThisMoment && this.pendingVerify) {
                        console.warn('Sin FINGER_OK tras espera → resolviendo como no_match.');
                        this.pendingVerify.resolve({ no_match: true });
                        this.pendingVerify = null;
                    }
                    this._noMatchTimer = null;
                }, 2500);
            }
            return;
        }

        if (line === 'SENSOR_FAIL') {
            console.error('❌ Sensor AS608 falló.');
            if (this.pendingVerify) {
                this.pendingVerify.reject(new Error('Error en sensor de huella'));
                this.pendingVerify = null;
            }
            return;
        }

        if (line === 'SENSOR_OK' || line === 'READY') {
            console.log('ESP32 listo.');
            return;
        }
    }

    // ─── Verificar huella (retorna Promise) ──────────────────
    verifyFingerprint(timeoutMs = 30000) {
        return new Promise((resolve, reject) => {
            if (!this.port || !this.reader) {
                return reject(new Error('ESP32 no conectado'));
            }

            this.pendingVerify = { resolve, reject };
            console.log(`🔍 Esperando huella... (${timeoutMs / 1000}s máx.)`);

            const timer = setTimeout(() => {
                if (this.pendingVerify) {
                    this.pendingVerify.reject(
                        new Error('timeout — Acerque el dedo al lector')
                    );
                    this.pendingVerify = null;
                }
            }, timeoutMs);

            const originalResolve = resolve;
            const originalReject  = reject;
            this.pendingVerify.resolve = (val) => { clearTimeout(timer); originalResolve(val); };
            this.pendingVerify.reject  = (err) => { clearTimeout(timer); originalReject(err); };
        });
    }

    // ─── Auto-detect puerto ya autorizado ───────────────────
    async getAvailablePort() {
        const ports = await navigator.serial.getPorts();
        if (ports.length === 0) return null;

        console.log(`Puerto(s) disponibles: ${ports.length}`);

        if (ports.length === 1) return ports[0];

        // 0x10C4 = Silicon Labs CP210x | 0x1A86 = CH340 | 0x0403 = FTDI | 0x303A = Espressif nativo
        const ESP32_VIDS = [0x10C4, 0x1A86, 0x0403, 0x303A];

        for (const port of ports) {
            const info = port.getInfo();
            console.log('Puerto info:', JSON.stringify(info));
            if (ESP32_VIDS.includes(info.usbVendorId)) {
                console.log(`ESP32 identificado (VID: 0x${info.usbVendorId?.toString(16)})`);
                return port;
            }
        }

        console.warn('No se identificó ESP32 por VID, usando ports[0] como fallback');
        return ports[0];
    }

    // ─── Enviar comando al ESP32 ─────────────────────────────
    async sendCommand(cmd) {
        if (!this.port || !this.port.writable) return;
        try {
            const writer = this.port.writable.getWriter();
            await writer.write(new TextEncoder().encode(cmd + '\n'));
            writer.releaseLock();
            console.log('→ ESP32 cmd:', cmd);
        } catch (e) {
            console.warn('sendCommand error:', e.message);
        }
    }

    // ─── Desconectar limpiamente ─────────────────────────────
    async disconnect() {
        if (this.pendingVerify) {
            this.pendingVerify.reject(new Error('ESP32 desconectado'));
            this.pendingVerify = null;
        }
        try {
            if (this.reader) {
                await this.reader.cancel();
                this.reader = null;
            }
            if (this.port) {
                await this.port.close();
                this.port = null;
            }
        } catch (e) {
            // Ignorar errores al cerrar
        }
        this.buffer = '';
        console.log('ESP32 desconectado.');
    }

    // ─── Auto-connect con reintentos ─────────────────────────
    async autoConnect() {
        if (this.connecting) return;
        this.connecting = true;

        const MAX_INTENTOS  = 10;
        const DELAY_MS      = 2000;

        for (let i = 0; i < MAX_INTENTOS; i++) {
            const ok = await this.connect();
            if (ok) {
                this.connecting = false;
                return true;
            }
            await new Promise(r => setTimeout(r, DELAY_MS));
        }

        this.connecting = false;
        console.warn('ESP32: no conectado después de los reintentos.');
        return false;
    }
}

// ─── Instancia global ────────────────────────────────────────
const huella = new HuellaESP32();

// Eventos USB plug/unplug
if (navigator.serial) {
    navigator.serial.addEventListener('connect', (e) => {
        console.log('USB conectado:', e.target);
        huella.autoConnect();
    });

    navigator.serial.addEventListener('disconnect', (e) => {
        console.log('USB desconectado:', e.target);
        huella.disconnect();
    });
}

// ─── Botón de primer permiso ─────────────────────────────────
function _inyectarBotonPermiso() {
    if (document.getElementById('btn-esp32-permiso')) return;

    if (!document.getElementById('esp32-btn-style')) {
        const style = document.createElement('style');
        style.id = 'esp32-btn-style';
        style.textContent = `
            @keyframes esp32Pulse {
                0%,100% { box-shadow: 0 8px 24px rgba(34,211,238,.35); }
                50%      { box-shadow: 0 8px 36px rgba(34,211,238,.65); }
            }
        `;
        document.head.appendChild(style);
    }

    const btn = document.createElement('button');
    btn.id = 'btn-esp32-permiso';
    btn.innerHTML = '<i class="bi bi-usb-plug-fill" style="margin-right:8px"></i>Conectar lector de huellas';
    btn.style.cssText = [
        'position:fixed', 'bottom:24px', 'right:24px', 'z-index:9999',
        'background:linear-gradient(135deg,#22D3EE,#06B6D4)',
        'color:#0F172A', 'border:none', 'padding:12px 22px',
        'border-radius:12px', 'font-weight:700', 'font-size:.95rem',
        'cursor:pointer', 'display:flex', 'align-items:center',
        'animation:esp32Pulse 2s infinite'
    ].join(';');

    btn.onclick = async () => {
        try {
            const port = await navigator.serial.requestPort();
            console.log('Puerto autorizado:', port.getInfo());
            btn.remove();
            await huella.autoConnect();
        } catch (e) {
            console.warn('Permiso cancelado por el usuario:', e.message);
        }
    };

    document.body.appendChild(btn);
    console.log('Haz clic en el botón flotante para autorizar el ESP32 (solo la primera vez).');
}

// ─── Auto-init al cargar la página ──────────────────────────
document.addEventListener('DOMContentLoaded', async () => {
    console.log('VotoSecure: iniciando ESP32...');
    window.addEventListener('beforeunload', () => huella.disconnect());

    if (!navigator.serial) {
        console.error('Web Serial no soportado. Usa Chrome o Edge.');
        return;
    }

    const ports = await navigator.serial.getPorts();

    if (ports.length > 0) {
        huella.autoConnect();
    } else {
        _inyectarBotonPermiso();
    }

    // ─── Selección / deselección visual de cards ─────────────
    // pointer-events:none en el radio para que todos los clicks
    // lleguen al label y no al input directamente.
    document.querySelectorAll('.candidate-card .radio-input').forEach(r => {
        r.style.pointerEvents = 'none';
    });

    document.querySelectorAll('.candidate-card').forEach(card => {
        card.addEventListener('mousedown', function (e) {
            e.preventDefault(); // Evita que el browser marque el radio antes que nosotros

            const puesto     = this.dataset.puesto;
            const yaSelected = this.classList.contains('selected');
            const radio      = this.querySelector('.radio-input');

            // Limpiar todas las cards del mismo puesto
            document.querySelectorAll(`[data-puesto="${puesto}"]`).forEach(c => {
                c.classList.remove('selected');
                c.querySelector('.radio-input').checked = false;
            });

            if (yaSelected) {
                // Segundo click → deseleccionar (queda vacío)
                radio.checked = false;
            } else {
                // Primer click → seleccionar
                this.classList.add('selected');
                radio.checked = true;
            }
        });
    });

    // ─── Botón VOTAR ─────────────────────────────────────────
    const btnVotar = document.getElementById('btnVotar');
    if (btnVotar) {
        btnVotar.addEventListener('click', function () {
            const formData = new FormData(document.getElementById('boletaForm'));
            const votos    = Object.fromEntries(formData);

            // Detectar puestos sin selección
            const omitidos = [];
            document.querySelectorAll('.election-section').forEach(section => {
                const puesto      = section.dataset.position.toUpperCase();
                const seleccionado = document.querySelector(
                    `input[name="voto[${puesto}]"]:checked`
                );
                if (!seleccionado) omitidos.push(puesto);
            });

            if (omitidos.length) {
                Swal.fire({
                    title: 'Votos omitidos',
                    html: `<strong>Puestos sin selección:</strong><br><br>
                           ${omitidos.join('<br>')}
                           <br><br><small><em>Se considerarán votos en blanco</em></small>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Aceptar',
                    cancelButtonText: 'Revisar',
                    confirmButtonColor: '#22D3EE'
                }).then(result => {
                    if (result.isConfirmed) _verificarHuella(votos);
                });
            } else {
                _verificarHuella(votos);
            }
        });
    }
});

// ─── Mostrar modal de espera con contador regresivo ──────────
function _mostrarModalEspera(segundos, onCancelar) {
    const TIMEOUT_MS = segundos * 1000;

    Swal.fire({
        title: 'Verificación Biométrica',
        html: `<div class="text-center">
                 <div class="spinner-border text-primary mb-3" style="width:4rem;height:4rem;"></div>
                 <p class="mb-0"><strong>Acerque su huella digital</strong><br>
                 <small class="text-muted">Coloque el dedo en el lector</small></p>
                 <div class="mt-3">
                   <span id="swal-countdown"
                         style="font-size:2rem;font-weight:700;color:#06B6D4;">${segundos}</span>
                   <span class="text-muted" style="font-size:.85rem;">s</span>
                 </div>
               </div>`,
        allowOutsideClick: false,
        showConfirmButton: false,
        showCancelButton: true,
        cancelButtonText: 'Cancelar',
        didOpen: () => {
            // Cancelar
            Swal.getCancelButton().onclick = () => {
                if (window.huella.pendingVerify) {
                    window.huella.pendingVerify.reject(new Error('Proceso cancelado por usuario'));
                    window.huella.pendingVerify = null;
                }
                Swal.close();
                if (typeof onCancelar === 'function') onCancelar();
            };

            // Contador regresivo
            let restante = segundos;
            const countdownEl = document.getElementById('swal-countdown');
            const intervalo = setInterval(() => {
                restante--;
                if (countdownEl) {
                    countdownEl.textContent = restante;
                    // Cambiar color al llegar a los últimos 10s
                    if (restante <= 10) countdownEl.style.color = '#EF4444';
                }
                if (restante <= 0) clearInterval(intervalo);
            }, 1000);

            // Guardar referencia para limpiarlo si el modal se cierra antes
            Swal._countdownInterval = intervalo;
        },
        willClose: () => {
            if (Swal._countdownInterval) {
                clearInterval(Swal._countdownInterval);
                Swal._countdownInterval = null;
            }
        }
    });

    return TIMEOUT_MS;
}

// ─── Verificación biométrica y envío de voto ─────────────────
async function _verificarHuella(votos, intentoActual = 1) {
    const MAX_INTENTOS = 3;

    if (!window.huella?.port) {
        Swal.fire({
            title: 'ESP32 No Detectado',
            text: 'Conecta el lector de huellas USB y recarga la página',
            icon: 'warning',
            confirmButtonText: 'OK'
        });
        return;
    }

    try {
        // Modal con contador regresivo
        _mostrarModalEspera(30);

        console.log(`Verificando huella... (intento ${intentoActual}/${MAX_INTENTOS})`);
        const huellaData = await window.huella.verifyFingerprint(30000);

        Swal.close();

        // Huella no reconocida → reintentar hasta MAX_INTENTOS
        if (huellaData.no_match) {
            if (intentoActual < MAX_INTENTOS) {
                const result = await Swal.fire({
                    title: '❌ Huella no reconocida',
                    html: `<p>No se pudo identificar tu huella.<br>
                           <strong>Intento ${intentoActual} de ${MAX_INTENTOS}</strong></p>
                           <p class="text-muted" style="font-size:.9rem;">
                             Coloca el dedo con más presión y centrado en el sensor.
                           </p>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '🔄 Reintentar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#22D3EE'
                });

                if (result.isConfirmed) {
                    return _verificarHuella(votos, intentoActual + 1);
                }
                // Usuario canceló en el aviso
                return;
            } else {
                // Agotó todos los intentos
                Swal.fire({
                    title: '⛔ Acceso denegado',
                    html: `<p>No se reconoció tu huella después de <strong>${MAX_INTENTOS} intentos</strong>.</p>
                           <p class="text-muted" style="font-size:.9rem;">
                             Contacta a un administrador si crees que es un error.
                           </p>`,
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
                return;
            }
        }

        console.log('Huella capturada, ID sensor:', huellaData.votante_id);

        // Enviar voto al backend
        const response = await fetch('/VotoSecure/api/votarHuella.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                votos:             votos,
                huella_votante_id: huellaData.votante_id
            })
        });

        const data = await response.json();
        console.log('Voto API:', data);

        if (data.success) {
            await window.huella.sendCommand('VOTO_OK');
            _limpiarBoleta();
            Swal.fire({
                title: '¡VOTO REGISTRADO!',
                html: '¡Gracias por votar!',
                icon: 'success',
                confirmButtonText: 'Finalizar'
            });
        } else if (data.ya_voto) {
            await window.huella.sendCommand('VOTO_ERROR');
            _limpiarBoleta();
            Swal.fire({
                title: 'Voto duplicado',
                text: 'Este usuario ya emitió su voto.',
                icon: 'warning',
                confirmButtonText: 'Aceptar'
            });
        } else {
            await window.huella.sendCommand('VOTO_ERROR');
            Swal.fire('Error', data.message || 'Error al guardar el voto', 'error');
        }

    } catch (error) {
        Swal.close();
        console.error('Huella Error:', error.message);

        let msg = error.message;
        if (msg.includes('timeout'))       msg = 'Tiempo agotado. Acerque la huella nuevamente.';
        else if (msg.includes('desconect')) msg = 'Lector ESP32 desconectado. Reconecta el USB.';

        Swal.fire({
            title: 'Proceso cancelado',
            text: msg,
            icon: 'error',
            confirmButtonText: 'Reintentar'
        });
    }
}

// ─── Limpiar boleta tras finalizar el proceso ────────────────
// Deselecciona todas las cards y desmarca todos los radios.
function _limpiarBoleta() {
    document.querySelectorAll('.candidate-card.selected').forEach(c => {
        c.classList.remove('selected');
    });
    document.querySelectorAll('.candidate-card .radio-input').forEach(r => {
        r.checked = false;
    });
}

// Disponible globalmente para Plantilla.php
window.huella = huella;