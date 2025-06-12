<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css'); ?>">
    <link rel="shortcut icon" type="image/png" href="<?= base_url('assets/img/utn.png'); ?>">
    <!-- Global site tag (gtag.js) - Google Analytics -->
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
        <div class="container">
            <a class="navbar-brand" href="<?= base_url('admin'); ?>">Ticket</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown"
                aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav mr-auto">
                    <?php if ($this->session->userdata('is_admin')) : ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('admin/menu'); ?>">Menu</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"><?= $this->session->userdata('apellido'); ?>,
                            <?= $this->session->userdata('nombre'); ?></a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <?php if (in_array($this->session->userdata('admin_lvl'), [2])) : ?>
                            <li>
                                <a class="dropdown-item"
                                    href="<?= base_url("admin/repartidor/historial/".time()); ?>">Asistencia</a>
                            </li>
                            <?php endif; ?>
                            <?php if (in_array($this->session->userdata('admin_lvl'), [0,1])) : ?>
                            <li>
                                <a class="dropdown-item" href="<?= base_url('admin/historial'); ?>">Historial de
                                    cargas</a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= base_url('admin/nuevo_usuario'); ?>">Crear nuevo
                                    usuario</a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= base_url('admin/listados'); ?>">Descargar
                                    Listados</a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= base_url('admin/informe'); ?>">Cierre
                                    de Caja</a>
                            </li>
                            <?php endif; ?>
                            <!-- <li>
                                <a class="dropdown-item" href="<?= base_url('admin/ver_historial_menu'); ?>">Historial de comidas</a>
                            </li> -->
                            <!-- <li>
                                    <a class="dropdown-item" href="<?= base_url('admin/ver_historial_menu'); ?>">Historial de comidas</a>
                            </li> -->
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= base_url('logout'); ?>">Logout</a>
                            </li>
                        </ul>
                    </li>
                    <?php if ($this->session->userdata('admin_lvl') == 1) : ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">Administracion</a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li>
                                <a class="dropdown-item" href="<?= base_url('admin/ver_comentarios'); ?>">Ver
                                    Comentarios</a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= base_url('admin/cargasvirtuales/list/'.date('Y-m-d')); ?>">Ver Cargas Virtuales</a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= base_url('admin/crear_vendedor'); ?>">Nuevo
                                    Vendedor</a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= base_url('admin/csv_carga'); ?>">Cargar desde CSV</a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">Configuraciones</a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li>
                                <a class="dropdown-item"
                                    href="<?= base_url('admin/configuracion/periodos'); ?>">Periodos de
                                    funcionamiento</a>
                            </li>
                            <li>
                                <a class="dropdown-item"
                                    href="<?= base_url('admin/configuracion/feriados_list/'.date('Y')); ?>">Feriados</a>
                            </li>
                            <li>
                                <a class="dropdown-item"
                                    href="<?= base_url('admin/configuracion/links'); ?>">Botones de Pagos</a>
                            </li>
                            <li>
                                <a class="dropdown-item"
                                    href="<?= base_url('admin/configuracion/precios'); ?>">Precios</a>
                            </li>
                        </ul>
                    </li>
                    <?php endif; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>