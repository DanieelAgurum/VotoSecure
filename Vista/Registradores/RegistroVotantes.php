<?php
session_start();
// // Verificar que el usuario sea un registrador
// if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'registrador') {
//     header('Location: ../login.php');
//     exit;
// }
define('BASE_URL', '/VotoSecure');
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Votantes - VotoSecure</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../../css/RegistroVotantes.css">
</head>

<body class="body">
    <div class="form-card">
        <div class="form-header">
            <i class="fas fa-id-card"></i>
            <h1>Registro de Votante</h1>
            <p>Ingrese los datos solicitados para el trámite de credencial INE</p>
        </div>

        <div class="form-body">
            <form action="" method="POST" enctype="multipart/form-data" id="registroForm">
                <!-- Conexión ESP32 -->
                <div class="section-title">
                    <i class="fas fa-microchip"></i> Dispositivo ESP32
                </div>
                <div class="row g-3 mb-4 align-items-center">
                    <div class="col-md-4">
                        <button type="button" id="btnConnect" class="btn btn-warning w-100" onclick="toggleConnectESP32()">
                            <i class="fas fa-plug"></i> Conectar ESP32
                        </button>
                    </div>
                    <div class="col-md-4">
                        <span id="esp32Status"><span class="badge bg-secondary">No conectado</span></span>
                    </div>
                </div>

                <!-- Información de Foto -->
                <div class="section-title">
                    <i class="fas fa-camera"></i> Fotografía del Votante
                </div>
                <div class="row mb-4 align-items-center">
                    <div class="col-lg-4 col-md-12 mb-3 text-center">
                        <!-- Vista previa de la foto -->
                        <div class="photo-preview-container mb-3">
                            <img id="imagePreview" class="photo-preview" alt="Vista previa">
                            <div id="placeholderPreview" class="photo-placeholder">
                                <i class="fas fa-user"></i>
                            </div>
                        </div>
                        <!-- Campo oculto para almacenar la foto capturada -->
                        <input type="hidden" id="foto_capturada" name="foto_capturada">
                    </div>
                    <div class="col-lg-4 col-md-6 mb-3">
                        <div class="photo-options">
                            <!-- Opción: Tomar foto con cámara -->
                            <button type="button" class="btn btn-outline-primary w-100 mb-2" onclick="openCamera()">
                                <i class="fas fa-camera"></i> Tomar Foto
                            </button>
                            <!-- Opción: Buscar en archivos -->
                            <button type="button" class="btn btn-outline-secondary w-100" onclick="document.getElementById('foto').click()">
                                <i class="fas fa-folder-open"></i> Buscar Archivo
                            </button>
                            <input type="file" id="foto" name="foto" accept="image/*" style="display: none;" onchange="previewImage(event)">
                        </div>
                        <small class="text-muted d-block mt-2">Formato: JPG, PNG (máx 2MB)</small>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-3">
                        <div class="info-box">
                            <i class="fas fa-info-circle"></i>
                            <strong>Requisitos de la foto:</strong>
                            <ul class="mb-0 mt-2 small">
                                <li>Fondo blanco o claro</li>
                                <li>Vista frontal sin lentes</li>
                                <li>Ropa formal</li>
                                <li>Sin accesorios</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Modal para captura de cámara -->
                <div class="modal fade" id="cameraModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Capturar Fotografía</h5>
                                <button type="button" class="btn-close" onclick="closeCamera()"></button>
                            </div>
                            <div class="modal-body text-center">
                                <div class="camera-container" style="position: relative; display: inline-block;">
                                    <video id="videoElement" autoplay playsinline style="max-width: 100%; border-radius: 10px; transform: scaleX(-1);"></video>
                                    <canvas id="canvasElement" style="display: none;"></canvas>
                                    <img id="capturedPhoto" class="img-fluid mt-2" style="display: none; border-radius: 10px; max-height: 300px; transform: scaleX(-1);">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" onclick="closeCamera()">Cancelar</button>
                                <button type="button" class="btn btn-primary" id="captureBtn" onclick="capturePhoto()">
                                    <i class="fas fa-camera"></i> Capturar
                                </button>
                                <button type="button" class="btn btn-success" id="retakeBtn" onclick="retakePhoto()" style="display: none;">
                                    <i class="fas fa-redo"></i> Repetir
                                </button>
                                <button type="button" class="btn btn-info" id="usePhotoBtn" onclick="usePhoto()" style="display: none;">
                                    <i class="fas fa-check"></i> Usar Foto
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Datos Personales -->
                <div class="section-title">
                    <i class="fas fa-user"></i> Datos Personales
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label required-field">Nombre(s)</label>
                        <div class="input-wrapper">
                            <i class="fas fa-user"></i>
                            <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre completo">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label required-field">Apellido Paterno</label>
                        <div class="input-wrapper">
                            <i class="fas fa-user"></i>
                            <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno" placeholder="Apellido paterno">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label required-field">Apellido Materno</label>
                        <div class="input-wrapper">
                            <i class="fas fa-user"></i>
                            <input type="text" class="form-control" id="apellido_materno" name="apellido_materno" placeholder="Apellido materno">
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label required-field">Fecha de Nacimiento</label>
                        <div class="input-wrapper">
                            <i class="fas fa-calendar"></i>
                            <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label required-field">Género</label>
                        <div class="input-wrapper">
                            <i class="fas fa-venus-mars"></i>
                            <select class="form-select" id="genero" name="genero">
                                <option value="">Seleccionar...</option>
                                <option value="H">Hombre</option>
                                <option value="M">Mujer</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label required-field">Nacionalidad</label>
                        <div class="input-wrapper">
                            <i class="fas fa-globe"></i>
                            <input type="text" class="form-control" id="nacionalidad" name="nacionalidad" placeholder="Mexicana" value="Mexicana">
                        </div>
                    </div>
                </div>

                <!-- CURP y RFC -->
                <div class="section-title">
                    <i class="fas fa-id-card"></i> Identificadores Oficiales
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label required-field">CURP</label>
                        <div class="input-wrapper">
                            <i class="fas fa-fingerprint"></i>
                            <input type="text" class="form-control" id="curp" name="curp" placeholder="AAAA000000HGRRRR01" maxlength="18" style="text-transform: uppercase;">
                            <button type="button" class="btn btn-sm btn-outline-primary btn-generar" onclick="generarCURP()" title="Generar CURP">
                                <i class="fas fa-magic"></i>
                            </button>
                        </div>
                        <small class="text-muted">18 caracteres - Se auto-llena con los datos ingresados</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label required-field">RFC</label>
                        <div class="input-wrapper">
                            <i class="fas fa-file-contract"></i>
                            <input type="text" class="form-control" id="rfc" name="rfc" placeholder="AAAA000000XXX" maxlength="13" style="text-transform: uppercase;">
                            <button type="button" class="btn btn-sm btn-outline-primary btn-generar" onclick="generarRFC()" title="Generar RFC">
                                <i class="fas fa-magic"></i>
                            </button>
                        </div>
                        <small class="text-muted">10 o 13 caracteres con homoclave - Se auto-llena con los datos ingresados</small>
                    </div>
                </div>

                <!-- Domicilio -->
                <div class="section-title">
                    <i class="fas fa-home"></i> Domicilio
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label required-field">Calle</label>
                        <div class="input-wrapper">
                            <i class="fas fa-road"></i>
                            <input type="text" class="form-control" id="calle" name="calle" placeholder="Nombre de la calle">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label required-field">Número Exterior</label>
                        <div class="input-wrapper">
                            <i class="fas fa-hashtag"></i>
                            <input type="text" class="form-control" id="num_exterior" name="num_exterior" placeholder="123">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Número Interior</label>
                        <div class="input-wrapper">
                            <i class="fas fa-hashtag"></i>
                            <input type="text" class="form-control" id="num_interior" name="num_interior" placeholder="Oficina, depto...">
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label required-field">Colonia</label>
                        <div class="input-wrapper">
                            <i class="fas fa-map-marker-alt"></i>
                            <input type="text" class="form-control" id="colonia" name="colonia" placeholder="Colonia">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label required-field">Código Postal</label>
                        <div class="input-wrapper">
                            <i class="fas fa-mail-bulk"></i>
                            <input type="text" class="form-control" id="codigo_postal" name="codigo_postal" placeholder="00000" maxlength="5">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label required-field">Municipio</label>
                        <div class="input-wrapper">
                            <i class="fas fa-city"></i>
                            <input type="text" class="form-control" id="municipio" name="municipio" placeholder="Municipio">
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label required-field">Entidad Federativa</label>
                        <div class="input-wrapper">
                            <i class="fas fa-map"></i>
                            <select class="form-select" id="entidad" name="entidad">
                                <option value="">Seleccionar estado...</option>
                                <option value="AGS">Aguascalientes</option>
                                <option value="BC">Baja California</option>
                                <option value="BCS">Baja California Sur</option>
                                <option value="CAMP">Campeche</option>
                                <option value="COAH">Coahuila</option>
                                <option value="COL">Colima</option>
                                <option value="CHIS">Chiapas</option>
                                <option value="CHIH">Chihuahua</option>
                                <option value="CDMX">Ciudad de México</option>
                                <option value="DGO">Durango</option>
                                <option value="GTO">Guanajuato</option>
                                <option value="GRO">Guerrero</option>
                                <option value="HGO">Hidalgo</option>
                                <option value="JAL">Jalisco</option>
                                <option value="MEX">Estado de México</option>
                                <option value="MICH">Michoacán</option>
                                <option value="MOR">Morelos</option>
                                <option value="NAY">Nayarit</option>
                                <option value="NL">Nuevo León</option>
                                <option value="OAX">Oaxaca</option>
                                <option value="PUE">Puebla</option>
                                <option value="QRO">Querétaro</option>
                                <option value="QROO">Quintana Roo</option>
                                <option value="SLP">San Luis Potosí</option>
                                <option value="SIN">Sinaloa</option>
                                <option value="SON">Sonora</option>
                                <option value="TAB">Tabasco</option>
                                <option value="TAMS">Tamaulipas</option>
                                <option value="TLAX">Tlaxcala</option>
                                <option value="VER">Veracruz</option>
                                <option value="YUC">Yucatán</option>
                                <option value="ZAC">Zacatecas</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Entre Calles</label>
                        <div class="input-wrapper">
                            <i class="fas fa-exchange-alt"></i>
                            <input type="text" class="form-control" id="entre_calles" name="entre_calles" placeholder="Referencias del domicilio">
                        </div>
                    </div>
                </div>

                <!-- Información de Contacto -->
                <div class="section-title">
                    <i class="fas fa-address-book"></i> Información de Contacto
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label required-field">Correo Electrónico</label>
                        <div class="input-wrapper">
                            <i class="fas fa-envelope"></i>
                            <input type="email" class="form-control" id="correo" name="correo" placeholder="correo@ejemplo.com">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label required-field">Teléfono Celular</label>
                        <div class="input-wrapper">
                            <i class="fas fa-mobile-alt"></i>
                            <input type="tel" class="form-control" id="telefono" name="telefono" placeholder="(999) 999-9999">
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Teléfono Fijo</label>
                        <div class="input-wrapper">
                            <i class="fas fa-phone"></i>
                            <input type="tel" class="form-control" id="telefono_fijo" name="telefono_fijo" placeholder="(999) 999-9999">
                        </div>
                    </div>
                </div>

                <!-- Información Electoral -->
                <div class="section-title">
                    <i class="fas fa-vote-yea"></i> Información Electoral
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label required-field">Sección Electoral</label>
                        <div class="input-wrapper">
                            <i class="fas fa-layer-group"></i>
                            <input type="text" class="form-control" id="seccion" name="seccion" placeholder="0000" maxlength="4">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label required-field">Clave de Elector</label>
                        <div class="input-wrapper">
                            <i class="fas fa-key"></i>
                            <input type="text" class="form-control" id="clave_elector" name="clave_elector" placeholder="000000000000" maxlength="18" style="text-transform: uppercase;">
                            <button type="button" class="btn btn-sm btn-outline-primary btn-generar" onclick="generarClaveElector()" title="Generar Clave de Elector">
                                <i class="fas fa-magic"></i>
                            </button>
                        </div>
                        <small class="text-muted">18 caracteres - Se auto-llena con los datos ingresados</small>
                    </div>
                </div>

                <!-- Botón de Envío -->
                <button type="submit" class="btn-submit" id="btnRegistrar">
                    <i class="fas fa-save"></i> Registrar Votante
                </button>

            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../js/registroVotantes.js"></script>
</body>

</html>