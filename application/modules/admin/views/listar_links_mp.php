<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Links de Pagos Habilitados</title>
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

    
        .table-responsive {
            margin-top: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .table.table-sm.text-center thead th {
        
            text-align: left;
        }

        .table.table-sm.text-center tbody td {
        
            text-align: left;
        }
        
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            line-height: 1.5;
            border-radius: 0.2rem;
        }

        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }

        .accordion-button {
            background-color: #e9ecef;
            color: #495057;
        }

        .accordion-button:not(.collapsed) {
            color: #000;
            background-color: #e7f1ff;
            box-shadow: inset 0 0px 0 rgba(0, 0, 0, .125);
        }

        .form-control::placeholder {
            color: #6c757d;
            opacity: 1;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col mt-5">
                <h2 class="text-center"># Links de Pagos Habilitados</h2>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="table-responsive">
                    <table class="table table-sm text-center">
                        <thead>
                            <tr>
                                <th style="text-align: left;" class="col">Tipo</th>
                                <th class="col">Monto</th>
                                <th class="col">Link</th>
                                <th style="text-align: right;" class="col"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($links as $link) : ?>
                            <tr>
                                <td style="text-align: left;"><?= $link->tipo_user; ?></td>
                                <td>$ <?= $link->valor; ?></td>
                                <td><a href="<?= $link->link; ?>" target="_blank"><?= $link->link; ?></a></td>
                                <td style="text-align: right;">
                                    <?= form_open(base_url('admin/configuracion/links/rm'), ['style' => 'display:inline;']); ?>
                                        <input type="hidden" name="id_link" value="<?= $link->id; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    <?= form_close(); ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row form-center">
            <h1 class="text-center">Añadir Boton de Pago</h1>
            <div class="col-12">
                <div class="accordion accordion-flush" id="accordionFlushExample">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-headingOne">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                                Añadir un Boton
                            </button>
                        </h2>
                        <div id="flush-collapseOne" class="accordion-collapse collapse" aria-labelledby="flush-headingOne"
                            data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body">
                                <?= form_open(base_url('admin/configuracion/links/add')); ?>
                                <p>Indique tipo de usuario, monto y link:</p>
                                <div class="input-group mb-3">
                                    <select name="tipo_usuario" class="form-control" required>
                                        <option value="" disabled selected>Seleccione un tipo de usuario</option>
                                        <option value="estudiante">Estudiante</option>
                                        <option value="docente">Docente</option>
                                        <option value="no_docente">No Docente</option>
                                    </select>
                                    <input type="int" placeholder="Monto" name="monto" class="form-control" required>
                                    <input type="text" placeholder="Link del Boton" name="link" class="form-control" >
                                    <div class="input-group-append">
                                        <button class="btn btn-success" type="submit">Agregar</button>
                                    </div>
                                </div>
                                <?= form_close(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>