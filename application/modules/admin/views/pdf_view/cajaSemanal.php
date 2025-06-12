<!DOCTYPE html>
<html lang="en">

<head>
    <style>

    </style>
</head>

<body>
    <div style="text-align: center;">
        <h1 style="margin: 1%;">Cierre de Caja Semanal</h1>
        <h2 style="margin: 1%;">Del <?= $fecha1; ?> al <?= $fecha2; ?></h2>
        <p style="margin: 1%;"> Vendedor: <strong><?= $vendedor->nombre_usuario; ?></strong></p>
    </div>

    <div style="align-items: center;">
        <h3 class="text-center">Informe de cargas en efectivo</h3>
        <table style="width: 100%; text-align: center;">
            <thead style="border-bottom: 3px solid #000">
                <tr>
                    <th>Fecha</th>
                    <th>Cantidad</th>
                    <th>Monto</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($detalle as $key => $det) : ?>
                <?php if ($detalle[$key]['cantidad_efectivo'] !== 0) : ?>
                <tr>
                    <td style="border-bottom: 1px solid #ddd"><?= date('d-M', strtotime($detalle[$key]['fecha'])); ?>
                    </td>
                    <td style="border-bottom: 1px solid #ddd"><?= $detalle[$key]['cantidad_efectivo']; ?></td>
                    <td style="border-bottom: 1px solid #ddd">$
                        <?= number_format($detalle[$key]['total_efectivo'], 0, ',', '.'); ?></td>
                </tr>
                <?php endif; ?>
                <?php endforeach; ?>
                <tr>
                    <td style="border-bottom: 2px solid #000; border-top: 1px solid #000"> Total </td>
                    <td style="border-bottom: 2px solid #000; border-top: 1px solid #000"><?= $cantidad_efectivo; ?>
                    </td>
                    <td style="border-bottom: 2px solid #000; border-top: 1px solid #000"> $
                        <?= number_format($total_efectivo, 0, ',', '.'); ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div style="align-items: center;">
        <h3 class="text-center">Informe de cargas virtuales</h3>
        <table style="width: 100%; text-align: center;">
            <thead style="border-bottom: 3px solid #000">
                <tr>
                    <th>Fecha</th>
                    <th>Cantidad</th>
                    <th>Monto</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($detalle as $key => $det) : ?>
                <?php if ($detalle[$key]['cantidad_virtual'] !== 0) : ?>
                <tr>
                    <td style="border-bottom: 1px solid #ddd"><?= date('d-M', strtotime($detalle[$key]['fecha'])); ?>
                    </td>
                    <td style="border-bottom: 1px solid #ddd"><?= $detalle[$key]['cantidad_virtual']; ?></td>
                    <td style="border-bottom: 1px solid #ddd">$
                        <?= number_format($detalle[$key]['total_virtual'], 0, ',', '.'); ?></td>
                </tr>
                <?php endif; ?>
                <?php endforeach; ?>
                <tr>
                    <td style="border-bottom: 2px solid #000; border-top: 1px solid #000"> Total </td>
                    <td style="border-bottom: 2px solid #000; border-top: 1px solid #000"><?= $cantidad_virtual; ?></td>
                    <td style="border-bottom: 2px solid #000; border-top: 1px solid #000"> $
                        <?= number_format($total_virtual, 0, ',', '.'); ?></td>
                </tr>
            </tbody>
        </table>
    </div>

</body>

</html>