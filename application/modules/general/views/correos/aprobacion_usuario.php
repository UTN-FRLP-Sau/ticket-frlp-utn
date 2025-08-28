<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡Tu cuenta ha sido aprobada!</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 20px auto; padding: 0; border: 1px solid #ddd; border-radius: 8px; background-color: #ffffff; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header { background-color: #4CAF50;
                  color: white; padding: 15px 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { padding: 20px; }
        .footer { text-align: center; font-size: 0.8em; color: #777; margin-top: 20px; border-top: 1px solid #eee; padding-top: 10px; }
        .button { display: inline-block; padding: 10px 20px; margin-top: 10px; background-color: #007bff; color: #ffffff !important; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>¡Tu cuenta ha sido aprobada!</h2>
        </div>
        <div class="content">
            <p>Hola, <?= $user_name; ?></p>
            <p>Te informamos que tu solicitud de registro en el sistema del Comedor Universitario ha sido aprobada.</p>
            <p>Ahora puedes acceder a la plataforma, iniciar sesión y comenzar a comprar tus viandas.</p>
            <p style="text-align: center;">
                <a href="<?= base_url('login'); ?>" class="button">Ir a Iniciar Sesión</a>
            </p>
            <p>Si tienes alguna pregunta, por favor comunícate a 
                <a href="mailto:<?= htmlspecialchars($correo_contacto); ?>"><?= htmlspecialchars($correo_contacto); ?></a>.
            </p>
        </div>
        <div class="footer">
            <p>Saludos,</p>
            <p>El equipo de la SAU - UTN FRLP</p>
        </div>
    </div>
</body>
</html>