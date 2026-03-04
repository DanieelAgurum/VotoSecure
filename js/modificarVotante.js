// ================= MODIFICAR VOTANTE - CONEXIÓN ESP32 VIA USB =================
let port = null;
let reader = null;
let writer = null;
let inputDone = null;
let outputDone = null;
let serialBuffer = "";
let isConnected = false;
let isConnecting = false;
let currentAction = null;
let cancelRequested = false;
let esp32Connected = false;

// ================= LOG =================
function log(msg) {
    console.log("[MODIFICAR]", msg);
}

function logEnvio(mensaje) {
    const timestamp = new Date().toLocaleTimeString();
    console.log(`[${timestamp}] ➤ ENVÍO:`, mensaje);
}

function logRecepcion(mensaje) {
    const timestamp = new Date().toLocaleTimeString();
    console.log(`[${timestamp}] ◄ RECIBIDO:`, mensaje);
}

// ================= CONEXIÓN SERIE =================
async function conectarESP32() {
    if (isConnecting) return;
    
    if (!("serial" in navigator)) {
        mostrarNotificacion("Navegador no compatible. Use Chrome o Edge.", "error");
        return;
    }
    
    try {
        isConnecting = true;
        actualizarEstadoESP32("connecting");
        
        port = await navigator.serial.requestPort();
        await port.open({ baudRate: 115200 });

        const decoder = new TextDecoderStream();
        inputDone = port.readable.pipeTo(decoder.writable);
        reader = decoder.readable.getReader();

        const encoder = new TextEncoderStream();
        outputDone = encoder.readable.pipeTo(port.writable);
        writer = encoder.writable.getWriter();

        await safeWrite("CONNECT\n");
        
        const respuesta = await waitFor("CONNECTED_OK", 3000);
        
        if (respuesta) {
            isConnected = true;
            actualizarEstadoESP32("connected");
            mostrarNotificacion("ESP32 conectado correctamente", "success");
            console.log("ESP32 conectado vía USB");
            
            // Verificar estado del NFC después de conectar
            await verificarEstadoNFC();
        } else {
            throw new Error("Sin respuesta del ESP32");
        }

    } catch (err) {
        console.error("Error conexión:", err);
        await desconectarESP32(false);
        
        // Verificar si el usuario canceló la selección de puerto o hubo error
        const errorMessage = err.message || "";
        if (errorMessage.includes("No port selected") || 
            errorMessage.includes("cancel") || 
            errorMessage.includes("AbortError") ||
            errorMessage.includes("The port was already closed")) {
            // Usuario canceló o salió del proceso - mostrar SweetAlert de 2 segundos
            Swal.fire({
                icon: 'error',
                title: 'Error de conexión',
                text: 'No se pudo conectar al ESP32. Verifique que el dispositivo esté conectado.',
                timer: 2000,
                timerProgressBar: true,
                showConfirmButton: false
            });
        } else {
            mostrarNotificacion("Error de conexión: " + err.message, "error");
        }
    } finally {
        isConnecting = false;
    }
}

async function desconectarESP32(mostrarAlerta = true) {
    try {
        if (!port) {
            isConnected = false;
            actualizarEstadoESP32("disconnected");
            return;
        }
        
        // Primero marcar como desconectado
        isConnected = false;
        currentAction = null;
        
        // Cerrar writer PRIMERO y enviar DISCONNECT
        if (writer) {
            try {
                await writer.write("DISCONNECT\n");
                await writer.close();
            } catch (_) {}
            writer = null;
        }
        
        // Luego cerrar reader
        if (reader) {
            try {
                await reader.cancel();
            } catch (_) {}
            reader = null;
        }
        
        // Cerrar streams
        if (inputDone) {
            await inputDone.catch(() => {});
            inputDone = null;
        }
        if (outputDone) {
            await outputDone.catch(() => {});
            outputDone = null;
        }
        
        // Cerrar puerto
        await port.close().catch(() => {});
        port = null;
        serialBuffer = "";

        actualizarEstadoESP32("disconnected");
        
        if (mostrarAlerta) {
            mostrarNotificacion("ESP32 desconectado", "success");
        }
        
        console.log("ESP32 desconectado");

    } catch (err) {
        console.error("Error desconexión:", err);
        isConnected = false;
        reader = null;
        writer = null;
        port = null;
        serialBuffer = "";
        actualizarEstadoESP32("disconnected");
    }
}

