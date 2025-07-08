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

    // Mapeo para nombres de turno amigables
    $turnos = [
        'manana' => 'Mediodía',
        'noche'  => 'Noche'
    ];
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-9">
            <div class="card shadow-lg mb-5 border-0">
                <div id="card-titulo" class="card-header bg-primary text-white text-center py-3">
                    <div><img class="header-logo img-fluid mx-auto d-block" src="<?= base_url('assets/img/utn.png'); ?>" alt="Logo UTN FRLP"></div>
                    <div><h2 class="my-0 fw-bold">Ticket Web - Devolución de Viandas - UTN FRLP</h2></div>
                </div>
                <div class="card-body p-4">
                    <div class="alert alert-info d-flex align-items-center mb-4 border-0" role="alert">
                        <i class="bi bi-info-circle-fill me-3 fs-4"></i>
                        <div>
                            <strong>Advertencia:</strong> Cuando realiza la devolución de una compra, el importe se acredita como saldo en su cuenta.
                            Este saldo se aplicará automáticamente en sus próximas compras.
                        </div>
                    </div>

                    <?php if (!empty($compras)) : ?>
                        <h1 class="text-center mb-4">Gestionar Devoluciones</h1>

                        <?= form_open(current_url(), ['id' => 'formDevolucion']); ?>
                            <?php
                            $compras_por_semana = [];
                            foreach ($compras as $compra) {
                                $fecha_compra = new DateTime($compra->dia_comprado);
                                $semana_iso = $fecha_compra->format('o-W'); // Año y número de semana
                                if (!isset($compras_por_semana[$semana_iso])) {
                                    $compras_por_semana[$semana_iso] = [
                                        'start_date' => (clone $fecha_compra)->modify('monday this week')->format('d \d\e F'),
                                        'end_date' => (clone $fecha_compra)->modify('friday this week')->format('d \d\e F'),
                                        'days' => []
                                    ];
                                }
                                $compras_por_semana[$semana_iso]['days'][] = $compra;
                            }

                            // Para traducir los meses en el título de la semana
                            $meses_titulo = array(
                                'January' => 'Enero', 'February' => 'Febrero', 'March' => 'Marzo',
                                'April' => 'Abril', 'May' => 'Mayo', 'June' => 'Junio',
                                'July' => 'Julio', 'August' => 'Agosto', 'September' => 'Septiembre',
                                'October' => 'Octubre', 'November' => 'Noviembre', 'December' => 'Diciembre'
                            );
                            ?>

                            <?php foreach ($compras_por_semana as $semana_iso => $weekData): ?>
                                <?php
                                    $weekTitle = 'Semana del ' . strtr($weekData['start_date'], $meses_titulo) . ' al ' . strtr($weekData['end_date'], $meses_titulo);
                                ?>
                                <div class="card shadow-sm border mb-4">
                                    <div class="card-header bg-light py-3">
                                        <h5 class="mb-0 fw-bold text-center"><?= $weekTitle ?></h5>
                                    </div>
                                    <div class="card-body p-3">
                                        <div class="row flex-nowrap overflow-scroll-x g-3 pb-3">
                                            <?php foreach ($weekData['days'] as $compra): ?>
                                                <div class="col-10 col-md-6 col-lg-4"> <div class="card h-100 day-option-card">
                                                        <div class="card-header d-flex justify-content-between align-items-center py-2 day-card-normal-header-bg">
                                                            <h6 class="mb-0 fw-bold text-capitalize">
                                                                <?php
                                                                    $dia_semana_ingles = date('l', strtotime($compra->dia_comprado));
                                                                    echo $diasSemana[$dia_semana_ingles];
                                                                ?>
                                                                <span class="text-muted fw-normal ms-1"><?= (new DateTime($compra->dia_comprado))->format('d/m') ?></span>
                                                            </h6>
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input checkbox-devolver" type="checkbox" role="switch"
                                                                    id="devolver_<?= $compra->id; ?>"
                                                                    name="devolver[]"
                                                                    value="<?= $compra->id; ?>"
                                                                    data-precio="<?= $compra->precio;?>">
                                                                <label class="form-check-label visually-hidden" for="devolver_<?= $compra->id; ?>">Seleccionar para devolver</label>
                                                            </div>
                                                        </div>
                                                        <div class="card-body py-3 px-3">
                                                            <p class="card-text mb-1"><strong>Turno:</strong> <?= $turnos[$compra->turno];?></p>
                                                            <p class="card-text mb-1"><strong>Menú:</strong> <?= $compra->menu; ?></p>
                                                            <p class="card-text mb-0"><strong>Costo:</strong> <span class="fw-bold text-success">$<?= number_format($compra->precio, 2); ?></span></p>
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
                                        <div class="card-header bg-danger text-white text-center py-3">
                                            <h5 class="mb-0"><i class="bi bi-arrow-return-left me-2"></i>Resumen de Devolución</h5>
                                        </div>
                                        <div class="card-body p-4">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="text-muted">Viandas seleccionadas:</span>
                                                <span class="fw-bold fs-5" id="cantidadViandasDevolver">0</span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="text-muted">Monto total a devolver:</span>
                                                <strong class="fs-5 text-success" id="montoTotalDevolver">$0.00</strong>
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

                    <?php else : ?>
                        <div class="text-center my-5">
                            <h3 class="mb-4">No existen compras realizadas que puedan ser devueltas.</h3>
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

