<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Descargar Listados</title>
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

    
        .form-center {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        h1.text-center {
            margin-top: 3rem;
            margin-bottom: 2rem;
        }

        .input-group-append .btn-success {
            background-color: #28a745;
            border-color: #28a745;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            border-radius: 0 0.25rem 0.25rem 0;
            transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .input-group-append .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
        }

        .form-control[readonly] {
            background-color: #e9ecef;
            opacity: 1;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row form-center">
            <h1 class="text-center"> Descargar Listados </h1>
            <div class="col-10 col-md-7 col-lg-4 my-3">
                <?php $dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes']; ?>
                <?php foreach ($dias as $key => $dia) : ?>
                <?php
                    $nroDia = date('N');
                    $proximo = time() + ((7 - $nroDia + ($key + 1)) * 24 * 60 * 60);
                    $proxima_fecha = date('Y-m-d', $proximo);
                    ?>
                <?= form_open(current_url()); ?>
                <div class="input-group mb-3">
                    <label class="form-control"><?= ucwords($dia); ?>:</label>
                    <input type="timestamp" class="form-control" name="fecha" readonly value="<?= $proxima_fecha; ?>">
                    <div class="input-group-append">
                        <button class="btn btn-success" type="submit">Descargar</button>
                    </div>
                </div>
                <?= form_close(); ?>
                <?php endforeach; ?>
                <?= form_open(current_url()); ?>
                <div class="input-group mb-3">
                    <input type="date" name="fecha" class="form-control">
                    <div class="input-group-append">
                        <button class="btn btn-success" type="submit">Descargar</button>
                    </div>
                </div>
                <?= form_close(); ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>