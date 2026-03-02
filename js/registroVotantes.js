// ================= ESP32 NFC + FINGERPRINT =================
let port = null;
let reader = null;
let writer = null;
let inputDone = null;
let outputDone = null;
let serialBuffer = "";
let isConnected = false;
let isConnecting = false;
let cancelNFCScan = false;
let capturedImageData = null;
let videoStream = null;

const esp32Status = document.getElementById("esp32Status");

// ================= LOG =================
function log(msg) {
  const logEl = document.getElementById("esp32Log");
  // console.log("[ESP32]", msg);
  if (!logEl) return;
  logEl.style.display = "block";
  logEl.textContent += msg + "\n";
  logEl.scrollTop = logEl.scrollHeight;
}

// ================= VALIDACIÓN REAL =================
function esp32Disponible() {
  return (
    isConnected === true &&
    port &&
    port.readable &&
    port.writable &&
    reader &&
    writer
  );
}

// ================= CONECTAR / DESCONECTAR =================
async function toggleConnectESP32() {
  if (isConnecting) return;

  if (!("serial" in navigator)) {
    Swal.fire({
      icon: "error",
      title: "Navegador no compatible",
      text: "Use Chrome o Edge",
    });
    return;
  }

  if (!isConnected) {
    await connectESP32();
  } else {
    await disconnectESP32(true);
  }
}

// ================= CONECTAR =================
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
    await safeWrite("CONNECT\n");

    const ok = await waitFor("CONNECTED_OK", 5000);
    if (!ok) throw new Error("No se recibió CONNECTED_OK");

    isConnected = true;
    updateUIConnected(true);

    Swal.fire({
      icon: "success",
      title: "ESP32 Conectado",
      timer: 1500,
      showConfirmButton: false,
    });

    log("Conexión establecida.");
  } catch (err) {
    log("Error conexión: " + err.message);
    await disconnectESP32(false);

    Swal.fire({
      icon: "error",
      title: "Error de conexión",
      text: err.message,
    });
  } finally {
    isConnecting = false;
  }
}

// ================= DESCONECTAR =================
async function disconnectESP32(showAlert = true) {
  try {
    if (!port) return;

    isConnected = false;

    try {
      await safeWrite("DISCONNECT\n");
    } catch (_) {}

    if (reader) {
      await reader.cancel().catch(() => {});
      reader = null;
    }

    if (writer) {
      await writer.close().catch(() => {});
      writer = null;
    }

    if (inputDone) await inputDone.catch(() => {});
    if (outputDone) await outputDone.catch(() => {});

    await port.close().catch(() => {});
    port = null;

    serialBuffer = "";

    updateUIConnected(false);

    if (showAlert) {
      Swal.fire({
        icon: "info",
        title: "ESP32 Desconectado",
        timer: 1200,
        showConfirmButton: false,
      });
    }

    log("Puerto cerrado correctamente.");
  } catch (err) {
    log("Error desconexión: " + err.message);
  }
}

// ================= WRITE SEGURO =================
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

// ================= ESPERAR MENSAJE =================
async function waitFor(keyword, timeout = 5000) {
  const start = Date.now();

  while (Date.now() - start < timeout) {
    const msg = await readLine();

    if (msg === null) {
      return false; // puerto cerrado
    }

    if (!msg) continue;

    log("ESP32: " + msg);

    if (msg.includes(keyword)) return true;
  }

  return false;
}

// ================= LEER LÍNEA =================
async function readLine() {
  if (!reader) return null;

  try {
    while (true) {
      const { value, done } = await reader.read();

      if (done) {
        log("Puerto cerrado por dispositivo.");
        isConnected = false;
        updateUIConnected(false);
        return null;
      }

      if (!value) continue;

      serialBuffer += value;

      const lines = serialBuffer.split(/\r?\n/);
      serialBuffer = lines.pop();

      for (let line of lines) {
        line = line.trim();
        if (line.length > 0) {
          return line;
        }
      }
    }
  } catch (err) {
    log("Error lectura: " + err.message);
    isConnected = false;
    updateUIConnected(false);
    return null;
  }
}

