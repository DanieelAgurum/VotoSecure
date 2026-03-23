<!-- ══════════════════════════════════════════════
     MODAL AGREGAR CASILLA
     ══════════════════════════════════════════════ -->
<div class="modal fade" id="modalAgregar" tabindex="-1" aria-labelledby="modalAgregarLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <form id="formAgregar">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalAgregarLabel">
                        <i class="bi bi-building-add me-2"></i>Agregar Casilla
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">

                        <!-- Sección (Select2 igual que RegistroVotantes) -->
                        <div class="col-12">
                            <label class="form-label">Sección Electoral <span class="text-danger">*</span></label>
                            <select class="form-select select2-seccion-agregar"
                                    id="agregar_seccion" name="numero_seccion" required>
                                <option value=""></option>
                                <?php foreach ($grupos as $grupo => $nums):
                                    [$estado, $municipio] = explode(' — ', $grupo, 2);
                                ?>
                                    <optgroup label="<?= htmlspecialchars($grupo) ?>">
                                        <?php foreach ($nums as $num): ?>
                                            <option value="<?= $num ?>"
                                                data-search="<?= htmlspecialchars($num . ' ' . $municipio . ' ' . $estado) ?>">
                                                Sección <?= $num ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Busca por número de sección, municipio o estado.</small>
                        </div>

                        <!-- Tipo -->
                        <div class="col-md-6">
                            <label class="form-label">Tipo de Casilla <span class="text-danger">*</span></label>
                            <select name="tipo" id="agregar_tipo" class="form-select" required>
                                <option value="">Seleccione...</option>
                                <option value="Normal">Normal</option>
                                <option value="Especial">Especial</option>
                            </select>
                        </div>

                        <!-- Activa -->
                        <div class="col-md-6">
                            <label class="form-label">¿Activa?</label>
                            <select name="activa" id="agregar_activa" class="form-select">
                                <option value="1" selected>Sí</option>
                                <option value="0">No</option>
                            </select>
                        </div>

                        <!-- Dirección -->
                        <div class="col-12">
                            <label class="form-label">Dirección <span class="text-danger">*</span></label>
                            <input type="text" name="direccion" id="agregar_direccion"
                                   class="form-control"
                                   placeholder="Calle, número, colonia" required>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-success" id="btnGuardar">
                        <i class="bi bi-check-circle me-1"></i>Guardar
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>


<!-- ══════════════════════════════════════════════
     MODAL MODIFICAR CASILLA
     ══════════════════════════════════════════════ -->
<div class="modal fade" id="modalModificar" tabindex="-1" aria-labelledby="modalModificarLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <form id="formModificar">

                <input type="hidden" name="id_casilla" id="mod_id_casilla">

                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="modalModificarLabel">
                        <i class="bi bi-pencil-square me-2"></i>Modificar Casilla
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">

                        <!-- Sección (Select2) -->
                        <div class="col-12">
                            <label class="form-label">Sección Electoral <span class="text-danger">*</span></label>
                            <select class="form-select select2-seccion-modificar"
                                    id="mod_seccion" name="numero_seccion" required>
                                <option value=""></option>
                                <?php foreach ($grupos as $grupo => $nums):
                                    [$estado, $municipio] = explode(' — ', $grupo, 2);
                                ?>
                                    <optgroup label="<?= htmlspecialchars($grupo) ?>">
                                        <?php foreach ($nums as $num): ?>
                                            <option value="<?= $num ?>"
                                                data-search="<?= htmlspecialchars($num . ' ' . $municipio . ' ' . $estado) ?>">
                                                Sección <?= $num ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Busca por número de sección, municipio o estado.</small>
                        </div>

                        <!-- Tipo -->
                        <div class="col-md-6">
                            <label class="form-label">Tipo de Casilla <span class="text-danger">*</span></label>
                            <select name="tipo" id="mod_tipo" class="form-select" required>
                                <option value="">Seleccione...</option>
                                <option value="Normal">Normal</option>
                                <option value="Especial">Especial</option>
                            </select>
                        </div>

                        <!-- Activa -->
                        <div class="col-md-6">
                            <label class="form-label">¿Activa?</label>
                            <select name="activa" id="mod_activa" class="form-select">
                                <option value="1">Sí</option>
                                <option value="0">No</option>
                            </select>
                        </div>

                        <!-- Dirección -->
                        <div class="col-12">
                            <label class="form-label">Dirección <span class="text-danger">*</span></label>
                            <input type="text" name="direccion" id="mod_direccion"
                                   class="form-control" required>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-warning" id="btnModificar">
                        <i class="bi bi-arrow-clockwise me-1"></i>Actualizar
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>