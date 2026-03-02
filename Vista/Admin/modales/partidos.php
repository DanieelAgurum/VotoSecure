<!-- Modal para agregar partido -->
<div class="modal fade" id="agregarContenido" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Registrar Partido</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="formCreatePartido" method="POST" action="../../Controlador/partidosCtrl.php" enctype="multipart/form-data">
                    <input type="hidden" name="accion" value="crear">
                    <div class="row">
                        <div class="col-md-5 text-center">
                            <label class="form-label fw-bold">Vista previa</label>

                            <div class="rounded p-3 mb-3">
                                <img id="previewCreate"
                                    src="https://via.placeholder.com/200x200?text=Logo"
                                    class="img-fluid rounded"
                                    style="max-height:200px;">
                            </div>

                            <input type="file"
                                class="form-control"
                                name="logo"
                                accept="image/*"
                                onchange="previewImage(event, 'previewCreate')">
                        </div>

                        <div class="col-md-7">

                            <div class="mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" class="form-control" name="nombre" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Siglas</label>
                                <input type="text" class="form-control" name="siglas" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Estatus</label>
                                <select class="form-select" name="estatus">
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="text-end mt-3">
                        <button type="submit" class="btn btn-success">
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar partido -->
<div class="modal fade" id="editarPartido_<?php echo $row['id_partido']; ?>" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">Editar Partido</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="formEditPartido_<?php echo $row['id_partido']; ?>" method="POST" action="../../Controlador/partidosCtrl.php" enctype="multipart/form-data">
                    <input type="hidden" name="accion" value="actualizar">
                    <input type="hidden" name="id_partido" value="<?php echo $row['id_partido']; ?>">
                    <input type="hidden" name="logo_actual" value="<?php echo $row['logo_partido']; ?>">

                    <div class="row">
                        <div class="col-md-5 text-center">
                            <label class="form-label fw-bold">Vista previa</label>
                            <div class="rounded p-3 mb-3">
                                <img id="previewEdit_<?php echo $row['id_partido']; ?>"
                                    src="<?php echo !empty($row['logo_partido']) ? '../../' . $row['logo_partido'] : 'https://via.placeholder.com/200x200?text=Logo'; ?>"
                                    class="img-fluid rounded"
                                    style="max-height:200px;">
                            </div>
                            <input type="file"
                                class="form-control"
                                name="logo"
                                accept="image/*"
                                onchange="previewImage(event, 'previewEdit_<?php echo $row['id_partido']; ?>')">
                        </div>
                        <div class="col-md-7">
                            <div class="mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" class="form-control" name="nombre" value="<?php echo htmlspecialchars($row['nombre_partido']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Siglas</label>
                                <input type="text" class="form-control" name="siglas" value="<?php echo htmlspecialchars($row['siglas']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Estatus</label>
                                <select class="form-select" name="estatus">
                                    <option value="1" <?php echo $row['estatus'] == 1 ? 'selected' : ''; ?>>Activo</option>
                                    <option value="0" <?php echo $row['estatus'] == 0 ? 'selected' : ''; ?>>Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="text-end mt-3">
                        <button type="submit" class="btn btn-success">
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



<!-- Modal para activar/desactivar contenido-->
<div class=" modal fade" id="estadoPartido_<?php echo $row['id_partido']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    <?php echo $row['estatus'] == 1 ? 'Desactivar' : 'Activar'; ?> Partido
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body text-center">
                <p>
                    ¿Seguro que desea
                    <strong><?php echo $row['estatus'] == 1 ? 'desactivar' : 'activar'; ?></strong>
                    este partido?
                </p>
                <p class="fw-bold text-primary"><?= htmlspecialchars($row['nombre_partido']); ?></p>
            </div>

            <div class="modal-footer justify-content-end">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancelar
                </button>
                <form action="../../Controlador/partidosCtrl.php" method="post">
                    <input type="hidden" name="accion" value="cambiarEstado">
                    <input type="hidden" name="id_partido" value="<?php echo $row['id_partido']; ?>">
                    <input type="hidden" name="nuevo_estado" value="<?php echo $row['estatus'] == 1 ? 0 : 1; ?>">
                    <button type="submit" class="btn <?php echo $row['estatus'] == 1 ? 'btn-danger' : 'btn-success'; ?>">
                        Confirmar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal eliminar partido -->
<div class="modal fade" id="eliminarPartido_<?= $row['id_partido']; ?>" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Eliminar Partido</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="text-center m-3">
                <p>¿Estás seguro de eliminar este partido?</p>
                <h5 class="fw-bold text-danger">"<?= htmlspecialchars($row['nombre_partido']); ?>"</h5>
                <p class="text-muted mb-0">Esta acción no se puede deshacer.</p>
            </div>

            <div class="modal-footer justify-content-end">
                <form method="POST" action="../../Controlador/partidosCtrl.php">
                    <input type="hidden" name="accion" value="eliminar">
                    <input type="hidden" name="id_partido" id="eliminar" value="<?= $row['id_partido']; ?>">

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
</div>