// ================= UI =================
function updateUIConnected(state) {
  const btn = document.querySelector('button[onclick="toggleConnectESP32()"]');

  if (state) {
    esp32Status.innerHTML =
      '<span class="badge bg-success">✅ Conectado</span>';
    btn.innerHTML = '<i class="fas fa-plug-slash"></i> Desconectar ESP32';
    btn.classList.replace("btn-warning", "btn-danger");
  } else {
    esp32Status.innerHTML =
      '<span class="badge bg-secondary">No conectado</span>';
    btn.innerHTML = '<i class="fas fa-plug"></i> Conectar ESP32';
    btn.classList.replace("btn-danger", "btn-warning");
  }
}

// ================= DESCONEXIÓN FÍSICA =================
navigator.serial.addEventListener("disconnect", async () => {
  log("Desconexión física detectada.");
  await disconnectESP32(true);
});

// ================= FOTO =================
// Función para cerrar cualquier SweetAlert abierto
async function closeSwal() {
  if (Swal.isVisible()) {
    Swal.close();
    await new Promise((resolve) => setTimeout(resolve, 100));
  }
}

// Vista previa de la imagen (desde archivo)
function previewImage(event) {
  const reader = new FileReader();
  reader.onload = function () {
    // Verificar tamaño máximo (500KB)
    const file = event.target.files[0];
    if (file && file.size > 500 * 1024) {
      Swal.fire({
        icon: "error",
        title: "Archivo muy grande",
        text: "La imagen no debe exceder 500KB",
      });
      event.target.value = ""; // Limpiar input
      return;
    }

    const output = document.getElementById("imagePreview");
    output.src = reader.result;
    output.style.display = "block";
    document.getElementById("placeholderPreview").style.display = "none";
    capturedImageData = reader.result;
    document.getElementById("foto_capturada").value = capturedImageData;
  };
  reader.readAsDataURL(event.target.files[0]);
}

// Abrir cámara
function openCamera() {
  const modal = new bootstrap.Modal(document.getElementById("cameraModal"));
  modal.show();

  navigator.mediaDevices
    .getUserMedia({
      video: {
        facingMode: "user",
      },
    })
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
      Swal.fire({
        icon: "error",
        title: "Error de cámara",
        text: "No se pudo acceder a la cámara: " + err.message,
      });
      closeCamera();
    });
}

// Capturar foto
function capturePhoto() {
  const video = document.getElementById("videoElement");
  const canvas = document.getElementById("canvasElement");
  const capturedPhoto = document.getElementById("capturedPhoto");

  canvas.width = video.videoWidth;
  canvas.height = video.videoHeight;
  canvas.getContext("2d").drawImage(video, 0, 0);

  // Comprimir imagen antes de guardar (calidad 0.6 = 60%)
  capturedImageData = canvas.toDataURL("image/jpeg", 0.6);

  // Verificar tamaño (max 500KB)
  const sizeInKB = (capturedImageData.length * 3) / 4 / 1024;
  if (sizeInKB > 500) {
    // Recomprimir más si es muy grande
    capturedImageData = canvas.toDataURL("image/jpeg", 0.4);
  }

  capturedPhoto.src = capturedImageData;

  video.style.display = "none";
  capturedPhoto.style.display = "block";

  document.getElementById("captureBtn").style.display = "none";
  document.getElementById("retakeBtn").style.display = "inline-block";
  document.getElementById("usePhotoBtn").style.display = "inline-block";
}

// Repetir foto
function retakePhoto() {
  const video = document.getElementById("videoElement");
  const capturedPhoto = document.getElementById("capturedPhoto");

  video.style.display = "block";
  capturedPhoto.style.display = "none";

  document.getElementById("captureBtn").style.display = "inline-block";
  document.getElementById("retakeBtn").style.display = "none";
  document.getElementById("usePhotoBtn").style.display = "none";
}

// Usar foto capturada
function usePhoto() {
  const imagePreview = document.getElementById("imagePreview");
  const placeholderPreview = document.getElementById("placeholderPreview");

  imagePreview.src = capturedImageData;
  imagePreview.style.display = "block";
  placeholderPreview.style.display = "none";
  document.getElementById("foto_capturada").value = capturedImageData;

  closeCamera();
}

