<?php setlocale(LC_ALL, "spanish"); ?>
<?php
    $diasSemana = [
        "Monday"    => "Lunes",
        "Tuesday"   => "Martes",
        "Wednesday" => "Miércoles",
        "Thursday"  => "Jueves",
        "Friday"    => "Viernes",
        "Saturday"  => "Sábado",
        "Sunday"    => "Domingo"
    ];

    $meses = [
        "January"   => "Enero", "February"  => "Febrero", "March"     => "Marzo",
        "April"     => "Abril", "May"       => "Mayo",    "June"      => "Junio",
        "July"      => "Julio", "August"    => "Agosto",  "September" => "Septiembre",
        "October"   => "Octubre", "November"=> "Noviembre", "December"=> "Diciembre"
    ];

    $turnos = ['manana' => 'Mediodía', 'noche' => 'Noche'];
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-lg border-0 mb-4">
                <div id="card-titulo" class="card-header bg-primary text-white text-center py-3">
                    <div><img class="header-logo img-fluid mx-auto d-block" src="<?= base_url('assets/img/utn.png'); ?>" alt="Logo UTN FRLP"></div>
                    <div><h2 class="my-0 fw-bold">Ticket Web - Devolución de Viandas - UTN FRLP</h2></div>
                </div>
                <div class="card-body p-4">
                    <div class="alert alert-info d-flex align-items-center border-0" role="alert">
                        <i class="bi bi-info-circle-fill fs-4 me-3"></i>
                        <div><strong>Advertencia:</strong> Al realizar una devolución, el monto se acredita como saldo en su cuenta y se aplicará automáticamente en futuras compras.</div>
                    </div>

                    <?php if (!empty($compras)) : ?>
                        <h3 class="text-center mb-4">Gestionar Devoluciones</h3>

                        <?= form_open(current_url(), ['id' => 'formDevolucion']); ?>

                        <?php
                            $compras_por_semana = [];
                            foreach ($compras as $compra) {
                                $fecha = new DateTime($compra->dia_comprado);
                                $semana = $fecha->format('o-W');
                                if (!isset($compras_por_semana[$semana])) {
                                    $compras_por_semana[$semana] = [
                                        'start' => (clone $fecha)->modify('monday this week')->format('d \d\e F'),
                                        'end'   => (clone $fecha)->modify('friday this week')->format('d \d\e F'),
                                        'dias'  => []
                                    ];
                                }
                                $compras_por_semana[$semana]['dias'][] = $compra;
                            }
                        ?>

                        <div class="accordion" id="accordionSemanas">
                            <?php $isFirst = true; ?>
                            <?php foreach ($compras_por_semana as $semana => $data): ?>
                                <?php
                                    $idCollapse = 'collapse_' . str_replace('-', '_', $semana);
                                    $idHeading = 'heading_' . str_replace('-', '_', $semana);
                                    $titulo = '<span class="d-none d-md-inline">Semana del&nbsp;</span><strong>' . strtr($data['start'], $meses) . '</strong>&nbsp;al&nbsp;<strong>' . strtr($data['end'], $meses) . '</strong>';
                                ?>
                                <div class="accordion-item mb-3 border">
                                    <h2 class="accordion-header" id="<?= $idHeading ?>">
                                        <button class="accordion-button <?= !$isFirst ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#<?= $idCollapse ?>" aria-expanded="<?= $isFirst ? 'true' : 'false' ?>" aria-controls="<?= $idCollapse ?>">
                                            <?= $titulo ?>
                                        </button>
                                    </h2>
                                    <div id="<?= $idCollapse ?>" class="accordion-collapse collapse <?= $isFirst ? 'show' : '' ?>" aria-labelledby="<?= $idHeading ?>" data-bs-parent="#accordionSemanas">
                                        <div class="accordion-body">
                                            <div class="vianda-scroll-container">
                                                <?php foreach ($data['dias'] as $compra): ?>
                                                    <div class="vianda-card card">
                                                        <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                                                            <div>
                                                                <h5 class="mb-0 fw-semibold text-capitalize"><?= $diasSemana[date('l', strtotime($compra->dia_comprado))] ?> <?= (new DateTime($compra->dia_comprado))->format('d/m') ?></h5>
                                                                <h6 class="text-white"></h6>
                                                            </div>
                                                            <div class="form-check form-switch m-0">
                                                                <input class="form-check-input checkbox-devolver" type="checkbox"
                                                                    id="devolver_<?= $compra->id; ?>"
                                                                    name="devolver[]"
                                                                    value="<?= $compra->id; ?>"
                                                                    data-precio="<?= $compra->precio; ?>">
                                                            </div>
                                                        </div>
                                                        <div class="card-body p-3">
                                                            <p class="mb-2"><strong>Turno:</strong> <?= $turnos[$compra->turno]; ?></p>
                                                            <p class="mb-2"><strong>Menú:</strong> <?= $compra->menu; ?></p>
                                                            <p class="mb-0"><strong>Costo:</strong> <span class="text-success fw-bold">$<?= number_format($compra->precio, 2); ?></span></p>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php $isFirst = false; ?>
                            <?php endforeach; ?>
                        </div>

                        <div class="row mt-5 justify-content-center">
                            <div class="col-md-8 col-lg-6">
                                <div class="card summary-card shadow-sm mb-4">
                                    <div class="card-header bg-danger text-white text-center py-3">
                                        <h5 class="mb-0"><i class="bi bi-arrow-return-left me-2"></i>Resumen de Devolución</h5>
                                    </div>
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Viandas seleccionadas:</span>
                                            <span class="fw-bold fs-5" id="cantidadViandasDevolver">0</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Monto total:</span>
                                            <span class="fw-bold fs-5 text-success" id="montoTotalDevolver">$0.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-3 d-md-flex justify-content-md-center mt-4">
                            <button type="button" id="btnConfirmarDevolucion" class="btn btn-danger btn-lg flex-fill py-3" disabled>
                                <i class="bi bi-arrow-return-left me-2"></i> Confirmar Devolución
                            </button>
                            <button type="reset" id="btnReset" class="btn btn-warning btn-lg flex-fill py-3">
                                <i class="bi bi-arrow-counterclockwise me-2"></i> Reiniciar Selección
                            </button>
                        </div>
                        <?= form_close(); ?>

                    <?php else: ?>
                        <div class="text-center my-5">
                            <h4 class="mb-3">No existen compras realizadas que puedan ser devueltas.</h4>
                            <a href="<?= base_url('usuario'); ?>" class="btn btn-primary btn-lg">
                                <i class="bi bi-cart-plus me-2"></i> Comprar Viandas
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="confirmDevolucionModal" tabindex="-1" aria-labelledby="confirmDevolucionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="confirmDevolucionModalLabel"><i class="bi bi-exclamation-triangle me-2"></i>Confirmar Devolución</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p>Está a punto de devolver <strong id="modalCantidadDevolver">0</strong> viandas.</p>
                <p>Se le acreditará un total de <strong class="text-success" id="modalMontoDevolver">$0.00</strong> en su saldo.</p>
                <p class="mt-4 text-center">¿Desea continuar?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle me-1"></i>Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmDevolucion"><i class="bi bi-check-circle me-1"></i>Confirmar</button>
            </div>
        </div>
    </div>
