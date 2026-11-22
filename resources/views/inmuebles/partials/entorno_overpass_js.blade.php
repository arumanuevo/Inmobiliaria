<script>
// Helper compartido: consulta Overpass DESDE EL NAVEGADOR (CORS habilitado: Access-Control-Allow-Origin: *)
// con varios mirrors, reintentos con backoff ante 429/5xx y query liviana (out tags, sin geometria).
window.ENTORNO_MIRRORS = [
    'https://overpass-api.de/api/interpreter',
    'https://overpass.kumi.systems/api/interpreter',
    'https://overpass.private.coffee/api/interpreter',
    'https://overpass.osm.ch/api/interpreter',
    'https://overpass.openstreetmap.ru/api/interpreter'
];
window.entornoQuery = function (lat, lng) {
    return '[out:json][timeout:20];(way(around:150,' + lat + ',' + lng + ')[highway~"^(motorway|trunk|primary|secondary|tertiary|residential|unclassified|service|living_street)$"];'
        + 'nwr(around:400,' + lat + ',' + lng + ')[railway~"^(station|subway_entrance|halt)$"];'
        + 'nwr(around:400,' + lat + ',' + lng + ')[public_transport~"^(stop_position|station|platform)$"];'
        + 'nwr(around:400,' + lat + ',' + lng + ')[bus=yes];'
        + 'nwr(around:400,' + lat + ',' + lng + ')[shop];'
        + 'nwr(around:400,' + lat + ',' + lng + ')[amenity~"^(restaurant|cafe|bar|fast_food|bank|pharmacy|hospital|clinic|school|kindergarten|university|marketplace|supermarket|convenience|fuel|bus_station|police|fire_station|post_office)$"]);'
        + 'out tags 250;';
};
// encodeURIComponent no encodea ( ) ! * ' -> algunos servers tiran 400 con esos crudos
window.entornoEncode = function (s) {
    return encodeURIComponent(s).replace(/[()!*']/g, function (c) {
        return '%' + c.charCodeAt(0).toString(16).toUpperCase();
    });
};
// Devuelve elements[] o null
window.consultarEntorno = async function (lat, lng) {
    var q = window.entornoEncode(window.entornoQuery(lat, lng));
    for (var i = 0; i < window.ENTORNO_MIRRORS.length; i++) {
        for (var attempt = 0; attempt < 3; attempt++) {
            try {
                var r = await fetch(window.ENTORNO_MIRRORS[i] + '?data=' + q);
                if (r.ok) {
                    var j = await r.json();
                    if (j && j.elements) { return j.elements; }
                }
                if (r.status === 429 || r.status >= 500) {
                    await new Promise(function (res) { setTimeout(res, 800 * (attempt + 1)); });
                    continue; // mismo mirror, con backoff
                }
                break; // 400 u otro: pasar al siguiente mirror
            } catch (e) {
                break;
            }
        }
    }
    return null;
};
</script>
