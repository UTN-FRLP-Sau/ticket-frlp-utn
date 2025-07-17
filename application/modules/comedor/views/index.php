<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<?php if ($this->session->has_userdata('error_compra')): ?>
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-4 shadow-sm border-0 py-3 px-4" role="alert" style="font-size: 1.05rem;">
        <div class="flex-shrink-0">
            <i class="bi bi-exclamation-triangle-fill fs-2 text-danger"></i>
        </div>
        <div class="flex-grow-1">
            <h6 class="alert-heading fw-bold mb-2 text-danger">¡Atención!</h6>
            <?php foreach ($this->session->userdata('error_compra') as $error): ?>
                <p class="mb-2"><?= $error; ?></p>
            <?php endforeach; ?>
            <button type="button" class="btn btn-outline-danger btn-sm px-4 fw-semibold mt-2 shadow-sm" id="btnAbrirModalRetomarPago">
                <i class="bi bi-currency-dollar me-2"></i>Retomar Pago Pendiente
            </button>
        </div>
        <button type="button" class="btn-close ms-3" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
<?php endif; ?>

<?php if ($this->session->flashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-start gap-3" role="alert">
        <i class="bi bi-check-circle-fill fs-4 mt-1"></i>
        <div>
            <h6 class="alert-heading fw-semibold mb-2">¡Operación exitosa!</h6>
            <p class="mb-1"><?= $this->session->flashdata('success'); ?></p>
        </div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
<?php endif; ?>

<?php if ($this->session->flashdata('info')): ?>
    <div class="alert alert-info alert-dismissible fade show d-flex align-items-start gap-3" role="alert">
        <i class="bi bi-info-circle-fill fs-4 mt-1"></i>
        <div>
            <h6 class="alert-heading fw-semibold mb-2">Información</h6>
            <p class="mb-1"><?= $this->session->flashdata('info'); ?></p>
        </div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
<?php endif; ?>

<?php if ($this->session->flashdata('error_message')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <?= $this->session->flashdata('error_message'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
<?php endif; ?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-9">
            <div class="card shadow-lg mb-5 border-0">
                <div id="card-titulo" class="card-header bg-primary text-white text-center py-3">
                    <div><img class="header-logo img-fluid mx-auto d-block" src="<?= base_url('assets/img/utn.png'); ?>" alt="Logo UTN FRLP"></div>
                    <div><h2 class="my-0 fw-bold">Ticket Web - Compra de Viandas - UTN FRLP</h2></div>
                </div>
                <div class="card-body p-4">
                    <div id="tarjetaSaldo" class="alert alert-info d-flex align-items-center mb-4 border-0" role="alert">
                        <i class="bi bi-wallet-fill me-3 fs-4"></i>
                        <div>
                            <strong>Saldo disponible:</strong> $<span id="saldoUsuarioDisplay"><?= number_format($usuario->saldo, 2) ?></span>
                        </div>
                    </div>

                    <div class="alert alert-primary alert-dismissible fade show mb-4 border-0" role="alert">
                        <i class="bi bi-lightbulb-fill me-2"></i>
                        <strong>Importante:</strong> El saldo de tu cuenta se aplicará automáticamente para pagar la vianda. Si es suficiente, no necesitarás pagar adicionalmente. Si es parcial, solo abonarás la diferencia directamente a través de MercadoPago.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <div class="alert alert-primary alert-dismissible fade show mb-4 border-0" role="alert">
                        <i class="bi bi-lightbulb-fill me-2"></i>
                        <strong>Importante:</strong> La elección de una vianda turno mediodía o noche es excluyente. Solo podrás seleccionar la vianda de un solo turno en un mismo día.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <input type="number" id="saldoCuenta" value="<?= $usuario->saldo; ?>" hidden>
                    <input type="number" id="costoVianda" value="<?= $costoVianda; ?>" hidden>

                    <form method="post" action="<?= base_url('usuario/comprar'); ?>" id="formCompraId">
                        <div class="accordion mb-4" id="accordionSemanas">
                            <?php foreach ($weeksData as $week): ?>
                                <?php
                                    $weekIndex = $week['week_index'];
                                    $weekDays = $week['days'];
                                    $weekStartDateDisplay = $week['week_start_date_display'];
                                    $weekEndDateDisplay = $week['week_end_date_display'];

                                    $weekTitle = $weekIndex === 0
                                    ? '<strong>Esta Semana</strong>'
                                    : '<span class="d-none d-md-inline">Semana del&nbsp;</span><strong>' . $weekStartDateDisplay . '</strong>&nbsp;al&nbsp;<strong>' . $weekEndDateDisplay . '</strong>';



                                    $collapseId = "collapseSemana" . $weekIndex;
                                    $headingId = "headingSemana" . $weekIndex;
                                    $isFirst = $weekIndex === 0;
                                ?>
                                <div class="accordion-item mb-3 shadow-sm border">
                                    <h2 class="accordion-header" id="<?= $headingId ?>">
                                        <button class="accordion-button <?= $isFirst ? '' : 'collapsed' ?>" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#<?= $collapseId ?>"
                                                aria-expanded="<?= $isFirst ? 'true' : 'false' ?>"
                                                aria-controls="<?= $collapseId ?>">
                                            <?= $weekTitle ?>
                                        </button>
                                    </h2>
                                    <div id="<?= $collapseId ?>" class="accordion-collapse collapse <?= $isFirst ? 'show' : '' ?>"
                                        aria-labelledby="<?= $headingId ?>">
                                        <div class="accordion-body p-3">
                                            <div class="days-carousel-container d-flex overflow-auto pb-3">
                                                <?php foreach ($weekDays as $dayData): ?>
                                                    <?php
                                                        $date_ymd = $dayData['date_ymd'];
                                                        $dayName = $dayData['day_name'];
                                                        $date_display = $dayData['date_display'];
                                                        $comprado_mediodia = $dayData['comprado_mediodia'];
                                                        $comprado_noche = $dayData['comprado_noche'];
                                                        $comprado_mediodia_menu = $dayData['comprado_mediodia_menu'];
                                                        $comprado_noche_menu = $dayData['comprado_noche_menu'];
                                                        $es_feriado = $dayData['es_feriado'];
                                                        $es_receso_invernal = $dayData['es_receso_invernal'] ?? false;
                                                        $es_pasado = $dayData['es_pasado'];

                                                        $day_purchased_any_turn = $comprado_mediodia || $comprado_noche;
                                                        $disable_purchase_mediodia_backend = $dayData['disable_purchase_mediodia'];
                                                        $disable_purchase_noche_backend = $dayData['disable_purchase_noche'];

                                                        $disable_mediodia_total = $comprado_mediodia || $disable_purchase_mediodia_backend || ($day_purchased_any_turn && !$comprado_mediodia && !$permitir_ambos_turnos_mismo_dia);
                                                        $disable_noche_total = $comprado_noche || $disable_purchase_noche_backend || ($day_purchased_any_turn && !$comprado_noche && !$permitir_ambos_turnos_mismo_dia);
                                                    ?>
                                                    <div class="day-column flex-shrink-0 me-3" style="width: 250px;">
                                                        <div class="card h-100 day-option-card
                                                            <?= $es_feriado ? 'day-card-holiday' : '' ?>
                                                            <?= $es_receso_invernal ? 'day-card-recess' : '' ?>
                                                            <?= $es_pasado ? 'day-card-past' : '' ?>">
                                                            <div class="card-header d-flex flex-column align-items-center py-2
                                                                <?= $es_feriado ? 'day-card-holiday-header-bg' : ($es_receso_invernal ? 'day-card-recess-header-bg' : 'day-card-normal-header-bg') ?>">
                                                                <h5 class="mb-0 fw-bold text-capitalize"><?= $dayName ?> <span class="<?= ($es_feriado || $es_receso_invernal) ? 'text-black-custom' : 'text-white-custom' ?>"><?= $date_display ?></span></h5>
                                                                <?php if ($es_receso_invernal): ?>
                                                                    <span class="badge badge-recess"><i class="bi bi-snow me-1"></i>RECESO INVERNAL</span>
                                                                <?php elseif ($es_feriado): ?>
                                                                    <span class="badge badge-holiday"><i class="bi bi-calendar-x me-1"></i>FERIADO</span>
                                                                <?php endif; ?>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="mb-3 p-2 rounded-3 border bg-white meal-time-block <?= $disable_mediodia_total ? 'meal-disabled' : '' ?>">
                                                                    <div class="d-flex align-items-center">
                                                                        <label class="form-check-label fw-bold flex-grow-1" for="select<?= $dayData['date_ymd'] ?>Manana">
                                                                            <i class="bi bi-sun me-2"></i>Mediodía
                                                                        </label>
                                                                    </div>
                                                                    <?php if ($comprado_mediodia): ?>
                                                                        <div class="d-flex justify-content-end mt-1">
                                                                            <?php if (isset($dayData['mp_estado_mediodia']) && $dayData['mp_estado_mediodia'] === 'pasarela'): ?>
                                                                                <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Pago Pendiente: <?= htmlspecialchars($dayData['comprado_mediodia_menu']); ?></span>
                                                                            <?php elseif (isset($dayData['mp_estado_mediodia']) && $dayData['mp_estado_mediodia'] === 'pending'): ?>
                                                                                <span class="badge bg-info text-dark"><i class="bi bi-arrow-repeat me-1"></i>Esperando Acreditacion</span>
                                                                            <?php else: ?>
                                                                                <span class="badge badge-purchased"><i class="bi bi-check-circle me-1"></i>Comprado: <?= htmlspecialchars($dayData['comprado_mediodia_menu']) ?></span>
                                                                            <?php endif; ?>
                                                                        </div>
                                                                    <?php endif; ?>
                                                                    <div class="vianda-options mt-2">
                                                                        <select class="form-select form-select-sm meal-select"
                                                                                id="select<?= $dayData['date_ymd'] ?>Manana"
                                                                                name="selectMenu[<?= $dayData['date_ymd'] ?>][manana]"
                                                                                data-date="<?= $dayData['date_ymd'] ?>"
                                                                                data-time="manana"
                                                                                <?= $disable_mediodia_total ? 'disabled' : '' ?>>
                                                                            <option value="seleccionar" selected>Seleccionar</option>
                                                                            <option value="Basico">Menú Básico</option>
                                                                            <option value="Veggie">Menú Vegetariano</option>
                                                                            <option value="Celiaco">Sin TACC</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                <hr class="my-3">

                                                                <div class="mb-3 p-2 rounded-3 border bg-white meal-time-block <?= $disable_noche_total ? 'meal-disabled' : '' ?>">
                                                                    <div class="d-flex align-items-center">
                                                                        <label class="form-check-label fw-bold flex-grow-1" for="select<?= $dayData['date_ymd'] ?>Noche">
                                                                            <i class="bi bi-moon me-2"></i>Noche
                                                                        </label>
                                                                    </div>
                                                                    <?php if ($comprado_noche): ?>
                                                                        <div class="d-flex justify-content-end mt-1">
                                                                            <?php if (isset($dayData['mp_estado_noche']) && $dayData['mp_estado_noche'] === 'pasarela'): ?>
                                                                                <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Pago Pendiente: <?= htmlspecialchars($dayData['comprado_noche_menu']); ?></span>
                                                                            <?php elseif (isset($dayData['mp_estado_noche']) && $dayData['mp_estado_noche'] === 'pending'): ?>
                                                                                <span class="badge bg-info text-dark"><i class="bi bi-arrow-repeat me-1"></i>Esperando Acreditacion</span>
                                                                            <?php else: ?>
                                                                                <span class="badge badge-purchased"><i class="bi bi-check-circle me-1"></i>Comprado: <?= htmlspecialchars($dayData['comprado_noche_menu']) ?></span>
                                                                            <?php endif; ?>
                                                                        </div>
                                                                    <?php endif; ?>
                                                                    <div class="vianda-options mt-2">
                                                                        <select class="form-select form-select-sm meal-select"
                                                                                id="select<?= $dayData['date_ymd'] ?>Noche"
                                                                                name="selectMenu[<?= $dayData['date_ymd'] ?>][noche]"
                                                                                data-date="<?= $dayData['date_ymd'] ?>"
                                                                                data-time="noche"
                                                                                <?= $disable_noche_total ? 'disabled' : '' ?>>
                                                                            <option value="seleccionar" selected>Seleccionar</option>
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
                                            <strong class="fs-5 text-danger" id="costoDisplay">$0.00</strong>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-muted">Saldo disponible:</span>
                                            <strong class="fs-5 text-success" id="saldoInicialDisplay"></strong>
                                        </div>
                                        <hr class="my-3">
                                        <div class="d-flex justify-content-between align-items-center fw-bold fs-4">
                                            <span>TOTAL A PAGAR:</span>
                                            <span id="totalPagar" class="text-success">$0.00</span>
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
                <p>Costo total de viandas: <strong class="text-danger" id="modalCostoTotal">$0.00</strong>.</p>
                <p>Saldo disponible: <strong class="text-success" id="modalSaldoAplicado">$0.00</strong>.</p>
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

<div class="modal fade" id="pendingPurchaseModal" tabindex="-1" aria-labelledby="pendingPurchaseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-lg border-0 rounded-4">
            <div class="modal-header bg-primary text-white d-flex align-items-center justify-content-between py-3 px-4 rounded-top-4">
                <h5 class="modal-title fs-5 fw-bold" id="pendingPurchaseModalLabel">
                    <i class="bi bi-bell-fill me-3 fs-4"></i> Compra Pendiente Detectada
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex align-items-start mb-4">
                    <i class="bi bi-info-circle-fill text-info me-3 fs-3"></i>
                    <p class="mb-0 text-muted lh-base">Hemos detectado una compra de viandas que dejaste pendiente de pago. ¡No te preocupes! Aquí puedes ver los detalles y decidir qué hacer.</p>
                </div>

                <div class="card border-0 bg-light-subtle mb-4">
                    <div class="card-body py-3 px-4">
                        <div class="row g-2 align-items-center">
                            <div class="col-sm-6 text-dark fw-semibold d-flex align-items-center">
                                <i class="bi bi-cash-stack me-2 text-primary fs-5"></i>Monto Total:
                            </div>
                            <div class="col-sm-6 text-end text-sm-end fw-bold text-success fs-3">
                                $<span id="modalAmount"></span>
                            </div>
                        </div>
                        <div class="row g-2 mt-2 align-items-center">
                            <div class="col-sm-6 text-dark fw-semibold d-flex align-items-center">
                                <i class="bi bi-food-multiple me-2 text-primary fs-5"></i>Viandas Seleccionadas:
                            </div>
                            <div class="col-sm-6 text-end text-sm-end text-muted fw-semibold">
                                <span id="modalViandaCount"></span> viandas
                            </div>
                        </div>
                    </div>
                </div>

                <h6 class="mb-3 text-dark fw-bold border-bottom pb-2">
                    <i class="bi bi-box-seam-fill me-2 text-primary"></i>Detalle de Viandas:
                </h6>
                <div class="list-group list-group-flush border rounded-3 overflow-hidden mb-4">
                    <ul id="modalViandasList" class="list-group list-group-flush">
                    </ul>
                </div>
                
                <p class="text-center text-muted fw-semibold mb-4">
                    ¿Deseas <strong class="text-primary">retomar el pago</strong> de esta orden para finalizar la compra o prefieres <strong class="text-danger">cancelarla</strong> para iniciar una nueva selección?
                </p>
            </div>
            <div class="modal-footer d-flex justify-content-between p-3 rounded-bottom-4 bg-light">
                <button type="button" class="btn btn-outline-secondary flex-grow-1 me-2" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-2"></i>Cerrar
                </button>
                <button type="button" class="btn btn-outline-danger flex-grow-1 me-2" id="cancelPendingPurchaseBtn">
                    <i class="bi bi-trash-fill me-2"></i>Cancelar Orden
                </button>
                <button type="button" class="btn btn-success flex-grow-1" id="resumePendingPurchaseBtn">
                    <i class="bi bi-currency-dollar me-2"></i>Retomar Pago
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<script>
$(document).ready(function() {
    // Aquí puedes añadir una verificación simple para asegurar que jQuery está listo
    console.log('jQuery está cargado:', typeof $ !== 'undefined' ? 'Sí' : 'No');
    console.log('Objeto Bootstrap está cargado:', typeof bootstrap !== 'undefined' && typeof bootstrap.Modal !== 'undefined' ? 'Sí' : 'No');

    // Obtenemos el costo unitario de la vianda y el saldo inicial del usuario
    const costoViandaUnitario = parseFloat($('#costoVianda').val());
    const saldoUsuarioInicial = parseFloat($('#saldoCuenta').val());

    // Obtenemos el valor de la nueva variable PHP para la restricción
    const permitirAmbosTurnosMismoDia = <?= json_encode($permitir_ambos_turnos_mismo_dia); ?>;

    // Función para actualizar el total a pagar, la cantidad de viandas y el saldo aplicado
    function actualizarTotal() {
        // Contamos solo los selects que tienen un valor diferente de 'seleccionar' y no están deshabilitados
        const cantidadViandasSeleccionadas = $('.meal-select:not([disabled])').filter(function() {
            return $(this).val() !== 'seleccionar';
        }).length;

        const costoTotalViendas = cantidadViandasSeleccionadas * costoViandaUnitario;

        let saldoAplicado = 0;
        if (costoTotalViendas > 0) {
            saldoAplicado = Math.min(costoTotalViendas, saldoUsuarioInicial);
        }

        const totalAPagar = Math.max(0, costoTotalViendas - saldoUsuarioInicial);

        // --- Actualiza el Resumen de Compra en la página ---
        $('#cantidadViandas').text(cantidadViandasSeleccionadas);
        $('#costoDisplay').text('$' + costoTotalViendas.toFixed(2));
        // There's no #saldoAplicadoDisplay in your HTML for this specific purpose,
        // it seems saldoInicialDisplay is used to show the initial balance.
        // If you intended to show the *applied* balance, you'd need a new span.
        // For now, we'll keep it showing the initial balance as it is.
        $('#saldoInicialDisplay').text('$' + saldoUsuarioInicial.toFixed(2));
        $('#totalPagar').text('$' + totalAPagar.toFixed(2));

        // Aplica color al total a pagar según si es mayor que 0 o no
        if (totalAPagar > 0) {
            $('#totalPagar').addClass('text-danger').removeClass('text-success');
        } else {
            $('#totalPagar').removeClass('text-danger').addClass('text-success');
        }

        // Activa o desactiva el botón de compra
        $('#btnCompra').prop('disabled', cantidadViandasSeleccionadas === 0);
    }

    // Lógica para la restricción de un solo turno por día
    $(document).on('change', '.meal-select', function() {
        const currentSelect = $(this);
        const dateYMD = currentSelect.data('date');
        const currentTime = currentSelect.data('time');
        const isCurrentlySelected = currentSelect.val() !== 'seleccionar';

        // Si la restricción de un solo turno por día está activa
        if (!permitirAmbosTurnosMismoDia) {
            const otherTime = (currentTime === 'manana') ? 'noche' : 'manana';
            const otherSelectId = `#select${dateYMD}${otherTime.charAt(0).toUpperCase() + otherTime.slice(1)}`;
            const otherSelect = $(otherSelectId);

            // Determinar si el bloque del turno opuesto está deshabilitado por el backend (incluyendo feriado/receso invernal)
            const isOtherTimeBlockBackendDisabled = otherSelect.closest('.meal-time-block').hasClass('meal-disabled');

            if (isCurrentlySelected) {
                // Si el select actual tiene una opción de menú seleccionada, deshabilita el del turno opuesto
                // Solo si no fue deshabilitado por el backend
                if (!isOtherTimeBlockBackendDisabled) {
                    otherSelect.prop('disabled', true);
                    // Si el otro select tenía algo seleccionado, resetéalo
                    if (otherSelect.val() !== 'seleccionar') {
                        otherSelect.val('seleccionar'); // Restablece a 'Seleccionar'
                    }
                }
            } else { // Si el select actual vuelve a "Seleccionar"
                // Solo re-habilita el turno opuesto si NO fue deshabilitado por el backend
                if (!isOtherTimeBlockBackendDisabled) {
                    otherSelect.prop('disabled', false);
                }
            }
        }
        actualizarTotal(); // Actualiza el total al cambiar el estado del select
    });

    // Función para aplicar la lógica de restricción de un solo turno por día
    // Se utilizará tanto en la carga inicial como después de un reset
    function applySingleTurnRestriction() {
        if (!permitirAmbosTurnosMismoDia) {
            $('.meal-time-block').each(function() {
                const mealTimeBlock = $(this);
                // Si este bloque de tiempo está deshabilitado por el backend (comprado, feriado, receso invernal, etc.)
                if (mealTimeBlock.hasClass('meal-disabled')) {
                    const selectedMealSelect = mealTimeBlock.find('.meal-select');
                    const dateYMD = selectedMealSelect.data('date');
                    const currentTime = selectedMealSelect.data('time');

                    const otherTime = (currentTime === 'manana') ? 'noche' : 'manana';
                    const otherSelectId = `#select${dateYMD}${otherTime.charAt(0).toUpperCase() + otherTime.slice(1)}`;
                    const otherMealTimeBlock = $(otherSelectId).closest('.meal-time-block');
                    const otherSelect = otherMealTimeBlock.find('.meal-select');

                    // Si el otro bloque no está ya deshabilitado por backend, deshabilitarlo
                    if (!otherMealTimeBlock.hasClass('meal-disabled')) {
                        otherSelect.prop('disabled', true);
                        if (otherSelect.val() !== 'seleccionar') {
                            otherSelect.val('seleccionar'); // Resetear si tenía una selección
                        }
                    }
                }
            });
        }
    }


    // Inicializa el total al cargar la página
    actualizarTotal();
    // Aplica la restricción de un solo turno al cargar la página
    applySingleTurnRestriction();

    // Confirmación antes de comprar (Modal)
    $('#btnCompra').click(function(e) {
        e.preventDefault(); // Evita el envío directo del formulario

        // Declarar e inicializar las variables para asegurar que siempre estén definidas
        let cantidadViandasSeleccionadas = 0;
        let costoTotalViendas = 0;

        // Calcular cantidad de viandas seleccionadas
        cantidadViandasSeleccionadas = $('.meal-select:not([disabled])').filter(function() {
            return $(this).val() !== 'seleccionar';
        }).length;

        // Calcular costo total de viandas
        costoTotalViendas = cantidadViandasSeleccionadas * costoViandaUnitario;

        const saldoUsuario = saldoUsuarioInicial; // Usamos el saldo inicial del usuario

        let saldoAplicadoModal = 0;
        if (costoTotalViendas > 0) {
            saldoAplicadoModal = Math.min(costoTotalViendas, saldoUsuario);
        }

        const totalAPagarModal = Math.max(0, costoTotalViendas - saldoUsuario);

        if (cantidadViandasSeleccionadas === 0) {
            alert('Por favor seleccione al menos una vianda para comprar.');
            return;
        }

        $('#modalCantidad').text(cantidadViandasSeleccionadas);
        $('#modalCostoTotal').text('$' + costoTotalViendas.toFixed(2));
        $('#modalSaldoAplicado').text('$' + saldoUsuario.toFixed(2));

        // Se actualiza directamente el span con el valor final
        $('#modalPagar').html('Total a pagar: <span id="modalFinalPagarValor">$' + totalAPagarModal.toFixed(2) + '</span>');

        // Aplica color al total a pagar en el modal
        if (totalAPagarModal > 0) {
            $('#modalFinalPagarValor').addClass('text-danger').removeClass('text-success');
        } else {
            $('#modalFinalPagarValor').removeClass('text-danger').addClass('text-success');
        }

        // Initialize and show the modal correctly using Bootstrap's API
        const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
        confirmModal.show();
    });

    // Confirma la compra al hacer clic en el botón de confirmación del modal
    $('#confirmBuy').click(function() {
        // Store original names
        const originalNames = {};
        $('.meal-select').each(function() {
            const $this = $(this);
            // If the select is not chosen ("seleccionar") or is disabled, remove its name attribute
            if ($this.val() === 'seleccionar' || $this.prop('disabled')) {
                originalNames[$this.attr('id')] = $this.attr('name');
                $this.removeAttr('name'); // Remove name to prevent submission
            }
        });

        // Submit the form
        $('#formCompraId').submit();

        // Restore original names after a short delay to ensure submission completes
        // This is important so the client-side logic continues to work correctly after submission
        setTimeout(function() {
            for (const id in originalNames) {
                $(`#${id}`).attr('name', originalNames[id]);
            }
        }, 100);
    });

    // Botón reiniciar selección
    $('#btnReset').click(function() {
        // Reiniciar todos los selects que NO están deshabilitados por PHP
        $('.meal-select:not(.meal-disabled)').val('seleccionar'); // Excluir los que están deshabilitados por backend

        // Re-habilitar los selects que fueron deshabilitados por la lógica de un solo turno por día,
        // siempre y cuando no estuvieran deshabilitados por el backend inicialmente.
        if (!permitirAmbosTurnosMismoDia) {
            $('.meal-time-block').each(function() {
                const mealTimeBlock = $(this);
                // Si el bloque de tiempo NO está deshabilitado por el backend, asegurarse de que su select esté habilitado.
                if (!mealTimeBlock.hasClass('meal-disabled')) {
                    mealTimeBlock.find('.meal-select').prop('disabled', false);
                }
            });
            // Después de resetear y re-habilitar, vuelve a aplicar la lógica de restricción
            // Esto es crucial para que si un turno está comprado/feriado, el otro se deshabilite correctamente.
            applySingleTurnRestriction();
        }
        actualizarTotal(); // Actualiza el total al resetear
    });
    // Debug: valores de PHP en la consola JS
    console.log('--- Depuración de Variables PHP para Modal Pendiente ---');
    console.log('Valor de $show_pending_purchase_modal:', <?php echo isset($show_pending_purchase_modal) ? json_encode($show_pending_purchase_modal) : 'undefined'; ?>);
    console.log('Valor de $pending_purchase_details:', <?php echo isset($pending_purchase_details) ? json_encode($pending_purchase_details) : 'undefined'; ?>);
    console.log('--- Fin Depuración ---');

    // --- Lógica para mostrar el modal de compra pendiente ---
    <?php if (isset($show_pending_purchase_modal) && $show_pending_purchase_modal && !empty($pending_purchase_details)): ?>
    const pendingPurchaseDetails = <?php echo json_encode($pending_purchase_details); ?>;
    const pendingPurchaseViandas = <?php echo json_encode($pending_purchase_viandas); ?>;
    console.log('Detected pending purchase:', pendingPurchaseDetails);
    console.log('Detected pending purchase viandas:', pendingPurchaseViandas);

    // Mapeo de turnos para el frontend
    const turnoMapping = {
        'manana': 'Mediodía',
        'noche': 'Noche'
    };

    // Llenar los datos del modal
    $('#modalAmount').text(parseFloat(pendingPurchaseDetails.total).toFixed(2));
    $('#modalViandaCount').text(pendingPurchaseViandas.length);

    const viandasList = $('#modalViandasList');
    viandasList.empty();

    if (pendingPurchaseViandas && pendingPurchaseViandas.length > 0) {
        pendingPurchaseViandas.forEach(function(vianda) {
            // Formatear la fecha
            let fechaDisplay = vianda.dia_comprado || 'Fecha Desconocida';
            if (vianda.dia_comprado) {
                const date = new Date(vianda.dia_comprado + 'T00:00:00');
                fechaDisplay = date.toLocaleDateString('es-ES', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
                // Capitalizar la primera letra del día de la semana
                fechaDisplay = fechaDisplay.charAt(0).toUpperCase() + fechaDisplay.slice(1);
            }

            // Mapear el turno
            const turnoDisplay = turnoMapping[vianda.turno] || vianda.turno || 'Turno Desconocido';
            
            // Icono para el turno (depende si es Mediodía o Noche)
            const turnoIcon = (vianda.turno === 'manana') ? '<i class="bi bi-sun-fill text-warning me-2"></i>' : (vianda.turno === 'noche' ? '<i class="bi bi-moon-fill text-info me-2"></i>' : '');

            // Construir el HTML de cada vianda con los nuevos estilos e iconos
            viandasList.append(`
                <li class="list-group-item d-flex align-items-center py-3 px-3">
                    <div class="flex-grow-1">
                        <h6 class="mb-1 fw-bold text-dark">
                            <i class="bi bi-calendar-fill text-primary me-2"></i>${fechaDisplay}
                        </h6>
                        <p class="mb-0 text-muted small">
                            ${turnoIcon}${turnoDisplay} - <span class="fw-semibold">${vianda.menu || 'Vianda Desconocida'}</span>
                        </p>
                    </div>
                    <span class="badge bg-success-subtle text-success fs-6 fw-bold p-2 ms-3">
                        $${parseFloat(vianda.precio).toFixed(2)}
                    </span>
                </li>
            `);
        });
    } else {
        viandasList.append('<li class="list-group-item text-muted text-center py-3">No se encontraron detalles de viandas para esta orden.</li>');
    }

        // Crear la instancia del modal
        const pendingPurchaseModalElement = document.getElementById('pendingPurchaseModal');
        const pendingPurchaseModal = new bootstrap.Modal(pendingPurchaseModalElement, {
            backdrop: 'static',
            keyboard: false
        });
        
        // Mostrar el modal
        pendingPurchaseModal.show();

        // Manejar botón "Retomar Pago"
        $('#resumePendingPurchaseBtn').on('click', function() {
            window.location.href = '<?= base_url("comedor/pago/comprar"); ?>';
        });

        // Manejar botón "Cancelar Orden"
        $('#cancelPendingPurchaseBtn').on('click', function() {
            if (confirm('¿Estás seguro de que deseas cancelar esta orden? No podrás retomarla después.')) {
                $.ajax({
                    url: '<?= base_url("comedor/pago/cancelar_compra_ajax"); ?>', // Revisa si esta URL también necesita el prefijo "comedor/"
                    type: 'POST',
                    data: { external_reference: pendingPurchaseDetails.external_reference },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            pendingPurchaseModal.hide();
                            window.location.reload();
                        } else {
                            alert('Error al cancelar: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('Error de comunicación con el servidor al intentar cancelar la compra.');
                    }
                });
            }
        });
    <?php endif; ?>

    $('#btnAbrirModalRetomarPago').on('click', function() {
        // Usá la API de Bootstrap para abrir el modal
        var modal = document.getElementById('pendingPurchaseModal');
        if (modal) {
            var instance = bootstrap.Modal.getOrCreateInstance(modal);
            instance.show();
        }
    });
});
</script>