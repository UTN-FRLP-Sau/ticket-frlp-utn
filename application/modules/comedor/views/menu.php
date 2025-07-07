<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container py-5">
    <div class="row mb-5">
        <div class="col-12">
            <h1 class="text-center display-3 fw-bold text-dark-blue menu-title">
                <span class="d-block animate__animated animate__fadeInDown">Menú Semanal</span>
            </h1>
        </div>
    </div>

    <div class="row justify-content-center g-4">
        <?php if (!empty($menu)): ?>
            <?php foreach ($menu as $item) : ?>
                <div class="col-12 col-sm-6 col-md-4 col-lg-3 d-flex animate__animated animate__fadeInUp">
                    <div class="card menu-card shadow-lg border-0 rounded-4 flex-fill overflow-hidden">
                        <div class="card-header menu-card-header text-white text-center py-3">
                            <h3 class="card-title mb-0 fw-bolder text-capitalize"><?= htmlspecialchars($item->dia); ?></h3>
                        </div>
                        <div class="card-body menu-card-body p-4">
                            <div class="menu-item mb-3 pb-3 border-bottom border-dashed">
                                <h6 class="fw-bold text-primary mb-1">Menú Básico:</h6>
                                <p class="mb-0 text-dark-gray"><?= htmlspecialchars($item->menu1); ?></p>
                            </div>
                            <div class="menu-item mb-3 pb-3 border-bottom border-dashed">
                                <h6 class="fw-bold text-success mb-1">Opción Veggie:</h6>
                                <p class="mb-0 text-dark-gray"><?= htmlspecialchars($item->menu2); ?></p>
                            </div>
                            <div class="menu-item">
                                <h6 class="fw-bold text-info mb-1">Sin TACC:</h6>
                                <p class="mb-0 text-dark-gray"><?= htmlspecialchars($item->menu3); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <p class="lead text-muted">Aún no hay menú cargado.</p>
            </div>
        <?php endif; ?>
    </div>
</div>