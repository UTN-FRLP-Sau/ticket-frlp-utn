<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archivos de Logs</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fc;
        }
        .container {
            margin-top: 10px;
        }
        .card {
            border-left: .25rem solid #4e73df!important;
        }
        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
        }
        .table-responsive {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Archivos de Logs</h6>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <input type="text" class="form-control" id="logSearch" placeholder="Buscar por nombre de archivo...">
            </div>
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th id="thNombre" style="cursor:pointer">Nombre del Archivo <span id="sortNombreIcon"></span></th>
                            <th id="thFecha" style="cursor:pointer">Fecha de Modificación <span id="sortFechaIcon"></span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            // Ordena los archivos por fecha descendente (mas reciente primero)
                            if (!empty($log_files)) {
                                $dates = array_column($log_files, 'date');
                                array_multisort($dates, SORT_DESC, $log_files);
                            }
                        ?>
                        <?php foreach ($log_files as $file): ?>
                            <tr>
                                <td>
                                    <a href="<?= base_url('admin/logs/' . $file['name']) ?>">
                                        <?= $file['name'] ?>
                                    </a>
                                </td>
                                <td>
                                    <?= date('d/m/Y H:i:s', $file['date']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    $(document).ready(function() {
        // Filtro de busqueda
        $("#logSearch").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#dataTable tbody tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        // Ordenamiento de columnas
        let sortNombreAsc = false;
        let sortFechaAsc = false;

        function sortTable(colIndex, asc, isDate) {
            var rows = $('#dataTable tbody tr').get();
            rows.sort(function(a, b) {
                var A = $(a).children('td').eq(colIndex).text().toUpperCase();
                var B = $(b).children('td').eq(colIndex).text().toUpperCase();
                if (isDate) {
                    // Parsea fecha dd/mm/yyyy hh:mm:ss
                    var dateA = A.split('/');
                    var timeA = dateA[2].split(' ');
                    var dA = new Date(timeA[0], dateA[1]-1, dateA[0], timeA[1].split(':')[0], timeA[1].split(':')[1], timeA[1].split(':')[2]);
                    var dateB = B.split('/');
                    var timeB = dateB[2].split(' ');
                    var dB = new Date(timeB[0], dateB[1]-1, dateB[0], timeB[1].split(':')[0], timeB[1].split(':')[1], timeB[1].split(':')[2]);
                    return asc ? dA - dB : dB - dA;
                } else {
                    if (A < B) return asc ? -1 : 1;
                    if (A > B) return asc ? 1 : -1;
                    return 0;
                }
            });
            $.each(rows, function(index, row) {
                $('#dataTable tbody').append(row);
            });
        }

        $('#thNombre').click(function() {
            sortNombreAsc = !sortNombreAsc;
            sortTable(0, sortNombreAsc, false);
            $('#sortNombreIcon').html(sortNombreAsc ? '▲' : '▼');
            $('#sortFechaIcon').html('');
        });
        $('#thFecha').click(function() {
            sortFechaAsc = !sortFechaAsc;
            sortTable(1, sortFechaAsc, true);
            $('#sortFechaIcon').html(sortFechaAsc ? '▲' : '▼');
            $('#sortNombreIcon').html('');
        });
    });
</script>

</body>
</html>