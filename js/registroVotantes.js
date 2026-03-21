// ================= ESP32 NFC + FINGERPRINT =================
let port = null;
let reader = null;
let writer = null;
let inputDone = null;
let outputDone = null;
let serialBuffer = "";
let isConnected = false;
let isConnecting = false;
let cancelRequested = false;
let capturedImageData = null;
let videoStream = null;
let nfcStatus = false;
let isRegistering = false;
let nfcDisconnectNotified = false;
let nfcReconnectNotified = false;
let backgroundCheckInterval = null;
let registrationSwal = null;

const esp32Status = document.getElementById("esp32Status");

// ================= LOG =================
function log(msg) {
  const logEl = document.getElementById("esp32Log");
  if (!logEl) return;
  logEl.style.display = "block";
  logEl.textContent += msg + "\n";
  logEl.scrollTop = logEl.scrollHeight;
}

function logEnvio(mensaje, destino) {
  const timestamp = new Date().toLocaleTimeString();
  console.log(`[${timestamp}] ➤ ENVÍO [${destino}]:`, mensaje);
  log(`[${timestamp}] ➤ ENVÍO [${destino}]: ${mensaje}`);
}

function logRecepcion(mensaje, origen) {
  const timestamp = new Date().toLocaleTimeString();
  console.log(`[${timestamp}] ◄ RECIBIDO [${origen}]:`, mensaje);
  log(`[${timestamp}] ◄ RECIBIDO [${origen}]: ${mensaje}`);
}

function logDatos(datos, contexto) {
  const timestamp = new Date().toLocaleTimeString();
  console.log(`[${timestamp}]  [${contexto}]:`, datos);
  log(`[${timestamp}]  [${contexto}]: ${JSON.stringify(datos)}`);
}

// ================= TOAST SIMPLE =================
let currentToastId = null;

function showToast(message, type = "info") {
  console.log("showToast llamado:", message, type);
  
  if (currentToastId) {
    const existingToast = document.getElementById(currentToastId);
    if (existingToast) existingToast.remove();
  }
  
  if (!document.getElementById('toast-animations')) {
    const style = document.createElement('style');
    style.id = 'toast-animations';
    style.textContent = `
      @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
      @keyframes slideOut { from { transform: translateX(0); opacity: 1; } to { transform: translateX(100%); opacity: 0; } }
      .toast-container { position: fixed; top: 20px; right: 20px; z-index: 999999; }
    `;
    document.head.appendChild(style);
  }
  
  let bgColor = '#17a2b8', textColor = 'white', iconEmoji = 'ℹ️';
  switch(type) {
    case 'error': bgColor = '#dc3545'; iconEmoji = '❌'; break;
    case 'success': bgColor = '#28a745'; iconEmoji = '✅'; break;
    case 'warning': bgColor = '#ffc107'; textColor = '#000'; iconEmoji = '⚠️'; break;
  }
  
  let container = document.getElementById('toast-container');
  if (!container) {
    container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'toast-container';
    document.body.appendChild(container);
  }
  
  currentToastId = 'custom-toast-' + Date.now();
  const toastHtml = `
    <div id="${currentToastId}" style="padding: 16px 24px; border-radius: 8px; color: ${textColor}; font-weight: 500; font-size: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.4); animation: slideIn 0.3s ease-out; max-width: 400px; margin-bottom: 10px; background: ${bgColor}; display: flex; align-items: center; gap: 10px; border: 2px solid rgba(255,255,255,0.3);">
      <span style="font-size: 18px;">${iconEmoji}</span>
      <span>${message}</span>
    </div>
  `;
  container.insertAdjacentHTML('beforeend', toastHtml);
  
  setTimeout(() => {
    const toastEl = document.getElementById(currentToastId);
    if (toastEl) {
      toastEl.style.animation = 'slideOut 0.3s ease-in';
      setTimeout(() => { if (toastEl && toastEl.parentNode) toastEl.remove(); currentToastId = null; }, 300);
    }
  }, 5000);
}

// ================= ACTUALIZAR INDICADOR DE ESTADO =================
function updateHardwareStatus(nfc) {
  nfcStatus = nfc;
  if (!isConnected) return;
  
  let statusHtml = '<div class="d-flex gap-2">';
  if (nfc) {
    statusHtml += '<span class="badge bg-success">NFC OK</span>';
  } else {
    statusHtml += '<span class="badge bg-danger">NFC FAIL</span>';
  }
  statusHtml += '</div>';
  if (esp32Status) esp32Status.innerHTML = statusHtml;
}

