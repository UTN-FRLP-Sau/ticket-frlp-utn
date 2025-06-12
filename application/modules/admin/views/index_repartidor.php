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
        <h2>Ingrese documento para confirmar si realizo la compra:</h2>
        <div class="col-8 col-md-6 col-xl-5 my-3">
            <?= validation_errors('<div><p class="text-center alert alert-danger">', '</p></div>'); ?>
            <?= form_open(base_url('admin/repartidor')); ?>
            <div class="row form-group mb-4 form-inline">
                <input type="number" class="mb-2 form-control" placeholder="Ingrese DNI" name="numeroDni">
                <button type="submit" class="btn btn-success">Buscar</button>
            </div>
            <?= form_close(); ?>
        </div>
    </div>

    <?php if ((isset($usuario)) && (isset($compra)) && ($compra!='')): ?>
    <div class="row form-center">
        <div class="col-sm-2"></div>
        <div class="col-11 col-md-7 col-xl-6">
            <div class="alert alert-success" role="alert">
                <h4 class="alert-heading">El <?= ucwords($usuario->tipo) ?> <?= strtoupper($usuario->apellido) ?>,
                    <?= ucwords($usuario->nombre) ?></h4>
                <p>Realizo una compra para el día de hoy.</p>
                <hr>
                <p class="mb-0">Turno: <strong><?= ucwords($compra->turno) ?></strong></p>
                <hr>
                <p class="mb-0">Menu: <strong><?= ucwords($compra->menu) ?></strong></p>
                <hr>
                <?php if ($compra->retiro == 0) : ?>
                <?= form_open(base_url('admin/repartidor/entregar')); ?>
                <input type="number" readonly class="form-control-plaintext" id="idCompra" name="idCompra"
                    value="<?= $compra->id ?>" hidden>
                <button type="submit" class="btn btn-success mx-2">Entregar</button>
                <?= form_close(); ?>
                <?php else : ?>
                <p class="mb-0">El plato ya fue entregado por : <strong><?= strtoupper($repartidor->apellido) ?>,
                        <?= ucwords($repartidor->nombre) ?></strong></p>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-sm-2"></div>
    </div>

    <?php elseif ((isset($usuario)) && ($usuario == FALSE)) : ?>
    <div class="row form-center">
        <div class="col-sm-2"></div>
        <div class="col-sm-8">
            <div class="alert alert-danger" role="alert">
                <p>No existen usuarios vinculados a ese documento.</p>
            </div>
        </div>
        <div class="col-sm-2"></div>
    </div>

    <?php elseif ((isset($usuario)) && (isset($compra)) && ($compra=='')): ?>
    <div class="row form-center">
        <div class="col-sm-2"></div>
        <div class="col-sm-8">
            <div class="alert alert-danger" role="alert">
                <h4 class="alert-heading"><?= ucwords($usuario->tipo) ?>: <?= strtoupper($usuario->apellido) ?>,
                    <?= ucwords($usuario->nombre) ?></h4>
                <p>No realizo la compra para el dia de la fecha.</p>
            </div>
        </div>
        <div class="col-sm-2"></div>
    </div>

    <?php endif; ?>
</div>