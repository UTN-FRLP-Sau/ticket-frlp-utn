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
                        <strong>Información importante:</strong> Si usted posee saldo a favor, se aplicará automáticamente en esta compra.  
                        En caso de que el saldo sea suficiente, la operación se completará sin necesidad de realizar un pago adicional.  
                        Si el saldo es parcial, solo deberá abonar la diferencia restante mediante su cuenta de Mercado Pago.
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col mt-3">
                    <p style="padding: 0px;" class="text-center"><strong> Saldo: $ <?= $usuario->saldo; ?> -. </strong>
                    </p>
                    <p style="padding: 0px;" class="text-center" id="compra"><strong>Costo:</strong></h2>
                    </p>
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
                            ?>

                        <div class="my-1 form-check form-check-inline">
                            <fieldset
                                <?= (in_array(date('Y-m-d', $proximo), array_column($comprados, 'dia_comprado'))) || (in_array(date('Y-m-d', $proximo), array_column($feriados, 'fecha'))) ? 'disabled' : ''; ?>>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="check<?= ucwords($dia); ?>"
                                        name="check<?= ucwords($dia); ?>" value="<?= ucwords($dia); ?>"
                                        <?= (in_array(date('Y-m-d', $proximo), array_column($comprados, 'dia_comprado'))) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="check<?= ucwords($dia); ?>">
                                        <?= ucwords($dia); ?> <?= $proxima_fecha; ?>
                                    </label>
                                </div>
                                <fieldset id="<?= $dia; ?>" disabled>
                                    <div hidden>
                                        <select class="form-select" name="selectTipo<?= ucwords($dia); ?>"
                                            id="selectTipo<?= ucwords($dia); ?>">
                                            <option selected value="Comer aqui"> Comer aqui </option>
                                            <option value="Llevar"> Para llevar </option>
                                        </select>
                                    </div>
                                    <div>
                                        <select class="form-select" name="selectTurno<?= ucwords($dia); ?>"
                                            id="selectTurno<?= ucwords($dia); ?>">
                                            <option value="mañana"> Medio día</option>
                                            <option value="noche"> Noche </option>
                                        </select>
                                    </div>
                                    <div>
                                        <select class="form-select" name="selectMenu<?= ucwords($dia); ?>"
                                            id="selectMenu<?= ucwords($dia); ?>">
                                            <option value="Basico"> Básico </option>
                                            <option value="Veggie"> Veggie </option>
                                            <option value="Celiaco"> Sin TACC </option>
                                        </select>
                                    </div>
                                </fieldset>
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