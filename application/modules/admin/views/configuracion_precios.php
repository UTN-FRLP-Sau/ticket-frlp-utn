<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Precios de las viandas</title>
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

        
        @media (max-width: 575.98px) {
            .col-xs-7.col-sm-6.col-form-label {
                text-align: left !important; 
            }
        }
        
        
        .form-group.row {
            align-items: center;
        }

        
        .form-button-center {
            text-align: center; 
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="col-sm-5 col-md-5 col-lg-5 mx-auto">

            <h1 class="mb-2">Precios de las viandas</h1>

            <?= form_open(current_url()); ?>
            <?php foreach ($precios as $key => $precio) : ?>

            <div class="form-group row mb-2">
                <label for="precio_<?= strtolower($precio->id); ?>" class="col-xs-7 col-sm-6 col-form-label">
                    <?= $precio->tipo_user; ?></label>
                <div class="col-sm-6">
                    <input type="number" id="precio_<?= strtolower($precio->id); ?>"
                        name="precio_<?= strtolower($precio->id); ?>" class="form-control" step="0.01"
                        value="<?= $precio->costo ?>" required>
                </div>
            </div>
            <?php endforeach; ?>

            <div class="form-group row mb-2">
                <div class="col-sm-12 form-button-center"> 
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>