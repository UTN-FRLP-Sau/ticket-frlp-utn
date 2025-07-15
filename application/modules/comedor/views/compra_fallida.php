<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
      <div class="card shadow border-0 rounded-4 text-center p-4">
        <div class="card-body">
          <div class="mb-4">
            <i class="bi bi-x-circle-fill text-danger" style="font-size: 4rem;"></i>
          </div>
          <h2 class="card-title fw-bold mb-3 text-danger">El pago fue rechazado o hubo un error.</h2>
          <p class="card-text mb-4 fs-5 text-muted">Hubo un problema con el pago. Por favor, intente nuevamente o comunicate con nosotros.</p>
          <a href="<?= base_url('comedor/ticket'); ?>" class="btn btn-danger btn-lg px-5 shadow-sm">
            Volver a comprar
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

