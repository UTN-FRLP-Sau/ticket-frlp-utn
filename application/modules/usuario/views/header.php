<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= $titulo; ?></title>

  <link rel="shortcut icon" href="<?= base_url('assets/img/utn.png'); ?>" type="image/png" />
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
    integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3"
    crossorigin="anonymous"
  />
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    rel="stylesheet"
  />
  <link rel="stylesheet" href="<?= base_url('assets/css/style.css'); ?>" />

  <style>
    body { scroll-behavior: smooth; }

    .navbar { box-shadow: 0 2px 10px rgba(0,0,0,0.4); }

    .nav-link {
      padding: 0.6rem 1rem;
      font-weight: 500;
      text-transform: capitalize;
      transition: color 0.3s ease-in-out, background-color 0.3s ease-in-out;
      color: #ffffff !important;
      text-decoration: none !important;
    }
    .nav-link.dropdown-toggle {
      display: flex !important;
      justify-content: center;
      align-items: center;
      min-width: 180px;
      text-align: center;
      white-space: nowrap;
    }
    .nav-link:hover, .nav-link:focus {
      color: #0094e9ff !important;
      background-color: rgba(255, 255, 255, 0.05);
      border-radius: 0.3rem;
    }

    /* Dropdown menu base */
    .dropdown-menu {
      background-color: #212529;
      border-radius: 0.5rem;
      box-shadow: 0 0 12px rgba(0, 0, 0, 0.25);
      transition: opacity 0.3s ease-in-out, visibility 0.3s ease-in-out;
    }

    .dropdown-menu .dropdown-item {
      color: #ffffff;
      font-weight: 500;
      transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out;
      border-radius: 0.3rem;
    }

    .dropdown-menu .dropdown-item:hover,
    .dropdown-menu .dropdown-item:focus {
      background-color: #343a40;
      color: #0094e9ff;
    }

    .dropdown-divider {
      border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    @media (min-width: 992px) {
      /* Oculta dropdown y pone invisible inicialmente */
      .nav-item.dropdown .dropdown-menu {
        opacity: 0;
        visibility: hidden;
        display: block;
      }
      /* Al hacer hover en el dropdown padre muestra el menú */
      .nav-item.dropdown:hover > .dropdown-menu,
      .nav-item.dropdown:focus-within > .dropdown-menu {
        opacity: 1;
        visibility: visible;
      }
    }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark py-3">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="<?= base_url('usuario'); ?>">
      <i class="bi bi-ticket-detailed-fill me-1"></i> Ticket
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarHeader"
            aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarHeader">
      <ul class="navbar-nav ms-auto align-items-center">
        <li class="nav-item">
          <a class="nav-link" href="<?= base_url('usuario'); ?>"><i class="bi bi-cart-plus me-1"></i> Comprar</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= base_url('menu'); ?>"><i class="bi bi-journal-text me-1"></i> Menú</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= base_url('faq'); ?>"><i class="bi bi-question-circle me-1"></i> Preguntas Frecuentes</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= base_url('contacto'); ?>"><i class="bi bi-envelope me-1"></i> Contacto</a>
        </li>

        <?php if ($this->session->userdata('id_usuario')) : ?>
          <li class="nav-item">
            <a class="nav-link" href="<?= base_url('comentarios'); ?>"><i class="bi bi-chat-left-text me-1"></i> ¡Haz un comentario!</a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
               data-bs-toggle="dropdown" aria-expanded="false">
               <i class="bi bi-person-circle me-1"></i><?= $this->session->userdata('apellido'); ?>, <?= $this->session->userdata('nombre'); ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
              <li><a class="dropdown-item" href="<?= base_url('usuario/ultimos-movimientos'); ?>"><i class="bi bi-clock-history me-1"></i> Últimos movimientos</a></li>
              <li><a class="dropdown-item" href="<?= base_url('usuario/devolver_compra'); ?>"><i class="bi bi-arrow-counterclockwise me-1"></i> Gestionar devoluciones</a></li>
              <li><a class="dropdown-item" href="<?= base_url('usuario/cambio-password'); ?>"><i class="bi bi-key me-1"></i> Cambiar contraseña</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="<?= base_url('logout'); ?>"><i class="bi bi-box-arrow-right me-1"></i> Cerrar Sesión</a></li>
            </ul>
          </li>
        <?php endif; ?>

      </ul>
    </div>
  </div>
</nav>

<script
  src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
  integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgY sOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
  crossorigin="anonymous"></script>

<script>
  // Auto-colapsar el menú al clickear un enlace (solo en pantallas donde el toggle está visible)
  (function () {
    var navbarHeader = document.getElementById('navbarHeader');
    if (!navbarHeader) return;

    var bsCollapse = new bootstrap.Collapse(navbarHeader, { toggle: false });

    // Selecciona todos los links relevantes dentro del collapse
    var links = navbarHeader.querySelectorAll('.nav-link, .dropdown-item');

    links.forEach(function (link) {
      link.addEventListener('click', function (e) {
        // Si el toggler es visible (pantallas pequeñas), colapsar
        var toggler = document.querySelector('.navbar-toggler');
        var togglerVisible = toggler && window.getComputedStyle(toggler).display !== 'none';

        if (togglerVisible) {
          bsCollapse.hide();
        }
      });
    });
  })();
</script>

</body>
</html>