async function safeWrite(text) {
    if (!writer) throw new Error("Writer no disponible");
    try {
        await writer.write(text);
    } catch (err) {
        console.error("Error escribiendo:", err.message);
        await desconectarESP32(true);
        throw new Error("Conexión perdida con ESP32");
    }
}

async function waitFor(keyword, timeout = 5000) {
    const start = Date.now();
    while (Date.now() - start < timeout) {
        const msg = await readLine();
        if (msg === null) return false;
        if (!msg) continue;
        console.log("◄ RECIBIDO:", msg);
        if (msg.includes(keyword)) return true;
    }
    return false;
}

async function readLine() {
    if (!reader) return null;
    try {
        while (true) {
            const { value, done } = await reader.read();
            if (done) {
                console.log("Puerto cerrado por dispositivo.");
                isConnected = false;
                actualizarEstadoESP32("disconnected");
                return null;
            }
            if (!value) continue;
            serialBuffer += value;
            const lines = serialBuffer.split(/\r?\n/);
            serialBuffer = lines.pop();
            for (let line of lines) {
                line = line.trim();
                if (line.length > 0) return line;
            }
        }
    } catch (err) {
        console.error("Error lectura:", err.message);
        isConnected = false;
        actualizarEstadoESP32("disconnected");
        return null;
    }
}

// ================= DETECCIÓN DE DESCONEXIÓN FÍSICA =================
navigator.serial.addEventListener("disconnect", async () => {
    console.log("Desconexión física detectada.");
    isConnected = false;
    actualizarEstadoESP32("disconnected");
    mostrarNotificacion("Conexión perdida. Reconecte el dispositivo.", "error");
    await desconectarESP32(false);
});

// ================= UI =================
function actualizarEstadoESP32(estado) {
    const statusEl = document.getElementById('esp32Status');
    const btnEl = document.getElementById('btnConectarESP32');
    
    if (!statusEl || !btnEl) return;
    
    // Fuerza que el botón siempre esté habilitado al inicio
    btnEl.disabled = false;
    statusEl.className = 'esp32-status ' + estado;
    
    switch (estado) {
        case 'connected':
            statusEl.innerHTML = '<span class="pulse"></span><span>ESP32 Conectado</span>';
            btnEl.innerHTML = '<i class="bi bi-plug"></i> Desconectar';
            btnEl.classList.remove('btn-primary');
            btnEl.classList.add('btn-danger');
            esp32Connected = true;
            break;
        case 'connecting':
            statusEl.innerHTML = '<span class="pulse"></span><span>Conectando...</span>';
            btnEl.disabled = true;
            break;
        case 'disconnected':
        default:
            statusEl.innerHTML = '<span class="pulse"></span><span>ESP32 Desconectado</span>';
            btnEl.innerHTML = '<i class="bi bi-wifi"></i> Conectar ESP32';
            btnEl.classList.remove('btn-danger');
            btnEl.classList.add('btn-primary');
            esp32Connected = false;
            break;
    }
}

// ================= INICIALIZACIÓN =================
$(document).ready(function() {
    inicializarBotones();
});

