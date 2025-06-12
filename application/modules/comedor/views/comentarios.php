<head>
    <!-- Incluye Bootstrap CSS y JavaScript desde un CDN (Content Delivery Network) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
</head>
<div class="container" style="height: 60vh">
    <?php if ($this->session->flashdata('success')) : ?>
        <!-- Mensaje de éxito en un modal -->
        <div class="modal fade" data-bs-backdrop="static" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fs-5" id="successModalLabel"> <strong>¡Genial!</strong></h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <?= $this->session->flashdata('success'); ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (validation_errors()) : ?>
        <!-- Mensaje de error en un modal -->
        <div class="modal fade" data-bs-backdrop="static" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fs-5" id="errorModalLabel">Error</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <?= validation_errors(); ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('comedor/agregar_comentario'); ?>" method='post'>
        <div class='form-group' style="margin-top: 10px">
            <h2 for='comentario' style="font-family: tahoma">¡Dejanos tu comentario!</h2>
            <p> <strong>ACLARACIÓN: </strong>Por favor, sea lo más explicativo posible acerca de aquello que nos quiera comentar. Esto nos ayudará a evitar malentendidos y a resolver cualquier problemática de manera más rápida.</p>
            <textarea class='form-control' name='comentario' rows='4' placeholder='Ingrese su comentario...'></textarea>
        </div>
        <button style="margin-top: 10px" type='submit' class='btn btn-primary' name='agregar_comentario' id="submitButton">Enviar</button>
    </form>
</div>
<!-- Agrego scripts para mostrar los modales-->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<!--<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js" integrity="sha384-Rx+T1VzGupg4BHQYs2gCW9It+akI2MM/mndMCy36UVfodzcJcF0GGLxZIzObiEfa" crossorigin="anonymous"></script>-->
<!-- Aquí incluyo el código JavaScript para mostrar los modales -->
<script>
    $(document).ready(function(){
        // Muestra el modal de éxito si no existen errores de validación
        <?php if ($this->session->flashdata('success')) : ?>
            $('#successModal').modal('show');
        <?php endif; ?>

        // Muestra el modal de error si existen errores de validación
        <?php if (validation_errors()) : ?>
            $('#errorModal').modal('show');
        <?php endif; ?>
    });
</script>
</body>
