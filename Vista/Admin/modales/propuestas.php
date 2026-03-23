<!-- Agregar una nueva propuesta -->
<div class="modal fade" id="agregarPropuesta" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Registrar Propuesta</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="../../Controlador/propuestasCtrl.php">
                    <input type="hidden" name="accion" value="crear">
                    <div class="row">
                        <!-- COLUMNA IZQUIERDA -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Candidato</label>
                                <select class="form-select" name="candidato_id" required>
                                    <?php foreach ($candidatos as $c): ?>
                                        <option value="<?= $c['id']; ?>">
                                            <?= htmlspecialchars($c['nombre']); ?> <?= htmlspecialchars($c['apellido']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Título</label>
                                <input type="text" class="form-control" name="titulo" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Slogan</label>
                                <input type="text" class="form-control" name="slogan">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">URL del Video</label>
                                <input type="text" class="form-control" name="video_url">
                            </div>
                        </div>
                        <!-- COLUMNA DERECHA -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Misión</label>
                                <textarea class="form-control" name="mision" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Propuesta Detallada</label>
                                <textarea class="form-control" name="propuesta_detallada" rows="4"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Editar propuesta -->
<div class="modal fade" id="editarPropuesta_<?php echo $row['id_propuesta']; ?>" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">Editar Propuesta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="../../Controlador/propuestasCtrl.php">
                    <input type="hidden" name="accion" value="actualizar">
                    <input type="hidden" name="id" value="<?php echo $row['id_propuesta']; ?>">
                    <div class="row">
                        <!-- IZQUIERDA -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Candidato</label>
                                <select class="form-select" name="candidato_id">
                                    <?php foreach ($candidatos as $c): ?>
                                        <option value="<?= $c['id']; ?>"
                                            <?= $c['id'] == $row['candidato_id'] ? 'selected' : ''; ?>>
                                            <?= htmlspecialchars($c['nombre']); ?> <?= htmlspecialchars($c['apellido']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Título</label>
                                <input type="text" class="form-control" name="titulo"
                                    value="<?php echo htmlspecialchars($row['titulo']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Slogan</label>
                                <input type="text" class="form-control" name="slogan"
                                    value="<?php echo htmlspecialchars($row['slogan']); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">URL del Video</label>
                                <input type="text" class="form-control" name="video_url"
                                    value="<?php echo htmlspecialchars($row['video_url']); ?>">
                            </div>
                        </div>
                        <!-- DERECHA -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Misión</label>
                                <textarea class="form-control" name="mision" rows="3"><?php echo htmlspecialchars($row['mision']); ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Propuesta Detallada</label>
                                <textarea class="form-control" name="propuesta_detallada" rows="4"><?php echo htmlspecialchars($row['propuesta_detallada']); ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Eliminar propuesta -->
<!-- <div class="modal fade" id="eliminarPropuesta_<?= $row['id_propuesta']; ?>" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Eliminar Propuesta</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="text-center m-3">
                <p>¿Estás seguro de eliminar la propuesta de?</p>
                <h5 class="fw-bold text-danger">
                    "<?= htmlspecialchars($row['nombre']); ?> <?= htmlspecialchars($row['apellido']); ?>"
                </h5>
                <p class="text-muted mb-0">Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <form method="POST" action="../../Controlador/propuestasCtrl.php">
                    <input type="hidden" name="accion" value="eliminar">
                    <input type="hidden" name="id" value="<?= $row['id_propuesta']; ?>">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-danger">
                        Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div> -->