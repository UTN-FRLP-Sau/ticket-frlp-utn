<div class="container">
    <div class="row">
        <div class="col my-3 text-center">
            <input type="number" id="saldoCuenta" value="<?= $usuario->saldo; ?>" hidden>
            <input type="number" id="costoVianda" value="<?= $costoVianda; ?>" hidden>
        </div>
    </div>
    <div class="ticket">
        <div class="row">
            <div class="col mt-3">
                <h5 class="text-center">UTN FRLP Ticket Web - Compra</h5>
            </div>
            <div style="background-color: #e7f3fe; border-left: 6px solid #2196F3; padding: 15px 20px; margin: 20px 0; border-radius: 4px; font-family: Arial, sans-serif; color: #31708f;">
                <p style="margin: 0; font-size: 16px; line-height: 1.5;">
                    <strong>Información importante:</strong> Si usted posee saldo, se aplicará automáticamente en esta compra. 
                    En caso de que el saldo sea suficiente, la operación se completará sin necesidad de realizar un pago adicional.
                    Si el saldo es parcial, solo deberá abonar la diferencia restante mediante su cuenta de Mercado Pago.
                </p>
            </div>
        </div>
        <div class="row">
            <div class="col mt-3">
                <p style="padding: 0px;" class="text-center"><strong> Saldo: $ <?= $usuario->saldo; ?> -. </strong>
                </p>
                <p style="padding: 0px;" class="text-center" id="costoDisplay"><strong>Costo: $ 0.00 -.</strong></p>
            </div>
        </div>
        <div class="row">
            <div class="col my-2">
                <img class="img-fluid mx-auto d-block" src="<?= base_url('assets/img/utn.png'); ?>" alt="">
            </div>
        </div>
        <div class="row text-center">
            <div class="col my-2">
                <form method="post" action="<?= base_url('usuario/comprar'); ?>" id="formCompraId">
                    <?php $dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes']; ?>
                    <?php foreach ($dias as $key => $dia) : ?>
                    <?php
                        $nroDia = date('N');
                        $proximo = time() + ((7 - $nroDia + ($key + 1)) * 24 * 60 * 60);
                        $proxima_fecha = date('d', $proximo);
                        $proxima_fecha_ymd = date('Y-m-d', $proximo); // Formato Y-m-d para comparar
                        
                        // Verificar si el turno de mediodía o noche ya fue comprado para esta fecha
                        $dia_comprado_mediodia = in_array($proxima_fecha_ymd . '_manana', $comprados);
                        $dia_comprado_noche = in_array($proxima_fecha_ymd . '_noche', $comprados);
                        
                        $es_feriado = in_array($proxima_fecha_ymd, array_column($feriados, 'fecha'));
                    ?>

                    <div class="my-1 form-check form-check-inline border p-3 rounded mb-3"> <h5><?= ucwords($dia); ?> <?= $proxima_fecha; ?></h5>
                        <hr>

                        <fieldset id="<?= $dia; ?>_manana_fieldset" <?= ($dia_comprado_mediodia || $es_feriado) ? 'disabled' : ''; ?>>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input check-vianda" id="check<?= ucwords($dia); ?>Manana"
                                    name="check<?= ucwords($dia); ?>Manana" value="manana"
                                    <?= ($dia_comprado_mediodia) ? 'checked' : ''; ?>
                                    <?= ($es_feriado) ? 'data-es-feriado="true"' : ''; ?> >
                                <label class="form-check-label" for="check<?= ucwords($dia); ?>Manana">Mediodía</label>
                            </div>
                            <div class="mt-2" style="margin-left: 20px;">
                                <select class="form-select" name="selectTipo<?= ucwords($dia); ?>Manana" id="selectTipo<?= ucwords($dia); ?>Manana">
                                    <option selected value="Comer aqui"> Comer aquí </option>
                                    <option value="Llevar"> Para llevar </option>
                                </select>
                                <select class="form-select mt-1" name="selectMenu<?= ucwords($dia); ?>Manana" id="selectMenu<?= ucwords($dia); ?>Manana">
                                    <option value="Basico"> Básico </option>
                                    <option value="Veggie"> Veggie </option>
                                    <option value="Celiaco"> Sin TACC </option>
                                </select>
                            </div>
                        </fieldset>

                        <hr>

                        <fieldset id="<?= $dia; ?>_noche_fieldset" <?= ($dia_comprado_noche || $es_feriado) ? 'disabled' : ''; ?>>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input check-vianda" id="check<?= ucwords($dia); ?>Noche"
                                    name="check<?= ucwords($dia); ?>Noche" value="noche"
                                    <?= ($dia_comprado_noche) ? 'checked' : ''; ?>
                                    <?= ($es_feriado) ? 'data-es-feriado="true"' : ''; ?> >
                                <label class="form-check-label" for="check<?= ucwords($dia); ?>Noche">Noche</label>
                            </div>
                            <div class="mt-2" style="margin-left: 20px;">
                                <select class="form-select" name="selectTipo<?= ucwords($dia); ?>Noche" id="selectTipo<?= ucwords($dia); ?>Noche">
                                    <option selected value="Comer aqui"> Comer aquí </option>
                                    <option value="Llevar"> Para llevar </option>
                                </select>
                                <select class="form-select mt-1" name="selectMenu<?= ucwords($dia); ?>Noche" id="selectMenu<?= ucwords($dia); ?>Noche">
                                    <option value="Basico"> Básico </option>
                                    <option value="Veggie"> Veggie </option>
                                    <option value="Celiaco"> Sin TACC </option>
                                </select>
                            </div>
                        </fieldset>

                    </div>
                    <?php endforeach; ?>
                    <div class="form-check">
                        <div class="btn-group" role="group" aria-label="Basic example">
                            <button type="submit" id="btnCompra" class="btn btn-success mx-3">Comprar</button>
                            <button type="reset" id="btnReset" class="btn btn-warning mx-3">Reset</button>
                            <a href=" <?= base_url('usuario/devolver_compra'); ?>"
                                class="btn btn-danger mx-3">Devolver</a>
                        </div>
                    </div>
                    <div id="totalCompra"></div>
                </form>
            </div>
        </div>
    </div>
    <script src="<?= base_url('assets/js/scripts.js'); ?>"></script>
</div>