function inicializarBotones() {
    // Botón conectar/desconectar ESP32
    $('#btnConectarESP32').on('click', async function() {
        if (isConnected) {
            await desconectarESP32(true);
        } else {
            await conectarESP32();
        }
    });

    // Botones de acción por votante - HUELLA
    $('.btn-huella').on('click', function() {
        if (!isConnected) {
            mostrarNotificacion("Conecte el dispositivo ESP32 primero", "warning");
            return;
        }
        
        const id = $(this).data('id');
        const nombre = $(this).data('nombre');
        const fingerprint = $(this).data('fingerprint');
        abrirModalHuella(id, nombre, fingerprint);
    });

    // Botones de acción por votante - NFC
    $('.btn-nfc').on('click', function() {
        if (!isConnected) {
            mostrarNotificacion("Conecte el dispositivo ESP32 primero", "warning");
            return;
        }
        
        const id = $(this).data('id');
        const nombre = $(this).data('nombre');
        abrirModalNFC(id, nombre);
    });

    // Botones de acción por votante - EDITAR
    $('.btn-editar').on('click', function() {
        const id = $(this).data('id');
        const nombre = $(this).data('nombre');
        abrirModalEditar(id, nombre);
    });

    // Limpiar modal NFC al cerrar
    $('#modalNFC').on('hidden.bs.modal', function() {
        $('#nfcUidInput').val('');
        $('#nfcTokenInput').val('');
    });
    
    // Limpiar modal Huella al cerrar
    $('#modalHuella').on('hidden.bs.modal', function() {
        $('#huellaInput').val('');
        $('#huellaActual').val('');
    });
}

// ================= MODAL HUELLA =================
function abrirModalHuella(id, nombre, fingerprint) {
    currentAction = 'huella';
    cancelRequested = false;
    
    $('#huellaVotanteId').val(id);
    $('#huellaNombre').text(nombre);
    $('#huellaInput').val('');
    $('#huellaActual').val(fingerprint || '');
    $('#huellaSpinner').show();
    
    const modal = new bootstrap.Modal(document.getElementById('modalHuella'));
    modal.show();
    
    // Iniciar lectura de huella
    iniciarLecturaHuella(id, fingerprint);
}

// Función para eliminar huella del sensor
async function deleteFingerprintFromSensor(fingerId) {
    if (!isConnected || !fingerId) return false;
    
    try {
        log("Eliminando plantilla de huella del sensor: " + fingerId);
        await safeWrite("DELETE_FINGER:" + fingerId + "\n");
        
        const msg = await Promise.race([
            readLine(),
            new Promise(resolve => setTimeout(() => resolve(null), 2000))
        ]);
        
        if (msg && msg.includes("FINGER_DELETED")) {
            log("Huella eliminada correctamente del sensor");
            return true;
        } else {
            log("Huella no encontrada o error al eliminar: " + (msg || "sin respuesta"));
            return false;
        }
    } catch (e) {
        log("Error al borrar huella: " + e.message);
        return false;
    }
}

async function iniciarLecturaHuella(votanteId, currentFingerprint) {
    if (!isConnected) {
        mostrarNotificacion("ESP32 no conectado", "error");
        bootstrap.Modal.getInstance(document.getElementById('modalHuella')).hide();
        return;
    }
    
    // Si hay una huella existente, primero la eliminamos del sensor
    const fingerprintValue = currentFingerprint !== null && currentFingerprint !== undefined ? String(currentFingerprint) : '';
    if (fingerprintValue.trim() !== '') {
        log("Huella actual del votante: " + currentFingerprint);
        mostrarNotificacion("Eliminando huella anterior...", "info");
        
        // Intentar eliminar la huella anterior del sensor
        await deleteFingerprintFromSensor(currentFingerprint);
        
        // Pequeña pausa antes de registrar la nueva
        await new Promise(resolve => setTimeout(resolve, 500));
    }
    
    try {
        log("➤ ENVIANDO: CHANGE_FINGER:" + votanteId);
        await safeWrite("CHANGE_FINGER:" + votanteId + "\n");
        
        // Esperar respuesta
        while (true) {
            if (cancelRequested) {
                await safeWrite("CANCEL\n");
                log("Operación cancelada por el usuario");
                break;
            }
            
            const msg = await Promise.race([
                readLine(),
                new Promise(resolve => setTimeout(() => resolve(null), 5000))
            ]);
            
            if (!msg) {
                mostrarNotificacion("Tiempo de espera agotado", "warning");
                continue;
            }
            
            console.log("◄ RECIBIDO:", msg);
            
            if (msg.includes("PUT_FINGER")) {
                $('#huellaSpinner').hide();
                mostrarNotificacion("Coloque el dedo en el lector", "info");
            }
            else if (msg.includes("REMOVE_FINGER")) {
                mostrarNotificacion("Retire el dedo del lector", "info");
            }
            else if (msg.includes("FINGER_ID:")) {
                const fingerId = msg.split(":")[1].trim();
                $('#huellaInput').val(fingerId);
            }
            else if (msg.includes("FINGER_OK") || msg.includes("FINGER_RETRY_OK")) {
                // Huella leída correctamente, guardar automáticamente
                const fingerId = $('#huellaInput').val();
                if (fingerId) {
                    await guardarHuella(votanteId, fingerId);
                }
                break;
            }
            else if (msg.includes("FINGER_FAIL") || msg.includes("FINGER_TIMEOUT")) {
                mostrarNotificacion("Error en lectura de huella. Intente de nuevo.", "error");
                break;
            }
            else if (msg.includes("CANCELLED")) {
                mostrarNotificacion("Operación cancelada", "info");
                break;
            }
            else if (msg.includes("NO_SPACE_FINGER")) {
                mostrarNotificacion("No hay espacio para más huellas en el sensor", "error");
                break;
            }
        }
    } catch (err) {
        console.error("Error:", err);
        mostrarNotificacion("Error: " + err.message, "error");
    }
}

