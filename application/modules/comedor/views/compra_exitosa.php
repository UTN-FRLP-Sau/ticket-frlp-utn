<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Compra Exitosa</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(to right, #e6f7ea, #ffffff);
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
      background-color: #d4edda;
      border-radius: 50%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 100px;
      height: 100px;
      margin-bottom: 1.5rem;
      animation: bounceIn 0.8s ease-out;
    }

    .icon-container i {
      font-size: 3.5rem !important;
      color: #28a745;
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

    .btn-primary {
      background-color: #28a745;
      border-color: #28a745;
      color: #ffffff;
      font-weight: 600;
      padding: 0.8rem 3rem;
      border-radius: 50px;
      transition: all 0.3s ease;
    }

    .btn-primary:hover {
      background-color: #218838;
      border-color: #1e7e34;
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

  
    @keyframes bounceIn {
      0%, 20%, 40%, 60%, 80%, 100% {
        transition-timing-function: cubic-bezier(0.215, 0.610, 0.355, 1.000);
      }
      0% {
        opacity: 0;
        transform: scale3d(.3, .3, .3);
      }
      20% {
        transform: scale3d(1.1, 1.1, 1.1);
      }
      40% {
        transform: scale3d(.9, .9, .9);
      }
      60% {
        opacity: 1;
        transform: scale3d(1.03, 1.03, 1.03);
      }
      80% {
        transform: scale3d(.97, .97, .97);
      }
      100% {
        opacity: 1;
        transform: scale3d(1, 1, 1);
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
                <i class="bi bi-check-circle-fill"></i>
              </div>
              <h2 class="card-title">¡Gracias por tu pago!</h2>
              <p class="card-text">
                La compra fue exitosa. Recibirás la confirmación por correo.
              </p>
              <a href="<?= base_url('usuario'); ?>" class="btn btn-primary">
                Volver al inicio
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