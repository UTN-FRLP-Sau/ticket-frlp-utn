<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Documento</title>
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
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        /* Estilo para el formulario de búsqueda (DNI + Botón) */
        .search-form-group {
            display: flex;
            gap: 0.5rem;
            width: 100%;
            max-width: 450px;
            padding: 0 15px;
            flex-direction: column;
        }
        .search-form-group .btn {
            width: 100%;
        }
        @media (min-width: 576px) {
            .search-form-group {
                flex-direction: row;
                align-items: center;
            }
            .search-form-group input {
                flex-grow: 1;
            }
            .search-form-group .btn {
                width: auto;
            }
        }
        /* Estilos para las etiquetas en el formulario de datos del usuario */
        .user-data-form .col-form-label {
            text-align: left;
            font-weight: 500;
        }
        @media (min-width: 576px) {
            .user-data-form .col-form-label {
                text-align: right;
            }
        }
        .user-data-form .form-control-plaintext {
            text-align: left;
            padding-left: 0.75rem;
        }
        /* Estilo para el grupo de Carga/Método */
        .carga-method-group {
            display: flex;
            gap: 0.5rem;
            align-items: stretch;
        }
        .carga-method-group input {
            flex-grow: 1;
            min-width: 100px;
        }
        .carga-method-group select {
            flex-shrink: 0;
            width: 130px;
        }
    </style>
