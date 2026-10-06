<?php
require_once __DIR__ . '/../includes/formatos_guard.php';
requireFormatosAccess();

/**
 * Catálogo de formatos agrupado por categoría.
 * Para agregar un formato nuevo basta con añadir un renglón en la categoría que corresponda
 * (o crear una categoría nueva). Un formato sin 'url' se muestra como "Próximamente".
 */
$categorias = [
    [
        'titulo' => 'Correspondencia laboral',
        'detalle' => 'Comunicaciones oficiales dirigidas al empleado',
        'icono' => 'file-text',
        'formatos' => [
            [
                'titulo' => 'Renovación de contrato',
                'detalle' => 'Comunica al empleado la renovación de su contrato',
                'codigo' => 'AF-FT-02',
                'icono' => 'file-text',
                'url' => './formatos_renovacion.php',
            ],
            [
                'titulo' => 'Notificación de terminación',
                'detalle' => 'Preaviso de no renovación del contrato (Art. 46 CST)',
                'codigo' => 'AF-FT-02',
                'icono' => 'file-minus',
                'url' => './formatos_terminacion.php',
            ],
        ],
    ],
    [
        'titulo' => 'Exámenes médicos ocupacionales',
        'detalle' => 'Salud ocupacional del personal',
        'icono' => 'clipboard-list',
        'formatos' => [
            [
                'titulo' => 'Remisión de exámenes',
                'detalle' => 'Ingreso, periódicos y egreso',
                'codigo' => 'GH-FT-03',
                'icono' => 'clipboard-list',
                'url' => './formatos_remision_examenes.php',
            ],
        ],
    ],
    [
        'titulo' => 'Contratos y modificaciones',
        'detalle' => 'Contratación y cambios a las condiciones pactadas',
        'icono' => 'pencil',
        'formatos' => [
            [
                'titulo' => 'Contrato a término fijo',
                'detalle' => 'Contrato de trabajo a término fijo',
                'codigo' => '',
                'icono' => 'file-signature',
                'url' => '',   // pendiente: cuando exista, solo hay que poner aquí su vista
            ],
            [
                'titulo' => 'Otro Sí',
                'detalle' => 'Modificación del contrato',
                'codigo' => 'GH-FT-24',
                'icono' => 'pencil',
                'url' => './formatos_otrosi.php',
            ],
            [
                'titulo' => 'Otro Sí cambio de salario',
                'detalle' => 'Modificación de la remuneración',
                'codigo' => 'GH-FT-25',
                'icono' => 'pencil',
                'url' => './formatos_otrosi_salario.php',
            ],
        ],
    ],
    [
        'titulo' => 'Certificaciones',
        'detalle' => 'Constancias que se entregan al empleado',
        'icono' => 'file-signature',
        'formatos' => [
            [
                'titulo' => 'Certificado laboral',
                'detalle' => 'Para quien labora actualmente',
                'codigo' => 'GH-FT-10',
                'icono' => 'file-signature',
                'url' => './formatos_certificado_actual.php',
            ],
        ],
    ],
];

