<!-- MODAL AGREGAR CANDIDATO -->
<div class="modal fade" id="modalAgregar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <form id="formAgregarCandidato" enctype="multipart/form-data">

                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                <input type="hidden" name="accion" value="guardar">

                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">Agregar Candidato</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" name="nombre" id="nombre" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Apellido</label>
                                <input type="text" name="apellido" id="apellido" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Partido</label>
                                <select name="id_partido" id="id_partido" class="form-select" required>
                                    <option value="">Seleccione un partido</option>
                                    <?php foreach ($partidos as $partido): ?>
                                        <option value="<?= $partido['id_partido'] ?>">
                                            <?= htmlspecialchars($partido['nombre_partido']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Elección</label>
                                <select name="id_eleccion" id="id_eleccion" class="form-select" required>
                                    <option value="">Seleccione una elección</option>
                                    <?php foreach ($elecciones as $eleccion): ?>
                                        <option value="<?= $eleccion['id_eleccion'] ?>">
                                            <?= htmlspecialchars($eleccion['nombre_eleccion']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Cargo</label>
                                <select name="cargo" id="cargo" class="form-select" required>
                                    <option value="">Seleccione un cargo</option>
                                    <optgroup label="Presidencial">
                                        <option value="Presidente">Presidente</option>
                                        <option value="Vicepresidente">Vicepresidente</option>
                                    </optgroup>
                                    <optgroup label="Municipal">
                                        <option value="Alcalde">Alcalde</option>
                                        <option value="Regidor">Regidor</option>
                                        <option value="Síndico">Síndico</option>
                                    </optgroup>
                                    <optgroup label="Escolar">
                                        <option value="Presidente de Sociedad de Alumnos">Presidente de Sociedad de Alumnos</option>
                                        <option value="Secretario de Sociedad de Alumnos">Secretario de Sociedad de Alumnos</option>
                                        <option value="Tesorero de Sociedad de Alumnos">Tesorero de Sociedad de Alumnos</option>
                                        <option value="Vocal Escolar">Vocal Escolar</option>
                                    </optgroup>
                                    <optgroup label="Consejo Académico">
                                        <option value="Rector">Rector</option>
                                        <option value="Decano">Decano</option>
                                        <option value="Director Académico">Director Académico</option>
                                        <option value="Representante Estudiantil">Representante Estudiantil</option>
                                        <option value="Representante Docente">Representante Docente</option>
                                    </optgroup>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Distrito</label>
                                <input type="text" name="distrito" id="distrito" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Correo</label>
                                <input type="email" name="correo" id="correo" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Teléfono</label>
                                <input type="tel" name="telefono" id="telefono" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Foto del Candidato</label>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="position-relative">
                                        <img id="previewFoto"
                                            src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' fill='%23dee2e6' viewBox='0 0 16 16'%3E%3Cpath d='M8 4a4 4 0 1 1 0 8 4 4 0 0 1 0-8zM2 12.5a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5z'/%3E%3C/svg%3E"
                                            alt="Preview" class="rounded-circle"
                                            style="width:80px;height:80px;object-fit:cover;border:2px solid #dee2e6;cursor:pointer;">
                                        <input type="file" name="foto" id="foto"
                                            accept="image/jpeg,image/png,image/gif,image/webp"
                                            class="position-absolute"
                                            style="opacity:0;width:80px;height:80px;top:0;left:0;cursor:pointer;">
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Formatos: JPEG, PNG, GIF, WebP</small>
                                        <small class="text-muted d-block">Máximo: 5MB</small>
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
<div class="modal fade" id="modalModificar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <form id="formModificarCandidato" enctype="multipart/form-data">

                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                <input type="hidden" name="accion" value="modificar">
                <input type="hidden" name="id" id="modificar_id">

                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">Modificar Candidato</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" name="nombre" id="modificar_nombre" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Apellido</label>
                                <input type="text" name="apellido" id="modificar_apellido" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Partido</label>
                                <select name="id_partido" id="modificar_id_partido" class="form-select" required>
                                    <option value="">Seleccione un partido</option>
                                    <?php foreach ($partidos as $partido): ?>
                                        <option value="<?= $partido['id_partido'] ?>">
                                            <?= htmlspecialchars($partido['nombre_partido']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Elección</label>
                                <select name="id_eleccion" id="modificar_id_eleccion" class="form-select" required>
                                    <option value="">Seleccione una elección</option>
                                    <?php foreach ($elecciones as $eleccion): ?>
                                        <option value="<?= $eleccion['id_eleccion'] ?>">
                                            <?= htmlspecialchars($eleccion['nombre_eleccion']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Cargo</label>
                                <select name="cargo" id="modificar_cargo" class="form-select" required>
                                    <option value="">Seleccione un cargo</option>
                                    <optgroup label="Presidencial">
                                        <option value="Presidente">Presidente</option>
                                        <option value="Vicepresidente">Vicepresidente</option>
                                    </optgroup>
                                    <optgroup label="Municipal">
                                        <option value="Alcalde">Alcalde</option>
                                        <option value="Regidor">Regidor</option>
                                        <option value="Síndico">Síndico</option>
                                    </optgroup>
                                    <optgroup label="Escolar">
                                        <option value="Presidente de Sociedad de Alumnos">Presidente de Sociedad de Alumnos</option>
                                        <option value="Secretario de Sociedad de Alumnos">Secretario de Sociedad de Alumnos</option>
                                        <option value="Tesorero de Sociedad de Alumnos">Tesorero de Sociedad de Alumnos</option>
                                        <option value="Vocal Escolar">Vocal Escolar</option>
                                    </optgroup>
                                    <optgroup label="Consejo Académico">
                                        <option value="Rector">Rector</option>
                                        <option value="Decano">Decano</option>
                                        <option value="Director Académico">Director Académico</option>
                                        <option value="Representante Estudiantil">Representante Estudiantil</option>
                                        <option value="Representante Docente">Representante Docente</option>
                                    </optgroup>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Distrito</label>
                                <input type="text" name="distrito" id="modificar_distrito" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Correo</label>
                                <input type="email" name="correo" id="modificar_correo" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Teléfono</label>
                                <input type="tel" name="telefono" id="modificar_telefono" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Foto del Candidato</label>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="position-relative">
                                        <img id="modificar_previewFoto"
                                            src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' fill='%23dee2e6' viewBox='0 0 16 16'%3E%3Cpath d='M8 4a4 4 0 1 1 0 8 4 4 0 0 1 0-8zM2 12.5a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5z'/%3E%3C/svg%3E"
                                            alt="Preview" class="rounded-circle"
                                            style="width:80px;height:80px;object-fit:cover;border:2px solid #dee2e6;cursor:pointer;">
                                        <input type="file" name="foto" id="modificar_foto"
                                            accept="image/jpeg,image/png,image/gif,image/webp"
                                            class="position-absolute"
                                            style="opacity:0;width:80px;height:80px;top:0;left:0;cursor:pointer;">
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Formatos: JPEG, PNG, GIF, WebP</small>
                                        <small class="text-muted d-block">Máximo: 5MB</small>
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