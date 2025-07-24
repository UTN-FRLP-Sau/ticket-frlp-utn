<!DOCTYPE html>
<html lang="en">

<head>
    <style>

    </style>
</head>

<body>
    <div style="text-align: center;">
        <h1 style="margin: 1%;">Cierre de caja</h1>
        <h2 style="margin: 1%;"><?= $fecha; ?></h2>
        <p style="margin: 1%;"> Vendedor: <strong><?= $vendedor->nombre_usuario; ?></strong> </p>
        <p style="margin: 1%;"> Cantidad de cargas en Efectivo: <strong><?= $cantidad_efectivo; ?></strong> -
            Monto total en efectivo: <strong>$ <?= $total_efectivo; ?> -. </strong> </p>
        <p style="margin: 1%;"> Cantidad de cargas Virtuales: <strong><?= $cantidad_virtual; ?></strong> -
            Monto total virtual: <strong>$ <?= $total_virtual; ?> -. </strong> </p>

        <p style="margin: 1%;"> Cantidad de cargas Mercado Pago: <strong><?= $cantidad_mercadopago; ?></strong> -
            Monto total Mercado Pago: <strong>$ <?= $total_mercadopago; ?> -. </strong> </p>
        </div>

    <div style="align-items: center;">
        <table style="width: 100%; text-align: center;">
            <thead style="border-bottom: 3px solid #000">
                <tr>
                    <th style="text-align: left">Hora</th>
                    <th style="text-align: left">Documento</th>
                    <th style="text-align: left">Nombre</th>
                    <th style="text-align: left">Apellido</th>
                    <th style="text-align: left">Monto</th>
                    <th style="text-align: left">Forma</th>
                    <th style="text-align: left">Vendedor</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cargas as $key => $carga) : ?>
                <tr>
                    <td style="border-bottom: 1px solid #ddd; text-align: left"><?= $carga->hora; ?></td>
                    <td style="border-bottom: 1px solid #ddd; text-align: left"><?= $carga->documento; ?></td>
                    <td style="border-bottom: 1px solid #ddd; text-align: left"><?= $carga->nombre; ?></td>
                    <td style="border-bottom: 1px solid #ddd; text-align: left"><?= $carga->apellido; ?></td>
                    <td style="border-bottom: 1px solid #ddd; text-align: left"><?= $carga->monto; ?></td>
                    <td style="border-bottom: 1px solid #ddd; text-align: left"><?= $carga->formato; ?></td> <td style="border-bottom: 1px solid #ddd; text-align: left"><?= $carga->nombre_usuario; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>

</html>