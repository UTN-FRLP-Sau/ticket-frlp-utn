<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Legajos Inconsistentes</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fc;
        }
        .container {
            margin-top: 10px;
        }
        .card {
            border-left: .25rem solid #e74a3b!important;
        }
        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
        }
        .table-responsive {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-danger">Legajos Inconsistentes</h6>
        </div>
        <div class="card-body">
            <p class="text-muted">
                Estudiantes activos con legajo fuera del rango válido (mayor a 900000 o menor a 20000).
                Corregí el legajo desde "Modificar usuario" para que dejen de aparecer en este listado.
            </p>
            <div class="mb-3">
                <input type="text" class="form-control" id="legajoSearch" placeholder="Buscar por nombre, apellido, documento o legajo...">
            </div>
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Legajo</th>
                            <th>Documento</th>
                            <th>Apellido</th>
                            <th>Nombre</th>
                            <th>E-Mail</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($usuarios)) : ?>
                            <tr>
                                <td colspan="6" class="text-center">No hay estudiantes con legajo inconsistente.</td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ($usuarios as $usuario) : ?>
                                <tr>
                                    <td><?= $usuario->legajo ?></td>
                                    <td><?= htmlspecialchars($usuario->documento, ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($usuario->apellido, ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($usuario->nombre, ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($usuario->mail, ENT_QUOTES, 'UTF-8') ?></td>
                                    <td>
                                        <a href="<?= base_url('admin/modificar_usuario/' . $usuario->id) ?>" class="btn btn-primary btn-sm">
                                            Modificar usuario
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    $(document).ready(function() {
        $("#legajoSearch").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#dataTable tbody tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
</script>

</body>
</html>
