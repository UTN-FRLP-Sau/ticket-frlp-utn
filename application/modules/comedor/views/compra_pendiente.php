<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Compra Pendiente</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(to right, #ece9e6, #ffffff);
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
      background-color: #fff3cd;
      border-radius: 50%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 100px;
      height: 100px;
      margin-bottom: 1.5rem;
      animation: pulse 2s infinite ease-in-out;
    }

    .icon-container i {
      font-size: 3.5rem !important;
      color: #ffc107;
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
      margin-bottom: 1.5rem;
    }

    .notice-box {
      background-color: #fffbe6;
      border-left: 5px solid #ffc107;
      padding: 1rem 1.5rem;
      border-radius: 0.5rem;
      margin-bottom: 2rem;
      display: flex;
      align-items: center;
      gap: 1rem;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .notice-box .bi {
      font-size: 1.8rem;
      color: #ffc107;
    }

    .notice-box p {
      margin-bottom: 0;
      color: #6c757d;
      font-weight: 600;
    }

    .btn-warning {
      background-color: #ffc107;
      border-color: #ffc107;
      color: #343a40;
      font-weight: 600;
      padding: 0.8rem 3rem;
      border-radius: 50px;
      transition: all 0.3s ease;
    }

    .btn-warning:hover {
      background-color: #e0a800;
      border-color: #e0a800;
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    @keyframes pulse {
      0% { transform: scale(1); }
      50% { transform: scale(1.05); }
      100% { transform: scale(1); }
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
                <i class="bi bi-hourglass-split"></i>
              </div>
              <h2 class="card-title">Tu compra está pendiente.</h2>
              <p class="card-text">
                Te avisaremos por correo la confirmación de tu compra cuando se acredite el pago.
              </p>

              <div class="notice-box">
                <i class="bi bi-info-circle-fill"></i>
                <p>
                  Dependiendo del método de pago elegido, la acreditación puede tardar hasta 1 hora.
                </p>
              </div>

              <a href="<?php echo base_url('usuario'); ?>" class="btn btn-warning">
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