// ================= VERIFICACIÓN EN BACKGROUND PARA DETECTAR DESCONEXIONES =================
async function startBackgroundNFCCheck() {
  if (backgroundCheckInterval || isRegistering) return;
  
  log("Iniciando verificación de NFC en background...");
  
  backgroundCheckInterval = setInterval(async () => {
    if (!isConnected || isRegistering) {
      return;
    }

    if (!port || !port.readable || !port.writable) {
      log("Puerto no disponible para verificación NFC");
      return;
    }
    
    try {
      await safeWrite("STATUS\n");
      
      const msg = await Promise.race([
        readLine(),
        new Promise(resolve => setTimeout(() => resolve(null), 1000))
      ]);
      
      if (!msg) return;
      
      const cleanMsg = msg.trim();
      
      if (cleanMsg.startsWith("NFC:")) {
        const nfcOk = cleanMsg.includes("OK");
        
        if (nfcOk !== nfcStatus) {
          nfcDisconnectNotified = false;
          nfcReconnectNotified = false;
        }

        if (!nfcOk && nfcStatus && !nfcDisconnectNotified) {
          updateHardwareStatus(false);
          showToast("NFC desconectado", "error");
          log("NFC desconectado detectado - Estado: FAIL");
          nfcDisconnectNotified = true;
        }
        else if (nfcOk && !nfcStatus && !nfcReconnectNotified) {
          updateHardwareStatus(true);
          showToast("NFC reconectado", "success");
          log("NFC reconectado detectado - Estado: OK");
          nfcReconnectNotified = true;
        }
        else if (nfcOk !== nfcStatus) {
          nfcStatus = nfcOk;
          updateHardwareStatus(nfcOk);
          log("NFC estado actualizado: " + (nfcOk ? "OK" : "FAIL"));
        }
      }
    } catch (e) {
      log("Error en verificación NFC: " + e.message);
    }
  }, 3000);
}

function stopBackgroundNFCCheck() {
  if (backgroundCheckInterval) {
    clearInterval(backgroundCheckInterval);
    backgroundCheckInterval = null;
  }
  log("Verificación de NFC detenida.");
}

// ================= VERIFICACIÓN MANUAL DE ESTADO NFC =================
async function checkNFCCurrentStatus() {
  if (!isConnected) {
    showToast("ESP32 no conectado", "warning");
    return;
  }
  
  if (isRegistering) {
    showToast("Espere a que termine el registro actual", "warning");
    return;
  }
  
  try {
    log("Verificación manual de estado NFC...");
    logEnvio("STATUS", "ESP32");
    await safeWrite("STATUS\n");
    
    const msg = await Promise.race([
      readLine(),
      new Promise(resolve => setTimeout(() => resolve(null), 2000))
    ]);
    
    if (!msg) {
      showToast("Sin respuesta del dispositivo", "error");
      return;
    }
    
    const cleanMsg = msg.trim();
    logRecepcion(cleanMsg, "ESP32");
    
    if (cleanMsg.startsWith("NFC:")) {
      const nfcOk = cleanMsg.includes("OK");
      nfcStatus = nfcOk;
      updateHardwareStatus(nfcOk);
      
      if (nfcOk) {
        showToast("NFC conectado y funcionando", "success");
        log("NFC OK - Verificación manual");
      } else {
        showToast("NFC desconectado o no detectado", "error");
        log("NFC FAIL - Verificación manual");
      }
    } else {
      showToast("Respuesta no esperada del dispositivo", "warning");
    }
  } catch (e) {
    log("Error en verificación manual: " + e.message);
    showToast("Error al verificar: " + e.message, "error");
  }
}

// ================= VALIDACIÓN =================
function esp32Disponible() {
  return isConnected && port?.readable && port?.writable && reader && writer;
}

// ================= CONEXIÓN / DESCONECTAR =================
async function toggleConnectESP32() {
  if (isConnecting) return;
  if (!("serial" in navigator)) {
    Swal.fire({ icon: "error", title: "Navegador no compatible", text: "Use Chrome o Edge" });
    return;
  }
  if (!isConnected) await connectESP32();
  else await disconnectESP32(true);
}

async function connectESP32() {
  try {
    isConnecting = true;
    port = await navigator.serial.requestPort();
    await port.open({ baudRate: 115200 });

    const decoder = new TextDecoderStream();
    inputDone = port.readable.pipeTo(decoder.writable);
    reader = decoder.readable.getReader();

    const encoder = new TextEncoderStream();
    outputDone = encoder.readable.pipeTo(port.writable);
    writer = encoder.writable.getWriter();

    log("Esperando Conexión...");
    logEnvio("CONNECT", "ESP32");
    await safeWrite("CONNECT\n");

    const ok = await waitFor("CONNECTED_OK", 3000);
    isConnected = true;
    updateUIConnected(true);
    
    await new Promise(resolve => setTimeout(resolve, 200));

    log("Verificando estado del hardware...");
    const hardwareStatus = await checkHardwareStatus();
    
    if (!hardwareStatus.nfc) {
      showToast("NFC desconectado o no detectado", "error");
      log("NFC no detectado al conectar");
    } else {
      showToast("ESP32 y NFC conectados correctamente", "success");
      log("NFC detectado correctamente");
    }
    
    log("Conexión establecida.");
    startBackgroundNFCCheck();

  } catch (err) {
    log("Error conexión: " + err.message);
    await disconnectESP32(false);
    Swal.fire({ icon: "error", title: "Error de conexión" });
  } finally {
    isConnecting = false;
  }
}

