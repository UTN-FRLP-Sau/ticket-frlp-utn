<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Acceso al Comedor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
    
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #f8f9fa;
        }

        .container {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding-top: 20px;
            padding-bottom: 20px;
        }

    
        .form-center {
            display: flex;
            justify-content: center;
            width: 100%;
        }
    
        .img-fluid.mx-auto.d-block {
            max-width: 250px;
            height: auto;
        }

    
        .link-text-center {
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="row">
            <div class="col my-4">
                <img class="img-fluid mx-auto d-block" src="<?= base_url('assets/img/logo_comedor.png'); ?>" alt="">
            </div>
        </div>
        <div class="row w-100 justify-content-center">
            <div class="col-12 text-center link-text-center mb-2">
                <p style="font-size: larger; font-weight: bold;">Pedí tu usuario <a href="https://forms.gle/dt6NBGTLEsgbcaWS7" target="_blanck">ACÁ</a>.</p>
            </div>
            <div class="col-12 text-center link-text-center mb-3">
                <p style="font-size: larger; font-weight: bold;"><a href="<?= base_url('download/instructivo.pdf'); ?>" target="_blanck">Instructivo de Registro y Carga de Saldo</a></p>
            </div>
        </div>
        <div class="row form-center">
            <div class="col-6 col-md-5 col-xl-4 my-3">
                <?= form_open(current_url()); ?>
                <div class="form-group mb-4">
                    <input type="number" class="form-control" placeholder="Ingrese su DNI" name="documento">
                </div>
                <div class="form-group mb-4">
                    <input type="password" class="form-control" placeholder="Su contraseña" name="password">
                </div>
                <?php if ($this->session->flashdata('error') != null) : ?>
                <div class="alert alert-danger"><?= $this->session->flashdata('error'); ?></div>
                <?php endif; ?>
                <?php if ($this->session->flashdata('success') != null) : ?>
                <div class="alert alert-success"><?= $this->session->flashdata('success'); ?></div>
                <?php endif; ?>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success">Ingresar</button>
                    <a href="<?= base_url('usuario/recovery'); ?>" class="btn btn-primary mt-2">Restablecer
                        contraseña</a>
                </div>
                <?= form_close(); ?>
            </div>
        </div>
    </div>

</body>
</html>