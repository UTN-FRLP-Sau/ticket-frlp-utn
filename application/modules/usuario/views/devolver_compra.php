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
        'manana' => 'Mañana (Mediodía)',
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
                        
                        <?= form_open(current_url()); ?>
                            <?php foreach ($compras as $compra) : ?>
                                <div class="card mb-3 shadow-sm">
                                    <div class="card-body d-flex align-items-center justify-content-between p-3">
                                        <div class="form-check form-switch flex-grow-1">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                id="devolver_<?= $compra->id; ?>"
                                                name="devolver[]"
                                                value="<?= $compra->id; ?>">
                                            <label class="form-check-label ms-2" for="devolver_<?= $compra->id; ?>">
                                                Devolver vianda del 
                                                <b><?= $diasSemana[date('l', strtotime($compra->dia_comprado))]; ?>, 
                                                <?= date('d', strtotime($compra->dia_comprado)); ?> de 
                                                <?= $meses[date('F', strtotime($compra->dia_comprado))]; ?></b>
                                                (Turno: <b><?= $turnos[$compra->turno]; ?></b> | Menú: <b><?= $compra->menu; ?></b>)
                                            </label>
                                        </div>
                                        <span class="badge bg-secondary ms-auto me-2">Costo:</span>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <div class="d-grid gap-3 d-md-flex justify-content-md-center mt-4">
                                <button type="submit" id="btnDevolver" class="btn btn-danger btn-lg flex-fill py-3">
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