async function disconnectESP32(showAlert = true) {
  try {
    if (!port) return;
    isConnected = false;
    
    nfcDisconnectNotified = false;
    nfcReconnectNotified = false;
    
    stopBackgroundNFCCheck();
    
    updateHardwareStatus(false);
    try {
      logEnvio("DISCONNECT", "ESP32");
      await safeWrite("DISCONNECT\n");
    } catch (_) {}

    reader && (await reader.cancel().catch(() => {}));
    reader = null;
    writer && (await writer.close().catch(() => {}));
    writer = null;
    inputDone && (await inputDone.catch(() => {}));
    outputDone && (await outputDone.catch(() => {}));
    await port.close().catch(() => {});
    port = null;
    serialBuffer = "";

    updateUIConnected(false);
    if (showAlert) Swal.fire({ icon: "info", title: "ESP32 Desconectado", timer: 1200, showConfirmButton: false });
    log("Puerto cerrado correctamente.");
  } catch (err) {
    log("Error desconexión: " + err.message);
  }
}

async function safeWrite(text) {
  if (!writer) throw new Error("Writer no disponible");
  try {
    await writer.write(text);
  } catch (err) {
    log("Error escribiendo: " + err.message);
    await disconnectESP32(true);
    throw new Error("Conexión perdida con ESP32");
  }
}

async function waitFor(keyword, timeout = 5000) {
  const start = Date.now();
  while (Date.now() - start < timeout) {
    const msg = await readLine();
    if (msg === null) return false;
    if (!msg) continue;
    logRecepcion(msg, "ESP32");
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
        log("Puerto cerrado por dispositivo.");
        isConnected = false;
        updateUIConnected(false);
        updateHardwareStatus(false);
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
    log("Error lectura: " + err.message);
    isConnected = false;
    updateUIConnected(false);
    updateHardwareStatus(false);
    return null;
  }
}

// ================= UI =================
function updateUIConnected(state) {
  const btn = document.querySelector('button[onclick="toggleConnectESP32()"]');
  if (state) {
    esp32Status.innerHTML = '<span class="badge bg-success">Conectado</span>';
    btn.innerHTML = '<i class="fas fa-plug-slash"></i> Desconectar ESP32';
    btn.classList.replace("btn-warning", "btn-danger");
  } else {
    esp32Status.innerHTML = '<span class="badge bg-secondary">No conectado</span>';
    btn.innerHTML = '<i class="fas fa-plug"></i> Conectar ESP32';
    btn.classList.replace("btn-danger", "btn-warning");
  }
}

navigator.serial.addEventListener("disconnect", async () => {
  log("Desconexión física detectada.");
  if (isRegistering) {
    await closeSwal();
    showToast("Conexión perdida. Reconecte el dispositivo.", "error");
    isRegistering = false;
  }
  updateHardwareStatus(false);
  nfcDisconnectNotified = false;
  nfcReconnectNotified = false;
  await disconnectESP32(true);
});

// ================= SWAL MEJORADO =================
async function closeSwal() {
  if (Swal.isVisible()) {
    Swal.close();
    await new Promise((resolve) => setTimeout(resolve, 100));
  }
  registrationSwal = null;
}

function showRegistrationSwal(title, text) {
  // Solo mostrar botón cancelar
  registrationSwal = Swal.fire({
    title: title,
    html: `<div id="swal-progress"><p>${text}</p><div class="spinner-border text-primary mt-3" role="status"></div></div>`,
    icon: "info",
    showCancelButton: true,
    cancelButtonText: "Cancelar",
    showConfirmButton: false,
    allowOutsideClick: false,
    didOpen: () => {
      const cancelBtn = Swal.getCancelButton();
      if (cancelBtn) {
        cancelBtn.onclick = () => {
          cancelRequested = true;
          log("Usuario solicitó cancelación desde Swal");
        };
      }
    }
  });
}

function updateRegistrationSwal(title, text) {
  // Solo actualizar si está visible
  if (Swal.isVisible()) {
    Swal.update({
      title: title,
      html: `<div id="swal-progress"><p>${text}</p><div class="spinner-border text-primary mt-3" role="status"></div></div>`,
    });
  } else {
    // Si no está visible, crear nuevo
    showRegistrationSwal(title, text);
  }
}

// ================= FOTO =================
function previewImage(event) {
  const reader = new FileReader();
  reader.onload = function () {
    const file = event.target.files[0];
    if (file && file.size > 500 * 1024) {
      Swal.fire({ icon: "error", title: "Archivo muy grande", text: "La imagen no debe exceder 500KB" });
      event.target.value = "";
      return;
    }
    const output = document.getElementById("imagePreview");
    output.src = reader.result;
    output.style.display = "block";
    document.getElementById("placeholderPreview").style.display = "none";
    capturedImageData = reader.result;
    document.getElementById("foto_capturada").value = capturedImageData;
    logDatos({ size: file ? file.size : 0, type: "preview" }, "FOTO_CARGADA");
  };
  reader.readAsDataURL(event.target.files[0]);
}

function openCamera() {
  const modal = new bootstrap.Modal(document.getElementById("cameraModal"));
  modal.show();
  navigator.mediaDevices.getUserMedia({ video: { facingMode: "user" } })
    .then(function (stream) {
      videoStream = stream;
      document.getElementById("videoElement").srcObject = stream;
      document.getElementById("videoElement").style.display = "block";
      document.getElementById("capturedPhoto").style.display = "none";
      document.getElementById("captureBtn").style.display = "inline-block";
      document.getElementById("retakeBtn").style.display = "none";
      document.getElementById("usePhotoBtn").style.display = "none";
    })
    .catch(function (err) {
      Swal.fire({ icon: "error", title: "Error de cámara", text: "No se pudo acceder a la cámara: " + err.message });
      closeCamera();
    });
}

