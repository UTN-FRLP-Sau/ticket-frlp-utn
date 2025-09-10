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
    .bg-utn-orange { background-color: #FF7F00 !important; }
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
      color: #ffffff !important;
      background-color: rgba(255, 255, 255, 0.15);
      border-radius: 0.3rem;
    }

    .dropdown-menu {
      background-color: #212529;
      border-radius: 0.5rem;
      box-shadow: 0 0 12px rgba(0,0,0,0.25);
      transition: opacity 0.3s ease-in-out, visibility 0.3s ease-in-out;
    }
    .dropdown-menu .dropdown-item {
      color: #ffffff;
      font-weight: 500;
      border-radius: 0.3rem;
      transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out;
    }
    .dropdown-menu .dropdown-item:hover,
    .dropdown-menu .dropdown-item:focus {
      background-color: #343a40;
      color: #FF7F00;
    }
    .dropdown-divider {
      border-top: 1px solid rgba(255,255,255,0.1);
    }

    @media (min-width: 992px) {
      .nav-item.dropdown .dropdown-menu {
        opacity: 0;
        visibility: hidden;
        display: block;
      }
      .nav-item.dropdown:hover > .dropdown-menu,
      .nav-item.dropdown:focus-within > .dropdown-menu {
        opacity: 1;
        visibility: visible;
      }
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-utn-orange py-3">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="<?= base_url('admin'); ?>">
      <i class="bi bi-ticket-detailed-fill me-1"></i> Ticket <span class="badge bg-light text-dark ms-2">Admin</span>
    </a>

    <button class="navbar-toggler" type="button" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="navbar-collapse collapse" id="navbarHeader">
      <ul class="navbar-nav ms-auto align-items-center">
        <?php if ($this->session->userdata('is_admin')) : ?>

          <li class="nav-item">
            <a class="nav-link" href="<?= base_url('admin/menu'); ?>"><i class="bi bi-journal-text me-1"></i> Menú</a>
          </li>

          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownAdmin" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="bi bi-person-circle me-1"></i><?= $this->session->userdata('apellido'); ?>, <?= $this->session->userdata('nombre'); ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownAdmin">
              <?php if (in_array($this->session->userdata('admin_lvl'), [2])) : ?>
                <li><a class="dropdown-item" href="<?= base_url("admin/repartidor/historial/".time()); ?>"><i class="bi bi-clipboard-check me-1"></i> Asistencia</a></li>
              <?php endif; ?>
              <?php if (in_array($this->session->userdata('admin_lvl'), [0,1])) : ?>
                <li><a class="dropdown-item" href="<?= base_url('admin/historial'); ?>"><i class="bi bi-clock-history me-1"></i> Historial de cargas</a></li>
                <li><a class="dropdown-item" href="<?= base_url('admin/nuevo_usuario'); ?>"><i class="bi bi-person-plus me-1"></i> Crear nuevo usuario</a></li>
                <li><a class="dropdown-item" href="<?= base_url('admin/listados'); ?>"><i class="bi bi-download me-1"></i> Descargar Listados</a></li>
                <li><a class="dropdown-item" href="<?= base_url('admin/informe'); ?>"><i class="bi bi-cash-stack me-1"></i> Cierre de Caja</a></li>
              <?php endif; ?>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="<?= base_url('logout'); ?>"><i class="bi bi-box-arrow-right me-1"></i> Cerrar Sesión</a></li>
            </ul>
          </li>

          <?php if ($this->session->userdata('admin_lvl') == 1) : ?>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownAdminConfig" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-sliders me-1"></i> Administración
              </a>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownAdminConfig">
                <li><a class="dropdown-item" href="<?= base_url('admin/ver_comentarios'); ?>"><i class="bi bi-chat-left-text me-1"></i> Ver Comentarios</a></li>
                <li><a class="dropdown-item" href="<?= base_url('admin/cargasvirtuales/list/'.date('Y-m-d')); ?>"><i class="bi bi-cloud-upload me-1"></i> Ver Cargas Virtuales</a></li>
                <li><a class="dropdown-item" href="<?= base_url('admin/crear_vendedor'); ?>"><i class="bi bi-person-fill-add me-1"></i> Nuevo Vendedor</a></li>
                <li><a class="dropdown-item" href="<?= base_url('admin/csv_carga'); ?>"><i class="bi bi-filetype-csv me-1"></i> Cargar desde CSV</a></li>
              </ul>
            </li>

            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownSettings" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-gear me-1"></i> Configuraciones
              </a>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownSettings">
                <li><a class="dropdown-item" href="<?= base_url('admin/configuracion/periodos'); ?>"><i class="bi bi-calendar-range me-1"></i> Periodos de funcionamiento</a></li>
                <li><a class="dropdown-item" href="<?= base_url('admin/configuracion/feriados_list/'.date('Y')); ?>"><i class="bi bi-calendar-x me-1"></i> Feriados</a></li>
                <li><a class="dropdown-item" href="<?= base_url('admin/configuracion/links'); ?>"><i class="bi bi-link-45deg me-1"></i> Botones de Pagos</a></li>
                <li><a class="dropdown-item" href="<?= base_url('admin/configuracion/precios'); ?>"><i class="bi bi-currency-dollar me-1"></i> Precios</a></li>
              </ul>
            </li>
          <?php endif; ?>

        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
        crossorigin="anonymous"></script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const toggler = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.getElementById('navbarHeader');
    if (!toggler || !navbarCollapse) return;

    // Toggle manual del collapse
    toggler.addEventListener('click', () => {
      navbarCollapse.classList.toggle('show');
    });

    // Auto-cerrar menú en mobile al clickear links (excepto dropdown-toggle)
    navbarCollapse.querySelectorAll('.nav-link, .dropdown-item').forEach(link => {
      link.addEventListener('click', () => {
        const isTogglerVisible = window.getComputedStyle(toggler).display !== 'none';
        if (isTogglerVisible && !link.classList.contains('dropdown-toggle')) {
          navbarCollapse.classList.remove('show');
        }
      });
    });

    // Inicializa todos los dropdowns de Bootstrap para que funcionen
    var dropdownElements = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
    dropdownElements.map(function (dropdownToggleEl) {
      return new bootstrap.Dropdown(dropdownToggleEl);
    });
  });
</script>

</body>
</html>
