let videoStream = null;
let capturedImageData = null;

// ================= ESP32 NFC + FINGERPRINT =================
let port = null,
  reader = null,
  writer = null;
const esp32Status = document.getElementById("esp32Status");

const log = (m) => {
  const logEl = document.getElementById("esp32Log");
  if (logEl) {
    logEl.style.display = 'block';
    logEl.textContent += m + "\n";
    logEl.scrollTop = logEl.scrollHeight;
  }
   
};

// Conectar al ESP32
async function connectESP32() {
  try {
    port = await navigator.serial.requestPort();
    await port.open({
      baudRate: 115200,
    });

    const decoder = new TextDecoderStream();
    port.readable.pipeTo(decoder.writable);
    reader = decoder.readable.getReader();
    writer = port.writable.getWriter();

    esp32Status.innerHTML =
      '<span class="badge bg-success">✅ Conectado</span>';
    log("ESP32 conectado y listo");
    
    Swal.fire({
      icon: 'success',
      title: 'ESP32 Conectado',
      text: 'El dispositivo está listo para el registro',
      timer: 2000,
      showConfirmButton: false
    });
  } catch (e) {
    log("Error al conectar: " + e.message);
    Swal.fire({
      icon: 'error',
      title: 'Error de conexión',
      text: 'No se pudo conectar al ESP32: ' + e.message
    });
  }
}

// Leer línea del ESP32
async function readLine() {
  let buffer = "";
  while (true) {
    try {
      const { value, done } = await reader.read();
      if (done) return "";
      if (!value) continue;
      
      // Debug: log raw data
       
      
      buffer += value;
      const lines = buffer.split(/\r?\n/);
      buffer = lines.pop();
      for (let line of lines) {
        line = line.trim();
        if (line.length) {
           
          return line;
        }
      }
    } catch (e) {
      log("Error leyendo: " + e.message);
      return "";
    }
  }
}

// ================= FOTO =================

// Función para cerrar cualquier SweetAlert abierto
async function closeSwal() {
  if (Swal.isVisible()) {
    Swal.close();
    await new Promise(resolve => setTimeout(resolve, 100));
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
        icon: 'error',
        title: 'Archivo muy grande',
        text: 'La imagen no debe exceder 500KB'
      });
      event.target.value = ''; // Limpiar input
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
        icon: 'error',
        title: 'Error de cámara',
        text: 'No se pudo acceder a la cámara: ' + err.message
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
  if (!str || str.trim() === '') return 'X';
  return str.trim().charAt(0).toUpperCase();
}

function getFirstConsonant(str) {
  if (!str || str.trim() === '') return 'X';
  
  // Eliminar acentos y convertir a mayúsculas
  const sinAcentos = str.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
  const upperStr = sinAcentos.trim().toUpperCase();

  const consonants = 'BCDFGHJKLMNPQRSTVWXYZ';
  // Empezar desde la segunda letra (posición 1) para evitar la primera letra que ya es vocal
  for (let i = 1; i < upperStr.length; i++) {
    if (consonants.includes(upperStr[i])) {
      return upperStr[i];
    }
  }
  return 'X';
}

function getEntityCode(estado) {
  const estados = {
    'AGS': 'AS', 'BC': 'BC', 'BCS': 'BS', 'CAMP': 'CC', 'COAH': 'CL',
    'COL': 'CM', 'CHIS': 'CS', 'CHIH': 'CH', 'CDMX': 'DF', 'DGO': 'DG',
    'GTO': 'GT', 'GRO': 'GR', 'HGO': 'HG', 'JAL': 'JC', 'MEX': 'MC',
    'MICH': 'MN', 'MOR': 'MS', 'NAY': 'NL', 'NL': 'NL', 'OAX': 'OC',
    'PUE': 'PL', 'QRO': 'QT', 'QROO': 'QR', 'SLP': 'SL', 'SIN': 'SI',
    'SON': 'SR', 'TAB': 'TC', 'TAMS': 'TS', 'TLAX': 'TL', 'VER': 'VZ',
    'YUC': 'YN', 'ZAC': 'ZS'
  };
  return estados[estado] || 'NE';
}

// ================= GENERACIÓN AUTOMÁTICA DE RFC =================

