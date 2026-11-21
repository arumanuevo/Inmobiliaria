<script>
document.addEventListener('DOMContentLoaded', function () {
    var lista = document.getElementById('proximidades-list');
    var hidden = document.getElementById('proximidades_json');
    var btnAdd = document.getElementById('btn-agregar-proximidad');
    if (!lista || !hidden || !btnAdd) { return; }

    function sync() {
        var arr = [];
        lista.querySelectorAll('.prox-row').forEach(function (row) {
            var nombre = (row.querySelector('.prox-nombre').value || '').trim();
            var m = parseInt(row.querySelector('.prox-metros').value, 10);
            if (nombre) { arr.push({ nombre: nombre, metros: isNaN(m) ? null : m }); }
        });
        hidden.value = JSON.stringify(arr);
    }

    function agregar(nombre, metros) {
        var row = document.createElement('div');
        row.className = 'prox-row d-flex align-items-center gap-2 mb-2 flex-wrap';
        var nombreSafe = String(nombre || '').replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;');
        var metrosVal = (metros !== null && metros !== undefined && !isNaN(metros)) ? metros : '';
        row.innerHTML =
            '<input type="text" class="form-control form-control-sm prox-nombre" list="proximidades-suggest" maxlength="80" placeholder="Ej: Estación de tren" value="' + nombreSafe + '" style="max-width: 320px;">' +
            '<div class="input-group input-group-sm" style="width: 150px;">' +
                '<input type="number" min="0" max="100000" step="10" class="form-control prox-metros" placeholder="distancia" value="' + metrosVal + '">' +
                '<span class="input-group-text">m</span>' +
            '</div>' +
            '<button type="button" class="btn btn-outline-danger btn-sm prox-del" title="Quitar"><i class="fas fa-times"></i></button>';
        row.querySelector('.prox-nombre').addEventListener('input', sync);
        row.querySelector('.prox-metros').addEventListener('input', sync);
        row.querySelector('.prox-del').addEventListener('click', function () { row.remove(); sync(); });
        lista.appendChild(row);
        sync();
    }

    btnAdd.addEventListener('click', function () { agregar('', null); });

    try {
        var inicial = JSON.parse(hidden.value || '[]');
        if (Array.isArray(inicial)) {
            inicial.forEach(function (p) {
                agregar(p && p.nombre ? p.nombre : '', (p && p.metros !== null && p.metros !== undefined) ? p.metros : null);
            });
        }
    } catch (e) { /* json inválido: se arranca vacío */ }
    if (!lista.children.length) { agregar('', null); }

    var form = hidden.closest('form');
    if (form) { form.addEventListener('submit', sync); }
});
</script>
