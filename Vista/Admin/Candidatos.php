<?php
session_start();

require_once("../../Modelo/candidatosMdl.php");
require_once(__DIR__ . "/../../Controlador/candidatosCtrl.php");

if (!isset($_SESSION['id']) || $_SESSION['rol'] != 2) {
    header('Location: /VotoSecure/Vista/login.php');
    exit();
}

$candidato = new Candidato();
$lista = $candidato->obtenerCandidatos();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrativo Candidatos - VotoSecure</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../../css/admin.css">
    <link rel="stylesheet" href="../../css/dash.css">
    <link rel="stylesheet" href="../../css/candidatosAd.css">
</head>
<body>

<?php include __DIR__ . '/../../components/Admin/Navbar.php'; ?>

<div class="main-content">

    <div class="card shadow">
        <div class="card-header text-center fw-bold">
            Gestión de Candidatos
        </div>

        <div class="card-body">

            <button class="btn btn-primary mb-3"
                data-bs-toggle="modal"
                data-bs-target="#modalAgregar">
                Agregar Candidato
            </button>

            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Partido</th>
                            <th>Tipo</th>
                            <th>Cargo</th>
                            <th>Distrito</th>
                            <th>Estatus</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($lista as $row): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= $row['nombre'] . " " . $row['apellido'] ?></td>
                            <td><?= $row['partido_nombre'] ?></td>
                            <td><?= $row['tipo_nombre'] ?></td>
                            <td><?= $row['cargo'] ?></td>
                            <td><?= $row['distrito'] ?></td>
                            <td><?= $row['estatus'] ?></td>
                            <td>
                                <a href="#"
                                   class="btn btn-danger btn-sm btn-eliminar"
                                   data-id="<?= $row['id'] ?>">
                                   Eliminar
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>


<!-- MODAL -->
<div class="modal fade" id="modalAgregar" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">

      <form action="../../Controlador/candidatosCtrl.php" method="POST">

        <div class="modal-header">
          <h5 class="modal-title">Agregar Candidato</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

          <input class="form-control mb-2" type="text" name="nombre" placeholder="Nombre" required>
          <input class="form-control mb-2" type="text" name="apellido" placeholder="Apellido" required>
          <input class="form-control mb-2" type="number" name="id_partido" placeholder="ID Partido" required>
          <input class="form-control mb-2" type="number" name="id_tipo" placeholder="ID Tipo" required>
          <input class="form-control mb-2" type="text" name="cargo" placeholder="Cargo" required>
          <input class="form-control mb-2" type="text" name="distrito" placeholder="Distrito" required>
          <input class="form-control mb-2" type="email" name="correo" placeholder="Correo" required>
          <input class="form-control mb-2" type="text" name="telefono" placeholder="Teléfono">

          <select class="form-control" name="estatus">
              <option value="activo">Activo</option>
              <option value="inactivo">Inactivo</option>
          </select>

        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Guardar</button>
        </div>

      </form>

    </div>
  </div>
</div>
<!-- SCRIPTS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../../js/dash.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- SWEET ALERT SUCCESS -->
<?php if(isset($_GET['success'])): ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'Candidato agregado correctamente',
    showConfirmButton: false,
    timer: 1500
});
</script>
<?php endif; ?>

<!-- SWEET ALERT DELETE -->
<?php if(isset($_GET['deleted'])): ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'Candidato eliminado correctamente',
    showConfirmButton: false,
    timer: 1500
});
</script>
<?php endif; ?>

<!-- CONFIRMACIÓN ELIMINAR -->
<script>
document.querySelectorAll('.btn-eliminar').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        let id = this.getAttribute('data-id');

        Swal.fire({
            title: '¿Eliminar candidato?',
            text: "Esta acción no se puede deshacer",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "../../Controlador/candidatosCtrl.php?eliminar=" + id;
            }
        });
    });
});
</script>

</body>
</html>