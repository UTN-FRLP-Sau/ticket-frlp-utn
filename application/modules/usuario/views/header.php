<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css'); ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="shortcut icon" type="image/png" href="<?= base_url('assets/img/utn.png'); ?>">
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-2X3DWTXS1H"></script>
    <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'G-2X3DWTXS1H');
    </script>
    <title><?= $titulo; ?></title>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid"> 
            <a class="navbar-brand" href="<?= base_url('usuario'); ?>">Ticket</a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarHeader"
                aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarHeader">
                <ul class="navbar-nav ms-auto d-flex justify-content-center">
                    <li class="nav-item">
                        <a class="nav-link text-center" href="<?= base_url('menu'); ?>">Menu</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-center" href="<?= base_url('faq'); ?>">Preguntas Frecuentes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-center" href="<?= base_url('contacto'); ?>">Contacto</a>
                    </li>
                    <?php if ($this->session->userdata('id_usuario')) : ?>
                       <li class="nav-item">
                            <a class="nav-link text-center" href="<?= base_url('comentarios'); ?>">¡Haz un comentario!</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"><?= $this->session->userdata('apellido'); ?>,
                                <?= $this->session->userdata('nombre'); ?></a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li>
                                    <a class="dropdown-item" href="<?= base_url('usuario/ultimos-movimientos'); ?>">Ultimos
                                        movimientos</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?= base_url('usuario/devolver_compra'); ?>">Devolver
                                        compras</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?= base_url('usuario/cambio-password'); ?>">Cambiar
                                        contraseña</a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?= base_url('logout'); ?>">Logout</a>
                                </li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>