// ================= MODAL NFC =================
function abrirModalNFC(id, nombre) {
    currentAction = 'nfc';
    cancelRequested = false;
    
    $('#nfcVotanteId').val(id);
    $('#nfcNombre').text(nombre);
    $('#nfcUidInput').val('');
    $('#nfcTokenInput').val('');
    $('#nfcSpinner').show();
    
    const modal = new bootstrap.Modal(document.getElementById('modalNFC'));
    modal.show();
    
    // Iniciar lectura NFC
    iniciarLecturaNFC(id);
}

async function iniciarLecturaNFC(votanteId) {
    if (!isConnected) {
        mostrarNotificacion("ESP32 no conectado", "error");
        bootstrap.Modal.getInstance(document.getElementById('modalNFC')).hide();
        return;
    }
    
    try {
        log("➤ ENVIANDO: CHANGE_NFC:" + votanteId);
        await safeWrite("CHANGE_NFC:" + votanteId + "\n");
        
        // Esperar respuesta
        while (true) {
            if (cancelRequested) {
                await safeWrite("CANCEL\n");
                break;
            }
            
            const msg = await Promise.race([
                readLine(),
                new Promise(resolve => setTimeout(() => resolve(null), 5000))
            ]);
            
            if (!msg) {
                mostrarNotificacion("Tiempo de espera agotado", "warning");
                continue;
            }
            
            console.log("◄ RECIBIDO:", msg);
            
            if (msg.includes("WAIT_CARD")) {
                $('#nfcSpinner').hide();
                mostrarNotificacion("Acerque la tarjeta NFC al lector", "info");
            }
            else if (msg.includes("UID:")) {
                const uid = msg.split(":")[1].trim();
                $('#nfcUidInput').val(uid);
            }
            else if (msg.includes("TOKEN:")) {
                const token = msg.split(":")[1].trim();
                $('#nfcTokenInput').val(token);
            }
            else if (msg.includes("NFC_OK")) {
                // NFC leído correctamente, guardar automáticamente
                const uid = $('#nfcUidInput').val();
                const token = $('#nfcTokenInput').val();
                if (uid && token) {
                    await guardarNFC(votanteId, uid, token);
                }
                break;
            }
            else if (msg.includes("ERROR_AUTH") || msg.includes("ERROR_WRITE")) {
                mostrarNotificacion("Error con la tarjeta NFC. Intente con otra.", "error");
                break;
            }
            else if (msg.includes("CANCELLED")) {
                mostrarNotificacion("Operación cancelada", "info");
                break;
            }
        }
    } catch (err) {
        console.error("Error:", err);
        mostrarNotificacion("Error: " + err.message, "error");
    }
}

