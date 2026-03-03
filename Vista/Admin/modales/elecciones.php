<!-- Modal Agregar Elección -->
<div class="modal fade" id="modalAgregarEleccion" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">

            <form action="../../Controlador/eleccionesCtrl.php" method="POST" enctype="multipart/form-data" id="formCreateEleccion">
                <input type="hidden" name="accion" value="crear">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">Agregar Elección</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Izquierda -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" name="nombre_eleccion" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Fecha Inicio</label>
                                <input type="datetime-local" name="fecha_inicio" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Fecha Fin</label>
                                <input type="datetime-local" name="fecha_fin" class="form-control" required>
                            </div>
                        </div>

                        <!-- Derecha -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tipo</label>
                                <select name="id_tipo" class="form-select" required>
                                    <option value="">Seleccione tipo</option>
                                    <?php foreach ($tipos as $tipo): ?>
                                        <option value="<?= $tipo['id_tipo']; ?>">
                                            <?= $tipo['nombre_tipo']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Descripción</label>
                                <textarea name="descripcion_eleccion" class="form-control" rows="5"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para editar elección -->
<div class="modal fade" id="editarEleccion_<?php echo $row['id_eleccion']; ?>" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <form action="../../Controlador/eleccionesCtrl.php" method="POST" enctype="multipart/form-data" id="formEditEleccion_<?php echo $row['id_eleccion']; ?>">
                <input type="hidden" name="accion" value="actualizar">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">Editar Elección</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <!-- Izquierda -->
                        <div class="col-md-6">
                            <input type="hidden" name="id_eleccion" value="<?php echo $row['id_eleccion']; ?>">
                            <div class="mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" name="nombre_eleccion" class="form-control" value="<?php echo htmlspecialchars($row['nombre_eleccion']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Fecha Inicio</label>
                                <input type="datetime-local" name="fecha_inicio" class="form-control" value="<?php echo date('Y-m-d\TH:i', strtotime($row['fecha_inicio'])); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Fecha Fin</label>
                                <input type="datetime-local" name="fecha_fin" class="form-control" value="<?php echo date('Y-m-d\TH:i', strtotime($row['fecha_fin'])); ?>" required>
                            </div>
                        </div>

                        <!-- Derecha -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tipo</label>
                                <select name="id_tipo" class="form-select" required>
                                    <option value="">Seleccione tipo</option>
                                    <?php foreach ($tipos as $tipo): ?>
                                        <option value="<?= $tipo['id_tipo']; ?>"
                                            <?php if ($row['id_tipo'] == $tipo['id_tipo']) echo 'selected'; ?>>
                                            <?= $tipo['nombre_tipo']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Descripción</label>
                                <textarea name="descripcion_eleccion" class="form-control" rows="5"><?php echo htmlspecialchars($row['descripcion_eleccion']); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para eliminar elección -->
<div class="modal fade" id="eliminarEleccion_<?php echo $row['id_eleccion']; ?>" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form action="../../Controlador/eleccionesCtrl.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="accion" value="eliminar">
                <input type="hidden" name="id_eleccion" id="eliminar" value="<?= $row['id_eleccion']; ?>">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Eliminar Elección</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body  text-center">
                    <p>¿Estás seguro de que deseas eliminar la elección?</p>
                    <h5 class="fw-bold text-danger">"<?= htmlspecialchars($row['nombre_eleccion']); ?>"</h5>
                    <p class="text-muted mb-0">Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para cancelar elección -->
<div class="modal fade" id="cancelarEleccion_<?php echo $row['id_eleccion']; ?>" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form action="../../Controlador/eleccionesCtrl.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="accion" value="cancelar">
                <input type="hidden" name="cancelar_id" id="cancelar_id" value="<?= $row['id_eleccion']; ?>">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">Cancelar Elección</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body  text-center">
                    <p>¿Estás seguro de que deseas cancelar la elección?</p>
                    <h5 class="fw-bold text-info">"<?= htmlspecialchars($row['nombre_eleccion']); ?>"</h5>
                    <p class="text-muted mb-0">Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-info">Cancelar Elección</button>
                </div>
            </form>
        </div>
    </div>
</div>