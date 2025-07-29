    <div class="container">
        <div class="row">
            <div class="col mt-5">
                <h5 class="title text-center">Cambiar contraseña</h5>
            </div>
        </div>
        <div class="row form-center">
            <div class="col-6 col-md-5 col-xl-4 my-3">
                <?php if ($this->session->flashdata('error') != null) : ?>
                <div class="alert alert-danger"><?= $this->session->flashdata('error'); ?></div>
                <?php endif; ?>
                <?php if ($this->session->flashdata('success') != null) : ?>
                <div class="alert alert-success"><?= $this->session->flashdata('success'); ?></div>
                <?php endif; ?>
                <?= validation_errors('<div><p class="text-center alert alert-danger">', '</p></div>'); ?>
                <?= form_open(current_url()); ?>
                <div class="mb-4">
                    <input type="password" class="form-control" name="password_anterior"
                        placeholder="Contraseña Actual">
                </div>
                <div class="mb-4">
                    <input type="password" class="form-control" name="password_nuevo" placeholder="Nueva Contraseña">
                </div>
                <div class="mb-4">
                    <input type="password" class="form-control" name="password_confirmado"
                        placeholder="Confirme Nueva Contraseña">
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Cambiar contraseña</button>
                </div>
                <?= form_close(); ?>
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
}
.container {
    flex: 1;
}
</style>