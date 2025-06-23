<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-9">
            <div class="card shadow-lg mb-5 border-0">
                <div id="card-titulo" class="card-header bg-primary text-white text-center py-3">
                    <div><img class="header-logo img-fluid mx-auto d-block" src="<?= base_url('assets/img/utn.png'); ?>" alt="Logo UTN FRLP"></div>
                    <div><h2 class="my-0 fw-bold">Ticket Web - Compra de Viandas - UTN FRLP</h2></div>
                </div>
                <div class="card-body p-4">
                    <div class="alert alert-info d-flex align-items-center mb-4 border-0" role="alert">
                        <i class="bi bi-wallet-fill me-3 fs-4"></i>
                        <div>
                            <strong>Saldo disponible:</strong> $<span id="saldoUsuarioDisplay"><?= number_format($usuario->saldo, 2) ?></span> | Seleccione las viandas que desea adquirir esta semana.
                        </div>
                    </div>

                    <div class="alert alert-primary alert-dismissible fade show mb-4 border-0" role="alert">
                        <i class="bi bi-lightbulb-fill me-2"></i>
                        <strong>Importante:</strong> El saldo se aplicará automáticamente. Si es suficiente, no necesitará pagar adicional. Si es parcial, solo abonará la diferencia.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>

                    <input type="number" id="saldoCuenta" value="<?= $usuario->saldo; ?>" hidden>
                    <input type="number" id="costoVianda" value="<?= $costoVianda; ?>" hidden>

                    <form method="post" action="<?= base_url('usuario/comprar'); ?>" id="formCompraId">
                        <div class="row g-4">
                            <?php $dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes']; ?>
                            <?php foreach ($dias as $key => $dia): ?>
                                <?php
                                    $nroDia = date('N');
                                    $proximo = time() + ((7 - $nroDia + ($key + 1)) * 24 * 60 * 60);
                                    $proxima_fecha = date('d', $proximo);
                                    $proxima_fecha_ymd = date('Y-m-d', $proximo);

                                    $dia_comprado_mediodia = in_array($proxima_fecha_ymd . '_manana', $comprados);
                                    $dia_comprado_noche = in_array($proxima_fecha_ymd . '_noche', $comprados);
                                    $es_feriado = in_array($proxima_fecha_ymd, array_column($feriados, 'fecha'));
                                ?>

                                <div class="col-sm-6 col-md-6 col-lg-4">
                                    <div class="card h-100 day-option-card <?= $es_feriado ? 'border-danger bg-light-danger' : '' ?>">
                                        <div class="card-header d-flex justify-content-between align-items-center <?= $es_feriado ? 'bg-danger text-white' : 'bg-light' ?> py-2">
                                            <h5 class="mb-0 fw-bold"><?= ucfirst($dia) ?> <span class="text-muted fw-normal fs-6 ms-1"><?= $proxima_fecha ?></span></h5>
                                            <?php if($es_feriado): ?>
                                                <span class="badge bg-warning text-dark"><i class="bi bi-calendar-x me-1"></i>FERIADO</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3 p-2 rounded-3 border bg-white meal-time-block <?= ($dia_comprado_mediodia || $es_feriado) ? 'meal-disabled' : '' ?>">
                                                <div class="form-check custom-checkbox d-flex align-items-center">
                                                    <input class="form-check-input meal-checkbox check-vianda"
                                                           type="checkbox"
                                                           id="check<?= ucfirst($dia) ?>Manana"
                                                           name="check<?= ucfirst($dia) ?>Manana"
                                                           value="manana"
                                                           <?= ($dia_comprado_mediodia) ? 'checked disabled' : '' ?>
                                                           <?= ($es_feriado) ? 'disabled data-es-feriado="true"' : '' ?>>
                                                    <label class="form-check-label fw-bold flex-grow-1" for="check<?= ucfirst($dia) ?>Manana">
                                                        <i class="bi bi-sun me-2"></i>Mediodía
                                                        <?php if($dia_comprado_mediodia): ?>
                                                            <span class="badge bg-secondary ms-2"><i class="bi bi-check-circle me-1"></i>Comprado</span>
                                                        <?php endif; ?>
                                                    </label>
                                                </div>
                                                <div class="vianda-options mt-2 ps-3 <?= ($dia_comprado_mediodia || $es_feriado) ? 'd-none' : '' ?>">
                                                    <select class="form-select form-select-sm" name="selectMenu<?= ucfirst($dia) ?>Manana">
                                                        <option value="Basico">Menú Básico</option>
                                                        <option value="Veggie">Menú Vegetariano</option>
                                                        <option value="Celiaco">Sin TACC</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <hr class="my-3">

                                            <div class="p-2 rounded-3 border bg-white meal-time-block <?= ($dia_comprado_noche || $es_feriado) ? 'meal-disabled' : '' ?>">
                                                <div class="form-check custom-checkbox d-flex align-items-center">
                                                    <input class="form-check-input meal-checkbox check-vianda"
                                                           type="checkbox"
                                                           id="check<?= ucfirst($dia) ?>Noche"
                                                           name="check<?= ucfirst($dia) ?>Noche"
                                                           value="noche"
                                                           <?= ($dia_comprado_noche) ? 'checked disabled' : '' ?>
                                                           <?= ($es_feriado) ? 'disabled data-es-feriado="true"' : '' ?>>
                                                    <label class="form-check-label fw-bold flex-grow-1" for="check<?= ucfirst($dia) ?>Noche">
                                                        <i class="bi bi-moon me-2"></i>Noche
                                                        <?php if($dia_comprado_noche): ?>
                                                            <span class="badge bg-secondary ms-2"><i class="bi bi-check-circle me-1"></i>Comprado</span>
                                                        <?php endif; ?>
                                                    </label>
                                                </div>
                                                <div class="vianda-options mt-2 ps-3 <?= ($dia_comprado_noche || $es_feriado) ? 'd-none' : '' ?>">
                                                    <select class="form-select form-select-sm" name="selectMenu<?= ucfirst($dia) ?>Noche">
                                                        <option value="Basico">Menú Básico</option>
                                                        <option value="Veggie">Menú Vegetariano</option>
                                                        <option value="Celiaco">Sin TACC</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="row mt-5 justify-content-center">
                            <div class="col-md-8 col-lg-6">
                                <div class="card summary-card shadow-sm mb-4">
                                    <div class="card-header bg-success text-white text-center py-3">
                                        <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Resumen de Compra</h5>
                                    </div>
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-muted">Viandas seleccionadas:</span>
                                            <span class="fw-bold fs-5" id="cantidadViandas">0</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-muted">Costo total de viandas:</span>
                                            <strong class="fs-5 text-primary" id="costoDisplay">$0.00</strong>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <span class="text-muted">Saldo aplicado:</span>
                                            <strong class="fs-5 text-success">-$<?= number_format($usuario->saldo, 2) ?></strong>
                                        </div>
                                        <hr class="my-3">
                                        <div class="d-flex justify-content-between align-items-center fw-bold fs-4">
                                            <span>TOTAL A PAGAR:</span>
                                            <span id="totalPagar">$0.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4 justify-content-center">
                            <div class="col-12 col-md-10 col-lg-8">
                                <div class="d-grid gap-3 d-md-flex justify-content-md-center">
                                    <button type="submit" id="btnCompra" class="btn btn-primary btn-lg flex-fill py-3" disabled>
                                        <i class="bi bi-cart-check me-2"></i>Confirmar Compra
                                    </button>
                                    <button type="reset" id="btnReset" class="btn btn-warning btn-lg flex-fill py-3">
                                        <i class="bi bi-arrow-counterclockwise me-2"></i>Reiniciar Selección
                                    </button>
                                    <a href="<?= base_url('usuario/devolver_compra'); ?>" class="btn btn-danger btn-lg flex-fill py-3">
                                        <i class="bi bi-arrow-return-left me-2"></i>Gestionar Devoluciones
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="confirmModalLabel"><i class="bi bi-check-circle me-2"></i>Confirmar Compra</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Está a punto de comprar <strong id="modalCantidad">0</strong> viandas.</p>
                <p>Costo total de viandas: <strong class="text-primary" id="modalCostoTotal">$0.00</strong>.</p>
                <p>Se aplicará su saldo de <strong class="text-success">$<?= number_format($usuario->saldo, 2) ?></strong>.</p>
                <p class="fw-bold fs-5 mt-3">Total a pagar: <span id="modalPagar">$0.00</span></p>
                <p class="mt-4 text-center">¿Desea continuar con esta operación?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle me-1"></i>Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmBuy"><i class="bi bi-check-circle me-1"></i>Confirmar</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    // Actualiza el total a pagar y la cantidad de viandas seleccionadas
    function actualizarTotal() {
        const costoVianda = parseFloat($('#costoVianda').val());
        const saldo = parseFloat($('#saldoCuenta').val());
        // cuntar las viandas seleccionadas que no están deshabilitadas 
        const cantidad = $('.check-vianda:checked:not(:disabled)').length;
        const total = cantidad * costoVianda;
        const aPagar = Math.max(0, total - saldo);
        
        $('#cantidadViandas').text(cantidad);
        $('#costoDisplay').text('$' + total.toFixed(2));
        $('#totalPagar').text('$' + aPagar.toFixed(2));
        
        // Aplica color al total a pagar según si es mayor que 0 o no
        if(aPagar > 0) {
            $('#totalPagar').addClass('text-danger').removeClass('text-success');
        } else {
            $('#totalPagar').removeClass('text-danger').addClass('text-success');
        }

        // Activa o desactiva el boton compra
        if (cantidad > 0) {
            $('#btnCompra').prop('disabled', false);
        } else {
            $('#btnCompra').prop('disabled', true);
        }
    }

    // Alterna la visibilidad de las opciones de vianda al cambiar el estado de los checkboxes
    $('.meal-checkbox').change(function() {
        const optionsDiv = $(this).closest('.meal-time-block').find('.vianda-options');
        if (this.checked) {
            optionsDiv.removeClass('d-none');
            // Activa elementos seleccionados cuando el checkbox está marcado
            optionsDiv.find('select').prop('disabled', false);
        } else {
            optionsDiv.addClass('d-none');
            // Desactiva elementos seleccionados cuando el checkbox  no está marcado
            optionsDiv.find('select').prop('disabled', true);
        }
        actualizarTotal(); // Actualiza el total al cambiar el estado del checkbox
    });
    
    // Actualiza el total al cambiar las opciones de vianda
    $('.form-select').change(actualizarTotal);

    // Inicializa el total al cargar la página
    actualizarTotal();

    // Confirmación antes de comprar
    $('#btnCompra').click(function(e) {
        e.preventDefault();
        const cantidad = $('.check-vianda:checked:not(:disabled)').length; // Cuenta solo las viandas seleccionadas que no están deshabilitadas
        const costoTotalViandas = cantidad * parseFloat($('#costoVianda').val());
        const saldoUsuario = parseFloat($('#saldoCuenta').val());
        const totalAPagar = Math.max(0, costoTotalViandas - saldoUsuario);

        if(cantidad === 0) {
            alert('Por favor seleccione al menos una vianda para comprar.');
            return;
        }
        
        $('#modalCantidad').text(cantidad);
        $('#modalCostoTotal').text('$' + costoTotalViandas.toFixed(2));
        $('#modalPagar').text('Total a pagar: $' + totalAPagar.toFixed(2));
        
        // Aplica color al total a pagar según si es mayor que 0 o no
        if (totalAPagar > 0) {
            $('#modalPagar').addClass('text-danger').removeClass('text-success');
        } else {
            $('#modalPagar').removeClass('text-danger').addClass('text-success');
        }

        $('#confirmModal').modal('show');
    });
    
    // Confirma la compra al hacer clic en el botón de confirmación del modal
    $('#confirmBuy').click(function() {
        $('#formCompraId').submit();
    });

    // Boton resetar
    $('#btnReset').click(function() {
        // Resetear todos los checkboxes
        $('.check-vianda:not(:disabled)').prop('checked', false);
        // Ocultar todas las opciones de vianda
        $('.vianda-options').addClass('d-none');
        // Reactiva todas las opciones de vianda
        $('.vianda-options select').prop('disabled', false);
        actualizarTotal(); // Actualiza el total al resetear
    });

    // Inicialmente oculta las opciones de vianda para checkboxes ya marcados o deshabilitados
    $('.meal-checkbox').each(function() {
        const optionsDiv = $(this).closest('.meal-time-block').find('.vianda-options');
        if (this.checked || $(this).prop('disabled')) {
            optionsDiv.addClass('d-none');
            optionsDiv.find('select').prop('disabled', true);
        }
    });
});
</script>