function generarRFC() {
  const nombre = document.getElementById('nombre').value.trim();
  const apellidoPaterno = document.getElementById('apellido_paterno').value.trim();
  const apellidoMaterno = document.getElementById('apellido_materno').value.trim();
  const fechaNacimiento = document.getElementById('fecha_nacimiento').value;
  
  if (!nombre || !apellidoPaterno || !fechaNacimiento) {
    return;
  }
  
  // Obtener el primer nombre (no compuesto)
  const nombres = nombre.split(' ');
  const primerNombre = nombres[0].toUpperCase();
  
  // Determinar qué nombre usar (si es compuesto, usar el segundo)
  let nombreParaRfc = primerNombre;
  if (['MARIA', 'JOSE', 'MA', 'JA'].some(n => primerNombre.startsWith(n)) && nombres.length > 1) {
    nombreParaRfc = nombres[1].toUpperCase();
  }
  
  let rfc = '';
  
  // Primera letra del apellido paterno
  rfc += getFirstLetter(apellidoPaterno);
  
  // Primera vocal del apellido paterno (después de la primera letra)
  const vocales = 'AEIOU';
  let segundaLetra = 'X';
  if (apellidoPaterno.length > 1) {
    // Buscar vocal desde la segunda posición
    const sinAcentos = apellidoPaterno.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toUpperCase();
    for (let i = 1; i < sinAcentos.length; i++) {
      if (vocales.includes(sinAcentos[i])) {
        segundaLetra = sinAcentos[i];
        break;
      }
    }
  }
  rfc += segundaLetra;
  
  // Primera letra del apellido materno
  rfc += getFirstLetter(apellidoMaterno);
  
  // Primera letra del nombre
  rfc += getFirstLetter(nombreParaRfc);
  
  // Fecha de nacimiento
  const fechaPartes = fechaNacimiento.split('-');
  const year = fechaPartes[0].slice(-2);
  const month = fechaPartes[1];
  const day = fechaPartes[2];
  rfc += year + month + day;
  
  // Homoclave (3 caracteres: 2 letras + 1 número) - aleatorio
  const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
  let homoclave = '';
  for (let i = 0; i < 2; i++) {
    homoclave += chars.charAt(Math.floor(Math.random() * 26));
  }
  homoclave += Math.floor(Math.random() * 10).toString();
  rfc += homoclave;
  
  document.getElementById('rfc').value = rfc;
}

// ================= GENERACIÓN AUTOMÁTICA DE CURP =================

function generarCURP() {
  const nombre = document.getElementById('nombre').value.trim();
  const apellidoPaterno = document.getElementById('apellido_paterno').value.trim();
  const apellidoMaterno = document.getElementById('apellido_materno').value.trim();
  const fechaNacimiento = document.getElementById('fecha_nacimiento').value;
  const genero = document.getElementById('genero').value;
  const entidad = document.getElementById('entidad').value;
  
  if (!nombre || !apellidoPaterno || !fechaNacimiento) {
    return;
  }
  
  // Obtener el primer nombre (no compuesto)
  const nombres = nombre.split(' ');
  const primerNombre = nombres[0].toUpperCase();
  
  // Determinar qué nombre usar (si es compuesto, usar el segundo)
  let nombreParaCurp = primerNombre;
  if (['MARIA', 'JOSE', 'MA', 'JA'].some(n => primerNombre.startsWith(n)) && nombres.length > 1) {
    nombreParaCurp = nombres[1].toUpperCase();
  }
  
  // Eliminar acentos del apellido paterno
  const paternoSinAcentos = apellidoPaterno.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toUpperCase();
  
  // Obtener la primera vocal del apellido paterno (después de la primera letra)
  const vocales = 'AEIOU';
  let primeraVocalPaterno = 'X';
  for (let i = 1; i < paternoSinAcentos.length; i++) {
    if (vocales.includes(paternoSinAcentos[i])) {
      primeraVocalPaterno = paternoSinAcentos[i];
      break;
    }
  }
  
  let curp = '';
  
  // Posición 1: Primera letra del apellido paterno
  curp += getFirstLetter(apellidoPaterno);
  
  // Posición 2: Primera vocal del apellido paterno (misma lógica que RFC)
  curp += primeraVocalPaterno;
  
  // Posición 3: Primera letra del apellido materno
  curp += getFirstLetter(apellidoMaterno);
  
  // Posición 4: Primera letra del nombre
  curp += getFirstLetter(nombreParaCurp);
  
  // Posición 5-6: Año de nacimiento (últimos 2 dígitos)
  const fechaPartes = fechaNacimiento.split('-');
  if (fechaPartes.length < 3) return;
  const year = fechaPartes[0].slice(-2);
  const month = fechaPartes[1];
  const day = fechaPartes[2];
  curp += year + month + day;
  
  // Posición 7: Género (H=Hombre, M=Mujer)
  curp += (genero === 'M') ? 'M' : 'H';
  
  // Posición 8-9: Clave de entidad federativa
  curp += getEntityCode(entidad);
  
  // Posición 10: Primera consonante del apellido paterno
  curp += getFirstConsonant(apellidoPaterno);
  
  // Posición 11: Primera consonante del apellido materno
  curp += getFirstConsonant(apellidoMaterno);
  
  // Posición 12: Primera consonante del nombre
  curp += getFirstConsonant(nombreParaCurp);
  
  // Posición 13-14: Homoclave (dígitos aleatorios) - NO se puede generar
  const homoclave = Math.floor(Math.random() * 90 + 10).toString();
  curp += homoclave;
  
  // Posición 15-18: Dígito verificador (uno aleatorio por ahora) - NO se puede generar
  curp += Math.floor(Math.random() * 10).toString();
  
  document.getElementById('curp').value = curp;
}