<div class="modal fade" id="confirmDevolucionModal" tabindex="-1" aria-labelledby="confirmDevolucionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="confirmDevolucionModalLabel"><i class="bi bi-exclamation-triangle me-2"></i>Confirmar Devolución</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Está a punto de devolver <strong id="modalCantidadDevolver">0</strong> viandas.</p>
                <p>Se le acreditará un monto total de <strong class="text-success" id="modalMontoDevolver">$0.00</strong> en su saldo.</p>
                <p class="mt-4 text-center">¿Desea continuar con esta operación?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle me-1"></i>Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmDevolucion"><i class="bi bi-check-circle me-1"></i>Confirmar Devolución</button>
            </div>
        </div>
    </div>
</div>

<style>
    .summary-card {
    border: 1px solid rgb(167, 40, 40) !important;
    }

    
    .overflow-scroll-x {
        overflow-x: auto; 
        -webkit-overflow-scrolling: touch; 
        white-space: nowrap; 
    }

    
    .overflow-scroll-x .col-10,
    .overflow-scroll-x .col-md-6,
    .overflow-scroll-x .col-lg-4 {
        flex-shrink: 0; 
    }

    .col-lg-4 {
        width: 20% !important;
        min-width: 190px;
    }
    .form-switch .form-check-input {
        margin-left: -2em;
    }
</style>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {

    // Función para actualizar el resumen de devolución
    function actualizarResumenDevolucion() {
        let cantidadSeleccionada = 0;
        let montoTotal = 0;

        // Itera sobre todos los checkboxes de devolución marcados
        $('.checkbox-devolver:checked').each(function() {
            cantidadSeleccionada++;
            // Obtiene el precio desde el atributo data-precio del checkbox
            montoTotal += parseFloat($(this).data('precio'));
        });

        // Actualiza el resumen
        $('#cantidadViandasDevolver').text(cantidadSeleccionada);
        $('#montoTotalDevolver').text('$' + montoTotal.toFixed(2));

        // Habilita/deshabilita el botón de confirmar devolución
        if (cantidadSeleccionada > 0) {
            $('#btnConfirmarDevolucion').prop('disabled', false);
        } else {
            $('#btnConfirmarDevolucion').prop('disabled', true);
        }
    }

    // Escucha cambios en los checkboxes de devolución
    $(document).on('change', '.checkbox-devolver', actualizarResumenDevolucion);

    // Inicializa el resumen al cargar la página
    actualizarResumenDevolucion();

    // Cuando se hace clic en el botón de confirmar devolución abre el modal
    $('#btnConfirmarDevolucion').click(function(e) {
        e.preventDefault(); // Evita que el formulario se envíe directamente

        let cantidadSeleccionada = 0;
        let montoTotal = 0;

        $('.checkbox-devolver:checked').each(function() {
            cantidadSeleccionada++;
            montoTotal += parseFloat($(this).data('precio'));
        });

        // Llena el modal con los datos actuales
        $('#modalCantidadDevolver').text(cantidadSeleccionada);
        $('#modalMontoDevolver').text('$' + montoTotal.toFixed(2));

        // Muestra el modal
        $('#confirmDevolucionModal').modal('show');
    });

    // Cuando se confirma la devolución en el modal
    $('#confirmDevolucion').click(function() {
        // Enviar el formulario
        $('#formDevolucion').submit();
    });

    // Botón reiniciar selección
    $('#btnReset').click(function() {
        // Desmarca todos los checkboxes
        $('.checkbox-devolver').prop('checked', false);
        // Actualiza el resumen después de resetear
        actualizarResumenDevolucion();
    });

});
</script>