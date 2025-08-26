<style>
    html, body {
        height: 100%;
        margin: 0;
        padding: 0;
    }
    body {
        display: flex;
        flex-direction: column;
    }
    .main-content {
        flex: 1 0 auto;
    }
    footer {
        flex-shrink: 0;
    }
</style>

<div class="main-content">
    <div class="container my-5">
        <div class="row">
            <div class="col-12">
                <h2>Aprobar Usuarios Pendientes</h2>
                <hr>
                <?php if ($this->session->flashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= $this->session->flashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                <?php if ($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= $this->session->flashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                <?php if ($this->session->flashdata('warning')): ?>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <?= $this->session->flashdata('warning') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-12">
                <?php if (empty($usuarios_pendientes)): ?>
                    <div class="alert alert-info">
                        No hay usuarios pendientes de aprobación.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead class="bg-dark text-white">
                                <tr>
                                    <th>Apellido</th>
                                    <th>Nombre</th>
                                    <th>DNI</th>
                                    <th>Legajo</th>
                                    <th>Correo</th>
                                    <th>Claustro</th>
                                    <th>Carrera</th>
                                    <th>Certificado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($usuarios_pendientes as $usuario): ?>
                                    <tr>
                                        <td><?= html_escape($usuario->apellido) ?></td>
                                        <td><?= html_escape($usuario->nombre) ?></td>
                                        <td><?= html_escape($usuario->documento) ?></td>
                                        <td><?= html_escape($usuario->legajo) ?></td>
                                        <td><?= html_escape($usuario->mail) ?></td>
                                        <td><?= html_escape($usuario->tipo) ?></td>
                                        <td><?= html_escape($usuario->especialidad) ?></td>
                                        <td>
                                            <?php if (!empty($usuario->certificado_path)): ?>
                                                <a href="<?= base_url($usuario->certificado_path) ?>" target="_blank" class="btn btn-sm btn-info">Ver Certificado</a>
                                            <?php else: ?>
                                                No disponible
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('admin/administrador/aprobar/' . $usuario->id) ?>" class="btn btn-primary btn-sm me-2">Aprobar</a>
                                            <a href="<?= base_url('admin/administrador/rechazar/' . $usuario->id) ?>" class="btn btn-secondary btn-sm">Rechazar</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>