function capturePhoto() {
  const video = document.getElementById("videoElement");
  const canvas = document.getElementById("canvasElement");
  const capturedPhoto = document.getElementById("capturedPhoto");
  canvas.width = video.videoWidth;
  canvas.height = video.videoHeight;
  canvas.getContext("2d").drawImage(video, 0, 0);
  capturedImageData = canvas.toDataURL("image/jpeg", 0.6);
  const sizeInKB = (capturedImageData.length * 3) / 4 / 1024;
  if (sizeInKB > 500) capturedImageData = canvas.toDataURL("image/jpeg", 0.4);
  capturedPhoto.src = capturedImageData;
  video.style.display = "none";
  capturedPhoto.style.display = "block";
  document.getElementById("captureBtn").style.display = "none";
  document.getElementById("retakeBtn").style.display = "inline-block";
  document.getElementById("usePhotoBtn").style.display = "inline-block";
  logDatos({ sizeKB: sizeInKB.toFixed(2) }, "FOTO_CAPTURADA");
}

function retakePhoto() {
  document.getElementById("videoElement").style.display = "block";
  document.getElementById("capturedPhoto").style.display = "none";
  document.getElementById("captureBtn").style.display = "inline-block";
  document.getElementById("retakeBtn").style.display = "none";
  document.getElementById("usePhotoBtn").style.display = "none";
}

function usePhoto() {
  const imagePreview = document.getElementById("imagePreview");
  const placeholderPreview = document.getElementById("placeholderPreview");
  imagePreview.src = capturedImageData;
  imagePreview.style.display = "block";
  placeholderPreview.style.display = "none";
  document.getElementById("foto_capturada").value = capturedImageData;
  closeCamera();
}

function closeCamera() {
  if (videoStream) {
    videoStream.getTracks().forEach((track) => track.stop());
    videoStream = null;
  }
  const modalEl = document.getElementById("cameraModal");
  const modal = bootstrap.Modal.getInstance(modalEl);
  if (modal) modal.hide();
  setTimeout(() => {
    document.getElementById("videoElement").srcObject = null;
    document.getElementById("capturedPhoto").src = "";
  }, 300);
}

// ================= FUNCIONES AUXILIARES =================
function getFirstLetter(str) {
  if (!str || str.trim() === "") return "X";
  return str.trim().charAt(0).toUpperCase();
}

function getFirstConsonant(str) {
  if (!str || str.trim() === "") return "X";
  const sinAcentos = str.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
  const upperStr = sinAcentos.trim().toUpperCase();
  const consonants = "BCDFGHJKLMNPQRSTVWXYZ";
  for (let i = 1; i < upperStr.length; i++) {
    if (consonants.includes(upperStr[i])) return upperStr[i];
  }
  return "X";
}

function getEntityCode(estado) {
  const estados = { AGS: "AS", BC: "BC", BCS: "BS", CAMP: "CC", COAH: "CL", COL: "CM", CHIS: "CS", CHIH: "CH", CDMX: "DF", DGO: "DG", GTO: "GT", GRO: "GR", HGO: "HG", JAL: "JC", MEX: "MC", MICH: "MN", MOR: "MS", NAY: "NL", NL: "NL", OAX: "OC", PUE: "PL", QRO: "QT", QROO: "QR", SLP: "SL", SIN: "SI", SON: "SR", TAB: "TC", TAMS: "TS", TLAX: "TL", VER: "VZ", YUC: "YN", ZAC: "ZS" };
  return estados[estado] || "NE";
}

function generarRFC() {
  const nombre = document.getElementById("nombre").value.trim();
  const apellidoPaterno = document.getElementById("apellido_paterno").value.trim();
  const apellidoMaterno = document.getElementById("apellido_materno").value.trim();
  const fechaNacimiento = document.getElementById("fecha_nacimiento").value;
  if (!nombre || !apellidoPaterno || !fechaNacimiento) return;

  const nombres = nombre.split(" ");
  let nombreParaRfc = nombres[0].toUpperCase();
  if (["MARIA", "JOSE", "MA", "JA"].some(n => nombre[0].startsWith(n)) && nombres.length > 1) {
    nombreParaRfc = nombres[1].toUpperCase();
  }

  let rfc = getFirstLetter(apellidoPaterno);
  const vocales = "AEIOU";
  let segundaLetra = "X";
  if (apellidoPaterno.length > 1) {
    const sinAcentos = apellidoPaterno.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toUpperCase();
    for (let i = 1; i < sinAcentos.length; i++) {
      if (vocales.includes(sinAcentos[i])) { segundaLetra = sinAcentos[i]; break; }
    }
  }
  rfc += segundaLetra + getFirstLetter(apellidoMaterno) + getFirstLetter(nombreParaRfc);
  const fechaPartes = fechaNacimiento.split("-");
  rfc += fechaPartes[0].slice(-2) + fechaPartes[1] + fechaPartes[2];
  document.getElementById("rfc").value = rfc;
}