// ================= MODAL EDITAR =================
function abrirModalEditar(id, nombre) {
    const modal = new bootstrap.Modal(document.getElementById('modalEditarVotante'));
    
    $.ajax({
        url: '/VotoSecure/Controlador/votentesCtrl.php',
        type: 'POST',
        data: {
            accion_ajax: 'obtener_votante',
            id: id
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const v = response.votante;

                $('#editarId').val(v.id);
                $('#editarNombre').val(v.nombre);
                $('#editarApellidoPaterno').val(v.apellido_paterno);
                $('#editarApellidoMaterno').val(v.apellido_materno || '');
                $('#editarFechaNacimiento').val(v.fecha_nacimiento);
                $('#editarGenero').val(v.genero);
                $('#editarCurp').val(v.curp);
                $('#editarRfc').val(v.rfc);
                $('#editarNacionalidad').val(v.nacionalidad || 'Mexicana');
                $('#editarCalle').val(v.calle);
                $('#editarNumExterior').val(v.num_exterior);
                $('#editarNumInterior').val(v.num_interior || '');
                $('#editarColonia').val(v.colonia);
                $('#editarMunicipio').val(v.municipio);
                $('#editarEntidad').val(v.entidad);
                $('#editarCodigoPostal').val(v.codigo_postal);
                $('#editarEntreCalles').val(v.entre_calles || '');
                $('#editarCorreo').val(v.correo);
                $('#editarTelefono').val(v.telefono);
                $('#editarTelefonoFijo').val(v.telefono_fijo || '');
                $('#editarSeccionElectoral').val(v.seccion_electoral);
                $('#editarClaveElector').val(v.clave_elector || '');

                modal.show();
            } else {
                mostrarNotificacion(response.message, 'danger');
            }
        },
        error: function() {
            mostrarNotificacion('Error al cargar los datos del votante', 'danger');
        }
    });
}

// ================= GUARDAR HUELLA =================
function guardarHuella(id, fingerId) {
    if (!fingerId) {
        mostrarNotificacion("No se ha leído ninguna huella", "warning");
        return;
    }

    $.ajax({
        url: '/VotoSecure/Controlador/votentesCtrl.php',
        type: 'POST',
        data: {
            accion_ajax: 'actualizar_huella',
            id: id,
            finger_id: fingerId
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                mostrarNotificacion("Huella actualizada correctamente", "success");
                
                // Cerrar modal
                bootstrap.Modal.getInstance(document.getElementById('modalHuella')).hide();
                
                // Actualizar botón en la tabla
                $(`.btn-huella[data-id="${id}"]`).data('finger-id', fingerId);
                
                // Recargar la página después de un momento
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                mostrarNotificacion(response.message, "danger");
            }
        },
        error: function() {
            mostrarNotificacion("Error al guardar la huella", "danger");
        }
    });
}

// ================= GUARDAR NFC =================
function guardarNFC(votanteId, uid, token) {
    $.ajax({
        url: '/VotoSecure/Controlador/votentesCtrl.php',
        type: 'POST',
        data: {
            accion_ajax: 'actualizar_nfc',
            id: votanteId,
            uid_nfc: uid,
            token_nfc: token
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                mostrarNotificacion("NFC actualizado correctamente", "success");
                
                // Cerrar modal
                bootstrap.Modal.getInstance(document.getElementById('modalNFC')).hide();
                
                // Recargar la página después de un momento
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                mostrarNotificacion(response.message, "danger");
            }
        },
        error: function() {
            mostrarNotificacion("Error al guardar el NFC", "danger");
        }
    });
}

