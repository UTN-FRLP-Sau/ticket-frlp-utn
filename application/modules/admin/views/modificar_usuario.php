    	<div class="container">
    	    <div class="row">
    	        <div class="col-10 my-3">
    	            <h1>Modificar usuario <?= $usuario->id ?></h1>
    	            <?= validation_errors('<div class="alert alert-danger alert-dismissible fade show" role="alert">', ' <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'); ?>
    	            <?= form_open(current_url()); ?>
    	            <div class="form-group col" id="formUpdateUser">
    	                <div class="row">
    	                    <label class="col-sm-2 col-form-label">Legajo:</label>
    	                    <div class="mb-2 col-sm-3">
    	                        <input type="number" class="form-control" name="legajo" value="<?= $usuario->legajo ?>">
    	                    </div>
    	                </div>
    	                <div class="row">
    	                    <label class="col-sm-2 col-form-label">DNI:</label>
    	                    <div class="mb-2 col-sm-3">
    	                        <input type="number" class="form-control" name="documento" value="<?= $usuario->documento ?>">
    	                    </div>
    	                </div>
    	                <div class="row">
    	                    <label class="col-sm-2 col-form-label">Nombre:</label>
    	                    <div class="mb-2 col-sm-3">
    	                        <input type="text" class="form-control" name="nombre" value="<?= $usuario->nombre ?>">
    	                    </div>
    	                </div>
    	                <div class="row">
    	                    <label class="col-sm-2 col-form-label">Apellido:</label>
    	                    <div class="mb-2 col-sm-3">
    	                        <input type="text" class="form-control" name="apellido" value="<?= $usuario->apellido ?>">
    	                    </div>
    	                </div>
    	                <div class="row">
    	                    <label class="col-sm-2 col-form-label">E-Mail:</label>
    	                    <div class="mb-2 col-sm-3">
    	                        <input type="text" class="form-control" name="email" value="<?= $usuario->mail ?>">
    	                    </div>
    	                </div>
    	                <div class="row">
    	                    <label class="col-sm-2 col-form-label">Claustro:</label>
    	                    <div class="col-md-3">
    	                        <select @change="esEstudiante" class="mb-2 form-select" name="claustro" v-model="selectClaustro">
    	                            <option <?= ($usuario->tipo == 'Estudiante') ? 'selected' : ''; ?> value="Estudiante">
    	                                Estudiante</option>
    	                            <option <?= ($usuario->tipo == 'Docente') ? 'selected' : ''; ?> value="Docente">Docente
    	                            </option>
    	                            <option <?= ($usuario->tipo == 'No Docente') ? 'selected' : ''; ?> value="No Docente">
    	                                No Docente</option>
    	                        </select>
    	                    </div>
    	                </div>
    	                <div class="row" v-if="es_estudiante">
    	                    <label class="col-sm-2 col-form-label">Especialidad:</label>
    	                    <div class="col-md-3">
    	                        <select class="mb-2 form-select" name="especialidad">
    	                            <option <?= ($usuario->especialidad == 'civil') ? 'selected' : ''; ?> value="Civil">
    	                                Civil</option>
    	                            <option <?= ($usuario->especialidad == 'Electrica') ? 'selected' : ''; ?>value="Electrica">
    	                                Electrica</option>
    	                            <option <?= ($usuario->especialidad == 'Industrial') ? 'selected' : ''; ?>value="Industrial">
    	                                Industrial</option>
    	                            <option <?= ($usuario->especialidad == 'Mecanica') ? 'selected' : ''; ?>value="Mecanica">
    	                                Mecanica</option>
    	                            <option <?= ($usuario->especialidad == 'Quimica') ? 'selected' : ''; ?>value="Quimica">
    	                                Quimica</option>
    	                            <option <?= ($usuario->especialidad == 'Sistemas') ? 'selected' : ''; ?>value="Sistmeas">
    	                                Sistemas</option>
    	                            <option <?= ($usuario->tipo == 'Estudiante') ? '' : 'selected'; ?> value="">No es
    	                                Estudiante
    	                            </option>
    	                        </select>
    	                    </div>
    	                </div>
    	                <div class="row">
    	                    <label class="col-sm-2 col-form-label">Tiene beca:</label>
    	                    <div class="col-md-3">
    	                        <select class="mb-2 form-select" name="beca">
    	                            <option <?= ($usuario->id_precio != 2) ? 'selected' : ''; ?> value="No">No
    	                            </option>
    	                            <option <?= ($usuario->id_precio == 2) ? 'selected' : ''; ?> value="Si">Si
    	                            </option>
    	                        </select>
    	                    </div>
    	                </div>
    	                <div>
    	                    <button type="submit" class="btn btn-success">Actualizar usuario</button>
    	                </div>
    	                <?= form_close(); ?>
    	            </div>
    	        </div>
    	    </div>
    	</div>
    	<script src="<?= base_url('assets/js/vue.js'); ?>"></script>
    	<script src="<?= base_url('assets/js/update_user.js'); ?>"></script>
