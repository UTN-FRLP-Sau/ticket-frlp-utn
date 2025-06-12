    <div class="container-fluid">
        <?php
            $diasSemana=[
                "Monday"    => "Lunes",
                "Tuesday"   => "Martes",
                "Wednesday" => "Miércoles",
                "Thursday"  => "Jueves",
                "Friday"    => "Viernes",
                "Saturday"  => "Sábado",
                "Sunday"    => "Domingo"
            ];
            $meses=[
                "January"   => "Enero",
                "February"  => "Febrero",
                "March"     => "Marzo",
                "April"     => "Abril",
                "May"       => "Mayo",
                "June"      => "Junio",
                "July"      => "Julio",
                "August"    => "Agosto",
                "September" => "Septiembre",
                "October"   => "Octubre",
                "November"  => "Noviembre",
                "December"  => "Diciembre"
            ];
        ?>
        <div class="row">
            <div class="col mt-5">
                <h2 class="text-center">Reintegro</h2>
                <p>
                    Codigo: <?= $recivoNumero; ?> <br>
                    Fecha:
                    <?= date('d', strtotime($compra->dia_comprado)).' de '.$meses[date('F', strtotime($compra->dia_comprado))].' de '.date('Y', strtotime($compra->dia_comprado)); ?>
                    <br>
                    Hora: <?= date('H:i', strtotime($horaAhora)); ?>
                </p>
            </div>
        </div>
        <div class="row">
            <div class="col">

                <p>Si ha recibido este correo electrónico, se debe a que el comedor universitario no funcionó el día
                    <strong><?= date('d', strtotime($compra->dia_comprado)).' de '.$meses[date('F', strtotime($compra->dia_comprado))].' de '.date('Y', strtotime($compra->dia_comprado)); ?></strong>.
                    Debido a que
                    realizó una
                    compra para ese día, se le ha reintegrado el costo de la misma.
                </p>

                <h2>Detalle del reintegro</h2>

                <ul>
                    <li><strong>Devolución:</strong> $<?= $compra->precio; ?></li>
                    <li><strong>Su nuevo saldo:</strong> $<?= $saldo; ?></li>
                </ul>

                <p>Atentamente,</p>

                <p><strong>Equipo del Comedor Universitario</strong></p>
            </div>
        </div>
    </div>