<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Menú Semanal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        :root {
            --dark-blue: #2c3e50;
            --primary-color: #3498db;
            --success-color: #27ae60;
            --info-color:rgb(246, 176, 14);
            --dark-gray: #34495e;
            --light-gray-bg: #f8f9fa;
        }


        .menu-title {
            color: var(--dark-blue);
            margin-bottom: 3rem;
            font-size: calc(2rem + 2vw);
        
        }

    
        .menu-card {
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
            border: 1px solid rgba(0,0,0,.125);
            border-radius: 0.5rem;
        }
        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15) !important;
        }

        .menu-card-header {
            background-color: var(--dark-blue);
            color: white;
            border-bottom: none;
        }

        .menu-card-body {
            background-color: var(--light-gray-bg);
            display: flex;         
            flex-direction: column;
            align-items: center;   
            text-align: center; 
        }

        .menu-item {
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }
        .menu-item:last-child {
            border-bottom: none !important;
            padding-bottom: 0;
            margin-bottom: 0;
        }
        .border-dashed {
            border-bottom: 1px dashed rgba(0,0,0,.2) !important;
        }

        .text-dark-blue { color: var(--dark-blue) !important; }
        .text-primary { color: var(--primary-color) !important; }
        .text-success { color: var(--success-color) !important; }
        .text-info { color: var(--info-color) !important; }
        .text-dark-gray { color: var(--dark-gray) !important; }

    
        @media (min-width: 1400px) {
            .row-cols-xxl-custom > * {
                flex: 0 0 auto;
            }
        }
        @media (max-width: 576px) {
            .menu-card {
                max-width: 80%;
            }
            .menu-card-body {
            
                display: flex;
                flex-direction: column;
                align-items: center;  
                text-align: center;   
            }
            .d-flex {
                display: flex !important;
                justify-content: center;
            }
        }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row mb-5">
        <div class="col-12">
            <h1 class="text-center display-3 fw-bold text-dark-blue menu-title">
                <span class="d-block animate__animated animate__fadeInDown">Menú Semanal</span>
            </h1>
        </div>
    </div>

    <div class="row justify-content-center g-4
                row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 row-cols-xxl-custom">
        <?php if (!empty($menu)): ?>
            <?php foreach ($menu as $item) : ?>
                <div class="col d-flex animate__animated animate__fadeInUp">
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
                                <h6 class="fw-bold text-success mb-1">Menú Vegetariano:</h6>
                                <p class="mb-0 text-dark-gray"><?= htmlspecialchars($item->menu2); ?></p>
                            </div>
                            <div class="menu-item">
                                <h6 class="fw-bold text-info mb-1">Menú Sin TACC:</h6>
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

</body>
</html>