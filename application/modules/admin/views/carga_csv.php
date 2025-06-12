<div class="container">
    <div class="row">
        <div class="col my-4">
            <h1>Cargar CSV</h1>
        </div>
    </div>
    <div class="row justify-content-center">
        <?php if (isset($correcto)) : ?>
        <div class="col-5 col-md-4 alert alert-info alert-dismissible fade show text-center" role="alert">
            <p> Las cargas se realizaron correctamente </p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        <?php if (isset($subidoError)) : ?>
        <div class="col-5 col-md-4 alert alert-danger alert-dismissible fade show text-center" role="alert">
            <p> <?= $subidoError ?> </p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        <h5 class="col-12 text-center">El CSV a subir debe ser como el del <a
                href="<?= base_url('download/carga.csv'); ?>">modelo</a></h5>
        <form action="<?= base_url('admin/csv_carga'); ?>" method="post" enctype="multipart/form-data">
            <div class="row d-flex justify-content-center align-items-stretch flex-row form-center g-3">
                <div class="col-12 col-md-6">
                    <input class="form-control" type="file" name="archivo_csv">
                </div>
                <div class="col-2 col-md-1">
                    <span style="background-color: #f7f7f7;" class="col-3 fw-bold border-0 input-group-text">Sep:</span>
                </div>
                <div class="col-3 col-md-1">
                    <select class="mb-1 form-select" name="separador">
                        <option selected value=";">;</option>
                        <option value=",">,</option>
                    </select>
                </div>
            </div>
            <div class="col-md-12 text-center">
                <button type="submit" class="btn btn-primary mt-2"> Ver Cargas </button>
            </div>
        </form>
        <div>
            <?php if (isset($cargas)) : ?>
            <div class="row">
                <div class="col">
                    <form action="<?= base_url('admin/csv_confirmar_carga'); ?>" method="post"
                        enctype="multipart/form-data">
                        <div style="max-height: 500px; overflow: scroll" class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Fecha</th>
                                        <th scope="col">Documento</th>
                                        <th scope="col">Apellido</th>
                                        <th scope="col">Nombre</th>
                                        <th scope="col">Monto</th>
                                        <th scope="col">Tipo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cargas as $key => $carga) : ?>
                                    <?php if (isset($cargas[$key][1])) : ?>
                                    <tr>
                                        <th scope="row"><?= $key + 1 ?></th>
                                        <td> <input type="numero" hidden name="<?= "fecha_{$key}"; ?>" readonly
                                                value="<?= $cargas[$key][0]; ?>"> <?= $cargas[$key][0]; ?> </td>
                                        <td> <input type="numero" hidden name="<?= "documento_{$key}"; ?>" readonly
                                                value="<?= $cargas[$key][1]; ?>"> <?= $cargas[$key][1]; ?> </td>
                                        <td> <input type="numero" hidden name="<?= "apellido_{$key}"; ?>" readonly
                                                value="<?= $cargas[$key][2]; ?>"> <?= $cargas[$key][2]; ?> </td>
                                        <td> <input type="numero" hidden name="<?= "nombre_{$key}"; ?>" readonly
                                                value="<?= $cargas[$key][3]; ?>"> <?= $cargas[$key][3]; ?> </td>
                                        <td> <input type="numero" hidden name="<?= "monto_{$key}"; ?>" readonly
                                                value="<?= $cargas[$key][4]; ?>"> <?= $cargas[$key][4]; ?> </td>
                                        <td> <input type="numero" hidden name="<?= "tipo_{$key}"; ?>" readonly
                                                value="<?= $cargas[$key][5]; ?>"> <?= $cargas[$key][5]; ?> </td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn btn-primary mt-2"> Confirmar Cargas </button>
                        </div>
                    </form>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <div class="text-center">
            <?php if (isset($errores)) : ?>
            <h6 class="alert alert-danger">No se pudieron realizar las siguientes cargas</h6>
            <div class="row">
                <div class="col">
                    <div style="max-height: 500px; overflow: scroll" class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Documento</th>
                                    <th scope="col">Apellido</th>
                                    <th scope="col">Nombre</th>
                                    <th scope="col">Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($errores as $key => $carga) : ?>
                                <tr>
                                    <th scope="row"><?= $key + 1 ?></th>
                                    <td> <?= $carga[0]; ?> </td>
                                    <td> <?= $carga[2]; ?> </td>
                                    <td> <?= $carga[1]; ?> </td>
                                    <td> <?= $carga[3]; ?> </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>