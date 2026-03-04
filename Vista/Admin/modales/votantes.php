<!-- MODAL: Cambiar Huella Digital -->
<div class="modal fade" id="modalHuella" tabindex="-1" aria-labelledby="modalHuellaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalHuellaLabel">
                    <i class="bi bi-fingerprint"></i> Cambiar Huella Digital
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <p class="mb-3">Votante: <strong id="huellaNombre"></strong></p>
                
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> 
                    Acerque el dedo al lector de huellas
                </div>
                
                <div class="mb-3">
                    <div class="spinner-border text-primary" role="status" id="huellaSpinner">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
                
                <!-- Campo oculto para almacenar el ID de huella -->
                <input type="hidden" id="huellaInput">
                <input type="hidden" id="huellaVotanteId">
                <input type="hidden" id="huellaActual">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: Cambiar NFC -->
<div class="modal fade" id="modalNFC" tabindex="-1" aria-labelledby="modalNFCLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalNFCLabel">
                    <i class="bi bi-nfc"></i> Cambiar Tarjeta NFC
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <p class="mb-3">Votante: <strong id="nfcNombre"></strong></p>
                
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> 
                    Acerque la tarjeta NFC al lector
                </div>
                
                <div class="mb-3">
                    <div class="spinner-border text-danger" role="status" id="nfcSpinner">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="nfcUidInput" class="form-label">UID NFC:</label>
                    <input type="text" class="form-control" id="nfcUidInput" placeholder="UID de la tarjeta" readonly>
                </div>
                
                <div class="mb-3">
                    <label for="nfcTokenInput" class="form-label">Token NFC:</label>
                    <input type="text" class="form-control" id="nfcTokenInput" placeholder="Token generado" readonly>
                </div>
                
                <input type="hidden" id="nfcVotanteId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: Editar Datos del Votante -->
<div class="modal fade modalEditarVotante" id="modalEditarVotante" tabindex="-1" aria-labelledby="modalEditarVotanteLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="modalEditarVotanteLabel">
                    <i class="bi bi-pencil-square"></i> Editar Datos del Votante
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formEditarVotante">
                    <input type="hidden" id="editarId">
                    
                    <!-- Nombre Completo -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="editarNombre" class="form-label">Nombre(s) </label>
                            <input type="text" class="form-control" id="editarNombre" required>
                        </div>
                        <div class="col-md-4">
                            <label for="editarApellidoPaterno" class="form-label">Apellido Paterno </label>
                            <input type="text" class="form-control" id="editarApellidoPaterno" required>
                        </div>
                        <div class="col-md-4">
                            <label for="editarApellidoMaterno" class="form-label">Apellido Materno</label>
                            <input type="text" class="form-control" id="editarApellidoMaterno">
                        </div>
                    </div>
                    
                    <!-- Fecha de Nacimiento y Género -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="editarFechaNacimiento" class="form-label">Fecha de Nacimiento </label>
                            <input type="date" class="form-control" id="editarFechaNacimiento" required>
                        </div>
                        <div class="col-md-6">
                            <label for="editarGenero" class="form-label">Género </label>
                            <select class="form-select" id="editarGenero" required>
                                <option value="H">Hombre</option>
                                <option value="M">Mujer</option>
                                <option value="O">Otro</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- CURP y RFC -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="editarCurp" class="form-label">CURP </label>
                            <input type="text" class="form-control" id="editarCurp" maxlength="18" required>
                        </div>
                        <div class="col-md-6">
                            <label for="editarRfc" class="form-label">RFC </label>
                            <input type="text" class="form-control" id="editarRfc" maxlength="13" required>
                        </div>
                    </div>
                    
                    <!-- Nacionalidad -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="editarNacionalidad" class="form-label">Nacionalidad</label>
                            <input type="text" class="form-control" id="editarNacionalidad" value="Mexicana">
                        </div>
                    </div>
                    
                    <!-- Dirección -->
                    <h6 class="mb-3 text-muted"><i class="bi bi-house"></i> Dirección</h6>
                    
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label for="editarCalle" class="form-label">Calle </label>
                            <input type="text" class="form-control" id="editarCalle" required>
                        </div>
                        <div class="col-md-2">
                            <label for="editarNumExterior" class="form-label">No. Exterior </label>
                            <input type="text" class="form-control" id="editarNumExterior" required>
                        </div>
                        <div class="col-md-2">
                            <label for="editarNumInterior" class="form-label">No. Interior</label>
                            <input type="text" class="form-control" id="editarNumInterior">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="editarColonia" class="form-label">Colonia </label>
                            <input type="text" class="form-control" id="editarColonia" required>
                        </div>
                        <div class="col-md-4">
                            <label for="editarMunicipio" class="form-label">Municipio </label>
                            <input type="text" class="form-control" id="editarMunicipio" required>
                        </div>
                        <div class="col-md-2">
                            <label for="editarEntidad" class="form-label">Entidad </label>
                            <input type="text" class="form-control" id="editarEntidad" required>
                        </div>
                        <div class="col-md-2">
                            <label for="editarCodigoPostal" class="form-label">C.P. </label>
                            <input type="text" class="form-control" id="editarCodigoPostal" maxlength="5" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="editarEntreCalles" class="form-label">Entre Calles</label>
                            <input type="text" class="form-control" id="editarEntreCalles">
                        </div>
                    </div>
                    
                    <!-- Datos de Contacto -->
                    <h6 class="mb-3 text-muted"><i class="bi bi-telephone"></i> Datos de Contacto</h6>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="editarCorreo" class="form-label">Correo Electrónico </label>
                            <input type="email" class="form-control" id="editarCorreo" required>
                        </div>
                        <div class="col-md-3">
                            <label for="editarTelefono" class="form-label">Teléfono Móvil </label>
                            <input type="text" class="form-control" id="editarTelefono" required>
                        </div>
                        <div class="col-md-3">
                            <label for="editarTelefonoFijo" class="form-label">Teléfono Fijo</label>
                            <input type="text" class="form-control" id="editarTelefonoFijo">
                        </div>
                    </div>
                    
                    <!-- Datos Electorales -->
                    <h6 class="mb-3 text-muted"><i class="bi bi-person-vcard"></i> Datos Electorales</h6>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="editarSeccionElectoral" class="form-label">Sección Electoral </label>
                            <input type="text" class="form-control" id="editarSeccionElectoral" maxlength="4" required>
                        </div>
                        <div class="col-md-6">
                            <label for="editarClaveElector" class="form-label">Clave de Elector</label>
                            <input type="text" class="form-control" id="editarClaveElector" maxlength="18">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning" id="btnGuardarDatos">
                    <i class="bi bi-save"></i> Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de loading genérico -->
<div class="modal fade" id="modalLoading" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-3 mb-0">Procesando...</p>
            </div>
        </div>
    </div>
</div>

<!-- Toast para notificaciones -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 99999;">
    <div id="toastNotificacion" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto" id="toastTitulo">Notificación</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="toastMensaje">
        </div>
    </div>
</div>

