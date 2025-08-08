<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pago Rechazado</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(to right, #fceaea, #ffffff); 
    }

    .content-wrapper {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 20px;
    }

    .card {
      border-radius: 1.5rem;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
      overflow: hidden;
      background-color: #ffffff;
      border: none;
    }

    .card-body {
      padding: 3rem;
    }

    .icon-container {
      background-color: #f8d7da; 
      border-radius: 50%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 100px;
      height: 100px;
      margin-bottom: 1.5rem;
      animation: shake 0.82s cubic-bezier(.36,.07,.19,.97) both; 
      transform: translate3d(0, 0, 0); 
      backface-visibility: hidden;
      perspective: 1000px;
    }

    .icon-container i {
      font-size: 3.5rem !important;
      color: #dc3545; 
    }

    h2.card-title {
      color: #343a40;
      font-weight: 700 !important;
      font-size: 2.25rem;
      margin-bottom: 1rem;
    }

    p.card-text {
      color: #6c757d;
      font-size: 1.1rem !important;
      line-height: 1.6;
      margin-bottom: 2rem; 
    }

    .btn-danger {
      background-color: #dc3545; 
      border-color: #dc3545;
      color: #ffffff;
      font-weight: 600;
      padding: 0.8rem 3rem;
      border-radius: 50px;
      transition: all 0.3s ease;
    }

    .btn-danger:hover {
      background-color: #c82333; 
      border-color: #bd2130;
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    
    @keyframes shake {
      10%, 90% {
        transform: translate3d(-1px, 0, 0);
      }
      20%, 80% {
        transform: translate3d(2px, 0, 0);
      }
      30%, 50%, 70% {
        transform: translate3d(-4px, 0, 0);
      }
      40%, 60% {
        transform: translate3d(4px, 0, 0);
      }
    }
  </style>
</head>
<body>
  <div class="content-wrapper">
    <div class="container py-5">
      <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
          <div class="card">
            <div class="card-body text-center">
              <div class="icon-container">
                <i class="bi bi-x-circle-fill"></i>
              </div>
              <h2 class="card-title">El pago fue rechazado o hubo un error.</h2>
              <p class="card-text">
                Hubo un problema con el pago. Por favor, intente nuevamente o comunícate con nosotros.
              </p>
              <a href="<?= base_url('comedor/ticket'); ?>" class="btn btn-danger">
                Volver a comprar
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>