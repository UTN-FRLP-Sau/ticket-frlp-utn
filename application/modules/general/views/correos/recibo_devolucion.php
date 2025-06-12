    <div class="container-fluid">
        <div class="row">
            <div class="col mt-5">
                <h2 class="text-center">Recibo de Devolucion</h2>
                <p>
                    Codigo: <?= $recivoNumero; ?> <br>
                    Fecha: <?= $fechaHoy; ?> <br>
                    Hora: <?= $horaAhora; ?>
                </p>

            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="table-responsive">
                    <table class="table table-sm text-center">
                        <thead>
                            <tr>
                                <th class="col">Fecha</th>
                                <th class="col">Dia</th>
                                <th class="col">Turno</th>
                                <th class="col">Menu</th>
                                <!-- <th class="col">Modo</th> -->
                                <th class="col">Costo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $dias = ['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes']; ?>
                            <?php foreach ($compras as $compra) : ?>
                            <tr>
                                <td><?= date('d-m-Y', strtotime($compra->dia_comprado)); ?></td>
                                <td><?= $dias[date('N', strtotime($compra->dia_comprado)) - 1]; ?></td>
                                <td><?= $compra->turno; ?></td>
                                <td><?= $compra->menu; ?></td>
                                <!-- <td><?= $compra->tipo; ?></td> -->
                                <td><?= $compra->precio; ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <!-- <td></td> -->
                                <td>Total:</td>
                                <td><?= $total; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>