</head>
<body>
    <?php
    $diasSemana=[
        "Monday"    => "Lunes",
        "Tuesday"   => "Martes",
        "Wednesday" => "Miércoles",
        "Thursday"  => "Jueves",
        "Friday"    => "Viernes",
        "Saturday"  => "Sábado",
        "Sunday"    => "Domingo"
    ];
    $meses=[
        "January"   => "Enero",
        "February"  => "Febrero",
        "March"     => "Marzo",
        "April"     => "Abril",
        "May"       => "Mayo",
        "June"      => "Junio",
        "July"      => "Julio",
        "August"    => "Agosto",
        "September" => "Septiembre",
        "October"   => "Octubre",
        "November"  => "Noviembre",
        "December"  => "Diciembre"
    ];
    ?>
    <div class="container mt-4 mb-5">
        <div class="row form-center">
            <div class="col-12 text-center mb-3">
                <h2>Ingrese un Documento a buscar</h2>
            </div>
            <div class="col-12 col-md-8 col-lg-6">
                <?php
                $status = $this->input->get('status');
                $msg = $this->input->get('msg');
                if ($status && $msg):
                    $alert_class = ($status == 'success') ? 'alert-success' : 'alert-danger';
                ?>
                    <div class="alert <?= $alert_class ?> alert-dismissible fade show" role="alert">
                        <?= urldecode($msg); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                <?php if (function_exists('validation_errors')): ?>
                    <?= validation_errors('<div class="alert alert-danger text-center">', '</div>'); ?>
                <?php endif; ?>
            </div>
            <div class="col-12 d-flex justify-content-center my-3">
                <div class="search-form-group">
                    <?php if (function_exists('form_open')): ?>
                        <?= form_open(current_url(), ['class' => 'd-flex flex-column flex-sm-row w-100 align-items-center gap-2']); ?>
                            <input type="number" class="form-control" placeholder="Ingrese DNI" name="numeroDni" required>
                            <button type="submit" class="btn btn-success flex-shrink-0"><i class="bi bi-search me-1"></i>Buscar</button>
                        <?= form_close(); ?>
                    <?php else: ?>
                        <form action="" method="post" class="d-flex flex-column flex-sm-row w-100 align-items-center gap-2">
                            <input type="number" class="form-control" placeholder="Ingrese DNI" name="numeroDni" required>
                            <button type="submit" class="btn btn-success flex-shrink-0"><i class="bi bi-search me-1"></i>Buscar</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php if ((isset($usuario)) && ($usuario != FALSE)) : ?>
        <hr>
        <div class="row form-center mt-4">
            <div class="col-11 col-md-8 col-lg-6">
                <h2 class="mb-4">
                    <?= ucwords($usuario->tipo ?? 'Usuario') ?>:
                    <span class="text-primary"><?= strtoupper($usuario->apellido) ?>, <?= ucwords($usuario->nombre) ?></span>
                </h2>
                <form action="<?= base_url('admin/cargar_saldo'); ?>" method="post" class="user-data-form">
                    <div class="row mb-3 align-items-center">
                        <label for="idSaldoActual" class="col-4 col-form-label">Saldo Actual:</label>
                        <div class="col-8">
                            <input type="text" readonly class="form-control-plaintext fw-bold" id="idSaldoActual"
                                value="$<?= number_format($usuario->saldo ?? 0, 2, ',', '.') ?>">
                        </div>
                    </div>
                    <div class="row mb-3 align-items-center">
                        <label for="carga" class="col-4 col-form-label">Carga: </label>
                        <div class="col-8">
                            <div class="carga-method-group">
                                <input type="number" class="form-control" name="carga" id="carga" placeholder="Monto" min="1" step="0.01" required>
                                <select name="metodo_carga" class="form-select">
                                    <option value='' selected> Método </option>
                                    <?php if (isset($this->session) && in_array($this->session->userdata('admin_lvl'), [1])) : ?>
                                    <option value="reintegro">Reintegro</option>
                                    <option value="compra">Compra</option>
                                    <option value="error">Error</option>
                                    <?php endif; ?>
                                    <option value="Efectivo">Efectivo</option>
                                    <option value="Virtual">Virtual</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2 align-items-center">
                        <label class="col-4 col-form-label">Legajo:</label>
                        <div class="col-8">
                            <input type="text" readonly class="form-control-plaintext" value="<?= $usuario->legajo ?>">
                        </div>
                    </div>
                    <div class="row mb-2 align-items-center">
                        <label class="col-4 col-form-label">DNI:</label>
                        <div class="col-8">
                            <input type="text" readonly name='dni' class="form-control-plaintext"
                                value="<?= $usuario->documento ?>">
                        </div>
                    </div>
                    <div class="row mb-2 align-items-center">
                        <label class="col-4 col-form-label">Especialidad:</label>
                        <div class="col-8">
                            <input type="text" readonly class="form-control-plaintext"
                                value="<?= $usuario->especialidad ?>">
                        </div>
                    </div>
                    <div class="row mb-2 align-items-center">
                        <label class="col-4 col-form-label">E-Mail:</label>
                        <div class="col-8">
                            <input type="text" readonly class="form-control-plaintext" value="<?= $usuario->mail ?>">
                        </div>
                    </div>
                    <hr class="mt-4 mb-3">
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center flex-wrap">
                        <button type="submit" class="btn btn-success"><i class="bi bi-plus-circle me-1"></i>Cargar Saldo</button>
                        <a class="btn btn-primary" href="<?= base_url("admin/modificar_usuario/{$usuario->id}"); ?>"
                            role="button"><i class="bi bi-pencil-square me-1"></i>Modificar usuario</a>
                        <?php if (in_array($this->session->userdata('admin_lvl'), [1])) : ?>
                        <a class="btn btn-info" href="<?= base_url("admin/compras/usuario/{$usuario->id}"); ?>"
                            role="button"><i class="bi bi-cart-check me-1"></i>Ver compras</a>
                        <?php endif; ?>
                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#confirmPasswordResetModal">
                            <i class="bi bi-key-fill me-1"></i>Cambiar Contraseña
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <?php elseif ((isset($usuario)) && ($usuario == FALSE)) : ?>
        <hr>
        <div class="row form-center mt-5">
            <div class="col-12 col-md-8 col-lg-6 text-center">
                <div class="alert alert-danger" role="alert">
                    <h2>Ese número de documento no existe</h2>
                </div>
                <a class="btn btn-primary btn-lg mt-3" href="<?= base_url('admin/nuevo_usuario'); ?>" role="button"><i class="bi bi-person-plus me-2"></i>Nuevo usuario</a>
            </div>
        </div>
        <?php endif; ?>
        <?php if ((isset($usuario)) && ($usuario != FALSE)) : ?>
        <div class="modal fade" id="confirmPasswordResetModal" tabindex="-1" aria-labelledby="confirmPasswordResetModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmPasswordResetModalLabel"><i class="bi bi-exclamation-triangle-fill text-warning me-2"></i> Confirmar Restablecimiento de Contraseña</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-start">
                        <p>Estás a punto de enviar un correo de restablecimiento de contraseña al usuario <strong><?= strtoupper($usuario->apellido) ?>, <?= ucwords($usuario->nombre) ?></strong>.</p>
                        <p class="mb-0">Se enviará un enlace de recuperación al correo: <strong class="text-primary"><?= $usuario->mail ?></strong>.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <a href="<?= base_url('admin/vendedor/triggerPasswordRecovery/' . $usuario->documento) ?>" class="btn btn-warning">Aceptar y Enviar Correo</a>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>