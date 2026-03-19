<?php
// ==========================
// DATOS ESTÁTICOS
// ==========================

$puestos = [
    'PRESIDENCIA' => [
        [
            'id' => 1,
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'partido_nombre' => 'Partido Azul',
            'foto' => '/VotoSecure/img/image.png'
        ],
        [
            'id' => 2,
            'nombre' => 'María',
            'apellido' => 'Gómez',
            'partido_nombre' => 'Partido Rojo',
            'foto' => '/VotoSecure/img/image.png'
        ]
    ],
    'GOBERNADORES' => [
        [
            'id' => 3,
            'nombre' => 'Carlos',
            'apellido' => 'Ramírez',
            'partido_nombre' => 'Partido Verde',
            'foto' => '/VotoSecure/img/image.png'
        ],
        [
            'id' => 4,
            'nombre' => 'Ana',
            'apellido' => 'López',
            'partido_nombre' => 'Independiente',
            'foto' => '/VotoSecure/img/image.png'
        ]
    ],
    'ALCALDÍAS' => [
        [
            'id' => 5,
            'nombre' => 'Luis',
            'apellido' => 'Martínez',
            'partido_nombre' => 'Partido Amarillo',
            'foto' => '/VotoSecure/img/image.png'
        ],
        [
            'id' => 6,
            'nombre' => 'Sofía',
            'apellido' => 'Hernández',
            'partido_nombre' => 'Independiente',
            'foto' => '/VotoSecure/img/image.png'
        ]
    ]
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🗳️ Boleta VotoSecure</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/VotoSecure/css/estilos.css">
    <link rel="stylesheet" href="/VotoSecure/css/candidatos.css">
    <link rel="stylesheet" href="/VotoSecure/css/boletaPlantilla.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="p-4">
    <main class="candidates-section container-xl" style="padding-top: 0;">
        <div class="text-center mb-5">
            <h1 class="candidates-title mb-2">BOLETA ELECTORAL</h1>
            <p class="lead text-muted">Selecciona 1 candidato por puesto</p>
        </div>

        <form id="boletaForm">
            <?php foreach (['PRESIDENCIA', 'GOBERNADORES', 'ALCALDÍAS'] as $puesto): ?>
                <section class="election-section mb-5" data-position="<?= strtolower($puesto) ?>">
                    <div class="election-category mb-4">
                        <span class="category-dot"></span>
                        <h2 class="category-title"><?= $puesto ?></h2>
                    </div>
                    
                    <div class="candidates-grid">
                        <?php 
                        $candsPuesto = $puestos[$puesto] ?? [];
                        if (empty($candsPuesto)): ?>
                            <div class="col-12 text-center py-5 bg-light rounded">
                                <i class="bi bi-person-x-lg" style="font-size: 4rem; opacity: 0.3;"></i>
                                <h5 class="mt-3 text-muted">Sin candidatos</h5>
                            </div>
                        <?php else: 
                        foreach ($candsPuesto as $cand): 
                            $nombreCompleto = trim($cand['nombre'] . ' ' . $cand['apellido']);
                            $partidoNombre = $cand['partido_nombre'] ?? 'Independiente';
                            $fotoSrc = $cand['foto'] ?: '/VotoSecure/img/image.png';
                        ?>
                            <label class="candidate-card h-100 position-relative cursor-pointer" data-puesto="<?= $puesto ?>">
                                <input type="radio" name="voto[<?= $puesto ?>]" value="<?= $cand['id'] ?>" 
                                       class="position-absolute top-0 end-0 m-3 radio-input" style="z-index: 10; width: 20px; height: 20px;">
                                <div class="card-body text-center p-4">
                                    <div class="avatar mb-3 mx-auto" style="background-image: url('<?= htmlspecialchars($fotoSrc) ?>'); background-size: cover; background-position: center;"></div>
                                    <h5 class="fw-bold candidate-name mb-2"><?= htmlspecialchars($nombreCompleto) ?></h5>
                                    <div class="partido-badge mb-3 mx-auto"><?= htmlspecialchars($partidoNombre) ?></div>
                                    <div class="d-flex justify-content-center">
                                        <span class="text-muted fw-medium">Seleccionar Candidato</span>
                                    </div>
                                </div>
                            </label>
                        <?php endforeach; endif; ?>
                    </div>
                </section>
            <?php endforeach; ?>
            
            <div class="text-center mt-5 pt-4 border-top">
                <button type="button" id="btnVotar" class="btn btn-accent btn-proposal px-5 py-3 fs-5 fw-bold">
                    <i class="bi bi-check2-circle me-2"></i> VOTAR
                </button>
            </div>
        </form>
    </main>

    <script src="/VotoSecure/js/huella_esp32.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Selección visual card
            document.querySelectorAll('.candidate-card').forEach(card => {
                card.addEventListener('click', function() {
                    const puesto = this.dataset.puesto;
                    // Deseleccionar otros mismo puesto
                    document.querySelectorAll(`[data-puesto="${puesto}"]`).forEach(c => {
                        c.classList.remove('selected');
                    });
                    this.classList.add('selected');
                });
            });

            document.getElementById('btnVotar').onclick = function() {
                const formData = new FormData(document.getElementById('boletaForm'));
                const votos = Object.fromEntries(formData);
                const omitidos = ['PRESIDENCIA', 'GOBERNADORES', 'ALCALDÍAS'].filter(puesto => !votos[`voto[${puesto}]`]);

                if (omitidos.length) {
                    Swal.fire({
                        title: '⚠️ Votos omitidos',
                        html: `<strong>Puestos sin selección:</strong><br><br>
                               ${omitidos.join('<br>')}
                               <br><br><small><em>Se considerarán votos en blanco</em></small>`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Aceptar',
                        cancelButtonText: 'Revisar',
                        confirmButtonColor: '#22D3EE'
                    }).then(result => {
                        if (result.isConfirmed) verificarHuella(votos);
                    });
                } else {
                    verificarHuella(votos);
                }
            };
        });

        function verificarHuella(votos) {
            Swal.fire({
                title: 'Verificación final',
                html: '<div class="text-center"><div class="spinner-border text-primary mb-3" style="width: 4rem; height: 4rem;"></div><p class="mb-0"><strong>Acerque su huella digital</strong><br><small class="text-muted">Coloque el dedo en el lector</small></p></div>',
                allowOutsideClick: false,
                showConfirmButton: false,
                showCancelButton: true,
                cancelButtonText: 'Cancelar'
            });
            
            console.log('=== DATOS ENVIADOS ===');
            console.log('Votos:', Object.fromEntries(new FormData(document.getElementById('boletaForm'))));
            
            // Simula ESP32 o POST directo
            fetch('/VotoSecure/api/guardar_voto.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({votos: Object.fromEntries(new FormData(document.getElementById('boletaForm')))})
            }).then(r => r.json()).then(data => {
                console.log('Respuesta API:', data);
                Swal.close();
                if (data.success) {
                    Swal.fire('¡Voto registrado!', 'Gracias por votar.', 'success');
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            }).catch(() => Swal.fire('Error', 'Sin conexión', 'error'));
        }
    </script>
</body>
</html>
