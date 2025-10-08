<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Descargar Informes</title>
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

        .accordion {
            margin-bottom: 2rem;
        }

        .accordion-button {
            font-weight: bold;
        }

        .accordion-body {
            padding: 1.5rem;
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

        .input-group p {
            margin-bottom: 0.5rem;
            width: 100%;
        }

        .input-group-prepend .input-group-text {
            background-color: #e9ecef;
            border-right: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row form-center">
            <h1 class="text-center"> Descargar Informes</h1>
            <div class="col-11 col-md-9 col-lg-7">
                <div class="accordion accordion-flush" id="accordionFlushExample">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-headingOne">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#flush-collapseOne" aria-expanded="false"
                                aria-controls="flush-collapseOne">
                                Caja del dia
                            </button>
                        </h2>
                        <div id="flush-collapseOne" class="accordion-collapse collapse"
                            aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body">
                                <?= form_open(base_url('admin/informe/diario')); ?>
                                <p>Indique la fecha:</p>
                                <div class="input-group mb-3">
                                    <input type="date" value="<?= date('Y-m-d') ?>" name="cierre_fecha"
                                        class="form-control" value="<?= date('d-m-Y') ?>">
                                    <div class="input-group-append">
                                        <button class="btn btn-success" formtarget="_blanck"
                                            type="submit">Descargar</button>
                                    </div>
                                </div>
                                <?= form_close(); ?>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#flush-collapseTwo" aria-expanded="false"
                                aria-controls="flush-collapseTwo">
                                Resumen de caja semanal
                            </button>
                        </h2>
                        <div id="flush-collapseTwo" class="accordion-collapse collapse"
                            aria-labelledby="flush-headingTwo" data-bs-parent="#accordionFlushExample">
                            <?= form_open(base_url('admin/informe/semana')); ?>
                            <div class="input-group mb-3">
                                <p>Indique las fechas entre las que desea el informe (No se mostraran los dias que no
                                    existan cargas):</p>
                                <div class="col-6">
                                    <div class="input-group-prepend">
                                        <span class="fw-bold border-0 input-group-text">Desde el </span>
                                    </div>
                                    <input type="date" name="cierre_fecha_1" class="form-control">
                                </div>
                                <div class="col-6">
                                    <div class="input-group-prepend">
                                        <span class="fw-bold border-0 input-group-text">hasta el
                                        </span>
                                    </div>
                                    <input type="date" name="cierre_fecha_2" class="form-control">
                                </div>
                                <div class="input-group-append">
                                    <button class="btn btn-success" formtarget="_blanck"
                                        type="submit">Descargar</button>
                                </div>
                            </div>
                            <?= form_close(); ?>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#flush-collapseThree" aria-expanded="false"
                                aria-controls="flush-collapseThree">
                                Resumen de pedidos semanales
                            </button>
                        </h2>
                        <div id="flush-collapseThree" class="accordion-collapse collapse"
                            aria-labelledby="flush-headingThree" data-bs-parent="#accordionFlushExample">
                            <?= form_open(base_url('admin/informe/pedido')); ?>
                            <div class="input-group">
                                <p>Indique las fechas entre las que desea el informe (No se mostraran los dias que no
                                    existan compras):</p>
                                <div class="col-6">
                                    <div class="input-group-prepend">
                                        <span class="fw-bold border-0 input-group-text">Desde el </span>
                                    </div>
                                    <input type="date" name="semana_fecha_1" class="form-control">
                                </div>
                                <div class="col-6">
                                    <div class="input-group-prepend">
                                        <span style="background-color: #f7f7f7;"
                                            class="fw-bold border-0 input-group-text">hasta el
                                        </span>
                                    </div>
                                    <input type="date" name="semana_fecha_2" class="form-control">
                                </div>
                                <div class="input-group-append">
                                    <button class="btn btn-success" formtarget="_blanck"
                                        type="submit">Descargar</button>
                                </div>
                            </div>
                            <?= form_close(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>