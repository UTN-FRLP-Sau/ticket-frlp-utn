<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= $titulo; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <style>
        body, html {
            height: 100%;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
        }
        .container {
            flex: 1;
        }
    </style>
</head>
<body>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Cambiar Correo de Contacto</h3>
                    </div>
                    <div class="card-body">
                        
                        <?php if (isset($error) && !empty($error)) : ?>
                            <div class="alert alert-danger" role="alert">
                                <?= $error; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($this->session->flashdata('error') != null) : ?>
                            <div class="alert alert-danger" role="alert">
                                <?= $this->session->flashdata('error'); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($this->session->flashdata('success') != null) : ?>
                            <div class="alert alert-success" role="alert">
                                <?= $this->session->flashdata('success'); ?>
                            </div>
                        <?php endif; ?>

                        <?= form_open('admin/cambiar_correo_contacto'); ?>
                            <div class="mb-3">
                                <label for="correo_actual" class="form-label">Correo Actual</label>
                                <input type="email" class="form-control" id="correo_actual" value="<?= htmlspecialchars($configuracion->correo_contacto); ?>" disabled>
                            </div>
                            <div class="mb-3">
                                <label for="nuevo_correo" class="form-label">Nuevo Correo</label>
                                <input type="email" class="form-control" id="nuevo_correo" name="nuevo_correo" value="<?= set_value('nuevo_correo'); ?>" required>
                                <?= form_error('nuevo_correo', '<div class="text-danger mt-1">', '</div>'); ?>
                            </div>
                            
                            <div class="d-flex justify-content-center">
                                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                            </div>
                        <?= form_close(); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>