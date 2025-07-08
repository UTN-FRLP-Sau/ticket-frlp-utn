<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
            <h1 class="text-center mb-4 text-primary fw-bold display-5">Preguntas Frecuentes</h1>
            <p class="text-center text-muted fs-5 mb-5">Encuentra respuestas a tus preguntas más comunes sobre la compra de viandas.</p>

            <div class="accordion shadow-lg rounded-3 overflow-hidden" id="faqAccordion">

                <div class="accordion-item border-0"> <h2 class="accordion-header" id="headingOne">
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

            </div><div class="text-center mt-5">
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
</style>