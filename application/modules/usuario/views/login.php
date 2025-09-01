<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Acceso al Comedor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            background-color: #f0f2f5;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .main-content {
            flex-grow: 1;
            padding-top: 20px;
            padding-bottom: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .login-card {
            max-width: 450px;
            width: 100%;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        }

        .logo {
            max-width: 200px;
            height: auto;
            margin-bottom: 1.1rem;
        }
        
        .btn-custom {
            font-size: 1.1rem;
            font-weight: bold;
        }
        
        #btn-instructivo {
            background-color: rgb(250 108 72);
            color: white;
            white-space: nowrap;
            font-size: 1.1rem;
        }
        
        #btn-instructivo:hover {
            background-color: rgb(220 90 60);
        }

        @media (max-width: 767.98px) {
            .logo {
                max-width: 125px;
            }
        }
    </style>
</head>
<body>

    <div class="main-content">
        <div class="container d-flex flex-column align-items-center">
            <div class="row w-100">
                <div class="col-12 text-center">
                    <img class="img-fluid mx-auto d-block logo" src="<?= base_url('assets/img/logo_comedor.png'); ?>" alt="Logo del Comedor">
                </div>
            </div>

            <div class="card login-card mt-3">
                <div class="card-body">
                    <h2 class="card-title text-center mb-4">Iniciar Sesión</h2>
                    <?= form_open(current_url()); ?>
                    <div class="mb-3">
                        <input type="number" class="form-control form-control-lg" placeholder="Ingrese su DNI" name="documento" required>
                    </div>
                    <div class="mb-4">
                        <input type="password" class="form-control form-control-lg" placeholder="Su contraseña" name="password" required>
                    </div>

                    <?php if ($this->session->flashdata('error') != null) : ?>
                    <div class="alert alert-danger"><?= $this->session->flashdata('error'); ?></div>
                    <?php endif; ?>
                    <?php if ($this->session->flashdata('success') != null) : ?>
                    <div class="alert alert-success"><?= $this->session->flashdata('success'); ?></div>
                    <?php endif; ?>

                    <div class="d-grid gap-2 mb-4">
                        <button type="submit" class="btn btn-success btn-lg btn-custom">Ingresar</button>
                        <a href="<?= base_url('usuario/recovery'); ?>" class="btn btn-outline-primary mt-2">Restablecer contraseña</a>
                    </div>
                    <?= form_close(); ?>
                    
                    <hr>

                    <div class="text-center">
                        <p class="text-muted mb-2">¿Aún no tenés cuenta?</p>
                        <div class="d-grid">
                            <a href="<?= base_url('usuario/registro'); ?>" class="btn btn-primary btn-lg">Registrarse</a>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center mt-4">
                        <a href="<?= base_url('uploads/instructivo/instructivo_compra.pdf?v=' . date('YmdHis')); ?>" target="_blank" class="btn btn-lg" id="btn-instructivo">Instructivo Registro y Compra de Viandas</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>