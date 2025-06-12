<div class="container">
    <div class="row">
        <?= $fecha ?>
    </div>
    <div class="row">
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
                    <td> - </td>
                    <td> - </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>