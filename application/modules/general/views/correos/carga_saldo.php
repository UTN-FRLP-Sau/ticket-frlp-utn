        <div>
            <table>
                <tr>
                    <td>
                        <p><b><u> Carga de saldo: <?= $transaccion; ?> <br></u></b></p>
                    </td>
                    <td></td>
                    <td width=100></td>
                </tr>
                <tr>
                    <td><b> Formato de pago: </b></td>
                    <td><?= $tipo; ?></td>
                </tr>
                <tr>
                    <td><b> Fecha: </b></td>
                    <td><?= $fecha; ?></td>
                </tr>
                <tr>
                    <td><b> Hora: </b></td>
                    <td><?= $hora; ?></td>
                </tr>
                <tr>
                    <td><b> Documento: </b></td>
                    <td><?= $documento; ?></td>
                </tr>
                <tr>
                    <td><b> Apellido: </b></td>
                    <td><?= $apellido; ?></td>
                </tr>
                <tr>
                    <td><b> Nombre: </b></td>
                    <td><?= $nombre; ?></td>
                </tr>
                <tr>
                    <td><b> Monto cargado: </b></td>
                    <td> $ <?= $monto; ?>.-</td>
                </tr>
                <tr>
                    <td><b> Saldo actual: </b></td>
                    <td>$ <?= $saldo; ?>.-</td>
                </tr>
            </table>
        </div>