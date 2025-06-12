    <div class="container">
        <div class="row form-center">
            <h1 class="text-center"> Descargar Informes</h1>
            <div class="col-11 col-md-9 col-lg-7">
                <div class="accordion accordion-flush" id="accordionFlushExample">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-headingOne">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#flush-collapseOne" aria-expanded="false"
                                aria-controls="flush-collapseOne">
                                Caja del dia
                            </button>
                        </h2>
                        <div id="flush-collapseOne" class="accordion-collapse collapse"
                            aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body">
                                <?= form_open(base_url('admin/informe/diario')); ?>
                                <p>Indique la fecha:</p>
                                <div class="input-group mb-3">
                                    <input type="date" value="<?= date('Y-m-d') ?>" name="cierre_fecha"
                                        class="form-control" value="<?= date('d-m-Y') ?>">
                                    <div class="input-group-append">
                                        <button class="btn btn-success" formtarget="_blanck"
                                            type="submit">Descargar</button>
                                    </div>
                                </div>
                                <?= form_close(); ?>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#flush-collapseTwo" aria-expanded="false"
                                aria-controls="flush-collapseTwo">
                                Resumen de caja semanal
                            </button>
                        </h2>
                        <div id="flush-collapseTwo" class="accordion-collapse collapse"
                            aria-labelledby="flush-headingTwo" data-bs-parent="#accordionFlushExample">
                            <?= form_open(base_url('admin/informe/semana')); ?>
                            <div class="input-group mb-3">
                                <p>Indique las fechas entre las que desea el informe (No se mostraran los dias que no
                                    existan cargas):</p>
                                <div class="col-6">
                                    <div class="input-group-prepend">
                                        <span class="fw-bold border-0 input-group-text">Desde el </span>
                                    </div>
                                    <input type="date" name="cierre_fecha_1" class="form-control">
                                </div>
                                <div class="col-6">
                                    <div class="input-group-prepend">
                                        <span class="fw-bold border-0 input-group-text">hasta el
                                        </span>
                                    </div>
                                    <input type="date" name="cierre_fecha_2" class="form-control">
                                </div>
                                <div class="input-group-append">
                                    <button class="btn btn-success" formtarget="_blanck"
                                        type="submit">Descargar</button>
                                </div>
                            </div>
                            <?= form_close(); ?>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#flush-collapseThree" aria-expanded="false"
                                aria-controls="flush-collapseThree">
                                Resumen de pedidos semanales
                            </button>
                        </h2>
                        <div id="flush-collapseThree" class="accordion-collapse collapse"
                            aria-labelledby="flush-headingThree" data-bs-parent="#accordionFlushExample">
                            <?= form_open(base_url('admin/informe/pedido')); ?>
                            <div class="input-group">
                                <p>Indique las fechas entre las que desea el informe (No se mostraran los dias que no
                                    existan compras):</p>
                                <div class="col-6">
                                    <div class="input-group-prepend">
                                        <span class="fw-bold border-0 input-group-text">Desde el </span>
                                    </div>
                                    <input type="date" name="semana_fecha_1" class="form-control">
                                </div>
                                <div class="col-6">
                                    <div class="input-group-prepend">
                                        <span style="background-color: #f7f7f7;"
                                            class="fw-bold border-0 input-group-text">hasta el
                                        </span>
                                    </div>
                                    <input type="date" name="semana_fecha_2" class="form-control">
                                </div>
                                <div class="input-group-append">
                                    <button class="btn btn-success" formtarget="_blanck"
                                        type="submit">Descargar</button>
                                </div>
                            </div>
                            <?= form_close(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>