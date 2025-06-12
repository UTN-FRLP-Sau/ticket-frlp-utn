<div class="container mt-4">
    <div class="col-sm-5 col-md-5 col-lg-5 mx-auto">

        <h1 class="mb-2">Precios de las viandas</h1>

        <?= form_open(current_url()); ?>
        <?php foreach ($precios as $key => $precio) : ?>

        <div class="form-group row mb-2">
            <label for="precio_<?= strtolower($precio->id); ?>" class="col-xs-7 col-sm-6 col-form-label">
                <?= $precio->tipo_user; ?></label>
            <div class="col-sm-6">
                <input type="number" id="precio_<?= strtolower($precio->id); ?>"
                    name="precio_<?= strtolower($precio->id); ?>" class="form-control" step="0.01"
                    value="<?= $precio->costo ?>" required>
            </div>
        </div>
        <?php endforeach; ?>

        <div class="form-group row mb-2">
            <div class="col-sm-4 col-md-3 offset-sm-3">
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </div>
        <?= form_close(); ?>
    </div>
</div>