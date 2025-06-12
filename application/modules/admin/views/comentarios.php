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
                <h1>Comentarios</h1>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <label for="fechaFiltro">Filtrar por fecha:</label>
                <input type="date" id="fechaFiltro">
                <button id="aplicarFiltro">Aplicar Filtro</button>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <label for="busqueda">Buscar comentarios:</label>
                <input type="text" id="busqueda">
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="table-responsive">
                    <table class="table table-sm text-center">
                        <thead>
                            <tr>
                                <th class=col style="text-align: center">ID</th>
                                <th class=col style="text-align: center">Usuario</th>
                                <th class=col style="text-align: center">Comentario</th>
                                <th class=col style="text-align: center">Fecha</th>
                                <th class=col style="text-align: center">Hora</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($comentarios as $comentario): ?>
                                <tr class="comment-row">
                                <td style="text-align: center"><?= $comentario['id']; ?></td>
                                <td style="text-align: center"><?= $comentario['usuario']; ?></td>
                                <td class="comment-cell" style="text-align: center">
                                    <span class="comment-short">
                                        <?= strlen($comentario['comentario']) > 50 ? substr($comentario['comentario'], 0, 20) . '...': $comentario['comentario']; ?>
                                    </span> 
                                    <span class="comment-full" style="display: none;">
                                        <?= $comentario['comentario']; ?>
                                    </span>
                                    <?php if (strlen($comentario['comentario']) > 50): ?>
                                        <a href="#" class="toggle-comment">Ver más</a>
                                    <?php endif; ?>
                                </td>
                                <td class="comment-fecha" style="text-align: center"><?= $comentario['fecha']; ?></td>
                                <td style="text-align: center"><?= date('H:i', strtotime($comentario['hora'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- ... -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleCommentLinks = document.querySelectorAll('.toggle-comment');

        toggleCommentLinks.forEach(function (link) {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const commentCell = e.target.parentElement;
                const commentShort = commentCell.querySelector('.comment-short');
                const commentFull = commentCell.querySelector('.comment-full');

                if (commentShort.style.display === 'none') {
                    // Si el comentario completo ya está visible, ocultarlo
                    commentShort.style.display = 'inline';
                    commentFull.style.display = 'none';
                    e.target.textContent = 'Ver más';
                } else {
                    // Si el comentario corto está visible, mostrar el completo
                    commentShort.style.display = 'none';
                    commentFull.style.display = 'inline';
                    e.target.textContent = 'Ver menos';
                }
            });
        });
    });

    document.addEventListener('DOMContentLoaded', function () {

    const aplicarFiltroBtn = document.getElementById('aplicarFiltro');
    aplicarFiltroBtn.addEventListener('click', aplicarFiltro);

    function aplicarFiltro() {
    const fechaFiltro = document.getElementById('fechaFiltro').value;
    const comentarios = document.querySelectorAll('.comment-row'); // Asigna una clase a las filas de comentarios

    comentarios.forEach(function (comentario) {
        const fechaComentario = comentario.querySelector('.comment-fecha').textContent; // Asigna una clase a la columna de fecha

        if (fechaFiltro === fechaComentario) {
            comentario.style.display = 'table-row'; // Muestra el comentario si coincide con la fecha seleccionada
        } else {
            comentario.style.display = 'none'; // Oculta el comentario si no coincide con la fecha seleccionada
        }
    });
    }

    });

    document.addEventListener('DOMContentLoaded', function () {

    const campoBusqueda = document.getElementById('busqueda');
    campoBusqueda.addEventListener('input', buscarComentarios);

    function buscarComentarios() {
    const palabraClave = document.getElementById('busqueda').value.toLowerCase();
    const comentarios = document.querySelectorAll('.comment-row'); // Asigna una clase a las filas de comentarios

    comentarios.forEach(function (comentario) {
        const textoComentario = comentario.querySelector('.comment-cell').textContent.toLowerCase(); // Asigna una clase al contenido del comentario

        if (textoComentario.includes(palabraClave) || palabraClave === '') {
            comentario.style.display = 'table-row'; // Muestra el comentario si contiene la palabra clave
        } else {
            comentario.style.display = 'none'; // Oculta el comentario si no contiene la palabra clave
        }
    });
    }

    });


</script>
</body>
</html>