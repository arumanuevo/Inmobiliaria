<script>
// Puntaje de entorno calculado DESDE EL NAVEGADOR al cargar coordenadas:
// Overpass (CORS) -> nuestro /api/entorno/puntuar (solo puntúa) -> hidden entorno_json -> se guarda con el form.
document.addEventListener('DOMContentLoaded', function () {
    var coord = document.getElementById('coordenadas');
    var hidden = document.getElementById('entorno_json');
    var msg = document.getElementById('entorno-calc-msg');
    if (!coord || !hidden) { return; }
    var lastCalc = null;

    async function calc() {
        var p = (coord.value || '').split(',');
        var lat = parseFloat(p[0]), lng = parseFloat(p[1]);
        if (isNaN(lat) || isNaN(lng)) { hidden.value = ''; if (msg) { msg.textContent = ''; } return; }
        var key = lat + ',' + lng;
        if (key === lastCalc) { return; } // no recalcular dos veces las mismas coordenadas
        lastCalc = key;
        if (msg) { msg.textContent = 'Calculando puntaje de entorno...'; msg.className = 'small d-block mt-1 text-muted'; }
        var elements = await window.consultarEntorno(lat, lng);
        if (!elements) {
            hidden.value = '';
            if (msg) { msg.textContent = 'No se pudo calcular ahora; se intentará de nuevo al guardar.'; msg.className = 'small d-block mt-1 text-muted'; }
            lastCalc = null;
            return;
        }
        try {
            var r2 = await fetch('{{ route('entorno.puntuar') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ _token: '{{ csrf_token() }}', elements: elements })
            });
            if (r2.ok) {
                var res = await r2.json();
                hidden.value = JSON.stringify(res);
                if (msg) { msg.textContent = 'Puntaje de entorno: ' + res.puntaje + '/100'; msg.className = 'small d-block mt-1 text-success fw-bold'; }
            }
        } catch (e) { /* sin puntaje */ }
    }

    var timer = null;
    coord.addEventListener('input', function () { clearTimeout(timer); timer = setTimeout(calc, 2000); });
    coord.addEventListener('change', calc);
    // el geocoder/clic de mapa setea .value sin disparar input: poll simple
    var last = coord.value;
    setInterval(function () { if (coord.value !== last) { last = coord.value; calc(); } }, 1500);
    calc();
});
</script>
