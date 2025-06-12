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

<?php if (in_array($tipo, ['compra'])) : ?>
<div class="row justify-content-center">
    <div class="col-11 col-md-7 col-xl-6 text-center my-5">
        <div class="alert alert-success" role="alert">
            <h1 class="text-center"> Confirmacion de compras </h1>
            <?= form_open(current_url()); ?>
            <?php foreach ($compras as $compra) : ?>
            <div class="input-group mb-3">
                <div class="form-check form-switch">
                    <label class="form-check-label" for="flexSwitchCheckDefault">Compra realizada para el
                        <b><?= $diasSemana[date('l', strtotime($compra->dia_comprado))].', '.date('d', strtotime($compra->dia_comprado)).' de '.$meses[date('F', strtotime($compra->dia_comprado))].' de '.date('Y', strtotime($compra->dia_comprado)); ?></b>
                        Turno: <b><?= $compra->turno; ?></b>
                        (Menu: <b><?= $compra->menu; ?></b>)
                    </label>
                </div>
            </div>
            <?php endforeach; ?>
            <input type="number" readonly class="form-control-plaintext" id="idTransaccion" name="idTransaccion"
                value="<?= $transaccion ?>" hidden>
            <button type="submit" class="btn btn-success" id="btnCompra" class="btn btn-danger mx-3">Aceptar</button>
            <?= form_close(); ?>
        </div>
    </div>
</div>
<?php endif; ?>


<?php if (in_array($tipo, ['devolucion'])) : ?>
<div class="row justify-content-center">
    <div class="col-11 col-md-7 col-xl-6 text-center my-5">
        <div class="alert alert-warning" role="alert">
            <h1 class="text-center"> Confirmacion de devolucion </h1>
            <?= form_open(current_url()); ?>
            <?php foreach ($compras as $compra) : ?>
            <div class="input-group mb-3">
                <div class="form-check form-switch">
                    <label class="form-check-label" for="flexSwitchCheckDefault">Devolucion realizada para el
                        <b><?= $diasSemana[date('l', strtotime($compra->dia_comprado))].', '.date('d', strtotime($compra->dia_comprado)).' de '.$meses[date('F', strtotime($compra->dia_comprado))].' de '.date('Y', strtotime($compra->dia_comprado)); ?></b>
                        Turno: <b><?= $compra->turno; ?></b>
                        (Menu: <b><?= $compra->menu; ?></b>)
                    </label>
                </div>
            </div>
            <?php endforeach; ?>
            <input type="number" readonly class="form-control-plaintext" id="idTransaccion" name="idTransaccion"
                value="<?= $transaccion ?>" hidden>
            <button type="submit" class="btn btn-success" id="btnCompra" class="btn btn-danger mx-3">Aceptar</button>
            <?= form_close(); ?>
        </div>
    </div>
</div>
<?php endif; ?>
