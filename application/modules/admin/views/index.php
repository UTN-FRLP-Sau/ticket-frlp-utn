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

<div class="container" style="min-height: 25em;">
    <div class="row form-center text-center">
        <h2>Ingrese un Documento a buscar</h2>
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
        <div class="col-sm-2"></div>
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
                <div>
                    <button type="submit" class="btn btn-success mx-2">Cargar Saldo</button>
                    <a class="btn btn-primary mx-2" href="<?= base_url("admin/modificar_usuario/{$usuario->id}"); ?>"
                        role="button">Modificar usuario</a>
                    <?php if (in_array($this->session->userdata('admin_lvl'), [1])) : ?>
                    <a class="btn btn-info mx-2" href="<?= base_url("admin/compras/usuario/{$usuario->id}"); ?>"
                        role="button">Ver compras</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        <div class="col-sm-2"></div>
    </div>

    <?php elseif ((isset($usuario)) && ($usuario == FALSE)) : ?>
    <div class="row form-center">
        <div class="col-sm-2"></div>
        <div class="col-sm-8">
            <h2>Ese numero de documento no existe</h2>
            <a class="btn btn-primary" href="<?= base_url('admin/nuevo_usuario'); ?>" role="button">Nuevo
                usuario</a>
        </div>
        <div class="col-sm-2"></div>
    </div>

    <?php endif; ?>
</div>