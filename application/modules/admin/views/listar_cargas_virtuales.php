<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Links de Pagos a Confirmar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            display: flex;
            flex-direction: column;
        }

        .container {
            flex: 1;
        }


        .row {
            text-align: center;
            margin-bottom: 20px;
        }

        .table thead th {
            background-color: #e4e4e4ff;
            color: black;
            border-bottom: 2px solid #e7e7e7ff;
            padding: 0.75rem;
            text-align: center;
        }

        .table tbody td {
            vertical-align: middle;
            padding: 0.5rem;
        }

        .table tbody tr:nth-of-type(odd) {
            background-color: #f8f9fa;
        }

        .filter-input {
            width: 100%;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            padding: 0.375rem 0.75rem;
            box-sizing: border-box;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            line-height: 1.5;
            border-radius: 0.2rem;
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }

        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }

        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col mt-5">
                <h2 class="text-center"># Links de pagos a Confirmar</h2>
                <?php if (!empty($fecha_filtrada)) : ?>
                    <h4 class="text-center">Fecha: <?= strftime('%d-%B-%Y', strtotime($fecha_filtrada)); ?></h4>
                <?php endif; ?>
                <form method="POST" action="<?= base_url('admin/cargasvirtuales/list'); ?>" class="text-center">
                    <input type="date" name="filter_date" class="form-control d-inline-block w-auto">
                    <button type="submit" class="btn btn-primary btn-sm">Aplicar Filtro</button>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="table-responsive">
                    <table class="table table-sm text-center">
                        <thead>
                            <tr>
                                <th style="text-align: left;" class="col">Documento</th>
                                <th class="col">Fecha</th>
                                <th class="col">Apellido</th>
                                <th class="col">Nombre</th>
                                <th class="col">Monto</th>
                                <th style="text-align: right;" class="col">Acciones</th>
                                <th class="col">Aprobado por</th>
                                <th class="col">Aprobado el:</th>
                            </tr>
                            <tr>
                                <th><input type="text" class="form-control form-control-sm filter-input" data-column="0" placeholder="Filtrar Documento"></th>
                                <th><input type="text" class="form-control form-control-sm filter-input" data-column="1" placeholder="Filtrar Fecha"></th>
                                <th><input type="text" class="form-control form-control-sm filter-input" data-column="2" placeholder="Filtrar Apellido"></th>
                                <th><input type="text" class="form-control form-control-sm filter-input" data-column="3" placeholder="Filtrar Nombre"></th>
                                <th><input type="text" class="form-control form-control-sm filter-input" data-column="4" placeholder="Filtrar Monto"></th>
                                <th></th>
                                <th><input type="text" class="form-control form-control-sm filter-input" data-column="6" placeholder="Filtrar Aprobado por"></th>
                                <th><input type="text" class="form-control form-control-sm filter-input" data-column="7" placeholder="Filtrar Aprobado el"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cargas as $carga) : ?>
                            <tr>
                                <td style="text-align: left;"><?= $carga -> documento; ?></td>
                                <td> <?= date('d-M-Y', strtotime($carga->timestamp)); ?> </td>
                                <td><?= $carga -> apellido; ?></td>
                                <td><?= $carga -> nombre; ?></td>
                                <td>$<?= $carga -> monto; ?></td>
                                <?php if ($carga->estado !== 'revision') : ?>
                                    <?php if ($carga->estado === 'aprobado') : ?>
                                    <td style="text-align: right;">
                                        <span style="background-color: green; color: white; padding: 5px; border-radius: 3px;">Aprobado</span>
                                    </td>
                                    <?php else: ?>
                                    <td style="text-align: right;">
                                        <span style="background-color: red; color: white; padding: 5px; border-radius: 3px;">Rechazado</span>
                                    </td>
                                    <?php endif; ?>
                                    <td> <?= $carga -> vendedor_username; ?> </td>
                                    <td> <?= date('d-M-Y', strtotime($carga->confirmacion_timestamp)); ?> </td>
                                <?php else: ?>
                                <td style="text-align: right;">
                                    <form method="POST" action="<?= base_url('admin/cargasvirtuales/list/' . $fecha_filtrada . '/aprobar'); ?>" style="display: inline;">
                                        <input type="hidden" id="carga_id" name="carga_id" value="<?= $carga->id; ?>">
                                        <button type="submit" class="btn btn-success btn-sm" style="background-color: green; color: white;">
                                            <i class="bi bi-check"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="<?= base_url('admin/cargasvirtuales/list/' . $fecha_filtrada . '/rechazar'); ?>" style="display: inline;">
                                        <input type="hidden" id="carga_id" name="carga_id" value="<?= $carga->id; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" style="background-color: red; color: white;">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    </form>
                                </td>
                                <td> --- </td>
                                <td> --- </td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterInputs = document.querySelectorAll('.filter-input');
        const tableRows = document.querySelectorAll('tbody tr');

        filterInputs.forEach((input) => {
            input.addEventListener('keyup', function () {
                const column = this.getAttribute('data-column');
                const filterValue = this.value.toLowerCase();

                tableRows.forEach((row) => {
                    const cell = row.querySelectorAll('td')[column];
                    if (cell) {
                        const cellText = cell.textContent.toLowerCase();
                        if (cellText.includes(filterValue)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    }
                });
            });
        });
    });
    </script>
</body>
</html>