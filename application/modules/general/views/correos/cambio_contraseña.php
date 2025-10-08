<html>

<head>
</head>

<body>
    <?php if ($tipo == 'solicitud') : ?>

        <p>Hola <strong><?= strtoupper($apellido); ?>, <?= ucwords($nombre); ?></strong>:</p>
        <p>Ha enviado una solicitud para recuperar su contraseña de <strong>Ticket Web</strong> asociada al documento
            <strong><?= $dni; ?></strong>.
        </p>

        <p>Para reiniciar su contraseña, siga el siguiente link:</p>

        <a href="<?= $link ?>"><?= $link ?></a>

        <p>Si usted no la solicito, desestime este correo</p>

        <p> A su disposición, </br> El equipo de <strong>Ticket Web</strong></p>

    <?php elseif ($tipo == 'confirmacion') : ?>

        <p>Hola <strong><?= strtoupper($apellido); ?>, <?= ucwords($nombre); ?></strong>:</p>
        <p>La contraseña asociada al documento <strong><?= $dni; ?></strong>, se a restablecido exitosamente
        </p>

        <p> A su disposición, </br> El equipo de <strong>Ticket Web</strong></p>


    <?php endif; ?>
</body>

</html>