function generarCURP() {
  const nombre = document.getElementById("nombre").value.trim();
  const apellidoPaterno = document.getElementById("apellido_paterno").value.trim();
  const apellidoMaterno = document.getElementById("apellido_materno").value.trim();
  const fechaNacimiento = document.getElementById("fecha_nacimiento").value;
  const genero = document.getElementById("genero").value;
  const entidad = document.getElementById("entidad").value;
  if (!nombre || !apellidoPaterno || !fechaNacimiento) return;

  const nombres = nombre.split(" ");
  let nombreParaCurp = nombres[0].toUpperCase();
  if (["MARIA", "JOSE", "MA", "JA"].some(n => nombre[0].startsWith(n)) && nombres.length > 1) {
    nombreParaCurp = nombres[1].toUpperCase();
  }

  const paternoSinAcentos = apellidoPaterno.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toUpperCase();
  let primeraVocalPaterno = "X";
  for (let i = 1; i < paternoSinAcentos.length; i++) {
    if ("AEIOU".includes(paternoSinAcentos[i])) { primeraVocalPaterno = paternoSinAcentos[i]; break; }
  }

  let curp = getFirstLetter(apellidoPaterno) + primeraVocalPaterno + getFirstLetter(apellidoMaterno) + getFirstLetter(nombreParaCurp);
  const fechaPartes = fechaNacimiento.split("-");
  curp += fechaPartes[0].slice(-2) + fechaPartes[1] + fechaPartes[2];
  curp += genero === "H" ? "H" : "M";
  curp += getEntityCode(entidad);
  curp += getFirstConsonant(apellidoPaterno) + getFirstConsonant(apellidoMaterno) + getFirstConsonant(nombreParaCurp);
  document.getElementById("curp").value = curp;
}

function generarClaveElector() {
  const nombre = document.getElementById("nombre").value.trim();
  const apellidoPaterno = document.getElementById("apellido_paterno").value.trim();
  const apellidoMaterno = document.getElementById("apellido_materno").value.trim();
  const fechaNacimiento = document.getElementById("fecha_nacimiento").value;
  const genero = document.getElementById("genero").value;
  const entidad = document.getElementById("entidad").value;
  if (!nombre || !apellidoPaterno || !fechaNacimiento || !genero || !entidad) return;

  function getFirstConsonants(str, count) {
    if (!str || str.trim() === "") return "X".repeat(count);
    const sinAcentos = str.normalize("NFD").replace(/[\u0300-\u036f]/g, "").trim().toUpperCase();
    const consonants = "BCDFGHJKLMNPQRSTVWXYZ";
    let result = "", countFound = 0;
    for (let i = 0; i < sinAcentos.length && countFound < count; i++) {
      if (consonants.includes(sinAcentos[i])) { result += sinAcentos[i]; countFound++; }
    }
    while (result.length < count) result += "X";
    return result;
  }

  let claveElector = getFirstConsonants(apellidoPaterno, 2) + getFirstConsonants(apellidoMaterno, 2) + getFirstConsonants(nombre, 2);
  const fechaPartes = fechaNacimiento.split("-");
  claveElector += fechaPartes[0].slice(-2) + fechaPartes[1] + fechaPartes[2];
  const entidadNumerica = { AGS: "01", BC: "02", BCS: "03", CAMP: "04", COAH: "05", COL: "06", CHIS: "07", CHIH: "08", CDMX: "09", DGO: "10", GTO: "11", GRO: "12", HGO: "13", JAL: "14", MEX: "15", MICH: "16", MOR: "17", NAY: "18", NL: "19", OAX: "20", PUE: "21", QRO: "22", QROO: "23", SLP: "24", SIN: "25", SON: "26", TAB: "27", TAMS: "28", TLAX: "29", VER: "30", YUC: "31", ZAC: "32" };
  claveElector += entidadNumerica[entidad] || "00";
  claveElector += genero === "H" ? "H" : "M";
  claveElector += Math.floor(Math.random() * 900 + 100).toString();
  document.getElementById("clave_elector").value = claveElector;
}

// ================= EVENT LISTENERS =================
document.addEventListener("DOMContentLoaded", function () {
  const seccionInput = document.getElementById("seccion");
  if (seccionInput) seccionInput.addEventListener("input", function () { this.value = this.value.replace(/[^0-9]/g, ""); });

["nombre", "apellido_paterno", "apellido_materno"].forEach(id => {
    const el = document.getElementById(id);
    if (el) {
      el.addEventListener("input", function() { 
        this.value = this.value.toUpperCase(); 
        generarCURP(); generarRFC(); generarClaveElector(); 
      });
      el.addEventListener("change", function() { 
        this.value = this.value.toUpperCase(); 
        generarCURP(); generarRFC(); generarClaveElector(); 
      });
      el.addEventListener("paste", function() { 
        setTimeout(() => { 
          this.value = this.value.toUpperCase(); 
          generarCURP(); generarRFC(); generarClaveElector(); 
        }, 10);
      });
    }
  });

  ["fecha_nacimiento", "genero", "entidad"].forEach(id => {
    const el = document.getElementById(id);
    if (el) {
      el.addEventListener("input", () => { generarCURP(); generarRFC(); generarClaveElector(); });
      el.addEventListener("change", () => { generarCURP(); generarRFC(); generarClaveElector(); });
    }
  });

  ["curp", "rfc", "clave_elector"].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.addEventListener("input", function () { this.value = this.value.toUpperCase(); });
  });
});

