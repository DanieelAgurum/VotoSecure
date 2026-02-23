<?php
//session_start();
// Verificar autenticación de admin
//if (!isset($_SESSION['admin_id'])) {
//    header('Location: ../../Login.php');
//    exit();
//}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Candidatos - VotoSeguro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../css/Style.css">
     <link rel="stylesheet" href="../../css/admin.css">
    <link rel="stylesheet" href="../../css/dash.css">
</head>
<body>
    <?php include __DIR__ . '/../../components/Admin/Navbar.php'; ?>

   <div class="content-area">

    <div class="form-wrapper">

        <h2 class="form-title">Registrar Candidato</h2>

        <form action="../../Controller/Admin/RegistrarCandidato.php" method="POST" enctype="multipart/form-data">

            <div class="mb-4">
                <label class="form-label">Nombre completo</label>
                <input type="text" name="nombre" class="form-control custom-input" required>
            </div>

            <div class="mb-4">
                <label class="form-label">Partido político</label>
                <input type="text" name="partido" class="form-control custom-input" required>
            </div>

            <div class="mb-4">
                <label class="form-label">Propuesta</label>
                <textarea name="propuesta" rows="4" class="form-control custom-input" required></textarea>
            </div>

            <div class="mb-4">
                <label class="form-label">Foto del candidato</label>
                <input type="file" name="foto" class="form-control custom-input" accept="image/*" required>
            </div>

            <button type="submit" class="btn-submit">
                Registrar Candidato
            </button>

        </form>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../../js/dash.js"></script>
<script>
