    <div class="container">
        <div class="row">
            <div class="col my-4">
                <h2 class="text-center">Bienvenido, <?= $usuario->nombre; ?></h2>
                <p class="text-center">Saldo actual: $<?= number_format($usuario->saldo, 2); ?></p>
                <p hidden class="text-center">Monto a acreditar: $<?= number_format($monto_acreditar, 2); ?></p>
            </div>
            <div class="d-flex justify-content-center align-items-center">
                <div class="row form-center">
                    <div class="col-10 col-md-8 col-xl-7 my-5 text-center">
                        <?php foreach ($links as $link) : ?>
                        <form action="<?= base_url('usuario/carga_virtual/add') ?>" method="POST" class="d-inline"
                            target='_blanck' onsubmit="setTimeout(() => { location.reload(); }, 2000);">
                            <input type="hidden" name="id_link" value="<?= $link->id; ?>">
                            <button type="submit" class="btn btn-primary mt-3 btn-lg">Cargar:
                                $<?= $link->valor; ?>
                            </button>
                        </form>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="text-center my-3">
                <a href="https://wa.me/542216919358?text=<?= urlencode("Hola, te mando el comprobante de transferencia:\n\nNombre: {$usuario->nombre}\nApellido: {$usuario->apellido}\nN° DNI: {$usuario->documento}\n\nGracias!") ?>"
                    target="_blank" class="btn btn-success">
                    Enviar Comprobante
                </a>
            </div>
        </div>
    </div>