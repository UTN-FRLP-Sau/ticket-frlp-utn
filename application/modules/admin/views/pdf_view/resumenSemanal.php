<!DOCTYPE html>
<html lang="en">

<head>
    <style>

    </style>
</head>

<body>
    <div style="text-align: center;">
        <h1 style="margin: 1%;">Detalle de Viandas para la semana</h1>
        <h2 style="margin: 1%;">del <?= $fecha1; ?> al <?= $fecha2; ?></h2>
        <p style="margin: 1%;"> UTN - Facultad Regional La Plata</strong></p>
    </div>

    <div style="align-items: center;">
        <table style="width: 100%; text-align: center;">
            <thead style="border-bottom: 3px solid #000">
                <tr>
                    <th>Fecha</th>
                    <th>Básico</th>
                    <th>Vegano</th>
                    <th>Sin TACC</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($detalle as $key => $det) : ?>
                <?php if ($detalle[$key]['basico'] + $detalle[$key]['vegano'] + $detalle[$key]['celiaco'] !== 0) : ?>
                <tr>
                    <td style="border-bottom: 1px solid #ddd"><?= date('d-M', strtotime($detalle[$key]['fecha'])); ?>
                    </td>
                    <td style="border-bottom: 1px solid #ddd"><?= $detalle[$key]['basico']; ?></td>
                    <td style="border-bottom: 1px solid #ddd"><?= $detalle[$key]['vegano']; ?></td>
                    <td style="border-bottom: 1px solid #ddd"><?= $detalle[$key]['celiaco']; ?></td>
                </tr>
                <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>

</html>