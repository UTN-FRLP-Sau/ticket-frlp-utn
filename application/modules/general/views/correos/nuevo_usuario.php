<html>

<head>
</head>

<body>

    <p>Hola <strong><?= strtoupper($apellido); ?>, <?= ucwords($nombre); ?></strong>, te damos la bienvenida al nuevo sistema del Comedor
        Universitario.</p>
    <p>Su nueva cuenta ha sido creada y asociada al documento <strong><?= $dni; ?></strong>.</p>

    <p>Para poder realizar la compra debe ingresar al siguiente <a href="<?= base_url() ?>">Link</a></p>

    <p>Las credenciales para ingresar son: <br>
    <ul>
        <li>Usuario: <strong><?= $dni; ?></strong></li>
        <li>Contraseña: <strong><?= $password; ?></strong></li>
    </ul>

    <p>Por favor, vuelva a cambiar su contraseña en cuanto se conecte.</p>

    <p> A su disposición, </br> El equipo de <strong>Ticket Web</strong></p>

</body>

</html>
