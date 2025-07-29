<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
            <h1 class="text-center mb-4 text-primary fw-bold display-5">Preguntas Frecuentes</h1>
            <p class="text-center text-muted fs-5 mb-5">Encuentra respuestas a tus preguntas más comunes sobre la compra de viandas.</p>

            <div class="input-group mb-4 shadow-sm rounded-pill overflow-hidden faq-search-bar">
                <span class="input-group-text border-0 bg-white ps-4">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" class="form-control border-0 py-3 ps-2" id="faqSearchInput" placeholder="Busca tu pregunta...">
                <button class="btn btn-outline-secondary border-0 pe-4" type="button" id="clearSearchButton" style="display: none;">
                    <i class="bi bi-x-lg text-muted"></i>
                </button>
            </div>
            <div class="accordion shadow-lg rounded-3 overflow-hidden" id="faqAccordion">

                <div class="accordion-item border-0">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button collapsed fs-5 py-3" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                            <i class="bi bi-question-circle-fill me-3 text-secondary"></i> ¿Cómo funciona el sistema de compra de viandas?
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne"
                        data-bs-parent="#faqAccordion">
                        <div class="accordion-body bg-light text-secondary"> Puedes seleccionar las viandas que deseas comprar para diferentes días y turnos (mediodía/noche). El sistema te mostrará el costo total y aplicará automáticamente tu saldo disponible. Si el saldo no es suficiente, pagarás la diferencia a través de MercadoPago.
                        </div>
                    </div>
                </div>

                <div class="accordion-item border-0">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed fs-5 py-3" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            <i class="bi bi-question-circle-fill me-3 text-secondary"></i>
                            ¿Puedo comprar viandas para mediodía y noche el mismo día?
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                        data-bs-parent="#faqAccordion">
                        <div class="accordion-body bg-light text-secondary">
                            No, la elección de una vianda para el turno mediodía o noche en un mismo día es excluyente. Una vez que seleccionas un menú para un turno, el otro turno para ese día se deshabilitará.
                        </div>
                    </div>
                </div>

                <div class="accordion-item border-0">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed fs-5 py-3" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            <i class="bi bi-question-circle-fill me-3 text-secondary"></i>
                            ¿Qué hago si mi saldo no cubre el total de la compra?
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                        data-bs-parent="#faqAccordion">
                        <div class="accordion-body bg-light text-secondary">
                            El sistema calculará automáticamente la diferencia que necesitas pagar. Podrás abonar este monto restante utilizando MercadoPago al finalizar tu compra.
                        </div>
                    </div>
                </div>

                <div class="accordion-item border-0">
                    <h2 class="accordion-header" id="headingFour">
                        <button class="accordion-button collapsed fs-5 py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                            <i class="bi bi-question-circle-fill me-3 text-secondary"></i>
                            ¿Cómo puedo devolver una vianda comprada?
                        </button>
                    </h2>
                    <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour"
                        data-bs-parent="#faqAccordion">
                        <div class="accordion-body bg-light text-secondary">
                            Para gestionar la devolución de una vianda, debes ir a la sección "Gestionar Devoluciones" en la página principal de compra. Allí encontrarás las opciones para iniciar el proceso de devolución.
                        </div>
                    </div>
                </div>

                <div class="accordion-item border-0">
                    <h2 class="accordion-header" id="headingFive">
                        <button class="accordion-button collapsed fs-5 py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                            <i class="bi bi-question-circle-fill me-3 text-secondary"></i>
                            ¿Qué significa si un día está marcado como "FERIADO" o "RECESO INVERNAL"?
                        </button>
                    </h2>
                    <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive"
                        data-bs-parent="#faqAccordion">
                        <div class="accordion-body bg-light text-secondary">
                            Si un día está marcado como "FERIADO" o "RECESO INVERNAL", no podrás seleccionar viandas para ese día, ya que el comedor no estará operativo. Estos días están inhabilitados para la compra.
                        </div>
                    </div>
                </div>

                <div class="accordion-item border-0">
                    <h2 class="accordion-header" id="headingSix">
                        <button class="accordion-button collapsed fs-5 py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                            <i class="bi bi-question-circle-fill me-3 text-secondary"></i>
                            ¿Hay un límite de tiempo para comprar o devolver las viandas?
                        </button>
                    </h2>
                    <div id="collapseSix" class="accordion-collapse collapse" aria-labelledby="headingSix"
                        data-bs-parent="#faqAccordion">
                        <div class="accordion-body bg-light text-secondary">
                            Sí, las viandas solo pueden ser compradas o devueltas con una semana de anticipación. No será posible pedir viandas o devolverlas para la semana en curso.
                        </div>
                    </div>
                </div>

                <div class="accordion-item border-0">
                    <h2 class="accordion-header" id="headingSeven">
                        <button class="accordion-button collapsed fs-5 py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven">
                            <i class="bi bi-question-circle-fill me-3 text-secondary"></i>
                            ¿Si devuelvo una vianda, me transfieren de vuelta el dinero?
                        </button>
                    </h2>
                    <div id="collapseSeven" class="accordion-collapse collapse" aria-labelledby="headingSeven"
                        data-bs-parent="#faqAccordion">
                        <div class="accordion-body bg-light text-secondary">
                            No, al devolver una vianda, el dinero no se transfiere de vuelta a tu cuenta bancaria o billetera virtual. En su lugar, el saldo se acredita a tu cuenta del comedor para futuras compras. Esto significa que podrás usar ese saldo en lugar de dinero real para tus próximas compras de viandas.
                        </div>
                    </div>
                </div>

                <div class="accordion-item border-0">
                    <h2 class="accordion-header" id="headingEight">
                        <button class="accordion-button collapsed fs-5 py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEight" aria-expanded="false" aria-controls="collapseEight">
                            <i class="bi bi-question-circle-fill me-3 text-secondary"></i>
                            ¿Qué significa la etiqueta o estado "Esperando Acreditación"?
                        </button>
                    </h2>
                    <div id="collapseEight" class="accordion-collapse collapse" aria-labelledby="headingEight"
                        data-bs-parent="#faqAccordion">
                        <div class="accordion-body bg-light text-secondary">
                            Significa que el dinero salío de la cuenta origen, pero aún no se ha acreditado en la cuenta del comedor. Este proceso puede tardar un tiempo dpendiendo del medio de pago elegido, y una vez que se complete, recibirás un correo con el recibo de la compra o en caso de que no se complete por cualquier motivo recibiras un correo de compra rechazada.
                        </div>
                    </div>
                </div>

                <div class="accordion-item border-0">
                    <h2 class="accordion-header" id="headingNine">
                        <button class="accordion-button collapsed fs-5 py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseNine" aria-expanded="false" aria-controls="collapseNine">
                            <i class="bi bi-question-circle-fill me-3 text-secondary"></i>
                            ¿Qué significa la etiqueta o estado "Pago Pendiente"?
                        </button>
                    </h2>
                    <div id="collapseNine" class="accordion-collapse collapse" aria-labelledby="headingNine"
                        data-bs-parent="#faqAccordion">
                        <div class="accordion-body bg-light text-secondary">
                            Significa que se detectó un proceso de compra que no se completó y requiere que se realice el pago. Esto puede ocurrir si el proceso de compra se interrumpió antes de finalizar el pago. Para resolverlo, debes o retomar el pago, o cancelar la compra.
                        </div>
                    </div>
                </div>

            </div>
            <div class="text-center mt-5">
                <p class="fs-5 text-muted">¿No encontraste lo que buscabas?</p>
                <a href="<?= base_url('contacto'); ?>" class="btn btn-primary btn-lg mt-3 shadow-sm">
                    <i class="bi bi-envelope-fill me-2"></i> Contáctanos
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .text-primary{
        color: #FF5722 !important;
    }
    .text-secondary{
        color:rgb(35, 35, 35) !important;
    }
    .btn-primary {
        background-color: #FF5722 !important;
        border-color: #FF5722 !important;
    }
    /* Estilos para la barra de búsqueda redondeada y sin cortes */
    .faq-search-bar .input-group-text,
    .faq-search-bar .form-control,
    .faq-search-bar .btn {
        border-radius: 50px !important;
        border: 1px solid #dee2e6;
        background-color: white;
    }

    .faq-search-bar .input-group-text {
        border-right: none !important; /* Elimina el borde derecho del icono para fusionar */
    }

    .faq-search-bar .form-control {
        border-left: none !important; /* Elimina el borde izquierdo del input para fusionar */
    }

    .faq-search-bar .btn {
        border-left: none !important; /* Elimina el borde izquierdo del botón para fusionar */
    }

    /* Ajustes específicos para los bordes cuando están en un grupo */
    .faq-search-bar .input-group-text:first-child {
        border-top-right-radius: 0 !important;
        border-bottom-right-radius: 0 !important;
    }

    .faq-search-bar .form-control:not(:last-child) {
        border-top-left-radius: 0 !important;
        border-bottom-left-radius: 0 !important;
    }

    .faq-search-bar .btn:last-child {
        border-top-left-radius: 0 !important;
        border-bottom-left-radius: 0 !important;
    }

    /* Elimina el outline/sombra azul al hacer foco */
    .faq-search-bar .form-control:focus,
    .faq-search-bar .btn:focus {
        outline: none !important; /* Elimina el contorno predeterminado del navegador */
        box-shadow: none !important; /* Elimina la sombra azul que Bootstrap añade al foco */
        border-color: #dee2e6 !important;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('faqSearchInput');
    const clearSearchButton = document.getElementById('clearSearchButton');
    const accordionItems = document.querySelectorAll('.accordion-item');

    // Función para normalizar texto (eliminar acentos y convertir a minúsculas)
    function normalizeText(text) {
        return text.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
    }

    // Función para filtrar los elementos del acordeón
    function filterFaqItems() {
        const searchTerm = normalizeText(searchInput.value);

        accordionItems.forEach(item => {
            const questionElement = item.querySelector('.accordion-button');
            const answerElement = item.querySelector('.accordion-body');

            // Normaliza el texto de la pregunta y la respuesta
            const normalizedQuestion = normalizeText(questionElement.textContent);
            const normalizedAnswer = normalizeText(answerElement.textContent);

            if (normalizedQuestion.includes(searchTerm) || normalizedAnswer.includes(searchTerm)) {
                item.style.display = ''; // Muestra el elemento
            } else {
                item.style.display = 'none'; // Oculta el elemento
            }
        });

        // Muestra/oculta el botón de limpiar
        if (searchInput.value.length > 0) { // Usamos searchInput.value para ver si hay texto, no el normalizado
            clearSearchButton.style.display = 'block';
        } else {
            clearSearchButton.style.display = 'none';
        }
    }

    // Event listener para cambios en el input
    searchInput.addEventListener('input', filterFaqItems);

    // Event listener para el botón de limpiar
    clearSearchButton.addEventListener('click', function() {
        searchInput.value = ''; // Limpia el input
        filterFaqItems(); // Vuelve a filtrar para mostrar todos los elementos
    });
});
</script>