function generarClaveElector() {
  const nombre = document.getElementById('nombre').value.trim();
  const apellidoPaterno = document.getElementById('apellido_paterno').value.trim();
  const apellidoMaterno = document.getElementById('apellido_materno').value.trim();
  const fechaNacimiento = document.getElementById('fecha_nacimiento').value;
  const genero = document.getElementById('genero').value;
  const entidad = document.getElementById('entidad').value;
  
  if (!nombre || !apellidoPaterno || !fechaNacimiento || !genero || !entidad) {
    return;
  }
  
  // Función para obtener las primeras consonantes de una cadena
  function getFirstConsonants(str, count) {
    if (!str || str.trim() === '') return 'X'.repeat(count);
    
    // Eliminar acentos y convertir a mayúsculas
    const sinAcentos = str.normalize("NFD").replace(/[\u0300-\u036f]/g, "").trim().toUpperCase();
    const consonants = 'BCDFGHJKLMNPQRSTVWXYZ';
    let result = '';
    let countFound = 0;
    
    for (let i = 0; i < sinAcentos.length && countFound < count; i++) {
      if (consonants.includes(sinAcentos[i])) {
        result += sinAcentos[i];
        countFound++;
      }
    }
    
    // Si no hay suficientes consonantes, completar con X
    while (result.length < count) {
      result += 'X';
    }
    
    return result;
  }
  
  let claveElector = '';
  
  // 1° y 2° dígitos: Dos consonantes iniciales del primer apellido
  claveElector += getFirstConsonants(apellidoPaterno, 2);
  
  // 3° y 4° dígitos: Dos consonantes iniciales del segundo apellido
  claveElector += getFirstConsonants(apellidoMaterno, 2);
  
  // 5° y 6° dígitos: Dos consonantes iniciales del nombre
  claveElector += getFirstConsonants(nombre, 2);
  
  // 7° a 12° dígitos: Fecha de nacimiento (año, mes, día - 2 dígitos cada uno)
  const fechaPartes = fechaNacimiento.split('-');
  const year = fechaPartes[0].slice(-2);
  const month = fechaPartes[1];
  const day = fechaPartes[2];
  claveElector += year + month + day;
  
  // 13° y 14° dígitos: Número de la entidad federativa
  const entidadNumerica = {
    'AGS': '01', 'BC': '02', 'BCS': '03', 'CAMP': '04', 'COAH': '05',
    'COL': '06', 'CHIS': '07', 'CHIH': '08', 'CDMX': '09', 'DGO': '10',
    'GTO': '11', 'GRO': '12', 'HGO': '13', 'JAL': '14', 'MEX': '15',
    'MICH': '16', 'MOR': '17', 'NAY': '18', 'NL': '19', 'OAX': '20',
    'PUE': '21', 'QRO': '22', 'QROO': '23', 'SLP': '24', 'SIN': '25',
    'SON': '26', 'TAB': '27', 'TAMS': '28', 'TLAX': '29', 'VER': '30',
    'YUC': '31', 'ZAC': '32'
  };
  claveElector += entidadNumerica[entidad] || '00';
  
  // 15° dígito: Género (M = Masculino, F = Femenino)
  claveElector += (genero === 'M') ? 'F' : 'M';
  
  // 16° a 18° dígitos: Homoclave aleatoria de tres dígitos
  const homoclave = Math.floor(Math.random() * 900 + 100).toString();
  claveElector += homoclave;
  
  document.getElementById('clave_elector').value = claveElector;
}

