    <div class="container">
        <div class="row">
            <div class="col my-4">
                <img class="img-fluid mx-auto d-block" src="<?= base_url('assets/img/Logo_comedor_vendedor.png'); ?>"
                    alt="">
            </div>
        </div>
        <div class="row form-center">
            <div class="col-6 col-md-5 col-xl-4 my-3">
                <?= form_open(current_url()); ?>
                <div class="form-group mb-4">
                    <input type="text" class="form-control" placeholder="Ingrese su Usuario" name="nick-name">
                </div>
                <div class="form-group mb-4">
                    <input type="password" class="form-control" placeholder="Su contraseña" name="password">
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success">Ingresar</button>
                </div>
                <?= form_close(); ?>
            </div>
        </div>
    </div>