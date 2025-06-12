<!DOCTYPE html>
<html>
<head>
    <title>Términos y Condiciones</title>
    <style>
        #content-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 60vh;
            margin: 0;
        }
        
        /* Estilos específicos para el div #content-container */
        #content-container h2 {
            font-size: 24px;
            color: #333;
        }
        
        #content-container p {
            font-size: 16px;
            color: #666;
        }
        
        #content-container button {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div id="content-container">
        <h2>Términos y Condiciones</h2>
        <!-- Aquí van tus términos y condiciones -->
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit...</p>
        <div class="col-sm-6">
            <button id="acceptButton" style="margin-top: 10px" onclick="aceptarTerminos()">Aceptar</button>
        </div>
        <div class="col-sm-6">
            <button id="rejectButton" style="margin-top: 10px" onclick="rechazarTerminos()">No Aceptar</button>
        </div>
    </div>

    <script>
        function aceptarTerminos() {
            // Realiza una solicitud al servidor para aceptar los términos
            window.location.href = '<?= base_url('aceptarTerminos') ?>';
        }

        function rechazarTerminos() {
            // Realiza una solicitud al servidor para rechazar los términos
            window.location.href = '<?= base_url('rechazarTerminos') ?>';
        }
    </script>
</body>
</html>