// Event listeners para generación automática
document.addEventListener('DOMContentLoaded', function() {
  const camposCURP = ['nombre', 'apellido_paterno', 'apellido_materno', 'fecha_nacimiento', 'genero', 'entidad'];
  const camposClaveElector = ['nombre', 'apellido_paterno', 'apellido_materno', 'fecha_nacimiento', 'genero', 'entidad'];
  const camposRFC = ['nombre', 'apellido_paterno', 'apellido_materno', 'fecha_nacimiento'];
  
  camposCURP.forEach(function(id) {
    const elemento = document.getElementById(id);
    if (elemento) {
      elemento.addEventListener('input', generarCURP);
      elemento.addEventListener('change', generarCURP);
      elemento.addEventListener('blur', generarCURP);
    }
  });
  
  camposRFC.forEach(function(id) {
    const elemento = document.getElementById(id);
    if (elemento) {
      elemento.addEventListener('input', generarRFC);
      elemento.addEventListener('change', generarRFC);
      elemento.addEventListener('blur', generarRFC);
    }
  });
  
  camposClaveElector.forEach(function(id) {
    const elemento = document.getElementById(id);
    if (elemento) {
      elemento.addEventListener('input', generarClaveElector);
      elemento.addEventListener('change', generarClaveElector);
      elemento.addEventListener('blur', generarClaveElector);
    }
  });
});

