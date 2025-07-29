<div class="container py-5">
    <!-- Modal de éxito -->
    <?php if ($this->session->flashdata('success')) : ?>
        <div class="modal fade" id="successModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-success">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="successModalLabel"><i class="bi bi-check-circle-fill me-2"></i>¡Genial!</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <?= $this->session->flashdata('success'); ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-success" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Modal de error -->
    <?php if (validation_errors()) : ?>
        <div class="modal fade" id="errorModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-danger">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="errorModalLabel"><i class="bi bi-exclamation-triangle-fill me-2"></i>Error</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <?= validation_errors(); ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Formulario de comentarios -->
    <form action="<?= base_url('comedor/agregar_comentario'); ?>" method="post" class="bg-light p-4 rounded shadow-sm">
        <h2 class="mb-3 text-primary"><i class="bi bi-chat-left-text-fill me-2"></i>¡Dejanos tu comentario!</h2>
        <p><strong>ACLARACIÓN:</strong> Por favor, sé lo más explicativo posible. Esto nos ayudará a evitar malentendidos y resolver más rápido cualquier problemática.</p>
        <div class="form-group mb-3">
            <textarea class="form-control" name="comentario" rows="5" placeholder="Ingrese su comentario..." required></textarea>
        </div>
        <button type="submit" class="btn btn-primary" name="agregar_comentario" id="submitButton">
            <i class="bi bi-send-fill me-1"></i>Enviar
        </button>
    </form>
</div>

<style>
    #submitButton{
        width: 100%;
    }
    html, body {
        height: 100%;
        margin: 0;
    }

    body {
        display: flex;
        flex-direction: column;
    }
    .container.py-5 {
        flex: 1;
    }
    .container.py-5 {
        flex: 1;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        <?php if ($this->session->flashdata('success')) : ?>
            var successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
        <?php endif; ?>

        <?php if (validation_errors()) : ?>
            var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
            errorModal.show();
        <?php endif; ?>
    });
</script>
