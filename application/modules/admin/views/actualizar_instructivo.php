<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructivo de Login</title>
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
            flex-direction: row;
        }

        .alert {
            margin-top: 1rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col my-4">
                <h1>Instructivo de Login</h1>
            </div>
        </div>
        <div class="row justify-content-center">
            <?php if (isset($subidoCorrecto)) : ?>
            <div class="col-5 col-md-4 alert alert-success alert-dismissible fade show text-center" role="alert">
                <p> El instructivo se actualizó correctamente </p>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>
            <?php if (isset($subidoError)) : ?>
            <div class="col-5 col-md-4 alert alert-danger alert-dismissible fade show text-center" role="alert">
                <p> <?= $subidoError ?> </p>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>
            <h5 class="col-12 text-center">
                Instructivo vigente: <a href="<?= base_url('uploads/instructivo_login.pdf'); ?>" target="_blanck">ver PDF actual</a>
            </h5>
            <form action="<?= base_url('admin/instructivo'); ?>" method="post" enctype="multipart/form-data">
                <div class="row d-flex justify-content-center align-items-stretch flex-row form-center g-3">
                    <div class="col-12 col-md-6">
                        <input class="form-control" type="file" name="archivo_instructivo" accept="application/pdf">
                    </div>
                </div>
                <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-primary mt-2"> Subir Nuevo Instructivo </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
