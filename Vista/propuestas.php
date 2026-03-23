<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/VotoSecure/Modelo/propuestasMdl.php';
$modelo     = new PropuestasMdl();
$id         = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$propuesta  = $id > 0 ? $modelo->obtenerPorId($id) : null;
$propuestas = $id === 0 ? $modelo->obtenerTodas() : [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Propuestas - VotoSecure</title>
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

    <!-- ── LISTADO ── -->
    <div class="prop-listado-header">
        <h1 class="prop-listado-titulo">Propuestas de Candidatos</h1>
        <p class="prop-listado-sub">Conoce las propuestas de todos los candidatos registrados</p>
        <div class="prop-buscador-wrap">
            <i class="bi bi-search prop-buscador-icon"></i>
            <input type="text" id="buscador" class="prop-buscador"
                   placeholder="Buscar por nombre, partido o cargo...">
        </div>
    </div>

    <div class="prop-grid" id="propGrid">
        <?php if (empty($propuestas)): ?>
            <div class="prop-vacio">
                <i class="bi bi-file-earmark-x"></i>
                <p>No hay propuestas registradas aún</p>
            </div>
        <?php else: ?>
            <?php foreach ($propuestas as $p):
                $vid = '';
                if (!empty($p['video_url'])) {
                    if (preg_match('/embed\/([a-zA-Z0-9_-]+)/', $p['video_url'], $m)) $vid = $m[1];
                    elseif (preg_match('/v=([a-zA-Z0-9_-]+)/', $p['video_url'], $m))   $vid = $m[1];
                }
            ?>
            <div class="prop-card"
                 data-nombre="<?= strtolower($p['nombre'] . ' ' . $p['apellido']) ?>"
                 data-partido="<?= strtolower($p['nombre_partido'] ?? '') ?>"
                 data-cargo="<?= strtolower($p['cargo'] ?? '') ?>">

                <!-- ── Foto grande arriba ── -->
                <div class="prop-card-img-wrap"
                     <?= $vid ? "onclick=\"abrirVideo('{$vid}')\" style=\"cursor:pointer;\"" : '' ?>>

                    <?php if (!empty($p['foto'])): ?>
                        <img src="<?= htmlspecialchars($p['foto']) ?>"
                             alt="<?= htmlspecialchars($p['nombre']) ?>"
                             class="prop-card-foto">
                    <?php else: ?>
                        <div class="prop-card-foto-placeholder">
                            <i class="bi bi-person"></i>
                        </div>
                    <?php endif; ?>

                    <span class="prop-card-cargo-badge">
                        <?= htmlspecialchars($p['cargo'] ?? '') ?>
                    </span>

                    <span class="prop-card-partido-badge">
                        <?= htmlspecialchars($p['nombre_partido'] ?? 'Independiente') ?>
                    </span>

                    <?php if ($vid): ?>
                        <div class="prop-card-play-overlay">
                            <i class="bi bi-play-circle-fill"></i>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- ── Cuerpo ── -->
                <div class="prop-card-body">
                    <h3 class="prop-card-nombre">
                        <?= htmlspecialchars($p['nombre'] . ' ' . $p['apellido']) ?>
                    </h3>
                    <p class="prop-card-titulo">
                        <?= htmlspecialchars($p['titulo']) ?>
                    </p>
                    <?php if (!empty($p['slogan'])): ?>
                        <p class="prop-card-slogan">
                            "<?= htmlspecialchars($p['slogan']) ?>"
                        </p>
                    <?php endif; ?>
                    <p class="prop-card-preview">
                        <?= htmlspecialchars(mb_substr($p['propuesta_detallada'] ?? '', 0, 180)) ?>...
                    </p>
                </div>

                <!-- ── Footer ── -->
                <div class="prop-card-footer">
                    <a href="?id=<?= $p['id_propuesta'] ?>" class="prop-card-btn">
                        Ver propuesta <i class="bi bi-arrow-right"></i>
                    </a>
                    <?php if ($vid): ?>
                        <span class="prop-card-video-badge"
                              onclick="abrirVideo('<?= $vid ?>')"
                              style="cursor:pointer;">
                            <i class="bi bi-youtube" style="color:#EF4444;font-size:14px;"></i>
                            Video
                        </span>
                    <?php endif; ?>
                </div>

            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

<?php else: ?>

    <!-- ── DETALLE ── -->
    <div class="prop-detalle">

        <a href="propuestas.php" class="prop-volver">
            <i class="bi bi-arrow-left"></i> Volver a Propuestas
        </a>

        <div class="prop-detalle-header">
            <div class="prop-detalle-foto-wrap">
                <?php if (!empty($propuesta['foto'])): ?>
                    <img src="<?= htmlspecialchars($propuesta['foto']) ?>"
                         alt="<?= htmlspecialchars($propuesta['nombre']) ?>"
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
                    <?= htmlspecialchars($propuesta['nombre'] . ' ' . $propuesta['apellido']) ?>
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
                <?= htmlspecialchars($propuesta['titulo']) ?>
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

        <a href="propuestas.php" class="prop-volver mt-4">
            <i class="bi bi-arrow-left"></i> Volver a Propuestas
        </a>

    </div>

<?php endif; ?>
</main>

<!-- Modal video -->
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
const buscador = document.getElementById('buscador');
if (buscador) {
    buscador.addEventListener('input', function() {
        const q = this.value.toLowerCase().trim();
        document.querySelectorAll('.prop-card').forEach(card => {
            const match = card.dataset.nombre.includes(q) ||
                          card.dataset.partido.includes(q) ||
                          card.dataset.cargo.includes(q);
            card.style.display = match ? '' : 'none';
        });
    });
}

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