$h = static fn(string $s): string => htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <?php require __DIR__ . '/../includes/head.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHVB - Formatos</title>
    <link rel="stylesheet" href="./assets/css/tailwind.css">
    <style>
        .fmt-card{display:flex;align-items:center;gap:14px;height:100%;padding:16px;background:#fff;border:1px solid #f3f4f6;border-radius:16px;box-shadow:0 1px 2px rgba(0,0,0,.04);transition:border-color .15s,box-shadow .15s}
        a.fmt-card:hover{border-color:#fecaca;box-shadow:0 4px 12px rgba(0,0,0,.06)}
        a.fmt-card:focus-visible{outline:2px solid #fca5a5;outline-offset:2px}
        .fmt-card .fmt-ico{flex:none;width:44px;height:44px;border-radius:12px;background:#fef2f2;color:#dc2626;display:flex;align-items:center;justify-content:center}
        .fmt-card .fmt-txt{min-width:0;flex:1}
        .fmt-card .fmt-go{flex:none;color:#9ca3af}
        a.fmt-card:hover .fmt-go{color:#dc2626}
        .fmt-codigo{display:inline-block;margin-top:6px;padding:1px 8px;border-radius:999px;background:#f3f4f6;color:#4b5563;font-size:11px;font-weight:600;letter-spacing:.03em}
        .fmt-pronto{border-style:dashed;background:#f9fafb;box-shadow:none}
        .fmt-pronto .fmt-ico{background:#f3f4f6;color:#9ca3af}
        .fmt-pronto h3{color:#6b7280}
        .fmt-badge{display:inline-block;margin-top:6px;padding:1px 8px;border-radius:999px;background:#fffbeb;border:1px solid #fde68a;color:#92400e;font-size:11px;font-weight:600}
        .fmt-cat-titulo{display:flex;align-items:center;gap:10px;margin-bottom:12px}
        .fmt-cat-titulo .fmt-cat-ico{width:28px;height:28px;border-radius:8px;background:#fee2e2;color:#dc2626;display:flex;align-items:center;justify-content:center}
        .fmt-cat-titulo::after{content:"";flex:1;height:1px;background:#e5e7eb;margin-left:6px}
        .fmt-categoria+.fmt-categoria{margin-top:28px}
    </style>
</head>

<body class="bg-gray-50 min-h-screen text-gray-800">
    <?php require __DIR__ . '/../includes/sidebar.php'; ?>

    <div class="md:ml-64 pt-14 md:pt-0">
        <header class="bg-white border-b border-gray-100 px-4 sm:px-6 py-4">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3"><span
                        class="w-8 h-8 rounded-xl bg-red-50 text-red-600 flex items-center justify-center"><?= icon('file-text', 'w-5 h-5') ?></span>
                    <div>
                        <h1 class="text-base font-bold text-gray-800">Formatos</h1>
                        <p class="text-xs text-gray-600">Elige el formato que quieres generar</p>
                    </div>
                </div>
                <button type="button" onclick="abrirModalFunciones()"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                    <?= icon('clipboard-list', 'w-4 h-4') ?> Funciones
                </button>
            </div>
        </header>

        <main class="p-4 sm:p-6 max-w-7xl mx-auto">
            <?php foreach ($categorias as $cat): ?>
                <section class="fmt-categoria" aria-label="<?= $h($cat['titulo']) ?>">
                    <div class="fmt-cat-titulo">
                        <span class="fmt-cat-ico"><?= icon($cat['icono'], 'w-4 h-4') ?></span>
                        <div>
                            <h2 class="text-sm font-bold text-gray-800"><?= $h($cat['titulo']) ?></h2>
                            <p class="text-xs text-gray-600"><?= $h($cat['detalle']) ?></p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <?php foreach ($cat['formatos'] as $f): ?>
                            <?php $disponible = $f['url'] !== ''; ?>
                            <?php if ($disponible): ?>
                                <a href="<?= $h($f['url']) ?>" class="fmt-card" title="Abrir <?= $h($f['titulo']) ?>">
                            <?php else: ?>
                                <div class="fmt-card fmt-pronto" aria-disabled="true">
                            <?php endif; ?>
                                <span class="fmt-ico"><?= icon($f['icono'], 'w-5 h-5') ?></span>
                                <span class="fmt-txt">
                                    <h3 class="text-sm font-bold text-gray-800"><?= $h($f['titulo']) ?></h3>
                                    <span class="block text-xs text-gray-600 mt-1"><?= $h($f['detalle']) ?></span>
                                    <?php if ($disponible && $f['codigo'] !== ''): ?>
                                        <span class="fmt-codigo"><?= $h($f['codigo']) ?></span>
                                    <?php elseif (!$disponible): ?>
                                        <span class="fmt-badge">Próximamente</span>
                                    <?php endif; ?>
                                </span>
                                <?php if ($disponible): ?>
                                    <span class="fmt-go"><?= icon('chevron-right', 'w-5 h-5') ?></span>
                                <?php endif; ?>
                            <?php if ($disponible): ?></a><?php else: ?></div><?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endforeach; ?>
        </main>
    </div>
<?php require __DIR__ . '/../includes/funciones_modal.php'; ?>
</body>

</html>