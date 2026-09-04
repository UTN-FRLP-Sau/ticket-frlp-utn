<html>

<head>
</head>

<body>
    <p>Hola <strong><?= strtoupper($apellido); ?>, <?= ucwords($nombre); ?></strong>:</p>
    <p>Solicitaste cambiar el correo asociado a tu cuenta de <strong>Ticket Web</strong> (documento
        <strong><?= $dni; ?></strong>) por esta casilla.
    </p>

    <p>Para confirmar el cambio, seguí el siguiente link:</p>

    <a href="<?= $link ?>"><?= $link ?></a>

    <p>Si no lo solicitaste vos, ignorá este correo: tu casilla actual no se va a modificar.</p>

    <p> A su disposición, </br> El equipo de <strong>Ticket Web</strong></p>
</body>

</html>