// Cerrar cámara
function closeCamera() {
  if (videoStream) {
    videoStream.getTracks().forEach((track) => track.stop());
    videoStream = null;
  }
  const modalEl = document.getElementById("cameraModal");
  const modal = bootstrap.Modal.getInstance(modalEl);
  if (modal) {
    modal.hide();
  }
  setTimeout(() => {
    document.getElementById("videoElement").srcObject = null;
    document.getElementById("capturedPhoto").src = "";
  }, 300);
}

// Convertir CURP a mayúsculas automáticamente
document.getElementById("curp").addEventListener("input", function () {
  this.value = this.value.toUpperCase();
});

// Convertir RFC a mayúsculas automáticamente
document.getElementById("rfc").addEventListener("input", function () {
  this.value = this.value.toUpperCase();
});

// Convertir Clave de Elector a mayúsculas automáticamente
document.getElementById("clave_elector").addEventListener("input", function () {
  this.value = this.value.toUpperCase();
});

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
    if (consonants.includes(upperStr[i])) {
      return upperStr[i];
    }
  }
  return "X";
}

function getEntityCode(estado) {
  const estados = {
    AGS: "AS",
    BC: "BC",
    BCS: "BS",
    CAMP: "CC",
    COAH: "CL",
    COL: "CM",
    CHIS: "CS",
    CHIH: "CH",
    CDMX: "DF",
    DGO: "DG",
    GTO: "GT",
    GRO: "GR",
    HGO: "HG",
    JAL: "JC",
    MEX: "MC",
    MICH: "MN",
    MOR: "MS",
    NAY: "NL",
    NL: "NL",
    OAX: "OC",
    PUE: "PL",
    QRO: "QT",
    QROO: "QR",
    SLP: "SL",
    SIN: "SI",
    SON: "SR",
    TAB: "TC",
    TAMS: "TS",
    TLAX: "TL",
    VER: "VZ",
    YUC: "YN",
    ZAC: "ZS",
  };
  return estados[estado] || "NE";
}

// ================= GENERACIÓN AUTOMÁTICA DE RFC =================

function generarRFC() {
  const nombre = document.getElementById("nombre").value.trim();
  const apellidoPaterno = document
    .getElementById("apellido_paterno")
    .value.trim();
  const apellidoMaterno = document
    .getElementById("apellido_materno")
    .value.trim();
  const fechaNacimiento = document.getElementById("fecha_nacimiento").value;

  if (!nombre || !apellidoPaterno || !fechaNacimiento) return;

  const nombres = nombre.split(" ");
  const primerNombre = nombres[0].toUpperCase();

  let nombreParaRfc = primerNombre;
  if (
    ["MARIA", "JOSE", "MA", "JA"].some((n) => primerNombre.startsWith(n)) &&
    nombres.length > 1
  ) {
    nombreParaRfc = nombres[1].toUpperCase();
  }

  let rfc = "";
  rfc += getFirstLetter(apellidoPaterno);

  const vocales = "AEIOU";
  let segundaLetra = "X";
  if (apellidoPaterno.length > 1) {
    const sinAcentos = apellidoPaterno
      .normalize("NFD")
      .replace(/[\u0300-\u036f]/g, "")
      .toUpperCase();
    for (let i = 1; i < sinAcentos.length; i++) {
      if (vocales.includes(sinAcentos[i])) {
        segundaLetra = sinAcentos[i];
        break;
      }
    }
  }
  rfc += segundaLetra;
  rfc += getFirstLetter(apellidoMaterno);
  rfc += getFirstLetter(nombreParaRfc);

  const fechaPartes = fechaNacimiento.split("-");
  const year = fechaPartes[0].slice(-2);
  const month = fechaPartes[1];
  const day = fechaPartes[2];
  rfc += year + month + day;

  document.getElementById("rfc").value = rfc;
}

