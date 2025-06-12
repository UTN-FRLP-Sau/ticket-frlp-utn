<!DOCTYPE html>
<html lang="es">
<head>
    <style>
        .table-responsive {
            min-height:40vh;
        }

        .row {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container" >
        <div class="row">
            <div class="col mt-5">
                <h1>Historial de comidas</h1>
            </div>
        </div>
        <!-- <div class="row">
            <div class="col">
                <label for="fechaFiltro">Filtrar por fecha:</label>
                <input type="date" id="fechaFiltro">
                <button id="aplicarFiltro">Aplicar Filtro</button>
            </div>
        </div> -->
        <!-- <div class="row">
            <div class="col">
                <label for="busqueda">Buscar comidas:</label>
                <input type="text" id="busqueda">
            </div>
        </div> -->
        <div class="row">
            <div class="col">
                <div class="table-responsive">
                    <table class="table table-sm text-center">
                        <thead>
                            <tr>
                                <th class=col style="text-align: center">ID</th>
                                <th class=col style="text-align: center">Día</th>
                                <th class=col style="text-align: center">Vendedor</th>
                                <th class=col style="text-align: center">Menu1</th>
                                <th class=col style="text-align: center">Menu2</th>
                                <th class=col style="text-align: center">Menu3</th>
                                <th class=col style="text-align: center">Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($historial_menu as $historial): ?>
                                <tr class="history-row">
                                <td style="text-align: center"><?= $historial->id; ?></td>
                                <td style="text-align: center"><?= $historial->id_dia; ?></td>
                                <td style="text-align: center"><?= $historial->id_vendedor; ?></td>
                                <td style="text-align: center"><?= $historial->menu1; ?></td>
                                <td style="text-align: center"><?= $historial->menu2; ?></td>
                                <td style="text-align: center"><?= $historial->menu3; ?></td>
                                <td class="history-fecha" style="text-align: center"><?= $historial->fecha; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>