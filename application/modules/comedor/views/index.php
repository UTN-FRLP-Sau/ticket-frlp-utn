<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

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
                        <?php foreach ($weeksData as $week): ?>
                            <?php
                                $weekIndex = $week['week_index'];
                                $weekDays = $week['days'];
                                $weekStartDateDisplay = $week['week_start_date_display']; // Obtenemos la fecha del lunes
                                $weekEndDateDisplay = $week['week_end_date_display'];    // Obtenemos la fecha del viernes

                                // Definición del título de la semana para incluir el rango
                                if ($weekIndex === 0) {
                                    $weekTitle = 'Esta Semana';
                                } else {
                                    $weekTitle = 'Semana del ' . $weekStartDateDisplay . ' al ' . $weekEndDateDisplay;
                                }
                            ?>
                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-light py-3">
                                    <h5 class="mb-0 fw-bold text-center"><?= $weekTitle ?></h5>
                                </div>
                                <div class="card-body p-3">
                                    <div class="days-carousel-container d-flex overflow-auto pb-3">
                                        <?php foreach ($weekDays as $dayData): ?>
                                            <?php
                                                $date_ymd = $dayData['date_ymd'];
                                                $dayName = $dayData['day_name']; // 'lunes', 'martes', etc.
                                                $date_display = $dayData['date_display']; // Solo el día del mes
                                                $comprado_mediodia = $dayData['comprado_mediodia'];
                                                $comprado_noche = $dayData['comprado_noche'];
                                                $comprado_mediodia_menu = $dayData['comprado_mediodia_menu'];
                                                $comprado_noche_menu = $dayData['comprado_noche_menu'];
                                                $es_feriado = $dayData['es_feriado'];
                                                $es_receso_invernal = $dayData['es_receso_invernal'] ?? false; // Nuevo flag para receso invernal
                                                $es_pasado = $dayData['es_pasado'];
                                                $disable_purchase = $dayData['disable_purchase']; // This already includes backend disability (bought, holiday)
                                            ?>
                                            <div class="day-column flex-shrink-0 me-3" style="width: 250px;">
                                                <div class="card h-100 day-option-card <?= $es_feriado ? 'border-danger' : '' ?> <?= $es_receso_invernal ? 'border-info' : '' ?> <?= $es_pasado ? 'meal-past' : '' ?>">
                                                    <div class="card-header d-flex flex-column align-items-center <?= $es_feriado ? 'bg-danger text-white' : ($es_receso_invernal ? 'bg-info text-white' : 'bg-light') ?> py-2">
                                                        <h5 class="mb-0 fw-bold text-capitalize"><?= $dayName ?> <span class="text-muted fw-normal fs-6 ms-1"><?= $date_display ?></span></h5>
                                                        <?php if ($es_receso_invernal): ?>
                                                            <span class="badge bg-primary text-white"><i class="bi bi-snow me-1"></i>RECESO INVERNAL</span>
                                                        <?php elseif ($es_feriado): ?>
                                                            <span class="badge bg-warning text-dark"><i class="bi bi-calendar-x me-1"></i>FERIADO</span>
                                                        <?php elseif ($es_pasado): ?>
                                                            <span class="badge bg-info text-dark"><i class="bi bi-clock-history me-1"></i>Pasado</span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="mb-3 p-2 rounded-3 border bg-white meal-time-block <?= ($comprado_mediodia || $disable_purchase || $es_receso_invernal) ? 'meal-disabled' : '' ?>">
                                                            <div class="d-flex align-items-center">
                                                                <label class="form-check-label fw-bold flex-grow-1" for="select<?= $date_ymd ?>Manana">
                                                                    <i class="bi bi-sun me-2"></i>Mediodía
                                                                </label>
                                                            </div>
                                                            <?php if ($comprado_mediodia): ?>
                                                                <div class="d-flex justify-content-end mt-1">
                                                                    <span class="badge bg-secondary"><i class="bi bi-check-circle me-1"></i>Comprado: <?= htmlspecialchars($comprado_mediodia_menu) ?></span>
                                                                </div>
                                                            <?php endif; ?>
                                                            <div class="vianda-options mt-2">
                                                                <select class="form-select form-select-sm meal-select"
                                                                            id="select<?= $date_ymd ?>Manana"
                                                                            name="selectMenu[<?= $date_ymd ?>][manana]"
                                                                            data-date="<?= $date_ymd ?>"
                                                                            data-time="manana"
                                                                            <?= ($comprado_mediodia || $disable_purchase || $es_receso_invernal) ? 'disabled' : '' ?>>
                                                                    <option value="seleccionar" selected>Seleccionar</option>
                                                                    <option value="Basico">Menú Básico</option>
                                                                    <option value="Veggie">Menú Vegetariano</option>
                                                                    <option value="Celiaco">Sin TACC</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <hr class="my-3">

                                                        <div class="p-2 rounded-3 border bg-white meal-time-block <?= ($comprado_noche || $disable_purchase || $es_receso_invernal) ? 'meal-disabled' : '' ?>">
                                                            <div class="d-flex align-items-center">
                                                                <label class="form-check-label fw-bold flex-grow-1" for="select<?= $date_ymd ?>Noche">
                                                                    <i class="bi bi-moon me-2"></i>Noche
                                                                </label>
                                                            </div>
                                                            <?php if ($comprado_noche): ?>
                                                                <div class="d-flex justify-content-end mt-1">
                                                                    <span class="badge bg-secondary"><i class="bi bi-check-circle me-1"></i>Comprado: <?= htmlspecialchars($comprado_noche_menu) ?></span>
                                                                </div>
                                                            <?php endif; ?>
                                                            <div class="vianda-options mt-2">
                                                                <select class="form-select form-select-sm meal-select"
                                                                            id="select<?= $date_ymd ?>Noche"
                                                                            name="selectMenu[<?= $date_ymd ?>][noche]"
                                                                            data-date="<?= $date_ymd ?>"
                                                                            data-time="noche"
                                                                            <?= ($comprado_noche || $disable_purchase || $es_receso_invernal) ? 'disabled' : '' ?>>
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
                        <?php endforeach; ?>

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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
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

        const costoTotalViandas = cantidadViandasSeleccionadas * costoViandaUnitario;

        let saldoAplicado = 0;
        if (costoTotalViandas > 0) {
            saldoAplicado = Math.min(costoTotalViandas, saldoUsuarioInicial);
        }

        const totalAPagar = Math.max(0, costoTotalViandas - saldoUsuarioInicial);

        // --- Actualiza el Resumen de Compra en la página ---
        $('#cantidadViandas').text(cantidadViandasSeleccionadas);
        $('#costoDisplay').text('$' + costoTotalViandas.toFixed(2));
        $('#saldoAplicadoDisplay').text('-$' + saldoAplicado.toFixed(2));
        $('#totalPagar').text('$' + totalAPagar.toFixed(2));
        $('#saldoInicialDisplay').text('$' + saldoUsuarioInicial.toFixed(2));

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

        const cantidadViandasSeleccionadas = $('.meal-select:not([disabled])').filter(function() {
            return $(this).val() !== 'seleccionar';
        }).length;

        const costoTotalViandas = cantidadViandasSeleccionadas * costoViandaUnitario;
        const saldoUsuario = saldoUsuarioInicial; // Usamos el saldo inicial del usuario

        let saldoAplicadoModal = 0;
        if (costoTotalViandas > 0) {
            saldoAplicadoModal = Math.min(costoTotalViandas, saldoUsuario);
        }

        const totalAPagarModal = Math.max(0, costoTotalViandas - saldoUsuario);

        if (cantidadViandasSeleccionadas === 0) {
            alert('Por favor seleccione al menos una vianda para comprar.');
            return;
        }

        $('#modalCantidad').text(cantidadViandasSeleccionadas);
        $('#modalCostoTotal').text('$' + costoTotalViandas.toFixed(2));
        $('#modalSaldoAplicado').text('$' + saldoUsuario.toFixed(2));

        // Se actualiza directamente el span con el valor final
        $('#modalPagar').html('Total a pagar: <span id="modalFinalPagarValor">$' + totalAPagarModal.toFixed(2) + '</span>');

        // Aplica color al total a pagar en el modal
        if (totalAPagarModal > 0) {
            $('#modalFinalPagarValor').addClass('text-danger').removeClass('text-success');
        } else {
            $('#modalFinalPagarValor').removeClass('text-danger').addClass('text-success');
        }

        $('#confirmModal').modal('show');
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

});
</script>