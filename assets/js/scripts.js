var $cantidad_dias = 0;
var $monto = 0;
var $saldo_usuario = parseInt(document.getElementById("saldoCuenta").value);
var $costo = parseInt(document.getElementById("costoVianda").value);

function check(event) {
    if (document.getElementById(event.currentTarget.checkDia).checked) {
        document.getElementById(event.currentTarget.dia).disabled = false;
        $cantidad_dias += 1;
        $monto += $costo;
    } else {
        document.getElementById(event.currentTarget.dia).disabled = true;
        $cantidad_dias -= 1;
        $monto -= $costo;
    }

    if ($monto > 0) {
        document.getElementById("btnCompra").disabled = false;
    } else {
        document.getElementById("btnCompra").disabled = true;
    }

    document.getElementById("compra").innerHTML = "<strong>Costo: $ " + $monto + "-.<\strong>";

    document.getElementById("totalCompra").innerHTML =
        '<input name="total" value="' + $monto + '" hidden>';
}

const lunes = document.getElementById("checkLunes");
lunes.addEventListener("change", check, false);
lunes.checkDia = "checkLunes";
lunes.dia = "lunes";

const martes = document.getElementById("checkMartes");
martes.addEventListener("change", check, false);
martes.checkDia = "checkMartes";
martes.dia = "martes";

const miercoles = document.getElementById("checkMiercoles");
miercoles.addEventListener("change", check, false);
miercoles.checkDia = "checkMiercoles";
miercoles.dia = "miercoles";

const jueves = document.getElementById("checkJueves");
jueves.addEventListener("change", check, false);
jueves.checkDia = "checkJueves";
jueves.dia = "jueves";

const viernes = document.getElementById("checkViernes");
viernes.addEventListener("change", check, false);
viernes.checkDia = "checkViernes";
viernes.dia = "viernes";

document.getElementById("btnReset").addEventListener("click", function () {
    $monto = 0;
    document.getElementById("lunes").disabled = true;
    document.getElementById("martes").disabled = true;
    document.getElementById("miercoles").disabled = true;
    document.getElementById("jueves").disabled = true;
    document.getElementById("viernes").disabled = true;

    document.getElementById("btnCompra").disabled = true;
    
    document.getElementById("compra").innerHTML = "<strong>Costo: $ " + $monto + "-.<\strong>";

    document.getElementById("totalCompra").innerHTML =
        '<input name="total" value="' + $monto + '" hidden>';
});