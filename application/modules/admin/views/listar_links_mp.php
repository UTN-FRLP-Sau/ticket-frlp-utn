
<div class="container">
    <div class="row">
        <div class="col mt-5">
            <h2 class="text-center"># Links de Pagos Habilitados</h2>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="table-responsive">
                <table class="table table-sm text-center">
                    <thead>
                        <tr>
                            <th style="text-align: left;" class="col">Tipo</th>
                            <th class="col">Monto</th>
                            <th class="col">Link</th>
                            <th style="text-align: right;" class="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($links as $link) : ?>
                        <tr>
                            <td style="text-align: left;"><?= $link->tipo_user; ?></td>
                            <td>$ <?= $link->valor; ?></td>
                            <td><a href="<?= $link->link; ?>" target="_blank"><?= $link->link; ?></a></td>
                            <td style="text-align: right;">
                                <?= form_open(base_url('admin/configuracion/links/rm'), ['style' => 'display:inline;']); ?>
                                    <input type="hidden" name="id_link" value="<?= $link->id; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                <?= form_close(); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row form-center">
        <h1 class="text-center">Añadir Boton de Pago</h1>
        <div class="col-12">
            <div class="accordion accordion-flush" id="accordionFlushExample">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="flush-headingOne">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                            Añadir un Boton
                        </button>
                    </h2>
                    <div id="flush-collapseOne" class="accordion-collapse collapse" aria-labelledby="flush-headingOne"
                        data-bs-parent="#accordionFlushExample">
                        <div class="accordion-body">
                            <?= form_open(base_url('admin/configuracion/links/add')); ?>
                            <p>Indique tipo de usuario, monto y link:</p>
                            <div class="input-group mb-3">
                                <select name="tipo_usuario" class="form-control" required>
                                    <option value="" disabled selected>Seleccione un tipo de usuario</option>
                                    <option value="estudiante">Estudiante</option>
                                    <option value="docente">Docente</option>
                                    <option value="no_docente">No Docente</option>
                                </select>
                                <input type="int" placeholder="Monto" name="monto" class="form-control" required>
                                <input type="text" placeholder="Link del Boton" name="link" class="form-control" >
                                <div class="input-group-append">
                                    <button class="btn btn-success" type="submit">Agregar</button>
                                </div>
                            </div>
                            <?= form_close(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
