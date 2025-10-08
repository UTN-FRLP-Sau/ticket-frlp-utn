<footer>
    <div class="container">
        <div class="row">
            <div class="col">
                <p class="text-center mb-0" style="font-size:95%">Desarrollado por <strong>RONCONI, Jorge</strong> y
                    <strong>PAGANI,
                        Walter</strong>.
                </p>
                <p class="text-center mb-0" style="font-size:95%">Actualizado por <strong>Secretaria de Asuntos
                        Universitarios </strong> - <strong>UTN-FRLP</strong>.</p>
                <p class=" text-center mt-3 mb-0" style="font-size:95%">Basado en el sistema desarrollado por Lautaro BIFANO, Dario RUMBO y Jose DORCE.</p>
                <p class="text-center mb-0"><strong>Ticket-Web © Copyright <?= date('Y'); ?></strong></p>
            </div>
        </div>
    </div>
</footer>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"
    integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous">
</script>
<script src="<?= base_url('assets/js/vue.js'); ?>"></script>
</body>

</html>

<style>
footer{
    background-color: #212529;
    color:white;
    padding: 3rem;
}


@media (max-width: 767.98px) { 
    footer{
    background-color: #212529;
    color:white;
    padding: 1rem;
    font-size: 0.9rem;
}
}
</style>