    <div class="container">
        <div class="row">
            <div class="col mt-5">
                <h5 class="title text-center">Mi perfil</h5>
            </div>
        </div>
        <div class="row form-center">
            <div class="col-8 col-md-6 col-xl-5 my-3">
                <?php if ($this->session->flashdata('error') != null) : ?>
                <div class="alert alert-danger"><?= $this->session->flashdata('error'); ?></div>
                <?php endif; ?>
                <?php if ($this->session->flashdata('success') != null) : ?>
                <div class="alert alert-success"><?= $this->session->flashdata('success'); ?></div>
                <?php endif; ?>
                <?= validation_errors('<div class="alert alert-danger">', '</div>'); ?>
                <?= form_open(current_url()); ?>
                <div class="mb-3">
                    <label for="email" class="form-label">E-Mail</label>
                    <input type="text" class="form-control" id="email" name="email"
                        value="<?= set_value('email', $usuario->mail); ?>">
                </div>
                <?php if ($usuario->aspirante == 1) : ?>
                <div class="alert alert-warning">
                    Todavía no tenés cargado tu legajo oficial. Esta información será contrastada con el
                    sistema de alumnos: si no corresponde, tu usuario será bloqueado. Solo podés cargarlo
                    una vez, así que verificalo bien antes de guardar.
                </div>
                <div class="mb-4">
                    <label for="legajo" class="form-label">Legajo</label>
                    <input type="number" class="form-control" id="legajo" name="legajo"
                        value="<?= set_value('legajo', $usuario->legajo); ?>">
                </div>
                <?php else : ?>
                <div class="mb-4">
                    <label for="legajo" class="form-label">Legajo</label>
                    <input type="text" class="form-control" id="legajo" value="<?= $usuario->legajo; ?>" disabled>
                    <div class="form-text">
                        Tu legajo ya está cargado y no se puede modificar desde acá.
                    </div>
                </div>
                <?php endif; ?>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Guardar</button>
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
