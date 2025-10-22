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
            justify-content: center;
            text-align: center;
        }

    
        .form-inline {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }

        @media (min-width: 576px) {
            .form-inline {
                flex-direction: row;
                justify-content: center;
            }
            .form-inline .form-control {
                margin-bottom: 0 !important;
                margin-right: 0.5rem;
                max-width: 250px;
            }
        }
        
    
        .form-group.row .col-form-label {
            text-align: right;
        }
    </style>
</head>
<body>
    <?php
    // These PHP arrays should be defined here or included from another file.
    // Keeping them here for completeness based on your provided snippet.
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

    <div class="container mt-4">
        <div class="row form-center text-center">
            <h2>Ingrese un Documento a buscar</h2>
            <!-- Bloque para mostrar mensajes flash -->
            <?php if ($this->session->flashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $this->session->flashdata('success'); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php elseif ($this->session->flashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $this->session->flashdata('error'); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php elseif ($this->session->flashdata('info')): ?>
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <?= $this->session->flashdata('info'); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <div class="col-8 col-md-6 col-xl-5 my-3">
                <?= validation_errors('<div><p class="text-center alert alert-danger">', '</p></div>'); ?>
                <?= form_open(current_url()); ?>
                <div class="row form-group mb-4 form-inline">
                    <input type="number" class="mb-2 form-control" placeholder="Ingrese DNI" name="numeroDni">
                    <button type="submit" class="btn btn-success">Buscar</button>
                </div>
                <?= form_close(); ?>
            </div>
        </div>
        <?php if ((isset($usuario)) && ($usuario != FALSE)) : ?>
        <div class="row form-center">
            <div class="col-11 col-md-7 col-xl-5"> 
                <h2> <?= ucwords($usuario->tipo) ?>: <?= strtoupper($usuario->apellido) ?>,
                    <?= ucwords($usuario->nombre) ?>
                </h2>
                <form action="<?= base_url('admin/cargar_saldo'); ?>" method="post">
                    <div class="form-group row">
                        <label for="idSaldoActual" class="col-4 col-form-label">Saldo
                            actual:</label>
                        <div class="col-8">
                            <input type="number" readonly class="form-control-plaintext" id="idSaldoActual"
                                value="<?= $usuario->saldo ?>">
                        </div>
                        <label for="idCarga" class="col-4 col-form-label">Carga: </label>
                        <div class="col-4">
                            <input type="number" class="form-control" name="carga" id="carga">
                        </div>
                        <div class="col-4">
                            <select name="metodo_carga" class="form-control">
                                <option value='' selected> ----- </option>
                                <?php if (in_array($this->session->userdata('admin_lvl'), [1])) : ?>
                                <option value="reintegro">Reintegro</option>
                                <option value="compra">Compra</option>
                                <option value="error">Error</option>
                                <?php endif; ?>
                                <option value="Efectivo">Efectivo</option>
                                <option value="Virtual">Virtual</option>
                            </select>
                        </div>

                        <label class="col-4 col-form-label">Legajo:</label>
                        <div class="col-8">
                            <input type="text" readonly class="form-control-plaintext" value="<?= $usuario->legajo ?>">
                        </div>
                        <label class="col-4 col-form-label">DNI:</label>
                        <div class="col-8">
                            <input type="text" readonly name='dni' class="form-control-plaintext"
                                value="<?= $usuario->documento ?>">
                        </div>
                        <label class="col-4 col-form-label">Especialidad:</label>
                        <div class="col-8">
                            <input type="text" readonly class="form-control-plaintext"
                                value="<?= $usuario->especialidad ?>">
                        </div>

                        <label class="col-4 col-form-label">E-Mail:</label>
                        <div class="col-8">
                            <input type="text" readonly class="form-control-plaintext" value="<?= $usuario->mail ?>">
                        </div>
                    </div>
                    <div class="mt-3 text-center"> <button type="submit" class="btn btn-success mx-2">Cargar Saldo</button>
                        <a class="btn btn-primary mx-2" href="<?= base_url("admin/modificar_usuario/{$usuario->id}"); ?>"
                            role="button">Modificar usuario</a>
                        <?php if (in_array($this->session->userdata('admin_lvl'), [1])) : ?>
                        <a class="btn btn-info mx-2" href="<?= base_url("admin/compras/usuario/{$usuario->id}"); ?>"
                            role="button">Ver compras</a>
                        <?php endif; ?>
                        <!-- Botón para abrir el modal de cambio de contraseña -->
                        <button type="button" class="btn btn-warning mx-2" data-bs-toggle="modal" data-bs-target="#confirmPasswordResetModal">
                            Cambiar Contraseña
                        </button>
                    </div>
                </form>
            </div>
            </div>

        <?php elseif ((isset($usuario)) && ($usuario == FALSE)) : ?>
        <div class="row form-center">
            <div class="col-sm-8">
                <h2>Ese numero de documento no existe</h2>
                <a class="btn btn-primary" href="<?= base_url('admin/nuevo_usuario'); ?>" role="button">Nuevo
                    usuario</a>
            </div>
        </div>

        <?php endif; ?>

        <?php if ((isset($usuario)) && ($usuario != FALSE)) : ?>
        <!-- Modal de Confirmación -->
        <div class="modal fade" id="confirmPasswordResetModal" tabindex="-1" aria-labelledby="confirmPasswordResetModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmPasswordResetModalLabel">Confirmar Restablecimiento de Contraseña</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-start">
                        <p>¿Estás seguro de que deseas enviar un correo de restablecimiento de contraseña al usuario <strong><?= strtoupper($usuario->apellido) ?>, <?= ucwords($usuario->nombre) ?></strong>?</p>
                        <p>Se enviará un enlace al correo: <strong><?= $usuario->mail ?></strong>.</p>
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