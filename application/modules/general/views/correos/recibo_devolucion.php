<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo de Devolución</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { width: 95%; margin: 20px auto; padding: 0; border: 1px solid #ddd; border-radius: 8px; background-color: #ffffff; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header { background-color: #007BFF;
            color: white; padding: 15px 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { padding: 20px; }
        .footer { text-align: center; font-size: 0.8em; color: #777; margin-top: 20px; border-top: 1px solid #eee; padding-top: 10px; }
        .table-responsive { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #eee; padding: 10px 8px; text-align: left; }
        th { background-color: #e0e0e0; font-weight: bold; }
        .total-row td { font-weight: bold; background-color: #f2f2f2; }

    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Recibo de Devolución 📄</h2>
        </div>
        <div class="content">
            <p>Estimado/a <strong><?= $nombreCliente; ?></strong>:</p>
            <p>Te informamos que hemos procesado la devolución de tu compra según lo solicitado.</p>
            <p>El importe ha sido acreditado como saldo en tu cuenta del comedor.</p>
            <p>
                Aquí están los detalles de tu recibo de devolución:
            </p>
            <p>
                <strong>Código de Recibo:</strong> <?= $recivoNumero; ?> <br>
                <strong>Fecha de Devolución:</strong> <?= $fechaHoy; ?> <br>
                <strong>Hora:</strong> <?= $horaAhora; ?>
            </p>

            <h3>Detalle de Ítems Devueltos:</h3>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Día</th>
                            <th>Turno</th>
                            <th>Menú</th>
                            <th>Monto Devuelto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo']; ?>
                        <?php foreach ($compras as $compra) : ?>
                        <tr>
                            <td><?= date('d-m-Y', strtotime($compra->dia_comprado)); ?></td>
                            <td><?= $dias[date('N', strtotime($compra->dia_comprado)) - 1]; ?></td>
                            <td>
                                <?php
                                if (strpos($compra->turno, 'manana') !== false) {
                                    echo 'mañana';
                                } else {
                                    echo $compra->turno;
                                }
                                ?>
                            </td>
                            <td><?= $compra->menu; ?></td>
                            <td>$<?= number_format((float)$compra->precio, 2, '.', ','); ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <tr class="total-row">
                            <td colspan="4" style="text-align: right;">Total Devuelto:</td>
                            <td>$<?= number_format((float)$total, 2, '.', ','); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="footer">
            <p>Saludos,</p>
            <p>El equipo de la SAU</p>
        </div>
    </div>
</body>
</html>