// ================= VERIFICACIÓN DE HARDWARE =================
async function checkHardwareStatus() {
  if (!esp32Disponible()) { updateHardwareStatus(false); return { nfc: false }; }
  try {
    logEnvio("STATUS", "ESP32");
    await safeWrite("STATUS\n");
    let nfcOk = null;
    while (true) {
      const msg = await readLine();
      if (msg === null) break;
      const cleanMsg = msg.trim();
      logRecepcion(cleanMsg, "ESP32");
      if (cleanMsg.startsWith("NFC:")) { nfcOk = cleanMsg.includes("OK"); break; }
    }
    const finalNfcOk = nfcOk === null ? nfcStatus : nfcOk;
    updateHardwareStatus(finalNfcOk);
    return { nfc: finalNfcOk };
  } catch (e) {
    log("Error verificando hardware: " + e.message);
    updateHardwareStatus(false);
    return { nfc: false };
  }
}

// ================= RESET COMPLETO =================
async function resetRegistroCompleto() {
  log("🧹 RESET COMPLETO ejecutado");
  
  isRegistering = false;
  cancelRequested = false;
  serialBuffer = "";
  
  stopBackgroundNFCCheck();
  await closeSwal();
  
  const btn = document.getElementById("btnRegistrar");
  if (btn) btn.disabled = false;
  
  nfcDisconnectNotified = false;
  nfcReconnectNotified = false;
  
  if (esp32Disponible()) {
    try {
      await safeWrite("RESET_READERS\n");
      await new Promise(r => setTimeout(r, 500));
    } catch (e) {
      log("Cleanup ESP32: " + e.message);
    }
  }
  
  if (isConnected) startBackgroundNFCCheck();
  log("✅ Reset completo listo para nuevo registro");
}

async function cleanupAfterRegistration() {
  await resetRegistroCompleto();
}

// ================= BORRAR HUELLA DEL LECTOR =================
async function deleteFingerprintFromSensor(fingerId) {
  if (!esp32Disponible() || !fingerId) return;
  
  try {
    log("Eliminando plantilla de huella del sensor: " + fingerId);
    logEnvio("DELETE_FINGER:" + fingerId, "ESP32");
    await safeWrite("DELETE_FINGER:" + fingerId + "\n");
    
    const msg = await Promise.race([
      readLine(),
      new Promise(resolve => setTimeout(() => resolve(null), 2000))
    ]);
    
    if (msg && msg.includes("FINGER_DELETED")) {
      log("Huella eliminada correctamente del sensor");
    } else {
      log("Error al eliminar huella del sensor");
    }
  } catch (e) {
    log("Error al borrar huella: " + e.message);
  }
}

// ================= ENVIAR CANCEL AL ESP32 =================
async function sendCancelToESP32() {
  try {
    logEnvio("CANCEL", "ESP32");
    await safeWrite("CANCEL\n");
    
    // Esperar confirmación de cancelación
    const msg = await Promise.race([
      readLine(),
      new Promise(resolve => setTimeout(() => resolve(null), 2000))
    ]);
    
    if (msg && msg.includes("CANCELLED")) {
      log("ESP32 confirmó cancelación");
      return true;
    }
    return true;
  } catch (e) {
    log("Error enviando cancelación: " + e.message);
    return true;
  }
}

