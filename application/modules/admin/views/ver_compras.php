<div class="container">
    <div class="row">
        <div class="col mt-5">
            <h2 class="text-center">Historico de Compras de
                <?= strtoupper($usuario->apellido).', '.ucwords($usuario->nombre); ?></h2>
        </div>
    </div>
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
    <div class="row">
        <div class="col">
            <div class="table-responsive">
                <table class="table table-sm text-center dataTables">
                    <thead>
                        <tr>
                            <th style="width: 1%; white-space: nowrap; text-align: left;">Marca</th>
                            <th style="width: 1%; white-space: nowrap; text-align: left;">Referencia Externa</th>
                            <th style="width: 1%; white-space: nowrap;">Medio Pago</th>
                            <th style="width: 1%; white-space: nowrap; text-align: left;">Dia Comprado</th>
                            <th style="width: 1%; white-space: nowrap;">Turno</th>
                            <th style="width: 1%; white-space: nowrap;">Menu</th>
                            <th style="width: 1%; white-space: nowrap;">Precio</th>

                            <th style="width: 1%; white-space: nowrap;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($compras)) : ?>
                        <?php foreach ($compras as $compra) : ?>
                        <tr>
                            <td style="width: 1%; white-space: nowrap; text-align: left;"><?= $compra->fecha; ?> - <?= date('H:i', strtotime($compra->hora)); ?></td>
                            <td style="width: 1%; white-space: nowrap; text-align: left;"><?= $compra->external_reference; ?></td>
                            <td style="width: 1%; white-space: nowrap;"><?= $compra->medio_pago; ?></td>
                            <td style="width: 1%; white-space: nowrap; text-align: left;">
                                <?= $diasSemana[date('l', strtotime($compra->dia_comprado))].', '.date('d', strtotime($compra->dia_comprado)).' de '.$meses[date('F', strtotime($compra->dia_comprado))].' de '.date('Y', strtotime($compra->dia_comprado)); ?>
                            </td>
                            <td style="width: 1%; white-space: nowrap;"><?= $compra->turno; ?></td>
                            <td style="width: 1%; white-space: nowrap;"><?= $compra->menu; ?></td>
                            <td style="width: 1%; white-space: nowrap;">$ <?= number_format($compra->precio,2,',',''); ?>.-</td>
                            
                            <td>
                                <a class="btn btn-warning btn-sm" alt="Devolver compra"
                                    href="<?= base_url("admin/compras/devolver/{$usuario->id}/{$compra->id}") ?>">
                                    <i class="bi bi-arrow-repeat"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else : ?>
                        <tr>
                            <td colspan="8">No hay compras disponibles.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <nav aria-label="Page navigation example">
            <ul class="pagination justify-content-center">
                <?php if (!isset($primera)) : ?>
                <li class="page-item">
                    <a class="page-link" href="<?= base_url("admin/compras/usuario/{$usuario->id}/"); ?>" aria-label="Primera">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (isset($links)) : ?>
                <?php foreach ($links as $link) : ?>
                <?php if (isset($link['act'])) : ?>
                <li class="page-item active">
                    <span class="page-link"><?= $link['num']; ?></span>
                </li>
                <?php else : ?>
                <li class="page-item">
                    <a class="page-link" href="<?= base_url("admin/compras/usuario/{$usuario->id}/{$link['id']}"); ?>">
                        <?= $link['num']; ?>
                    </a>
                </li>
                <?php endif; ?>
                <?php endforeach; ?>
                <?php endif; ?>
                <?php if (!isset($ultima)) : ?>
                <li class="page-item">
                    <a class="page-link" href="<?= base_url("admin/compras/usuario/{$usuario->id}/{$ultimo}"); ?>" aria-label="Ultima">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</div>
