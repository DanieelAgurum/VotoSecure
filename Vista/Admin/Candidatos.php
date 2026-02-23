<?php
//session_start();
//if (!isset($_SESSION['admin_id'])) {
//    header('Location: ../../Login.php');
//    exit();
//}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Candidato - VotoSeguro</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

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
        <h2 class="form-title mb-4">Registrar Candidato</h2>

        <form action="../../Controller/Admin/RegistrarCandidato.php" 
              method="POST" 
              enctype="multipart/form-data">

            <!-- ========================= -->
            <!-- 1️⃣ DATOS PERSONALES -->
            <!-- ========================= -->
            <h5 class="mb-3 text-primary">Datos Personales</h5>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nombre completo</label>
                    <input type="text" name="nombre" class="form-control custom-input" required>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">CURP (simulada)</label>
                    <input type="text" name="curp" class="form-control custom-input">
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">Fecha de nacimiento</label>
                    <input type="date" name="fecha_nacimiento" class="form-control custom-input" required>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">Sexo</label>
                    <select name="sexo" class="form-select custom-input">
                        <option value="">Seleccionar</option>
                        <option value="Masculino">Masculino</option>
                        <option value="Femenino">Femenino</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">Nacionalidad</label>
                    <input type="text" name="nacionalidad" class="form-control custom-input" value="Mexicana">
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">Estado civil</label>
                    <select name="estado_civil" class="form-select custom-input">
                        <option value="">Seleccionar</option>
                        <option>Soltero/a</option>
                        <option>Casado/a</option>
                        <option>Divorciado/a</option>
                        <option>Viudo/a</option>
                    </select>
                </div>
            </div>

            <!-- ========================= -->
            <!-- 2️⃣ DATOS ELECTORALES -->
            <!-- ========================= -->
            <h5 class="mt-4 mb-3 text-primary">Datos Electorales</h5>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Cargo al que aspira</label>
                    <select name="cargo" class="form-select custom-input" required>
                        <option value="">Seleccionar</option>
                        <option>Presidente</option>
                        <option>Gobernador</option>
                        <option>Senador</option>
                        <option>Diputado</option>
                        <option>Presidente Municipal</option>
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Ámbito</label>
                    <select name="ambito" class="form-select custom-input">
                        <option>Federal</option>
                        <option>Estatal</option>
                        <option>Municipal</option>
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Entidad federativa</label>
                    <input type="text" name="entidad" class="form-control custom-input">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Partido político</label>
                    <input type="text" name="partido" class="form-control custom-input" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Candidato independiente</label>
                    <select name="independiente" class="form-select custom-input">
                        <option value="No">No</option>
                        <option value="Sí">Sí</option>
                    </select>
                </div>
            </div>

            <!-- ========================= -->
            <!-- 3️⃣ PERFIL PROFESIONAL -->
            <!-- ========================= -->
            <h5 class="mt-4 mb-3 text-primary">Perfil Profesional</h5>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nivel máximo de estudios</label>
                    <input type="text" name="estudios" class="form-control custom-input">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Profesión</label>
                    <input type="text" name="profesion" class="form-control custom-input">
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label">Experiencia política</label>
                    <textarea name="experiencia" rows="3" class="form-control custom-input"></textarea>
                </div>
            </div>

            <!-- ========================= -->
            <!-- 4️⃣ INFORMACIÓN PÚBLICA -->
            <!-- ========================= -->
            <h5 class="mt-4 mb-3 text-primary">Información Pública</h5>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Foto oficial</label>
                    <input type="file" name="foto" class="form-control custom-input" accept="image/*" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Eslogan</label>
                    <input type="text" name="eslogan" class="form-control custom-input">
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label">Propuesta de campaña</label>
                    <textarea name="propuesta" rows="4" class="form-control custom-input" required></textarea>
                </div>
            </div>

            <!-- ========================= -->
            <!-- 5️⃣ CAMPOS DEL SISTEMA -->
            <!-- ========================= -->
            <input type="hidden" name="estatus" value="Pendiente">
            <input type="hidden" name="fecha_solicitud" value="<?php echo date('Y-m-d'); ?>">

            <div class="mt-4">
                <button type="submit" class="btn-submit">
                    <i class="bi bi-person-plus-fill"></i> Registrar Candidato
                </button>
            </div>

        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../../js/dash.js"></script>

</body>
</html>