// ================= GENERACIÓN AUTOMÁTICA DE CURP =================
function generarCURP() {
  const nombre = document.getElementById("nombre").value.trim();
  const apellidoPaterno = document
    .getElementById("apellido_paterno")
    .value.trim();
  const apellidoMaterno = document
    .getElementById("apellido_materno")
    .value.trim();
  const fechaNacimiento = document.getElementById("fecha_nacimiento").value;
  const genero = document.getElementById("genero").value;
  const entidad = document.getElementById("entidad").value;

  if (!nombre || !apellidoPaterno || !fechaNacimiento) return;

  const nombres = nombre.split(" ");
  const primerNombre = nombres[0].toUpperCase();

  let nombreParaCurp = primerNombre;
  if (
    ["MARIA", "JOSE", "MA", "JA"].some((n) => primerNombre.startsWith(n)) &&
    nombres.length > 1
  ) {
    nombreParaCurp = nombres[1].toUpperCase();
  }

  const paternoSinAcentos = apellidoPaterno
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "")
    .toUpperCase();

  const vocales = "AEIOU";
  let primeraVocalPaterno = "X";
  for (let i = 1; i < paternoSinAcentos.length; i++) {
    if (vocales.includes(paternoSinAcentos[i])) {
      primeraVocalPaterno = paternoSinAcentos[i];
      break;
    }
  }

  let curp = "";
  curp += getFirstLetter(apellidoPaterno);
  curp += primeraVocalPaterno;
  curp += getFirstLetter(apellidoMaterno);
  curp += getFirstLetter(nombreParaCurp);

  const fechaPartes = fechaNacimiento.split("-");
  if (fechaPartes.length < 3) return;
  const year = fechaPartes[0].slice(-2);
  const month = fechaPartes[1];
  const day = fechaPartes[2];
  curp += year + month + day;
  curp += genero === "H" ? "H" : "M";
  curp += getEntityCode(entidad);
  curp += getFirstConsonant(apellidoPaterno);
  curp += getFirstConsonant(apellidoMaterno);
  curp += getFirstConsonant(nombreParaCurp);

  document.getElementById("curp").value = curp;
}

function generarClaveElector() {
  const nombre = document.getElementById("nombre").value.trim();
  const apellidoPaterno = document
    .getElementById("apellido_paterno")
    .value.trim();
  const apellidoMaterno = document
    .getElementById("apellido_materno")
    .value.trim();
  const fechaNacimiento = document.getElementById("fecha_nacimiento").value;
  const genero = document.getElementById("genero").value;
  const entidad = document.getElementById("entidad").value;

  if (!nombre || !apellidoPaterno || !fechaNacimiento || !genero || !entidad)
    return;

  function getFirstConsonants(str, count) {
    if (!str || str.trim() === "") return "X".repeat(count);

    const sinAcentos = str
      .normalize("NFD")
      .replace(/[\u0300-\u036f]/g, "")
      .trim()
      .toUpperCase();
    const consonants = "BCDFGHJKLMNPQRSTVWXYZ";
    let result = "";
    let countFound = 0;

    for (let i = 0; i < sinAcentos.length && countFound < count; i++) {
      if (consonants.includes(sinAcentos[i])) {
        result += sinAcentos[i];
        countFound++;
      }
    }

    while (result.length < count) result += "X";
    return result;
  }

  let claveElector = "";
  claveElector += getFirstConsonants(apellidoPaterno, 2);
  claveElector += getFirstConsonants(apellidoMaterno, 2);
  claveElector += getFirstConsonants(nombre, 2);

  const fechaPartes = fechaNacimiento.split("-");
  const year = fechaPartes[0].slice(-2);
  const month = fechaPartes[1];
  const day = fechaPartes[2];
  claveElector += year + month + day;

  const entidadNumerica = {
    AGS: "01",
    BC: "02",
    BCS: "03",
    CAMP: "04",
    COAH: "05",
    COL: "06",
    CHIS: "07",
    CHIH: "08",
    CDMX: "09",
    DGO: "10",
    GTO: "11",
    GRO: "12",
    HGO: "13",
    JAL: "14",
    MEX: "15",
    MICH: "16",
    MOR: "17",
    NAY: "18",
    NL: "19",
    OAX: "20",
    PUE: "21",
    QRO: "22",
    QROO: "23",
    SLP: "24",
    SIN: "25",
    SON: "26",
    TAB: "27",
    TAMS: "28",
    TLAX: "29",
    VER: "30",
    YUC: "31",
    ZAC: "32",
  };
  claveElector += entidadNumerica[entidad] || "00";
  claveElector += genero === "H" ? "H" : "M";
  claveElector += Math.floor(Math.random() * 900 + 100).toString();

  document.getElementById("clave_elector").value = claveElector;
}

