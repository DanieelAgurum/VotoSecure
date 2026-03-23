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
                                <select name="id_tipo" id="selectTipo" class="form-select" required>
                                    <option value="">Seleccione tipo</option>
                                    <?php foreach ($tipos as $tipo): ?>
                                        <option value="<?= $tipo['id_tipo']; ?>">
                                            <?= $tipo['nombre_tipo']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Estado -->
                            <div class="mb-3 d-none" id="containerEstado">
                                <label class="form-label">Estado</label>
                                <select name="id_estado" id="selectEstado" class="form-select">
                                    <option value="">Seleccione estado</option>
                                    <?php foreach ($estados as $estado): ?>
                                        <option value="<?= $estado['id_estado']; ?>">
                                            <?= $estado['nombre']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Municipio -->
                            <div class="mb-3 d-none" id="containerMunicipio">
                                <label class="form-label">Municipio</label>
                                <select name="id_municipio" id="selectMunicipio" class="form-select">
                                    <option value="">Seleccione municipio</option>
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
            <form action="../../Controlador/eleccionesCtrl.php" method="POST">
                <input type="hidden" name="accion" value="actualizar">

                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">Editar Elección</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row">

                        <!-- IZQUIERDA -->
                        <div class="col-md-6">
                            <input type="hidden" name="id_eleccion" value="<?= $row['id_eleccion']; ?>">

                            <div class="mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" name="nombre_eleccion" class="form-control"
                                    value="<?= htmlspecialchars($row['nombre_eleccion']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Fecha Inicio</label>
                                <input type="datetime-local" name="fecha_inicio" class="form-control"
                                    value="<?= !empty($row['fecha_inicio']) && $row['fecha_inicio'] != '0000-00-00 00:00:00'
                                                ? date('Y-m-d\TH:i', strtotime($row['fecha_inicio']))
                                                : '' ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Fecha Fin</label>
                                <input type="datetime-local" name="fecha_fin" class="form-control"
                                    value="<?= !empty($row['fecha_fin']) && $row['fecha_fin'] != '0000-00-00 00:00:00'
                                                ? date('Y-m-d\TH:i', strtotime($row['fecha_fin']))
                                                : '' ?>" required>
                            </div>
                        </div>

                        <!-- DERECHA -->
                        <div class="col-md-6">

                            <!-- TIPO -->
                            <div class="mb-3">
                                <label class="form-label">Tipo</label>
                                <select name="id_tipo" class="form-select selectTipoEdit" required>
                                    <option value="">Seleccione tipo</option>
                                    <?php foreach ($tipos as $tipo): ?>
                                        <option value="<?= $tipo['id_tipo']; ?>"
                                            <?= ($row['id_tipo'] == $tipo['id_tipo']) ? 'selected' : '' ?>>
                                            <?= $tipo['nombre_tipo']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- ESTADO -->
                            <div class="mb-3 containerEstadoEdit <?= ($row['id_tipo'] == 2 || $row['id_tipo'] == 3) ? '' : 'd-none'; ?>">
                                <label class="form-label">Estado</label>
                                <select name="id_estado" class="form-select selectEstadoEdit">
                                    <option value="">Seleccione estado</option>
                                    <?php foreach ($estados as $estado): ?>
                                        <option value="<?= $estado['id_estado']; ?>"
                                            <?= ($row['id_estado'] == $estado['id_estado']) ? 'selected' : '' ?>>
                                            <?= $estado['nombre']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- MUNICIPIO -->
                            <div class="mb-3 containerMunicipioEdit <?= ($row['id_tipo'] == 3) ? '' : 'd-none'; ?>">
                                <label class="form-label">Municipio</label>
                                <select name="id_municipio" class="form-select selectMunicipioEdit">
                                    <option value="">Seleccione municipio</option>

                                    <!-- Precarga si ya existe -->
                                    <?php if (!empty($row['id_municipio'])): ?>
                                        <option value="<?= $row['id_municipio']; ?>" selected>
                                            <?= $row['municipio_nombre']; ?>
                                        </option>
                                    <?php endif; ?>

                                </select>
                            </div>

                            <!-- DESCRIPCIÓN -->
                            <div class="mb-3">
                                <label class="form-label">Descripción</label>
                                <textarea name="descripcion_eleccion" class="form-control" rows="5"><?= htmlspecialchars($row['descripcion_eleccion']); ?></textarea>
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

            <form class="form-eliminar" action="../../Controlador/eleccionesCtrl.php" method="POST" enctype="multipart/form-data">
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

            <form class="form-cancelar" action="../../Controlador/eleccionesCtrl.php" method="POST" enctype="multipart/form-data">
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

<script>
    document.addEventListener("DOMContentLoaded", function() {

        const tipoCreate = document.getElementById("selectTipo");
        const estadoCreate = document.getElementById("containerEstado");
        const municipioCreate = document.getElementById("containerMunicipio");
        const estadoSelectCreate = document.getElementById("selectEstado");
        const municipioSelectCreate = document.getElementById("selectMunicipio");

        if (tipoCreate) {
            tipoCreate.addEventListener("change", function() {
                const tipo = this.value;

                estadoCreate.classList.add("d-none");
                municipioCreate.classList.add("d-none");

                if (tipo == 2) { // Estatal
                    estadoCreate.classList.remove("d-none");
                }

                if (tipo == 3) { // Municipal
                    estadoCreate.classList.remove("d-none");
                    municipioCreate.classList.remove("d-none");
                }
            });
        }

        if (estadoSelectCreate) {
            estadoSelectCreate.addEventListener("change", function() {
                const idEstado = this.value;

                fetch(`../../Controlador/eleccionesCtrl.php?accion=obtener_municipios&id_estado=${idEstado}`)
                    .then(res => res.json())
                    .then(data => {
                        municipioSelectCreate.innerHTML = '<option value="">Seleccione municipio</option>';

                        data.forEach(m => {
                            municipioSelectCreate.innerHTML += `<option value="${m.id_municipio}">${m.nombre}</option>`;
                        });
                    });
            });
        }

        document.querySelectorAll(".modal").forEach(modal => {

            const tipoEdit = modal.querySelector(".selectTipoEdit");
            const estadoEdit = modal.querySelector(".containerEstadoEdit");
            const municipioEdit = modal.querySelector(".containerMunicipioEdit");
            const estadoSelectEdit = modal.querySelector(".selectEstadoEdit");
            const municipioSelectEdit = modal.querySelector(".selectMunicipioEdit");

            if (!tipoEdit) return;

            tipoEdit.addEventListener("change", function() {
                const tipo = this.value;

                estadoEdit.classList.add("d-none");
                municipioEdit.classList.add("d-none");

                if (tipo == 2) {
                    estadoEdit.classList.remove("d-none");
                }

                if (tipo == 3) {
                    estadoEdit.classList.remove("d-none");
                    municipioEdit.classList.remove("d-none");
                }
            });

            if (estadoSelectEdit) {
                estadoSelectEdit.addEventListener("change", function() {
                    const idEstado = this.value;

                    fetch(`../../Controlador/eleccionesCtrl.php?accion=obtener_municipios&id_estado=${idEstado}`)
                        .then(res => res.json())
                        .then(data => {
                            municipioSelectEdit.innerHTML = '<option value="">Seleccione municipio</option>';

                            data.forEach(m => {
                                municipioSelectEdit.innerHTML += `<option value="${m.id_municipio}">${m.nombre}</option>`;
                            });
                        });
                });
            }

        });

    });
</script>