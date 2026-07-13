    <div class="container">
        <div class="row">
            <div class="col mt-5">
                <h5 class="title text-center">Mis notificaciones</h5>
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
                <?= form_open(current_url()); ?>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="notif_recordatorio_compra"
                        name="notif_recordatorio_compra" value="1"
                        <?= $preferencias->notif_recordatorio_compra ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="notif_recordatorio_compra">
                        Recordatorio semanal de compra
                    </label>
                    <div class="form-text">
                        Recibí un mail recordándote comprar tus viandas si todavía no compraste para la semana.
                    </div>
                </div>
                <div class="mb-4 form-check">
                    <input type="checkbox" class="form-check-input" id="notif_recordatorio_retiro"
                        name="notif_recordatorio_retiro" value="1"
                        <?= $preferencias->notif_recordatorio_retiro ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="notif_recordatorio_retiro">
                        Recordatorio diario de retiro
                    </label>
                    <div class="form-text">
                        Recibí un mail recordándote retirar tu vianda el día que te corresponde.
                    </div>
                </div>
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