// Event listeners para generación automática
document.addEventListener("DOMContentLoaded", function () {
  [
    "nombre",
    "apellido_paterno",
    "apellido_materno",
    "fecha_nacimiento",
    "genero",
    "entidad",
  ].forEach(function (id) {
    const elemento = document.getElementById(id);
    if (elemento) {
      elemento.addEventListener("input", generarCURP);
      elemento.addEventListener("change", generarCURP);
    }
  });

  [
    "nombre",
    "apellido_paterno",
    "apellido_materno",
    "fecha_nacimiento",
  ].forEach(function (id) {
    const elemento = document.getElementById(id);
    if (elemento) {
      elemento.addEventListener("input", generarRFC);
      elemento.addEventListener("change", generarRFC);
    }
  });

  [
    "nombre",
    "apellido_paterno",
    "apellido_materno",
    "fecha_nacimiento",
    "genero",
    "entidad",
  ].forEach(function (id) {
    const elemento = document.getElementById(id);
    if (elemento) {
      elemento.addEventListener("input", generarClaveElector);
      elemento.addEventListener("change", generarClaveElector);
    }
  });

  document.getElementById("curp").addEventListener("input", function () {
    this.value = this.value.toUpperCase();
  });
  document.getElementById("rfc").addEventListener("input", function () {
    this.value = this.value.toUpperCase();
  });
  document
    .getElementById("clave_elector")
    .addEventListener("input", function () {
      this.value = this.value.toUpperCase();
    });
});

