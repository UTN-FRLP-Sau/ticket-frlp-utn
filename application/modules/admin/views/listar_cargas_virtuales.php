<div class="container">
    <div class="row">
        <div class="col mt-5">
            <h2 class="text-center"># Links de pagos a Confirmar</h2>
            <?php if (!empty($fecha_filtrada)) : ?>
                <h4 class="text-center">Fecha: <?= strftime('%d-%B-%Y', strtotime($fecha_filtrada)); ?></h4>
            <?php endif; ?>
            <form method="POST" action="<?= base_url('admin/cargasvirtuales/list'); ?>" class="text-center">
                <input type="date" name="filter_date" class="form-control d-inline-block w-auto">
                <button type="submit" class="btn btn-primary btn-sm">Aplicar Filtro</button>
            </form>
        </div>
    </div>



    <div class="row">
        <div class="col">
            <div class="table-responsive">
                <table class="table table-sm text-center">
                    <thead>
                        <tr>
                            <th style="text-align: left;" class="col">Documento</th>
                            <th class="col">Fecha</th>
                            <th class="col">Apellido</th>
                            <th class="col">Nombre</th>
                            <th class="col">Monto</th>
                            <th style="text-align: right;" class="col">Acciones</th>
                            <th class="col">Aprobado por</th>
                            <th class="col">Aprobado el:</th>
                        </tr>
                        <tr>
                            <th><input type="text" class="form-control form-control-sm filter-input" data-column="0" placeholder="Filtrar Documento"></th>
                            <th><input type="text" class="form-control form-control-sm filter-input" data-column="1" placeholder="Filtrar Fecha"></th>
                            <th><input type="text" class="form-control form-control-sm filter-input" data-column="2" placeholder="Filtrar Apellido"></th>
                            <th><input type="text" class="form-control form-control-sm filter-input" data-column="3" placeholder="Filtrar Nombre"></th>
                            <th><input type="text" class="form-control form-control-sm filter-input" data-column="4" placeholder="Filtrar Monto"></th>
                            <th></th>
                            <th><input type="text" class="form-control form-control-sm filter-input" data-column="6" placeholder="Filtrar Aprobado por"></th>
                            <th><input type="text" class="form-control form-control-sm filter-input" data-column="7" placeholder="Filtrar Aprobado el"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cargas as $carga) : ?>
                        <tr>
                            <td style="text-align: left;"><?= $carga -> documento; ?></td>
                            <td> <?= date('d-M-Y', strtotime($carga->timestamp)); ?> </td>
                            <td><?= $carga -> apellido; ?></td>
                            <td><?= $carga -> nombre; ?></td>
                            <td>$<?= $carga -> monto; ?></td>
                            <?php if ($carga->estado !== 'revision') : ?>
                                <?php if ($carga->estado === 'aprobado') : ?>
                                <td style="text-align: right;">
                                    <span style="background-color: green; color: white; padding: 5px; border-radius: 3px;">Aprobado</span>
                                </td>
                                <?php else: ?>
                                <td style="text-align: right;">
                                    <span style="background-color: red; color: white; padding: 5px; border-radius: 3px;">Rechazado</span>
                                </td>
                                <?php endif; ?>
                                <td> <?= $carga -> vendedor_username; ?> </td>
                                <td> <?= date('d-M-Y', strtotime($carga->confirmacion_timestamp)); ?> </td>
                            <?php else: ?>
                            <td style="text-align: right;">
                                <form method="POST" action="<?= base_url('admin/cargasvirtuales/list/' . $fecha_filtrada . '/aprobar'); ?>" style="display: inline;">
                                    <input type="hidden" id="carga_id" name="carga_id" value="<?= $carga->id; ?>">
                                    <button type="submit" class="btn btn-success btn-sm" style="background-color: green; color: white;">
                                        <i class="bi bi-check"></i>
                                    </button>
                                </form>
                                <form method="POST" action="<?= base_url('admin/cargasvirtuales/list/' . $fecha_filtrada . '/rechazar'); ?>" style="display: inline;">
                                    <input type="hidden" id="carga_id" name="carga_id" value="<?= $carga->id; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm" style="background-color: red; color: white;">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </form>
                            </td>
                            <td> --- </td>
                            <td> --- </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterInputs = document.querySelectorAll('.filter-input');
    const tableRows = document.querySelectorAll('tbody tr');

    filterInputs.forEach((input) => {
        input.addEventListener('keyup', function () {
            const column = this.getAttribute('data-column');
            const filterValue = this.value.toLowerCase();

            tableRows.forEach((row) => {
                const cell = row.querySelectorAll('td')[column];
                if (cell) {
                    const cellText = cell.textContent.toLowerCase();
                    if (cellText.includes(filterValue)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
        });
    });
});
</script>