</div>

<!-- Estilos -->
<style>
    .vianda-scroll-container {
        display: flex;
        overflow-x: auto;
        gap: 1rem;
        padding-bottom: 1rem;
    }
    .form-check-input:checked {
        background-color: #212529;
        border-color: #ffffffff;
    }

    .vianda-card {
        min-width: 240px;
        flex: 0 0 auto;
        transition: transform 0.2s ease;
    }

    .vianda-card:hover {
        transform: translateY(-4px);
    }

    .vianda-card .card-header {
        padding: 0.75rem 1rem;
    }
    .accordion-body {
        padding: 1rem 0 1rem 0.5rem;
    }

    .summary-card {
        border: 1px solid rgb(167, 40, 40) !important;
    }
</style>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    function actualizarResumen() {
        let cantidad = 0;
        let monto = 0;
        $('.checkbox-devolver:checked').each(function() {
            cantidad++;
            monto += parseFloat($(this).data('precio'));
        });
        $('#cantidadViandasDevolver').text(cantidad);
        $('#montoTotalDevolver').text('$' + monto.toFixed(2));
        $('#btnConfirmarDevolucion').prop('disabled', cantidad === 0);
    }

    $('.checkbox-devolver').on('change', actualizarResumen);
    actualizarResumen();

    $('#btnConfirmarDevolucion').click(function(e) {
        e.preventDefault();
        let cantidad = 0;
        let monto = 0;
        $('.checkbox-devolver:checked').each(function() {
            cantidad++;
            monto += parseFloat($(this).data('precio'));
        });
        $('#modalCantidadDevolver').text(cantidad);
        $('#modalMontoDevolver').text('$' + monto.toFixed(2));
        $('#confirmDevolucionModal').modal('show');
    });

    $('#confirmDevolucion').click(function() {
        $('#formDevolucion').submit();
    });

    $('#btnReset').click(function() {
        $('.checkbox-devolver').prop('checked', false);
        actualizarResumen();
    });
});
</script>
