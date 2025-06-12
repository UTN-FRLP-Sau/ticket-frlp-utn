<div class="container mt-4">
    <h1 class="mb-2">Configuración General del Sitio</h1>
    <div class="mb-2" style="background-color: lightblue; padding: 20px;">
        <?php
    $dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sabado'];
    $diaInicial = $dias[$configuracion[0]->dia_inicial];
    $diaFinal = $dias[$configuracion[0]->dia_final];

    $fechaInicio = DateTime::createFromFormat('Y-m-d', $configuracion[0]->apertura)->format('j \d\e F \d\e\l Y');
    $fechaEntregaInicio = DateTime::createFromFormat('Y-m-d', $configuracion[0]->apertura)->modify('next monday')->format('j \d\e F \d\e\l Y');
    $fechaCierre = DateTime::createFromFormat('Y-m-d', $configuracion[0]->cierre)->format('j \d\e F \d\e\l Y');
    $fechaEntregaCierre = DateTime::createFromFormat('Y-m-d', $configuracion[0]->cierre)->modify('next friday')->format('j \d\e F \d\e\l Y');
    $fechaVacacionesInicio = DateTime::createFromFormat('Y-m-d', $configuracion[0]->vacaciones_i)->modify('next monday')->format('j \d\e F \d\e\l Y');
    $fechaVacacionesCierre = DateTime::createFromFormat('Y-m-d', $configuracion[0]->vacaciones_f)->modify('next friday')->format('j \d\e F \d\e\l Y');
    ?>
        Actualmente la compra se puede realizar desde las <strong>00:01 hs</strong> del
        <strong><?= $diaInicial; ?></strong>,
        hasta las <strong><?= (new DateTime($configuracion[0]->hora_final))->format('H:i'); ?> hs</strong> del <strong>
            <?= $diaFinal; ?> </strong> </br>

        La compra se encuentra habilitadas desde el <strong><?= $fechaInicio; ?></strong> hasta el
        <strong><?= $fechaCierre; ?></strong>, es decir,
        que la primera entrega se realiza el <strong><?= $fechaEntregaInicio; ?></strong> y la ultima el
        <strong><?= $fechaEntregaCierre; ?></strong> </br>

        No se entregaran comida desde el <strong><?= $fechaVacacionesInicio; ?></strong> hasta el
        <strong><?= $fechaVacacionesCierre; ?></strong>, por receso Invernal
    </div>

    <?= form_open(current_url()); ?>
    <div class="form-group row mb-2"> 
        <label for="apertura_comedor" class="col-sm-5 col-md-5 col-lg-3 col-form-label"> Apertura del Comedor:</label>
        <div class="col-sm-4 col-md-3">
            <input type="date" id="apertura_comedor" name="apertura_comedor" class="form-control"
                value="<?= $configuracion[0]->apertura ?>" required>
        </div>
    </div>

    <div class="form-group row mb-2">
        <label for="cierre_comedor" class="col-sm-5 col-md-5 col-lg-3 col-form-label">Cierre del Comedor:</label>
        <div class="col-sm-4 col-md-3">
            <input type="date" id="cierre_comedor" name="cierre_comedor" class="form-control"
                value="<?= $configuracion[0]->cierre ?>" required>
        </div>
    </div>

    <div class="form-group row mb-2">
        <label for="inicio_receso" class="col-sm-5 col-md-5 col-lg-3 col-form-label">Inicio del Receso:</label>
        <div class="col-sm-4 col-md-3">
            <input type="date" id="inicio_receso" name="inicio_receso" class="form-control"
                value="<?= $configuracion[0]->vacaciones_i ?>" required>
        </div>
    </div>

    <div class="form-group row mb-2">
        <label for="fin_receso" class="col-sm-5 col-md-5 col-lg-3 col-form-label">Fin del Receso:</label>
        <div class="col-sm-4 col-md-3">
            <input type="date" id="fin_receso" name="fin_receso" class="form-control"
                value="<?= $configuracion[0]->vacaciones_f ?>" required>
        </div>
    </div>

    <div class="form-group row mb-2">
        <label for="inicio_venta_semana" class="col-sm-5 col-md-5 col-lg-3 col-form-label">Inicio de la Venta en la
            Semana:</label>
        <div class="col-sm-4 col-md-3">
            <select id="inicio_venta_semana" name="inicio_venta_semana" class="form-control" required>
                <option value="1" <?= ($configuracion[0]->dia_inicial == '1') ? 'selected' : ''; ?>>Lunes</option>
                <option value="2" <?= ($configuracion[0]->dia_inicial == '2') ? 'selected' : ''; ?>>Martes</option>
                <option value="3" <?= ($configuracion[0]->dia_inicial == '3') ? 'selected' : ''; ?>>Miércoles</option>
                <option value="4" <?= ($configuracion[0]->dia_inicial == '4') ? 'selected' : ''; ?>>Jueves</option>
                <option value="5" <?= ($configuracion[0]->dia_inicial == '5') ? 'selected' : ''; ?>>Viernes</option>
                <option value="6" <?= ($configuracion[0]->dia_inicial == '6') ? 'selected' : ''; ?>>Sábado</option>
                <option value="0" <?= ($configuracion[0]->dia_inicial == '0') ? 'selected' : ''; ?>>Domingo</option>
            </select>
        </div>
    </div>

    <div class="form-group row mb-2">
        <label for="fin_venta_semana" class="col-sm-5 col-md-5 col-lg-3 col-form-label">Fin de la Venta en la
            Semana:</label>
        <div class="col-sm-4 col-md-3">
            <select id="fin_venta_semana" name="fin_venta_semana" class="form-control" required>
                <option value="1" <?= ($configuracion[0]->dia_final == '1') ? 'selected' : ''; ?>>Lunes</option>
                <option value="2" <?= ($configuracion[0]->dia_final == '2') ? 'selected' : ''; ?>>Martes</option>
                <option value="3" <?= ($configuracion[0]->dia_final == '3') ? 'selected' : ''; ?>>Miércoles</option>
                <option value="4" <?= ($configuracion[0]->dia_final == '4') ? 'selected' : ''; ?>>Jueves</option>
                <option value="5" <?= ($configuracion[0]->dia_final == '5') ? 'selected' : ''; ?>>Viernes</option>
                <option value="6" <?= ($configuracion[0]->dia_final == '6') ? 'selected' : ''; ?>>Sábado</option>
                <option value="0" <?= ($configuracion[0]->dia_final == '0') ? 'selected' : ''; ?>>Domingo</option>
            </select>
        </div>
    </div>

    <div class="form-group row mb-2">
        <label for="hora_cierre_venta" class="col-sm-5 col-md-5 col-lg-3 col-form-label">Hora de Cierre de la
            Venta:</label>
        <div class="col-sm-4 col-md-3">
            <input type="time" id="hora_cierre_venta" name="hora_cierre_venta" class="form-control"
                value="<?= $configuracion[0]->hora_final ?>" required>
        </div>
    </div>

    <div class="form-group row mb-2">
        <div class="col-sm-4 col-md-3 offset-sm-3">
            <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
    </div>
    <?= form_close(); ?>
</div>