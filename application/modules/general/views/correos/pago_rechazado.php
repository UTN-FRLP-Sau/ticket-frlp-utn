<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago Rechazado</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { width: 100%; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; background-color: #f9f9f9; }
        .header { background-color: #f44336; color: white; padding: 10px 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { padding: 20px; }
        .footer { text-align: center; font-size: 0.8em; color: #777; margin-top: 20px; border-top: 1px solid #eee; padding-top: 10px; }
        .table-responsive { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #eee; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>¡Tu Pago ha sido Rechazado! 😞</h2>
        </div>
        <div class="content">
            <p>Estimado/a <?php echo $user_name; ?>,</p>
            <p>Te informamos que el pago de tu compra con referencia #<?php echo $external_reference; ?> ha sido rechazado por Mercado Pago.</p>
            <p>Fecha: <?php echo $fechaHoy; ?> <br>
            Hora: <?php echo $horaAhora; ?></p>
            <p>
                Motivo del Rechazo: <?php echo $status_detail; ?>
            </p>

            <?php if (!empty($viandas)): ?>
                <h3>Detalle de la Compra Rechazada:</h3>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Día</th>
                                <th>Turno</th>
                                <th>Menú</th>
                                <th>Precio</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo']; ?>
                            <?php foreach ($viandas as $vianda): ?>
                                <tr>
                                    <td><?= date('d-m-Y', strtotime($vianda['dia_comprado'])); ?></td>
                                    <td><?= $dias[date('N', strtotime($vianda['dia_comprado'])) - 1]; ?></td>
                                    <td><?= $vianda['turno']; ?></td>
                                    <td><?= $vianda['menu']; ?></td>
                                    <td><?= $vianda['precio']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <p>Por favor, intenta realizar la compra nuevamente o verifica los detalles de tu medio de pago.</p>
            <p>Si tenés alguna pregunta, no dudes en contactarnos.</p>
        </div>
        <div class="footer">
            <p>Saludos,</p>
            <p>El equipo de la SAU - UTN FRLP</p>
        </div>
    </div>
</body>
</html>