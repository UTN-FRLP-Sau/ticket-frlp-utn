<div class="content-wrapper">
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-12 col-md-8 col-lg-6">
        <div class="card shadow border-0 rounded-4 text-center p-4">
          <div class="card-body">
            <div class="mb-4">
              <i class="bi bi-hourglass-split text-warning" style="font-size: 4rem;"></i>
            </div>
            <h2 class="card-title fw-bold mb-3 text-warning">Tu compra está pendiente.</h2>
            <p class="card-text mb-4 fs-5 text-muted">Te avisaremos por correo la confirmación de tu compra, cuando se acredite el pago.</p>
            <a href="<?php echo base_url('usuario'); ?>" class="btn btn-warning btn-lg px-5 shadow-sm">
              Volver al inicio
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
html, body {
  height: 100%;
  margin: 0;
  padding: 0;
}

body {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.content-wrapper {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}
</style>