<script>
// Puntaje de entorno calculado DESDE EL NAVEGADOR (Overpass tiene CORS): cuando hay coordenadas,
// se consultan los alrededores, se mandan los elementos a nuestro endpoint /api/entorno/puntuar
// (que solo puntúa, no llama a Overpass) y el resultado viaja en el hidden entorno_json al guardar.
document.addEventListener('DOMContentLoaded', function () {
    var coord = document.getElementById('coordenadas');
    var hidden = document.getElementById('entorno_json');
    var msg = document.getElementById('entorno-calc-msg');
    if (!coord || !hidden) { return; }

    function calc() {
        var p = (coord.value || '').split(',');
        var lat = parseFloat(p[0]), lng = parseFloat(p[1]);
        if (isNaN(lat) || isNaN(lng)) { hidden.value = ''; if (msg) { msg.textContent = ''; } return; }
        if (msg) { msg.textContent = 'Calculando puntaje de entorno...'; msg.className = 'small d-block mt-1 text-muted'; }
        var q = '[out:json][timeout:20];(way(around:150,' + lat + ',' + lng + ')[highway~"^(motorway|trunk|primary|secondary|tertiary|residential|unclassified|service|living_street)$"];'
            + 'nwr(around:400,' + lat + ',' + lng + ')[railway~"^(station|subway_entrance|halt)$"];'
            + 'nwr(around:400,' + lat + ',' + lng + ')[public_transport~"^(stop_position|station|platform)$"];'
            + 'nwr(around:400,' + lat + ',' + lng + ')[bus=yes];'
            + 'nwr(around:400,' + lat + ',' + lng + ')[shop];'
            + 'nwr(around:400,' + lat + ',' + lng + ')[amenity~"^(restaurant|cafe|bar|fast_food|bank|pharmacy|hospital|clinic|school|kindergarten|university|marketplace|supermarket|convenience|fuel|bus_station|police|fire_station|post_office)$"]);'
            + 'out tags center 300;';
        var mirrors = [
            'https://overpass-api.de/api/interpreter',
            'https://overpass.kumi.systems/api/interpreter',
            'https://overpass.private.coffee/api/interpreter'
        ];
        (async function () {
            var elements = null;
            for (var i = 0; i < mirrors.length && !elements; i++) {
                try {
                    var r = await fetch(mirrors[i] + '?data=' + encodeURIComponent(q));
                    if (r.ok) { var j = await r.json(); if (j && j.elements) { elements = j.elements; } }
                } catch (e) { /* siguiente mirror */ }
            }
            if (!elements) {
                hidden.value = '';
                if (msg) { msg.textContent = 'No se pudo calcular ahora; se intentará de nuevo al guardar.'; msg.className = 'small d-block mt-1 text-muted'; }
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
        })();
    }

    var timer = null;
    coord.addEventListener('input', function () { clearTimeout(timer); timer = setTimeout(calc, 1200); });
    coord.addEventListener('change', calc);
    // el geocoder/clic de mapa setea .value sin disparar input: poll simple
    var last = coord.value;
    setInterval(function () { if (coord.value !== last) { last = coord.value; calc(); } }, 1500);
    calc();
});
</script>
