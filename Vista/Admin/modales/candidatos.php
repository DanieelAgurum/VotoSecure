<!-- MODAL AGREGAR CANDIDATO -->
<div class="modal fade" id="modalAgregar" tabindex="-1" aria-labelledby="modalAgregarLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form id="formAgregarCandidato" enctype="multipart/form-data">

                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                <input type="hidden" name="accion" value="guardar">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalAgregarLabel">
                        <i class="bi bi-person-plus"></i> Agregar Candidato
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="nombre" id="nombre" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Apellido <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="apellido" id="apellido" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Partido <span class="text-danger">*</span></label>
                                <select class="form-select" name="id_partido" id="id_partido" required>
                                    <option value="">Seleccione un partido...</option>
                                    <?php foreach ($partidos as $partido): ?>
                                        <option value="<?= $partido['id_partido'] ?>">
                                            <?= htmlspecialchars($partido['nombre_partido']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Elección <span class="text-danger">*</span></label>
                                <select class="form-select" name="id_eleccion" id="id_eleccion" required>
                                    <option value="">Seleccione una elección...</option>
                                    <?php foreach ($elecciones as $eleccion): ?>
                                        <option value="<?= $eleccion['id_eleccion'] ?>">
                                            <?= htmlspecialchars($eleccion['nombre_eleccion']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Cargo <span class="text-danger">*</span></label>
                                <select class="form-select" name="cargo" id="cargo" required>
                                    <option value="">Seleccione un cargo...</option>
                                    <option value="Presidente">Presidente</option>
                                    <option value="Senadores">Senadores</option>
                                    <option value="Diputados">Diputados</option>
                                    <option value="Gobernador">Gobernador</option>
                                    <option value="Alcalde">Alcalde</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Distrito <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="distrito" id="distrito" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Correo <span class="text-danger">*</span></label>
                                <input class="form-control" type="email" name="correo" id="correo" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Teléfono</label>
                                <input class="form-control" type="tel" name="telefono" id="telefono">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Foto del Candidato</label>
                        <div class="d-flex align-items-center gap-3">
                            <div class="position-relative">
                                <img id="previewFoto"
                                    src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='120' fill='%23dee2e6' viewBox='0 0 16 16'%3E%3Cpath d='M8 4a4 4 0 1 1 0 8 4 4 0 0 1 0-8zM2 12.5a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5z'/%3E%3C/svg%3E"
                                    alt="Preview" class="rounded-circle"
                                    style="width:120px;height:120px;object-fit:cover;border:3px solid #dee2e6;cursor:pointer;">
                                <input type="file" name="foto" id="foto"
                                    accept="image/jpeg,image/png,image/gif,image/webp"
                                    class="position-absolute"
                                    style="opacity:0;width:120px;height:120px;top:0;left:0;cursor:pointer;">
                            </div>
                            <div>
                                <small class="text-muted">Formatos: JPEG, PNG, GIF, WebP</small><br>
                                <small class="text-muted">Tamaño máximo: 5MB</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Estatus</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="estatus" id="estatus_activo" value="activo" checked>
                                <label class="form-check-label" for="estatus_activo">Activo</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="estatus" id="estatus_inactivo" value="inactivo">
                                <label class="form-check-label" for="estatus_inactivo">Inactivo</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success" id="btnGuardar">Guardar</button>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- MODAL MODIFICAR CANDIDATO -->
<div class="modal fade" id="modalModificar" tabindex="-1" aria-labelledby="modalModificarLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form id="formModificarCandidato" enctype="multipart/form-data">

                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                <input type="hidden" name="accion" value="modificar">
                <input type="hidden" name="id" id="modificar_id">

                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="modalModificarLabel">
                        <i class="bi bi-pencil"></i> Modificar Candidato
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="nombre" id="modificar_nombre" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Apellido <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="apellido" id="modificar_apellido" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Partido <span class="text-danger">*</span></label>
                                <select class="form-select" name="id_partido" id="modificar_id_partido" required>
                                    <option value="">Seleccione un partido...</option>
                                    <?php foreach ($partidos as $partido): ?>
                                        <option value="<?= $partido['id_partido'] ?>">
                                            <?= htmlspecialchars($partido['nombre_partido']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Elección <span class="text-danger">*</span></label>
                                <select class="form-select" name="id_eleccion" id="modificar_id_eleccion" required>
                                    <option value="">Seleccione una elección...</option>
                                    <?php foreach ($elecciones as $eleccion): ?>
                                        <option value="<?= $eleccion['id_eleccion'] ?>">
                                            <?= htmlspecialchars($eleccion['nombre_eleccion']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Cargo <span class="text-danger">*</span></label>
                                <select class="form-select" name="cargo" id="modificar_cargo" required>
                                    <option value="">Seleccione un cargo...</option>
                                    <option value="Presidente">Presidente</option>
                                    <option value="Senadores">Senadores</option>
                                    <option value="Diputados">Diputados</option>
                                    <option value="Gobernador">Gobernador</option>
                                    <option value="Alcalde">Alcalde</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Distrito <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="distrito" id="modificar_distrito" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Correo <span class="text-danger">*</span></label>
                                <input class="form-control" type="email" name="correo" id="modificar_correo" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Teléfono</label>
                                <input class="form-control" type="tel" name="telefono" id="modificar_telefono">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Foto del Candidato</label>
                        <div class="d-flex align-items-center gap-3">
                            <div class="position-relative">
                                <img id="modificar_previewFoto"
                                    src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='120' fill='%23dee2e6' viewBox='0 0 16 16'%3E%3Cpath d='M8 4a4 4 0 1 1 0 8 4 4 0 0 1 0-8zM2 12.5a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5z'/%3E%3C/svg%3E"
                                    alt="Preview" class="rounded-circle"
                                    style="width:120px;height:120px;object-fit:cover;border:3px solid #dee2e6;cursor:pointer;">
                                <input type="file" name="foto" id="modificar_foto"
                                    accept="image/jpeg,image/png,image/gif,image/webp"
                                    class="position-absolute"
                                    style="opacity:0;width:120px;height:120px;top:0;left:0;cursor:pointer;">
                            </div>
                            <div>
                                <small class="text-muted">Formatos: JPEG, PNG, GIF, WebP</small><br>
                                <small class="text-muted">Tamaño máximo: 5MB</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Estatus</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="estatus" id="modificar_estatus_activo" value="activo">
                                <label class="form-check-label" for="modificar_estatus_activo">Activo</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="estatus" id="modificar_estatus_inactivo" value="inactivo">
                                <label class="form-check-label" for="modificar_estatus_inactivo">Inactivo</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning" id="btnModificar">Actualizar</button>
                </div>

            </form>
        </div>
    </div>
</div>