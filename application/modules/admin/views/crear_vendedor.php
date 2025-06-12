    <div class="container">
        <div class="row">
            <div class="col-10 my-3">
                <h1>Crear nuevo Vendedor</h1>
                <?= validation_errors('<div class="alert alert-danger alert-dismissible fade show" role="alert">', ' <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'); ?>
                <?= form_open(current_url()); ?>
                <div class="form-group col">
                    <div class="row">
                        <label class="col-sm-2 col-form-label">Usuario:</label>
                        <div class="mb-2 col-sm-3">
                            <input type="text" class="form-control" name="nickName">
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-2 col-form-label">Nombre:</label>
                        <div class="mb-2 col-sm-3">
                            <input type="text" class="form-control" name="nombre">
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-2 col-form-label">Apellido:</label>
                        <div class="mb-2 col-sm-3">
                            <input type="text" class="form-control" name="apellido">
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-2 col-form-label">DNI:</label>
                        <div class="mb-2 col-sm-3">
                            <input type="number" class="form-control" name="documento">
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-2 col-form-label">E-Mail:</label>
                        <div class="mb-2 col-sm-3">
                            <input type="text" class="form-control" name="email">
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-2 col-form-label">Tipo:</label>
                        <div class="mb-2 col-sm-3">
                            <select class="form-control" name="tipo">
                                <option value="0">Cajero</option>
                                <option value="2">Repartidor</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-success">Crear usuario</button>
                    </div>
                    <?= form_close(); ?>
                </div>
            </div>
        </div>
    </div>