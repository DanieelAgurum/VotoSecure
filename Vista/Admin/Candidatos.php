<?php 
//session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Candidato</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet"
     href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="../../css/Style.css">

</head>

<body>

<?php include('../../components/Admin/Navbar.php'); ?>

<div class="container-main">

    <div class="card">

        <div class="card-header">
            <h2><i class="fas fa-user-plus"></i> Registrar Candidato</h2>
        </div>

        <div class="card-body">

            <div id="alertContainer"></div>

            <form id="formCandidato"
                  method="POST"
                  action="../../Controlador/CandidatoController.php">

                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Nombre</label>
                            <input type="text" class="form-control" name="nombre" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Apellido</label>
                            <input type="text" class="form-control" name="apellido" required>
                        </div>
                    </div>

                </div>

                <div class="form-group">
                    <label class="form-label">Correo</label>
                    <input type="email" class="form-control" name="email" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Cédula</label>
                    <input type="text" class="form-control" name="cedula" required>
                </div>

                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Partido</label>
                            <select class="form-control" name="partido" required>
                                <option value="">Seleccione</option>
                                <option>Partido A</option>
                                <option>Partido B</option>
                                <option>Partido C</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Cargo</label>
                            <select class="form-control" name="cargo" required>
                                <option value="">Seleccione</option>
                                <option>Presidente</option>
                                <option>Vicepresidente</option>
                                <option>Diputado</option>
                            </select>
                        </div>
                    </div>

                </div>

                <div class="form-group">
                    <label class="form-label">Biografía</label>
                    <textarea class="form-control" name="biografia" rows="4"></textarea>
                </div>

                <div class="row-buttons">

                    <button type="submit" class="btn btn-submit">
                        <i class="fas fa-save"></i> Registrar
                    </button>

                    <button type="reset" class="btn btn-reset">
                        <i class="fas fa-redo"></i> Limpiar
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>
</body>
</html>