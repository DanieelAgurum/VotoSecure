<?php
// ==========================
// CANDIDATOS DESDE BASE DE DATOS
// ==========================
require_once '../../Modelo/candidatosMdl.php';

$candidatoMdl = new Candidato();
$todosCandidatos = $candidatoMdl->obtenerCandidatos();

// Agrupar por cargo → secciones dinámicas
$secciones = [];
foreach ($todosCandidatos as $cand) {
    if ($cand['estatus'] !== 'activo') continue;

    $cargo = trim($cand['cargo']);
    if (empty($cargo)) continue;

    $secciones[$cargo][] = [
        'id'             => $cand['id'],
        'nombre'         => $cand['nombre'],
        'apellido'       => $cand['apellido'],
        'partido_nombre' => $cand['partido_nombre'] ?? 'Independiente',
        'foto'           => !empty($cand['foto']) ? $cand['foto'] : '/VotoSecure/img/image.png'
    ];
}

ksort($secciones);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="/votosecure/img/vs.ico">
    <title>Boleta VotoSecure</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/VotoSecure/css/estilos.css">
    <link rel="stylesheet" href="/VotoSecure/css/candidatos.css">
    <link rel="stylesheet" href="/VotoSecure/css/boletaPlantilla.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="p-4">
    <main class="candidates-section container-xl" style="padding-top: 0;">
        <div class="text-center">
            <h1 class="candidates-title mb-2">BOLETA ELECTORAL</h1>
            <div id="esp32-status" class="mb-3" style="font-size: 1.1rem;">
                <span class="badge bg-secondary">ESP32: Esperando conexión USB...</span>
            </div>
            <p class="lead text-muted">Selecciona 1 candidato por puesto</p>
        </div>

        <form id="boletaForm">
            <?php if (empty($secciones)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-people-fill" style="font-size: 6rem; opacity: 0.3; color: #dee2e6;"></i>
                    <h3 class="mt-4 text-muted">No hay candidatos registrados</h3>
                    <p class="lead text-muted">Contacta al administrador</p>
                </div>
            <?php else: ?>
                <?php foreach ($secciones as $cargo => $listaCandidatos): ?>
                    <section class="election-section mb-5" data-position="<?= htmlspecialchars(strtolower($cargo)) ?>">
                        <div class="election-category mb-4">
                            <span class="category-dot"></span>
                            <h2 class="category-title"><?= htmlspecialchars(strtoupper($cargo)) ?></h2>
                        </div>

                        <div class="candidates-grid">
                            <?php if (empty($listaCandidatos)): ?>
                                <div class="col-12 text-center py-5 bg-light rounded">
                                    <i class="bi bi-person-x-lg" style="font-size: 4rem; opacity: 0.3;"></i>
                                    <h5 class="mt-3 text-muted">Sin candidatos para este cargo</h5>
                                </div>
                            <?php else: ?>
                                <?php foreach ($listaCandidatos as $cand): ?>
                                    <?php
                                    $nombreCompleto = trim($cand['nombre'] . ' ' . $cand['apellido']);
                                    $partidoNombre  = $cand['partido_nombre'] ?? 'Independiente';
                                    $fotoSrc        = $cand['foto'];
                                    ?>
                                    <label class="candidate-card h-100 position-relative cursor-pointer"
                                           data-puesto="<?= htmlspecialchars(strtolower($cargo)) ?>">
                                        <input type="radio"
                                               name="voto[<?= htmlspecialchars(strtoupper($cargo)) ?>]"
                                               value="<?= $cand['id'] ?>"
                                               class="position-absolute top-0 end-0 m-3 radio-input"
                                               style="z-index: 10; width: 20px; height: 20px;">
                                        <div class="card-body text-center p-4">
                                            <div class="avatar mb-3 mx-auto"
                                                 style="background-image: url('<?= htmlspecialchars($fotoSrc) ?>');
                                                        background-size: cover; background-position: center;">
                                            </div>
                                            <h5 class="fw-bold candidate-name mb-2">
                                                <?= htmlspecialchars($nombreCompleto) ?>
                                            </h5>
                                            <div class="partido-badge mb-3 mx-auto">
                                                <?= htmlspecialchars($partidoNombre) ?>
                                            </div>
                                            <div class="d-flex justify-content-center">
                                                <span class="text-muted fw-medium">Seleccionar Candidato</span>
                                            </div>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </section>
                <?php endforeach; ?>
            <?php endif; ?>

            <div class="text-center mt-5 pt-4 border-top">
                <button type="button" id="btnVotar"
                        class="btn btn-accent btn-proposal px-5 py-3 fs-5 fw-bold">
                    <i class="bi bi-check2-circle me-2"></i> VOTAR
                </button>
            </div>
        </form>
    </main>

    <!-- JS del ESP32 — contiene toda la lógica de selección, verificación y envío -->
    <script src="/votosecure/js/huella_esp32.js"></script>

    <!-- Status ESP32 en tiempo real (solo UI, sin lógica) -->
    <script>
        setInterval(() => {
            const statusEl = document.getElementById('esp32-status');
            if (!statusEl || !window.huella) return;

            if (window.huella.port && window.huella.reader) {
                statusEl.innerHTML = '<span class="badge bg-success fs-6">'
                    + '<i class="bi bi-usb-c me-1"></i>ESP32: Conectado ✅</span>';
            } else {
                statusEl.innerHTML = '<span class="badge bg-warning fs-6">'
                    + '<i class="bi bi-usb-plug me-1"></i>ESP32: Conecta USB...</span>';
            }
        }, 1000);
    </script>
</body>

</html>