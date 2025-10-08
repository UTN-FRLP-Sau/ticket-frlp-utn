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

<?php if (in_array($this->session->userdata('admin_lvl'), [2,3])) : ?>
<div class="container">
    <div class="row">
        <div class="col mt-5">
            <h2 class="text-center">Asistencia al comedor</h2>
            <h2 class="text-center">
                <?= $diasSemana[date('l', strtotime($fecha))].', '.date('d', strtotime($fecha)).' de '.$meses[date('F', strtotime($fecha))].' de '.date('Y', strtotime($fecha)); ?>
            </h2>

        </div>
    </div>
    <nav aria-label="Page navigation example">
        <ul class="pagination justify-content-center">
            <li class="page-item">
                <a class="page-link" href="<?= base_url("admin/repartidor/historial/".(strtotime($fecha)-86400)); ?>"
                    aria-label="Atras">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <li class="page-item active">
                <span class="page-link">
                    Hoy
                </span>
            </li>
            <li class="page-item">
                <a class="page-link" href="<?= base_url("admin/repartidor/historial/".(strtotime($fecha)+86400)); ?>"
                    aria-label="Adelante">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>

    <div class="row">
        <div class="col">
            <div class="table-responsive">
                <table class="table table-sm text-center">
                    <thead>
                        <tr>
                            <th class="col">Documento</th>
                            <th class="col">Nombre</th>
                            <th class="col">Apellido</th>
                            <th class="col">Menu</th>
                            <th class="col">Turno</th>
                            <th class="col">Retiro</th>
                            <th class="col">Repartidor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($compras as $compra) : ?>
                        <tr>
                            <td><?= $compra['documento']; ?></td>
                            <td><?= $compra['nombre']; ?></td>
                            <td><?= $compra['apellido']; ?></td>
                            <td><?= $compra['menu']; ?></td>
                            <td><?= $compra['turno']; ?></td>
                            <?php if ($compra['retiro'] == 1) : ?>
                            <td> Si </td>
                            <td> <?= $compra['repartidor']; ?></td>
                            <?php else:?>
                            <?php if (in_array($this->session->userdata('admin_lvl'), [2,3])) : ?>
                            <?php endif; ?>
                            <td> - </td>
                            <td> - </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