// Validación y envío del formulario con ESP32
document
  .getElementById("registroForm")
  .addEventListener("submit", async function (e) {
    e.preventDefault();

    const btn = document.getElementById("btnRegistrar");
    btn.disabled = true;

    try {
      // ===== VALIDACIONES =====
      const curp = document.getElementById("curp").value;
      const rfc = document.getElementById("rfc").value;
      const entidad = document.getElementById("entidad").value;
      const genero = document.getElementById("genero").value;

      if (!capturedImageData) throw new Error("Debe capturar una foto");
      if (!entidad) throw new Error("Seleccione entidad");
      if (!genero) throw new Error("Seleccione género");
      if (curp.length !== 18) throw new Error("CURP inválida");
      if (rfc.length < 10 || rfc.length > 13) throw new Error("RFC inválido");

      if (!esp32Disponible()) throw new Error("ESP32 no conectado");

      log("Enviando REGISTER...");
      await safeWrite("REGISTER\n");

      cancelNFCScan = false;

      Swal.fire({
        title: "Escanee tarjeta NFC...",
        text: "Acerque la tarjeta al lector",
        showCancelButton: true,
        cancelButtonText: "Cancelar",
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
          Swal.showLoading();
        },
      }).then(async (result) => {
        if (result.dismiss === Swal.DismissReason.cancel) {
          cancelNFCScan = true;
          log("Escaneo cancelado por usuario");
          try {
            await safeWrite("CANCEL\n");
          } catch (_) {}
        }
      });

      let currentFingerId = null;
      let uid = null;
      let token = null;
      let fingerTimeout = null;

      while (true) {
        if (cancelNFCScan) {
          clearTimeout(fingerTimeout);
          await closeSwal();

          if (currentFingerId) {
            await safeWrite("DELETE_FINGER:" + currentFingerId + "\n");
            log("Huella eliminada por cancelación");
          }

          throw new Error("Registro cancelado");
        }

        const msg = await readLine();

        if (msg === null) {
          throw new Error("Conexión perdida con ESP32");
        }

        if (!msg) continue;

        log("ESP32: " + msg);

        // ================= TARJETA =================
        if (msg === "WAIT_CARD") {
          Swal.update({
            title: "Escanee tarjeta NFC...",
            text: "Acerque la tarjeta al lector",
          });
          continue;
        }

        // ================= PRIMERA VEZ HUELLA =================
        if (msg === "PUT_FINGER") {
          await closeSwal();

          Swal.fire({
            title: "Ponga su huella dactilar",
            text: "Coloque su dedo en el lector",
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => Swal.showLoading(),
          });

          // ⏳ Timeout 20 segundos
          fingerTimeout = setTimeout(async () => {
            cancelNFCScan = true;
            try {
              await safeWrite("CANCEL\n");
            } catch (_) {}
          }, 20000);

          continue;
        }

        // ================= QUITAR HUELLA =================
        if (msg === "REMOVE_FINGER") {
          Swal.update({
            title: "Quite su huella",
            text: "Retire el dedo del lector",
          });
          continue;
        }

        // ================= SEGUNDA VEZ =================
        if (msg === "PUT_FINGER_AGAIN") {
          Swal.update({
            title: "Acerque de nuevo su huella",
            text: "Coloque el mismo dedo nuevamente",
          });
          continue;
        }

        // ================= HUELLA OK =================
        if (msg === "FINGER_OK") {
          clearTimeout(fingerTimeout);
          Swal.update({
            title: "Huella registrada correctamente",
            text: "Procesando información...",
          });
          continue;
        }

        // ================= RECIBE ID DE HUELLA =================
        if (msg.startsWith("FINGER_ID:")) {
          currentFingerId = msg.replace("FINGER_ID:", "").trim();
          log("ID de huella asignado: " + currentFingerId);
          continue;
        }

        // ================= RESULTADO FINAL ROBUSTO =================
        if (msg.startsWith("UID:")) {
          uid = msg.replace("UID:", "").trim();
          log("UID recibido: " + uid);
          continue;
        }

        if (msg.startsWith("TOKEN:")) {
          token = msg.replace("TOKEN:", "").trim();
          log("Token recibido: " + token);
          continue;
        }

        if (msg.startsWith("FINGER:")) {
          currentFingerId = msg.replace("FINGER:", "").trim();
          log("FINGER recibido: " + currentFingerId);

          // Solo continuar si tenemos UID y TOKEN
          if (!uid || !token) {
            log("Esperando UID o TOKEN antes de guardar votante...");
            continue;
          }

          await closeSwal();

          Swal.fire({
            title: "Guardando votante...",
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => Swal.showLoading(),
          });

          const formData = new FormData(this);
          formData.append("uid", uid);
          formData.append("token", token);
          formData.append("finger_id", currentFingerId);
          formData.append("foto", capturedImageData);

          const res = await fetch("/votosecure/api/guardar_votante.php", {
            method: "POST",
            body: formData,
          });

          const data = await res.json();

          if (data.status === "OK") {
            clearTimeout(fingerTimeout);
            cancelNFCScan = false;

            Swal.fire({
              icon: "success",
              title: "Votante registrado correctamente",
            });

            this.reset();
            capturedImageData = null;
            uid = null;
            token = null;
            currentFingerId = null;

            break;
          } else {
            if (fingerToDelete) {
              await safeWrite("DELETE_FINGER:" + fingerToDelete + "\n");
            }
            throw new Error(data.message || "Error guardando en servidor");
          }
        }

        // ================= CANCELACIÓN GLOBAL =================
        if (cancelNFCScan) {
          clearTimeout(fingerTimeout);
          await closeSwal();

          if (currentFingerId) {
            await safeWrite("DELETE_FINGER:" + currentFingerId + "\n");
          }

          throw new Error("Registro cancelado");
        }
      }

      if (cancelNFCScan) {
        throw new Error("Escaneo NFC cancelado");
      }
    } catch (err) {
      Swal.fire({
        icon: "error",
        title: "Error",
        text: err.message,
      });
      log("Error registro: " + err.message);
    } finally {
      btn.disabled = false;
    }
  });