// Validación y envío del formulario con ESP32
document
  .getElementById("registroForm")
  .addEventListener("submit", async function (e) {
    e.preventDefault();

    const curp = document.getElementById("curp").value;
    const rfc = document.getElementById("rfc").value;
    const entidad = document.getElementById("entidad").value;
    const genero = document.getElementById("genero").value;

    // Validar que se haya capturado o subido una foto
    if (!capturedImageData) {
      Swal.fire({
        icon: 'error',
        title: 'Foto requerida',
        text: 'Por favor capture o seleccione una fotografía del votante'
      });
      return;
    }

    if (!entidad) {
      Swal.fire({
        icon: 'error',
        title: 'Entidad requerida',
        text: 'Por favor seleccione su entidad federativa'
      });
      return;
    }

    if (!genero) {
      Swal.fire({
        icon: 'error',
        title: 'Género requerido',
        text: 'Por favor seleccione el género del votante'
      });
      return;
    }

    if (curp.length !== 18) {
      Swal.fire({
        icon: 'error',
        title: 'CURP Inválida',
        text: 'La CURP debe tener 18 caracteres'
      });
      return;
    }

    if (rfc.length < 10 || rfc.length > 13) {
      Swal.fire({
        icon: 'error',
        title: 'RFC Inválido',
        text: 'El RFC debe tener entre 10 y 13 caracteres'
      });
      return;
    }

    if (port && writer) {
      log("Iniciando registro en ESP32...");
      document.getElementById("btnRegistrar").disabled = true;
      
      const logEl = document.getElementById("esp32Log");
      if (logEl) logEl.textContent = "";

      Swal.fire({
        title: 'Esperando tarjeta NFC...',
        html: 'Acerque la tarjeta al lector',
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });

      try {
        await writer.write(new TextEncoder().encode("REGISTER\n"));

        while (true) {
          const msg = await readLine();
          log("ESP32: " + msg);

          if (msg.includes("READY")) {
            closeSwal();
            Swal.fire({
              icon: 'info',
              title: 'ESP32 Listo',
              text: 'Esperando comando de registro...'
            });
          }
          else if (msg.includes("WAIT_CARD")) {
            closeSwal();
            Swal.fire({
              title: 'Esperando tarjeta NFC...',
              html: 'Acerque la tarjeta al lector',
              allowOutsideClick: false,
              didOpen: () => {
                Swal.showLoading();
              }
            });
          }
          else if (msg.includes("FINGER_ATTEMPT")) {
            closeSwal();
            const attemptMatch = msg.match(/FINGER_ATTEMPT (\d+)/);
            const attempt = attemptMatch ? attemptMatch[1] : '1';
            Swal.fire({
              title: 'Capturando huella...',
              html: `Intento ${attempt} de 3<br>Coloque su dedo en el sensor`,
              allowOutsideClick: false,
              didOpen: () => {
                Swal.showLoading();
              }
            });
          }
          else if (msg.includes("PUT_FINGER")) {
            closeSwal();
            Swal.fire({
              title: 'Coloque su dedo',
              html: 'En el sensor de huella digital',
              allowOutsideClick: false,
              didOpen: () => {
                Swal.showLoading();
              }
            });
          }
          else if (msg.includes("REMOVE_FINGER")) {
            closeSwal();
            Swal.fire({
              title: 'Retire el dedo',
              html: 'Y vuelva a colocar para segunda captura',
              allowOutsideClick: false,
              didOpen: () => {
                Swal.showLoading();
              }
            });
          }
          else if (msg.includes("PUT_FINGER_AGAIN")) {
            closeSwal();
            Swal.fire({
              title: 'Segunda captura...',
              html: 'Coloque nuevamente su dedo',
              allowOutsideClick: false,
              didOpen: () => {
                Swal.showLoading();
              }
            });
          }
          else if (msg.includes("FINGER_IMAGE_OK")) {
            closeSwal();
            Swal.fire({
              title: 'Huella capturada',
              text: 'Procesando...',
              timer: 1000,
              showConfirmButton: false
            });
          }
          else if (msg.includes("FINGER_OK")) {
            closeSwal();
            Swal.fire({
              title: 'Huella registrada',
              text: 'Guardando datos...',
              timer: 1500,
              showConfirmButton: false
            });
          }
          else if (msg.includes("FINGER_TIMEOUT") || msg.includes("AUTH_FAIL") || msg.includes("WRITE_FAIL") || msg.includes("FINGER_ERR") || msg.includes("IMAGE2TZ_FAIL") || msg.includes("CREATE_MODEL_FAIL") || msg.includes("STORE_MODEL_FAIL") || msg.includes("FINGER_MAX_ATTEMPTS_FAIL") || msg.includes("FINGER_FAIL")) {
            closeSwal();
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'Error en el proceso de registro. Intente de nuevo.',
              confirmButtonText: 'Aceptar'
            });
            break;
          }
          else if (msg.includes("NFC_ERROR")) {
            Swal.fire({
              icon: 'error',
              title: 'Error NFC',
              text: 'No se detectó el módulo NFC. Verifique la conexión.'
            });
            break;
          }
          // Formato: OK|UID|TOKEN|FINGER o UID|TOKEN|FINGER o solo UID|FINGER (si NFC falla)
          else if (msg.includes("|")) {
            const partes = msg.split("|");
            
            let uid, token, finger;
            
            // Caso: OK|UID|TOKEN|FINGER (formato completo con OK)
            if (partes.length >= 4 && partes[0] === "OK") {
              uid = partes[1].trim();
              token = partes[2].trim();
              finger = partes[3].trim();
            }
            // Caso: UID|TOKEN|FINGER (formato sin OK)
            else if (partes.length >= 3) {
              uid = partes[0].trim();
              token = partes[1].trim();
              finger = partes[2].trim();
            }
            // Caso: UID|FINGER (NFC falló, solo tenemos UID y finger)
            else if (partes.length >= 2) {
              uid = partes[0].trim();
              finger = partes[1].trim();
              token = ""; // Token vacío porque NFC falló
              log("ADVERTENCIA: Token NFC no disponible");
            }
            else {
              log("Formato inesperado: " + msg);
              continue;
            }

            uid = uid || "";
            token = token || "";
            finger = finger || "";

            if (!uid || !finger) {
              log("Datos incompletos - UID: '" + uid + "' Token: '" + token + "' Finger: '" + finger + "'");
              continue;
            }

            log("Datos recibidos - UID: " + uid + ", Token: " + token + ", Finger: " + finger);

            closeSwal();
            Swal.fire({
              title: 'Registrando datos...',
              allowOutsideClick: false,
              didOpen: () => {
                Swal.showLoading();
              }
            });

            const formData = new FormData(this);
            formData.append("uid", uid);
            formData.append("token", token);
            formData.append("finger_id", finger);
            formData.append("foto", capturedImageData);

            log("Enviando datos al servidor...");

            const res = await fetch("/votosecure/api/guardar_votante.php", {
              method: "POST",
              body: formData,
            });

            const responseData = await res.json();
            log("Respuesta del servidor: " + JSON.stringify(responseData));

            if (responseData.status === "OK") {
              Swal.fire({
                icon: 'success',
                title: '¡Votante registrado!',
                text: 'El votante ha sido agregado a la base de datos correctamente.',
                confirmButtonText: 'Aceptar'
              });
              this.reset();
              document.getElementById("imagePreview").style.display = "none";
              document.getElementById("placeholderPreview").style.display = "flex";
              capturedImageData = null;
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Error al guardar',
                text: responseData.message || responseData.error || 'Error desconocido'
              });
            }

            break;
          }
        }
      } catch (err) {
        log("Error: " + err.message);
        Swal.fire({
          icon: 'error',
          title: 'Error de conexión',
          text: 'Error durante el registro: ' + err.message
        });
      }

      document.getElementById("btnRegistrar").disabled = false;
    } else {
      Swal.fire({
        icon: 'warning',
        title: 'ESP32 no conectado',
        text: 'Conecte el dispositivo para completar el registro'
      });
    }
    
    return false;
  });

