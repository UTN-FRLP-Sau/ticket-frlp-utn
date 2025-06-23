var $monto = 0;
var $costo = parseInt(document.getElementById("costoVianda").value);

const checkboxesViandas = document.querySelectorAll('input[type="checkbox"].check-vianda');
const costoDisplayDiv = document.getElementById('costoDisplay');
const btnCompra = document.getElementById('btnCompra');
const formCompra = document.getElementById('formCompraId');
const btnReset = document.getElementById('btnReset');

function calcularTotalYEstadoBoton() {
    $monto = 0;
    let algunaViandaSeleccionada = false;

    checkboxesViandas.forEach(checkbox => {
        const fieldsetPadre = checkbox.closest('fieldset');
        const selectsDentro = fieldsetPadre ? fieldsetPadre.querySelectorAll('select') : [];

        // Si el fieldset está deshabilitado por el backend (ya comprado o feriado)
        if (fieldsetPadre && fieldsetPadre.hasAttribute('disabled')) {
            checkbox.checked = checkbox.hasAttribute('checked');
            selectsDentro.forEach(select => select.disabled = true);
        } else {
            // Si el fieldset no está deshabilitado
            if (checkbox.checked) {
                $monto += $costo;
                algunaViandaSeleccionada = true;
                selectsDentro.forEach(select => select.disabled = false);
            } else {
                selectsDentro.forEach(select => select.disabled = true);
            }
        }
    });

    if (costoDisplayDiv) {
        costoDisplayDiv.innerHTML = "<strong>Costo: $ " + $monto.toFixed(2) + "-.</strong>";
    }

    if (btnCompra) {
        btnCompra.disabled = !algunaViandaSeleccionada;
    }

    let totalInput = formCompra.querySelector('input[name="total"]');
    if (!totalInput) {
        totalInput = document.createElement('input');
        totalInput.type = 'hidden';
        totalInput.name = 'total';
        formCompra.appendChild(totalInput);
    }
    totalInput.value = $monto.toFixed(2);
}

document.addEventListener('DOMContentLoaded', function() {
    calcularTotalYEstadoBoton();

    checkboxesViandas.forEach(checkbox => {
        checkbox.addEventListener('change', calcularTotalYEstadoBoton);
        
        // Al cargar la página, si un fieldset está deshabilitado (ya comprado o feriado)
        const fieldsetPadre = checkbox.closest('fieldset');
        if (fieldsetPadre && fieldsetPadre.hasAttribute('disabled')) {
            const selectsDentro = fieldsetPadre.querySelectorAll('select');
            selectsDentro.forEach(select => select.disabled = true);
        }
    });

    if (btnReset) {
        btnReset.addEventListener('click', function() {
            // Recorre todos los checkboxes y los desmarca
            checkboxesViandas.forEach(checkbox => {
                const fieldsetPadre = checkbox.closest('fieldset');
                if (fieldsetPadre && !fieldsetPadre.hasAttribute('disabled')) {
                    checkbox.checked = false;
                }
            });
            calcularTotalYEstadoBoton(); // Recalcula el monto y estados de los botones/selects
        });
    }
});