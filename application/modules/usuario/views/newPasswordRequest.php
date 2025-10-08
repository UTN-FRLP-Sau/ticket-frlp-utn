    <div class="container">
        <div class="row">
            <div class="col my-4">
                <img class="img-fluid mx-auto d-block" src="<?= base_url('assets/img/logo_comedor.png'); ?>" alt="">
            </div>
        </div>
        <div class="row form-center">
            <div class="col-4 my-3">
                <?php if ($this->session->flashdata('alerta') != null) : ?>
                <div class="alert alert-warning"><?= $this->session->flashdata('alerta'); ?></div>
                <?php endif; ?>
                <?= validation_errors(); ?>
                <?= form_open(current_url()); ?>
                <div class="form-group mb-4">
                    <input type="password" class="form-control" placeholder="Ingrese nueva contraseña" name="password1">
                </div>
                <div class="form-group mb-4">
                    <input type="password" class="form-control" placeholder="Repetir contraseña" name="password2">
                </div>
                <?php if (isset($alerta)) : ?>
                <div class="text-center alert alert-warning"><?= $alerta ?></div>
                <?php endif; ?>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success">Aceptar</button>
                </div>
                <?= form_close(); ?>
            </div>
        </div>
    </div>