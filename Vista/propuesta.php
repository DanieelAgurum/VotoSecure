<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/VotoSecure/modelo/config/conexion.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/VotoSecure/Modelo/propuestasMdl.php';

$modelo = new PropuestasMdl($conexion);
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$propuesta = null;
if ($id > 0) {
    $propuesta = $modelo->obtenerPorId($id);
    if (!$propuesta) {
        // Intentar por candidato_id si no encontró por id_propuesta
        $resultados = $modelo->obtenerPorCandidato($id);
        if (!empty($resultados)) {
            $propuesta = $modelo->obtenerPorId($resultados[0]['id_propuesta']);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Propuesta - VotoSecure</title>
    <link rel="icon" type="image/x-icon" href="/votosecure/img/vs.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/votosecure/css/estilos.css">
    <link rel="stylesheet" href="/votosecure/css/propuestas.css">
</head>

<body>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/VotoSecure/components/nav.php'; ?>

    <main class="prop-main">

        <?php if (!$propuesta): ?>

            <div class="prop-detalle" style="text-align:center;padding:80px 20px;">
                <div style="font-size:64px;margin-bottom:20px;">📋</div>
                <h2 style="font-size:24px;font-weight:800;color:#1a1a1a;margin-bottom:10px;">
                    Sin propuesta registrada
                </h2>
                <p style="font-size:15px;color:#6e6e6e;margin-bottom:28px;">
                    Este candidato aún no tiene una propuesta publicada.
                </p>
                <a href="candidatos.php" class="prop-volver" style="display:inline-flex;">
                    <i class="bi bi-arrow-left"></i> Volver a Candidatos
                </a>
            </div>

        <?php else: ?>

            <div class="prop-detalle">

                <a href="candidatos.php" class="prop-volver">
                    <i class="bi bi-arrow-left"></i> Volver a Candidatos
                </a>

                <div class="prop-detalle-header">
                    <div class="prop-detalle-foto-wrap">
                        <?php
                        $foto     = $propuesta['foto'] ?? '';
                        $esBase64 = strpos($foto, 'data:image') === 0;
                        $tieneRuta = !empty($foto) && !$esBase64;
                        ?>
                        <?php if ($esBase64 || $tieneRuta): ?>
                            <img src="<?= htmlspecialchars($foto) ?>"
                                alt="<?= htmlspecialchars($propuesta['nombre'] ?? '') ?>"
                                class="prop-detalle-foto">
                        <?php else: ?>
                            <div class="prop-detalle-foto-placeholder">
                                <i class="bi bi-person"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="prop-detalle-meta">
                        <div class="prop-detalle-partido">
                            <?= htmlspecialchars($propuesta['nombre_partido'] ?? 'Independiente') ?>
                            <?php if (!empty($propuesta['siglas'])): ?>
                                · <?= htmlspecialchars($propuesta['siglas']) ?>
                            <?php endif; ?>
                        </div>
                        <h1 class="prop-detalle-nombre">
                            <?= htmlspecialchars(($propuesta['nombre'] ?? '') . ' ' . ($propuesta['apellido'] ?? '')) ?>
                        </h1>
                        <div class="prop-detalle-cargo">
                            Candidato a: <?= htmlspecialchars($propuesta['cargo'] ?? '') ?>
                        </div>
                        <?php if (!empty($propuesta['slogan'])): ?>
                            <p class="prop-detalle-slogan">
                                "<?= htmlspecialchars($propuesta['slogan']) ?>"
                            </p>
                        <?php endif; ?>
                        <div class="prop-detalle-chips">
                            <?php if (!empty($propuesta['distrito'])): ?>
                                <span class="prop-chip">
                                    <i class="bi bi-geo-alt"></i>
                                    <?= htmlspecialchars($propuesta['distrito']) ?>
                                </span>
                            <?php endif; ?>
                            <?php if (!empty($propuesta['correo'])): ?>
                                <span class="prop-chip">
                                    <i class="bi bi-envelope"></i>
                                    <?= htmlspecialchars($propuesta['correo']) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <?php if (!empty($propuesta['video_url'])): ?>
                    <div class="prop-detalle-video-wrap">
                        <iframe src="<?= htmlspecialchars($propuesta['video_url']) ?>?rel=0"
                            class="prop-detalle-video"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen></iframe>
                    </div>
                <?php endif; ?>

                <div class="prop-detalle-titulo-wrap">
                    <h2 class="prop-detalle-titulo">
                        <?= htmlspecialchars($propuesta['titulo'] ?? '') ?>
                    </h2>
                    <span class="prop-detalle-fecha">
                        Publicada el <?= date('d/m/Y', strtotime($propuesta['created_at'])) ?>
                    </span>
                </div>

                <?php if (!empty($propuesta['mision'])): ?>
                    <div class="prop-seccion">
                        <div class="prop-seccion-header">
                            <i class="bi bi-bullseye"></i>
                            <h3>Misión</h3>
                        </div>
                        <div class="prop-seccion-body">
                            <?= nl2br(htmlspecialchars($propuesta['mision'])) ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($propuesta['propuesta_detallada'])): ?>
                    <div class="prop-seccion">
                        <div class="prop-seccion-header">
                            <i class="bi bi-file-text"></i>
                            <h3>Propuesta de Campaña</h3>
                        </div>
                        <div class="prop-seccion-body prop-detalle-texto">
                            <?= nl2br(htmlspecialchars($propuesta['propuesta_detallada'])) ?>
                        </div>
                    </div>
                <?php endif; ?>

                <a href="candidatos.php" class="prop-volver mt-4">
                    <i class="bi bi-arrow-left"></i> Volver a Candidatos
                </a>

            </div>

        <?php endif; ?>
    </main>

    <div class="prop-modal-video" id="modalVideo" onclick="cerrarVideo()">
        <div class="prop-modal-video-inner" onclick="event.stopPropagation()">
            <button class="prop-modal-close" onclick="cerrarVideo()">
                <i class="bi bi-x-lg"></i>
            </button>
            <iframe id="videoIframe" src="" frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen></iframe>
        </div>
    </div>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/VotoSecure/components/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/votosecure/js/nav.js"></script>
    <script>
        function abrirVideo(vid) {
            document.getElementById('videoIframe').src =
                'https://www.youtube.com/embed/' + vid + '?autoplay=1';
            document.getElementById('modalVideo').classList.add('activo');
            document.body.style.overflow = 'hidden';
        }

        function cerrarVideo() {
            document.getElementById('videoIframe').src = '';
            document.getElementById('modalVideo').classList.remove('activo');
            document.body.style.overflow = '';
        }
    </script>

</body>

</html>