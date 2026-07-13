<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recordatorio de Compra</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { width: 100%; margin: 20px auto; padding: 0; border: 1px solid #ddd; border-radius: 8px; background-color: #ffffff; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header { background-color: #ff9800; color: white; padding: 15px 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { padding: 20px; }
        .footer { text-align: center; font-size: 0.8em; color: #777; margin-top: 20px; border-top: 1px solid #eee; padding-top: 10px; }
        .btn { display: inline-block; margin-top: 10px; padding: 10px 20px; background-color: #4CAF50; color: #ffffff; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>¡No te quedes sin vianda la próxima semana! ⏰</h2>
        </div>
        <div class="content">
            <p>Hola <?= ucwords($nombre); ?> <?= ucwords($apellido); ?>,</p>
            <p>Te escribimos para recordarte que todavía no registramos ninguna compra de vianda (ni pendiente de pago) para la semana del <strong><?= date('d-m-Y', strtotime($fecha_inicio)); ?></strong> al <strong><?= date('d-m-Y', strtotime($fecha_fin)); ?></strong>.</p>
            <p>Si querés asegurarte tu vianda, podés comprarla ingresando al siguiente enlace:</p>
            <p style="text-align: center;">
                <a class="btn" href="<?= base_url('usuario/comprar'); ?>">Comprar mi vianda</a>
            </p>
            <p>Si ya realizaste tu compra o no pensás comprar esta semana, podés ignorar este mensaje.</p>
            <p>Si preferís no recibir este recordatorio en el futuro, podés desactivarlo desde <a href="<?= base_url('usuario/notificaciones'); ?>">Mis notificaciones</a>.</p>
        </div>
        <div class="footer">
            <p>Saludos,</p>
            <p>El equipo de la SAU - UTN FRLP</p>
        </div>
    </div>
</body>
</html>