// ================= GUARDAR DATOS =================
$('#btnGuardarDatos').on('click', function() {
    const id = $('#editarId').val();
    
    // Limpiar mensajes anteriores
    $('#editarCamposFaltantes').remove();
    
    const nombre = $('#editarNombre').val();
    const curp = $('#editarCurp').val();
    const rfc = $('#editarRfc').val();
    const apellidoPaterno = $('#editarApellidoPaterno').val();
    const fechaNacimiento = $('#editarFechaNacimiento').val();
    const genero = $('#editarGenero').val();
    const nacionalidad = $('#editarNacionalidad').val();
    const calle = $('#editarCalle').val();
    const numExterior = $('#editarNumExterior').val();
    const colonia = $('#editarColonia').val();
    const municipio = $('#editarMunicipio').val();
    const entidad = $('#editarEntidad').val();
    const codigoPostal = $('#editarCodigoPostal').val();
    const correo = $('#editarCorreo').val();
    const telefono = $('#editarTelefono').val();
    const seccionElectoral = $('#editarSeccionElectoral').val();
    
    // Validar campos obligatorios (excluyendo Entre Calles y Teléfono Fijo)
    const camposFaltantes = [];
    
    if (!nombre.trim()) camposFaltantes.push('Nombre(s)');
    if (!apellidoPaterno.trim()) camposFaltantes.push('Apellido Paterno');
    if (!fechaNacimiento.trim()) camposFaltantes.push('Fecha de Nacimiento');
    if (!genero.trim()) camposFaltantes.push('Género');
    if (!nacionalidad.trim()) camposFaltantes.push('Nacionalidad');
    if (!curp.trim()) camposFaltantes.push('CURP');
    if (!rfc.trim()) camposFaltantes.push('RFC');
    if (!calle.trim()) camposFaltantes.push('Calle');
    if (!numExterior.trim()) camposFaltantes.push('Número Exterior');
    if (!colonia.trim()) camposFaltantes.push('Colonia');
    if (!municipio.trim()) camposFaltantes.push('Municipio');
    if (!entidad.trim()) camposFaltantes.push('Entidad');
    if (!codigoPostal.trim()) camposFaltantes.push('Código Postal');
    if (!correo.trim()) camposFaltantes.push('Correo Electrónico');
    if (!telefono.trim()) camposFaltantes.push('Teléfono Móvil');
    if (!seccionElectoral.trim()) camposFaltantes.push('Sección Electoral');
    
    // Validar longitud de CURP (18 caracteres)
    if (curp.trim() && curp.length !== 18) {
        camposFaltantes.push('CURP debe tener 18 caracteres (actuales: ' + curp.length + ')');
    }
    
    // Validar longitud de RFC (13 caracteres)
    if (rfc.trim() && rfc.length !== 13) {
        camposFaltantes.push('RFC debe tener 13 caracteres (actuales: ' + rfc.length + ')');
    }
    
    // Si hay campos faltantes, mostrar lista con SweetAlert
    if (camposFaltantes.length > 0) {
        const listaHtml = camposFaltantes.map(campo => `<li style="text-align: left;">${campo}</li>`).join('');
        
        // Cerrar el modal primero para que el SweetAlert aparezca encima
        bootstrap.Modal.getInstance(document.getElementById('modalEditarVotante')).hide();
        
        setTimeout(() => {
            Swal.fire({
                icon: 'warning',
                title: 'Campos incompletos o inválidos',
                html: `<ul style="text-align: left; padding-left: 20px;">${listaHtml}</ul>`,
                confirmButtonColor: '#ffc107',
                confirmButtonText: 'Aceptar',
                allowOutsideClick: false,
                willClose: () => {
                    // Volver a abrir el modal después de cerrar el SweetAlert
                    setTimeout(() => {
                        const modal = new bootstrap.Modal(document.getElementById('modalEditarVotante'));
                        modal.show();
                    }, 200);
                }
            });
        }, 300);
        return;
    }

    $.ajax({
        url: '/VotoSecure/Controlador/votentesCtrl.php',
        type: 'POST',
        data: {
            accion_ajax: 'actualizar_datos',
            id: id,
            nombre: nombre,
            apellido_paterno: $('#editarApellidoPaterno').val(),
            apellido_materno: $('#editarApellidoMaterno').val(),
            fecha_nacimiento: $('#editarFechaNacimiento').val(),
            genero: $('#editarGenero').val(),
            nacionalidad: $('#editarNacionalidad').val(),
            curp: curp,
            rfc: $('#editarRfc').val(),
            calle: $('#editarCalle').val(),
            num_exterior: $('#editarNumExterior').val(),
            num_interior: $('#editarNumInterior').val(),
            colonia: $('#editarColonia').val(),
            codigo_postal: $('#editarCodigoPostal').val(),
            municipio: $('#editarMunicipio').val(),
            entidad: $('#editarEntidad').val(),
            entre_calles: $('#editarEntreCalles').val(),
            correo: $('#editarCorreo').val(),
            telefono: $('#editarTelefono').val(),
            telefono_fijo: $('#editarTelefonoFijo').val(),
            seccion_electoral: $('#editarSeccionElectoral').val(),
            clave_elector: $('#editarClaveElector').val()
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: 'Datos actualizados correctamente',
                    confirmButtonColor: '#28a745'
                });
                
                actualizarFilaTabla(response.votante);
                
                bootstrap.Modal.getInstance(document.getElementById('modalEditarVotante')).hide();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message,
                    confirmButtonColor: '#dc3545'
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al guardar los datos',
                confirmButtonColor: '#dc3545'
            });
        }
    });
});

