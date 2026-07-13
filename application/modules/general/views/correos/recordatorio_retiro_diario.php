<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recordatorio de Retiro</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { width: 100%; margin: 20px auto; padding: 0; border: 1px solid #ddd; border-radius: 8px; background-color: #ffffff; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header { background-color: #4CAF50; color: white; padding: 15px 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { padding: 20px; }
        .turno { background-color: #f9f9f9; border-left: 4px solid #4CAF50; padding: 10px 15px; margin: 15px 0; }
        .footer { text-align: center; font-size: 0.8em; color: #777; margin-top: 20px; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>¡Hoy podés retirar tu vianda! 🍽️</h2>
        </div>
        <div class="content">
            <p>Hola <?= ucwords($nombre); ?> <?= ucwords($apellido); ?>,</p>
            <p>Te recordamos que compraste tu vianda de hoy. Podés retirarla en el/los siguiente(s) horario(s):</p>

            <?php if ($retiro_mediodia): ?>
            <div class="turno">
                <p>Recordá que compraste tu vianda de hoy, podés retirarla de <strong><?= (new DateTime($retiro_mediodia_desde))->format('H:i'); ?></strong> a <strong><?= (new DateTime($retiro_mediodia_hasta))->format('H:i'); ?></strong> (turno mediodía).</p>
            </div>
            <?php endif; ?>

            <?php if ($retiro_noche): ?>
            <div class="turno">
                <p>Recordá que compraste tu vianda de hoy, podés retirarla de <strong><?= (new DateTime($retiro_noche_desde))->format('H:i'); ?></strong> a <strong><?= (new DateTime($retiro_noche_hasta))->format('H:i'); ?></strong> (turno noche).</p>
            </div>
            <?php endif; ?>

            <p>Si preferís no recibir este recordatorio en el futuro, podés desactivarlo desde <a href="<?= base_url('usuario/notificaciones'); ?>">Mis notificaciones</a>.</p>
        </div>
        <div class="footer">
            <p>Saludos,</p>
            <p>El equipo de la SAU - UTN FRLP</p>
        </div>
    </div>
</body>
</html>
