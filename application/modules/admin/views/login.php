<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
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
            display: flex; 
            flex-direction: column; 
            justify-content: center; 
            align-items: center; 
        }

        
        .form-center {
            display: flex;
            justify-content: center; 
            width: 100%; 
        }

        .form-group.mb-4 {
            width: 100%; 
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row w-100"> <div class="col my-4">
                <img class="img-fluid mx-auto d-block" src="<?= base_url('assets/img/Logo_comedor_vendedor.png'); ?>"
                    alt="Logo Comedor Vendedor">
            </div>
        </div>
        <div class="row form-center w-100"> <div class="col-6 col-md-5 col-xl-4 my-3">
                <?= form_open(current_url()); ?>
                <div class="form-group mb-4">
                    <input type="text" class="form-control" placeholder="Ingrese su Usuario" name="nick-name" required>
                </div>
                <div class="form-group mb-4">
                    <input type="password" class="form-control" placeholder="Su contraseña" name="password" required>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success">Ingresar</button>
                </div>
                <?= form_close(); ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>