function actualizarFilaTabla(votante) {
    const fila = $(`#votante-${votante.id}`);
    
    if (fila.length) {
        fila.find('td:nth-child(2)').html(`<strong>${votante.nombre} ${votante.apellido_paterno} ${votante.apellido_materno || ''}</strong>`);
        fila.find('td:nth-child(3)').html(`<small>${votante.direccion_completa}</small>`);
        fila.find('td:nth-child(4)').text(votante.correo);
        fila.find('td:nth-child(5)').text(votante.seccion_electoral);
        
        let estadoBadge = '';
        switch (votante.estado) {
            case 'activo':
                estadoBadge = '<span class="badge bg-success">Activo</span>';
                break;
            case 'inactivo':
                estadoBadge = '<span class="badge bg-secondary">Inactivo</span>';
                break;
            case 'votado':
                estadoBadge = '<span class="badge bg-info">Votado</span>';
                break;
        }
        fila.find('td:nth-child(6)').html(estadoBadge);
    }
}

// ================= NOTIFICACIONES =================
// Success = SweetAlert2 Modal, Error = SweetAlert2 de 2 segundos
function mostrarNotificacion(mensaje, tipo = 'info') {
    
    // Para éxito, mostrar SweetAlert2 modal
    if (tipo === 'success') {
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: mensaje,
            timer: 3000,
            timerProgressBar: true,
            confirmButtonColor: '#28a745'
        });
        return;
    }
    
    // Para errores y advertencias, usar SweetAlert2 con 2 segundos (sin allowOutsideClick para toasts)
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true
    });

    switch (tipo) {
        case 'danger':
        case 'error':
            Toast.fire({
                icon: 'error',
                title: 'Error',
                text: mensaje
            });
            break;
        case 'warning':
            Toast.fire({
                icon: 'warning',
                title: 'Advertencia',
                text: mensaje
            });
            break;
        default:
            Toast.fire({
                icon: 'info',
                title: 'Notificación',
                text: mensaje
            });
    }
}

// ================= VERIFICAR ESTADO NFC =================
async function verificarEstadoNFC() {
    if (!isConnected) return;
    
    try {
        await safeWrite("STATUS\n");
        
        const msg = await Promise.race([
            readLine(),
            new Promise(resolve => setTimeout(() => resolve(null), 3000))
        ]);
        
        if (msg && msg.includes("NFC:FAIL")) {
            mostrarNotificacion("NFC desconectado. Verifique la conexión.", "error");
        } else if (msg && msg.includes("NFC:OK")) {
            console.log("NFC conectado correctamente");
        }
    } catch (err) {
        console.error("Error verificando estado NFC:", err);
    }
}

// ================= EXPORTAR FUNCIONES GLOBALES =================
window.conectarESP32 = conectarESP32;
window.desconectarESP32 = desconectarESP32;