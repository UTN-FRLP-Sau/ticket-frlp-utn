<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo; ?></title>
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
            flex-grow: 1;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <h2 class="text-center mb-4">Cargar Instructivo de Compra</h2>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-body">
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

                        <div class="mb-4 text-center">
                            <?php if ($instructivo_actual) : ?>
                                <p>El instructivo actual se ha cargado. Puedes verlo haciendo clic en el enlace de abajo.</p>
                                <a href="<?= base_url('uploads/instructivo/instructivo_compra.pdf?v=' . date('YmdHis')); ?>" target="_blank" class="btn btn-warning btn-lg">
                                    <i class="bi bi-file-earmark-arrow-down me-2"></i> Ver Instructivo Actual
                                </a>
                            <?php else : ?>
                                <p>No se ha cargado ningún instructivo aún. Sube el archivo a continuación.</p>
                            <?php endif; ?>
                        </div>

                        <hr>

                        <h5 class="card-title text-center mb-3">Subir un nuevo Instructivo</h5>
                        <?= form_open_multipart(base_url('admin/configuracion/instructivo')); ?>
                            <div class="mb-3">
                                <label for="instructivo_pdf" class="form-label">Selecciona el archivo PDF:</label>
                                <input class="form-control" type="file" id="instructivo_pdf" name="instructivo_pdf" accept=".pdf" required>
                                <div class="form-text">
                                    El archivo debe ser un PDF y no exceder los 20MB.
                                </div>
                            </div>
                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-cloud-arrow-up-fill me-2"></i> Subir Instructivo
                                </button>
                            </div>
                        <?= form_close(); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>