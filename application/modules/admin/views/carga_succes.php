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

<div class="row justify-content-center">
    <div class="col-11 col-md-7 col-xl-6 text-center my-5">
        <div class="alert alert-success" role="alert">
            <h4 class="alert-heading">Confirmacion de carga a <strong><?= strtoupper($apellido) ?>,
                    <?= ucwords($nombre) ?></strong></h4>
            <p>Se le acredito la carga de <strong>$ <?= ucwords($monto) ?>-.</strong></p>
            <?= form_open(current_url()); ?>
            <input type="number" readonly class="form-control-plaintext" id="idTransaccion" name="idTransaccion"
                value="<?= $transaccion ?>" hidden>
            <button type="submit" class="btn btn-success">Confirmar</button>
            <?= form_close(); ?>
        </div>
    </div>
</div>

