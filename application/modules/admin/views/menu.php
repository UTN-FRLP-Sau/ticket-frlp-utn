<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú del Comedor Universitario</title>
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

        h1.text-center {
            margin-top: 3rem;
            margin-bottom: 2rem;
        }

        .table-sm {
            margin-bottom: 1.5rem;
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

        .table tbody input[type="text"] {
            width: 100%;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            padding: 0.375rem 0.75rem;
            box-sizing: border-box;
            text-align: center;
        }

        .btn-group {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
            padding: 0.75rem 2rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 0.3rem;
            transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
        }

        .alert-danger {
            margin-top: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col mt-5">
                <h1 class="text-center"># Menu</h1>
            </div>
        </div>
        <div class="row d-flex justify-content-center">
            <div class="col-8">
                <?= validation_errors('<div class="alert alert-danger alert-dismissible fade show" role="alert">', ' <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'); ?>
                <?= form_open(current_url()); ?>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Día</th>
                            <th>Menu Básico</th>
                            <th>Opción Veggie</th>
                            <th>Sin TACC</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($menu as $key => $item) : ?>
                            <tr>
                                <td><?= $item->dia; ?></td>
                                <td> <input name="basico_<?= $item->id; ?>" type="text" value="<?= $item->menu1; ?>"> </td>
                                <td> <input name="veggie_<?= $item->id; ?>" type="text" value="<?= $item->menu2; ?>"> </td>
                                <td> <input name="sin_tacc_<?= $item->id; ?>" type="text" value="<?= $item->menu3; ?>"> </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="row">
                    <div class="btn-group">
                        <button class="btn btn-success mx-5" type="submit">Actualizar Menu</button>
                    </div>
                </div>
                <?= form_close(); ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>