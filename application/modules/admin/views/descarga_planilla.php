    <div class="container">
        <div class="row form-center">
            <h1 class="text-center"> Descargar Listados </h1>
            <div class="col-10 col-md-7 col-lg-4 my-3">
                <?php $dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes']; ?>
                <?php foreach ($dias as $key => $dia) : ?>
                <?php
                    $nroDia = date('N');
                    $proximo = time() + ((7 - $nroDia + ($key + 1)) * 24 * 60 * 60);
                    $proxima_fecha = date('Y-m-d', $proximo);
                    ?>
                <?= form_open(current_url()); ?>
                <div class="input-group mb-3">
                    <label class="form-control"><?= ucwords($dia); ?>:</label>
                    <input type="timestamp" class="form-control" name="fecha" readonly value="<?= $proxima_fecha; ?>">
                    <div class="input-group-append">
                        <button class="btn btn-success" type="submit">Descargar</button>
                    </div>
                </div>
                <?= form_close(); ?>
                <?php endforeach; ?>
                <?= form_open(current_url()); ?>
                <div class="input-group mb-3">
                    <input type="date" name="fecha" class="form-control">
                    <div class="input-group-append">
                        <button class="btn btn-success" type="submit">Descargar</button>
                    </div>
                </div>
                <?= form_close(); ?>
            </div>
        </div>
    </div>