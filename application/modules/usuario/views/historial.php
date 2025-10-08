<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Movimientos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        html, body {
            height: 100%;
            margin: 0;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

    <!-- Contenido principal -->
    <main class="flex-grow-1">
        <div class="container py-4">
            <div class="row mb-4">
                <div class="col text-center">
                    <h2 class="text-primary fw-bold">
                        <i class="bi bi-clock-history me-2"></i>Histórico de Movimientos
                    </h2>
                    <hr class="mx-auto" style="width: 80px; border-top: 3px solid #0d6efd;">
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="table-responsive shadow-sm rounded">
                        <table class="table table-striped table-bordered table-hover align-middle text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Movimiento</th>
                                    <th>Monto</th>
                                    <th>Saldo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($compras)) : ?>
                                    <?php foreach ($compras as $compra) : ?>
                                        <tr>
                                            <td><?= $compra->id; ?></td>
                                            <td><?= $compra->fecha; ?></td>
                                            <td><?= $compra->hora; ?></td>
                                            <td><?= $compra->transaccion; ?></td>
                                            <td>$<?= number_format($compra->monto, 2, ',', '.'); ?></td>
                                            <td>$<?= number_format($compra->saldo, 2, ',', '.'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="bi bi-info-circle me-2"></i>No hay movimientos disponibles.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <nav aria-label="Historial paginado" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php if (!isset($primera)) : ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?= base_url("usuario/ultimos-movimientos"); ?>" aria-label="Primera">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if (isset($links)) : ?>
                                <?php foreach ($links as $link) : ?>
                                    <?php if (isset($link['act'])) : ?>
                                        <li class="page-item active" aria-current="page">
                                            <span class="page-link"><?= $link['num']; ?></span>
                                        </li>
                                    <?php else : ?>
                                        <li class="page-item">
                                            <a class="page-link" href="<?= base_url("usuario/ultimos-movimientos/{$link['id']}"); ?>">
                                                <?= $link['num']; ?>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <?php if (!isset($ultima)) : ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?= base_url("usuario/ultimos-movimientos/{$ultimo}"); ?>" aria-label="Última">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </main>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