// ================= FORMULARIO =================
document.getElementById("registroForm").addEventListener("submit", async function (e) {
  e.preventDefault();
  
  const btn = document.getElementById("btnRegistrar");
  if (btn.disabled) return;
  
  btn.disabled = true;
  isRegistering = true;
  cancelRequested = false;
  
  stopBackgroundNFCCheck();

  try {
    const rfcInput = document.getElementById("rfc");
    const curpInput = document.getElementById("curp");
    const seccionInput = document.getElementById("seccion");
    let errores = [];

    if (!rfcInput || rfcInput.value.trim() === "") errores.push("El campo <b>RFC</b> es obligatorio.");
    else if (rfcInput.value.trim().length !== 13) errores.push("El <b>RFC</b> debe tener exactamente 13 caracteres.");

    if (!curpInput || curpInput.value.trim() === "") errores.push("El campo <b>CURP</b> es obligatorio.");
    else if (curpInput.value.trim().length !== 18) errores.push("La <b>CURP</b> debe tener exactamente 18 caracteres.");

    if (!seccionInput || seccionInput.value.trim() === "") errores.push("La <b>Sección Electoral</b> es obligatoria.");
    if (!capturedImageData) errores.push("Debe <b>capturar la fotografía</b> del votante.");

    if (errores.length > 0) {
      await Swal.fire({ icon: "warning", title: "Campos faltantes", html: "<ul>" + errores.map(e => "<li>" + e + "</li>").join("") + "</ul>", confirmButtonText: "Corregir" });
      btn.disabled = false;
      isRegistering = false;
      nfcDisconnectNotified = false;
      nfcReconnectNotified = false;
      if (isConnected) startBackgroundNFCCheck();
      return;
    }

    if (!esp32Disponible()) {
      await closeSwal();
      showToast("El dispositivo ESP32 no está conectado.", "error");
      btn.disabled = false;
      isRegistering = false;
      nfcDisconnectNotified = false;
      nfcReconnectNotified = false;
      if (isConnected) startBackgroundNFCCheck();
      return;
    }

    log("==========================================");
    log("INICIANDO PROCESO DE REGISTRO...");
    logDatos({ rfc: rfcInput.value, curp: curpInput.value, seccion: seccionInput.value, tieneFoto: !!capturedImageData }, "DATOS_VOTANTE");
    
    serialBuffer = "";
    
    logEnvio("REGISTER", "ESP32");
    await safeWrite("REGISTER\n");

    let uid = null;
    let token = null;
    let fingerId = null;

    // Mostrar Swal optimizado (sin "Paso 1")
    showRegistrationSwal("Escanear Tarjeta", "Acerque la credencial al lector NFC");


    while (true) {
      if (cancelRequested) {
        log("Cancelación solicitada, enviando comando al ESP32...");
        await sendCancelToESP32();
        serialBuffer = "";
        await closeSwal();
        showToast("Registro cancelado.", "info");
        await cleanupAfterRegistration();
        break;
      }

      const msg = await Promise.race([
        readLine(),
        new Promise(resolve => setTimeout(() => resolve(null), 5000))
      ]);

      if (msg === null) {
        if (cancelRequested) {
          await sendCancelToESP32();
          await closeSwal();
          showToast("Registro cancelado.", "info");
          await cleanupAfterRegistration();
          break;
        }
        log("Timeout esperando respuesta del ESP32");
        continue;
      }

      let cleanMsg = msg.trim();
      if (!cleanMsg) continue;
      logRecepcion(cleanMsg, "ESP32");

      if (cancelRequested) {
        log("Cancelación detectada después de: " + cleanMsg);
        await sendCancelToESP32();
        serialBuffer = "";
        await closeSwal();
        showToast("Registro cancelado.", "info");
        await cleanupAfterRegistration();
        break;
      }

      if (cleanMsg === "ERROR_AUTH") {
        await closeSwal();
        showToast("Error de autenticación NFC. Use otra tarjeta.", "error");
        log("ERROR_AUTH - Tarjeta no compatible");
        await cleanupAfterRegistration();
        break;
      }
      if (cleanMsg === "ERROR_WRITE") {
        await closeSwal();
        showToast("Error al escribir en la tarjeta. Intente con otra.", "error");
        log("ERROR_WRITE");
        await cleanupAfterRegistration();
        break;
      }
      if (cleanMsg === "CARD_TIMEOUT") {
        await closeSwal();
        showToast("Tiempo agotado. No se detectó tarjeta.", "warning");
        log("CARD_TIMEOUT");
        await cleanupAfterRegistration();
        break;
      }
      if (cleanMsg === "CANCELLED_BY_USER" || cleanMsg === "REGISTER_CANCELLED") {
        await closeSwal();
        showToast("Registro cancelado.", "info");
        await cleanupAfterRegistration();
        break;
      }

      if (cleanMsg === "WAIT_CARD") {
        log("Esperando tarjeta NFC...");
        updateRegistrationSwal("Leyendo Tarjeta...", "Detectando chip NFC, acerque la tarjeta al lector.");
      }
      else if (cleanMsg === "PUT_FINGER") {
        log("Coloque el dedo en el lector...");
        updateRegistrationSwal("Huella Digital", "Coloque el dedo en el lector de huellas");
      }
      else if (cleanMsg === "REMOVE_FINGER") {
        log("Retire el dedo del lector...");
        updateRegistrationSwal("Huella Digital", "Retire el dedo del lector");
      }
      else if (cleanMsg === "PUT_FINGER_AGAIN") {
        log("Coloque el dedo nuevamente...");
        updateRegistrationSwal("Huella Digital", "Coloque el mismo dedo nuevamente");
      }
      else if (cleanMsg.startsWith("FINGER_ID:")) {
        fingerId = cleanMsg.split(":")[1].trim();
        logDatos({ fingerId: fingerId }, "FINGER_ID");
      }
      else if (cleanMsg === "FINGER_OK") {
        log("Huella registrada correctamente");
      }
      else if (cleanMsg === "FINGER_RETRY_OK") {
        log("Huella reintentada exitosamente");
        updateRegistrationSwal("Sincronizando", "Guardando datos...");
        
        const formData = new FormData(this);
        formData.append("uid", uid);
        formData.append("token", token);
        formData.append("finger_id", fingerId);
        formData.append("foto", capturedImageData);

        try {
          const res = await fetch("/votosecure/api/guardar_votante.php", { method: "POST", body: formData });
          const data = await res.json();
          logRecepcion(data, "SERVIDOR");

          if (data.status === "FINISH") {
            await Swal.fire({ icon: "success", title: "¡Éxito!", text: "Votante registrado.", timer: 2000 });
            this.reset();
            capturedImageData = null;
            document.getElementById("foto_capturada").value = "";
            document.getElementById("imagePreview").src = "";
            document.getElementById("imagePreview").style.display = "none";
            document.getElementById("placeholderPreview").style.display = "block";
            log("REGISTRO COMPLETADO");
            await cleanupAfterRegistration();
            break;
          } else {
            await closeSwal();
            
            if (data.message && data.message.includes("ya existe")) {
              showToast("Votante duplicado: " + (data.duplicado?.curp || " datos ya registrados"), "warning");
              log("DUPLICADO DETECTADO");
              
              if (esp32Disponible() && fingerId) {
                await deleteFingerprintFromSensor(fingerId);
              }
            } else {
              showToast("Error: " + (data.message || "Datos inválidos"), "error");
            }
            
            await cleanupAfterRegistration();
            break;
          }
        } catch (fetchErr) {
          if (esp32Disponible() && fingerId) {
            await deleteFingerprintFromSensor(fingerId);
          }
          await closeSwal();
          showToast("Error de conexión: " + fetchErr.message, "error");
          await cleanupAfterRegistration();
          break;
        }
      }
      else if (cleanMsg === "FINGER_FAIL" || cleanMsg === "FINGER_TIMEOUT") {
        // Error de huella - reintentar automáticamente sin preguntar
        log("Error en lectura de huella - Reintentando automáticamente...");
        
        // Cerrar Swal y crear uno nuevo
        await closeSwal();
        await new Promise(r => setTimeout(r, 200));
        
        // Reintentar directamente
        logEnvio("RETRY_FINGER", "ESP32");
        await safeWrite("RETRY_FINGER\n");
        
        // Mostrar nuevo Swal
        showRegistrationSwal("Huella Digital", "Reintentando lectura de huella...");
      }
      else if (cleanMsg.startsWith("UID:")) {
        uid = cleanMsg.split(":")[1].trim();
        logDatos({ uid: uid }, "NFC_UID");
      }
      else if (cleanMsg.startsWith("TOKEN:")) {
        token = cleanMsg.split(":")[1].trim();
        logDatos({ token: token.substring(0, 20) + "..." }, "TOKEN_NFC");
      }
      else if (cleanMsg === "FINISH") {
        log("PROCESO COMPLETADO");
        
        if (!uid || !token || !fingerId) {
          log("Datos incompletos - UID: " + !!uid + " TOKEN: " + !!token + " FINGER: " + !!fingerId);
          
          if (!fingerId && uid && token) {
            await closeSwal();
            showToast("Huella no detectada. Intente de nuevo.", "warning");
            log("Huella no recibida - Reintentando registro");
            serialBuffer = "";
            await safeWrite("REGISTER\n");
            continue;
          }
          
          await cleanupAfterRegistration();
          await closeSwal();
          showToast("Datos incompletos. Intente de nuevo.", "error");
          break;
        }

        await closeSwal();
        Swal.fire({ title: "Sincronizando", text: "Guardando datos...", allowOutsideClick: false, didOpen: () => Swal.showLoading() });

        const formData = new FormData(this);
        formData.append("uid", uid);
        formData.append("token", token);
        formData.append("finger_id", fingerId);
        formData.append("foto", capturedImageData);

        try {
          const res = await fetch("/votosecure/api/guardar_votante.php", { method: "POST", body: formData });
          const data = await res.json();
          logRecepcion(data, "SERVIDOR");

          if (data.status === "FINISH") {
            await Swal.fire({ icon: "success", title: "¡Éxito!", text: "Votante registrado.", timer: 2000 });
            this.reset();
            capturedImageData = null;
            document.getElementById("foto_capturada").value = "";
            document.getElementById("imagePreview").src = "";
            document.getElementById("imagePreview").style.display = "none";
            document.getElementById("placeholderPreview").style.display = "block";
            log("REGISTRO COMPLETADO");
            await cleanupAfterRegistration();
            break;
          } else {
            await closeSwal();
            
            if (data.message && data.message.includes("ya existe")) {
              showToast("Votante duplicado: " + (data.duplicado?.curp || " datos ya registrados"), "warning");
              log("DUPLICADO DETECTADO - Borrando plantilla de huella del sensor");
              
              if (esp32Disponible() && fingerId) {
                await deleteFingerprintFromSensor(fingerId);
              }
            } else {
              showToast("Error: " + (data.message || "Datos inválidos"), "error");
            }
            
            await cleanupAfterRegistration();
            break;
          }
        } catch (fetchErr) {
          if (esp32Disponible() && fingerId) {
            await deleteFingerprintFromSensor(fingerId);
          }
          await closeSwal();
          showToast("Error de conexión: " + fetchErr.message, "error");
          await cleanupAfterRegistration();
          break;
        }
      }
    }
  } catch (err) {
    await closeSwal();
    log("Error: " + err.message);
    showToast("Error: " + err.message, "error");
  } finally {
    // 🔧 RESET COMPLETO GARANTIZADO
    await resetRegistroCompleto();
    log("✅ Form submit finalizado con reset completo");
  }
});


async function hardResetHardware() {
  if (!esp32Disponible()) {
    Swal.fire("Aviso", "El hardware no está conectado.", "info");
    return;
  }
  try {
    log("Reset...");
    await safeWrite("RESET_READERS\n");
    serialBuffer = "";
    await new Promise(r => setTimeout(r, 500));
    await safeWrite("CONNECT\n");
    await checkHardwareStatus();
    Swal.fire("Reset exitoso", "Hardware listo.", "success");
  } catch (e) {
    log("Error: " + e.message);
  }
}