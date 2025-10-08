<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Cargas</title>
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

    
        h2.text-center {
            margin-top: 3rem;
            margin-bottom: 2rem;
        }

        .table-responsive {
            margin-top: 1rem;
            margin-bottom: 2rem;
        }

        .table thead th {
            background-color: #007bff;
            color: white;
            border-bottom: 2px solid #0056b3;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col mt-5">
                <h2 class="text-center"># Historial de cargas</h2>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="table-responsive">
                    <table class="table table-sm text-center">
                        <thead>
                            <tr>
                                <th style="text-align: left;" class="col">Fecha</th>
                                <th class="col">Hora</th>
                                <th class="col">Nombre</th>
                                <th class="col">Apellido</th>
                                <th style="text-align: right;" class="col">Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cargas as $carga) : ?>
                            <tr>
                                <td style="text-align: left;"><?= $carga->fecha; ?></td>
                                <td><?= $carga->hora; ?></td>
                                <td><?= $carga->nombre; ?></td>
                                <td><?= $carga->apellido; ?></td>
                                <td style="text-align: right;" id=>$ <?= $carga->monto; ?>.-</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>