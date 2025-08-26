<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Registro de Usuario - Comedor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #f1f1f1;
        }
        
        .container {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding-top: 20px;
            padding-bottom: 20px;
        }
    
        .form-center {
            display: flex;
            justify-content: center;
            width: 100%;
        }
    
        .img-fluid.mx-auto.d-block {
            max-width: 250px;
            height: auto;
        }

        .link-text-center {
            text-align: center;
        }

        .footer {
            width: 100%;
            background-color: #f1f1f1;
            padding: 20px 0;
            text-align: center;
            margin-top: auto;
        }

        /* contenedor del formulario */
        .form-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            margin-bottom: 20px;
        }
    </style>

</head>
<body>

    <div class="container">
        <div class="row">
            <div class="col my-4">
                <img class="img-fluid mx-auto d-block" src="<?= base_url('assets/img/logo_comedor.png'); ?>" alt="Logo del Comedor">
            </div>
        </div>
        <div class="row w-100 justify-content-center">
            <div class="col-12 text-center link-text-center mb-4">
                <h1 class="h3 mb-3 fw-normal">Registro de Usuario</h1>
            </div>
            <div class="col-12 text-center link-text-center">
                <p>¿Ya tienes una cuenta? <a href="<?= base_url('login'); ?>">Ingresa aquí</a>.</p>
            </div>
        </div>
        <div class="row form-center">
            <div class="col-12 col-md-8 col-lg-6 my-3">
                <div class="form-container">
                    <?= form_open_multipart('usuario/registro/registro', 'class="row g-3"'); ?>
                    
                    <?php if (validation_errors() != null) : ?>
                        <div class="alert alert-danger" role="alert">
                            <?= validation_errors(); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($this->session->flashdata('error') != null) : ?>
                        <div class="alert alert-danger" role="alert"><?= $this->session->flashdata('error'); ?></div>
                    <?php endif; ?>

                    <?php if ($this->session->flashdata('success') != null) : ?>
                        <div class="alert alert-success" role="alert"><?= $this->session->flashdata('success'); ?></div>
                    <?php endif; ?>

                    <div class="col-md-6">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" value="<?= set_value('nombre'); ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="apellido" class="form-label">Apellido</label>
                        <input type="text" class="form-control" id="apellido" name="apellido" value="<?= set_value('apellido'); ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="dni" class="form-label">DNI</label>
                        <input type="number" class="form-control" id="dni" name="dni" value="<?= set_value('dni'); ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="legajo" class="form-label">Legajo</label>
                        <input type="number" class="form-control" id="legajo" name="legajo" value="<?= set_value('legajo'); ?>">
                    </div>
                    <div class="col-12">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= set_value('email'); ?>">
                    </div>
                    <div class="col-12">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>
                    <div class="col-12">
                        <label for="passconf" class="form-label">Confirmar Contraseña</label>
                        <input type="password" class="form-control" id="passconf" name="passconf">
                    </div>
                    <div class="col-md-6">
                        <label for="claustro" class="form-label">Claustro</label>
                        <select id="claustro" name="claustro" class="form-select">
                            <option value="">Seleccione...</option>
                            <option value="Alumno" <?= set_select('claustro', 'Alumno'); ?>>Alumno</option>
                            <option value="No docente" <?= set_select('claustro', 'No docente'); ?>>No docente</option>
                            <option value="Docente" <?= set_select('claustro', 'Docente'); ?>>Docente</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="carrera" class="form-label">Carrera (solo para alumnos)</label>
                        <select id="carrera" name="carrera" class="form-select">
                            <option value="">Seleccione...</option>
                            <option value="Civil" <?= set_select('carrera', 'Civil'); ?>>Civil</option>
                            <option value="Industrial" <?= set_select('carrera', 'Industrial'); ?>>Industrial</option>
                            <option value="Eléctrica" <?= set_select('carrera', 'Eléctrica'); ?>>Eléctrica</option>
                            <option value="Mecánica" <?= set_select('carrera', 'Mecánica'); ?>>Mecánica</option>
                            <option value="Sistemas" <?= set_select('carrera', 'Sistemas'); ?>>Sistemas</option>
                            <option value="Química" <?= set_select('carrera', 'Química'); ?>>Química</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label for="userfile" class="form-label">Adjuntar Certificado de Alumno Regular (PDF o imagen)</label>
                        <input class="form-control" type="file" id="userfile" name="userfile">
                        <div class="form-text">El archivo puede ser .pdf, .jpg, .jpeg, o .png. Tamaño máximo 2MB.</div>
                    </div>
                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-success">Registrarme</button>
                        <a href="<?= base_url('login'); ?>" class="btn btn-secondary mt-2">Volver al login</a>
                    </div>
                    <?= form_close(); ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const claustroSelect = document.getElementById('claustro');
            const carreraSelect = document.getElementById('carrera');
            const userfileInput = document.getElementById('userfile');

            function toggleCarrera() {
                if (claustroSelect.value === 'Alumno') {
                    carreraSelect.disabled = false;
                    userfileInput.disabled = false;
                } else {
                    carreraSelect.disabled = true;
                    carreraSelect.value = '';
                    userfileInput.disabled = true;
                    userfileInput.value = '';
                }
            }

            // Llamar a la función al cargar la página para reflejar el valor inicial
            toggleCarrera();

            // Llamar a la función cada vez que el valor del select de claustro cambie
            claustroSelect.addEventListener('change', toggleCarrera);
        });
    </script>
</body>
</html>