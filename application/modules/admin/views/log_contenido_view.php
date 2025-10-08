<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log: <?= htmlspecialchars($file_name) ?></title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #212529;
            color: #e9ecef;
            font-family: 'Consolas', 'Monaco', monospace;
            padding-top: 20px;
        }
        .container-fluid {
            padding-left: 15px;
            padding-right: 15px;
        }
        .header {
            color: #ffffff;
            font-size: 1.5rem;
            margin-bottom: 20px;
            border-bottom: 2px solid #6c757d;
            padding-bottom: 10px;
        }
        .log-content {
            background-color: #2c313a;
            color: #d1d5da;
            padding: 20px;
            border-radius: 8px;
            white-space: pre-wrap;
            word-wrap: break-word;
            overflow-y: auto;
            max-height: calc(100vh - 120px);
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            font-size: 1rem;
            text-decoration: none;
            color: #adb5bd;
        }
        .back-link:hover {
            color: #ffffff;
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="header d-flex justify-content-between align-items-center">
        <span>Contenido del Log: <?= htmlspecialchars($file_name) ?></span>
        <a href="<?= base_url('admin/logs') ?>" class="back-link">Volver a la lista</a>
    </div>

    <div class="log-content"><?= htmlspecialchars($log_contenido) ?></div>
</div>

</body>
</html>