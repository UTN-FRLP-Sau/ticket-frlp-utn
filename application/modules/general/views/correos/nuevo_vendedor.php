<html>

<head>
</head>

<body>

    <p>Hola <strong><?= strtoupper($apellido); ?>, <?= ucwords($nombre); ?></strong>, usted a sido dado de alta como
        administrador <strong> nivel <?= $lvl; ?></strong> del nuevo sistema del Comedor Universitario.</p>

    <p>Su nueva cuenta ha sido creada y asociada al usuario <strong><?= $nickName; ?></strong>.</p>

    <p>Recuerde que debe ingresar al siguiente <a href="<?= base_url('admin') ?>"></a><?= base_url('admin') ?></p>

    <p>Las credenciales para ingresar son: <br>
    <ul>
        <li>Usuario: <strong><?= $nickName; ?></strong></li>
        <li>Contraseña: <strong><?= $password; ?></strong></li>
    </ul>

    <p> A su disposición, </br> El equipo de <strong>Ticket Web</strong></p>

</body>

</html>
