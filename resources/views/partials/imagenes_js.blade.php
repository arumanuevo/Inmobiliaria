{{--
  Normalización cliente de imágenes ANTES de subirlas (funciona en celular y desktop):
  - window.normalizarImagen(file, opts) => Promise<File> (reescala a opts.max px de lado mayor
    y comprime a JPEG/PNG; si el navegador no soporta canvas/createImageBitmap, devuelve el original)
  - Auto-hook: cualquier <input type="file" data-normalizar> normaliza sus archivos apenas se eligen
    (reemplaza input.files con los optimizados) y avisa en el elemento [data-norm-aviso] del contenedor [data-norm-zone].
  Atributos: data-normalizar, data-max="1600", data-maxkb="700", data-tipo="jpg|png", data-calidad="0.82"
--}}
<script>
(function () {
  window.normalizarImagen = async function (file, opts) {
    opts = opts || {};
    if (!file || !file.type || !file.type.startsWith('image/') || file.type === 'image/svg+xml') return file;
    var max = parseInt(opts.max || '1600', 10);
    var maxKB = parseInt(opts.maxkb || '700', 10);
    // tipo: 'jpg' fuerza JPEG, 'png' fuerza PNG, 'auto' (default) conserva PNG si ya era PNG
    var tipo = opts.tipo === 'png' ? 'image/png'
        : (opts.tipo === 'jpg' ? 'image/jpeg'
        : (file.type === 'image/png' ? 'image/png' : 'image/jpeg'));
    try {
      if (typeof createImageBitmap !== 'function') return file;
      var bmp = await createImageBitmap(file);
      var w = bmp.width, h = bmp.height;
      var yaChica = w <= max && h <= max && file.size <= maxKB * 1024;
      if (yaChica) { if (bmp.close) bmp.close(); return file; }
      var ratio = Math.min(1, max / Math.max(w, h));
      var nw = Math.max(1, Math.round(w * ratio));
      var nh = Math.max(1, Math.round(h * ratio));
      var canvas = document.createElement('canvas');
      canvas.width = nw; canvas.height = nh;
      var ctx = canvas.getContext('2d');
      if (tipo === 'image/png') { ctx.clearRect(0, 0, nw, nh); }
      else { ctx.fillStyle = '#ffffff'; ctx.fillRect(0, 0, nw, nh); }
      ctx.drawImage(bmp, 0, 0, nw, nh);
      if (bmp.close) bmp.close();
      var blob;
      if (tipo === 'image/png') {
        blob = await new Promise(function (res) { canvas.toBlob(res, 'image/png'); });
      } else {
        var calidad = parseFloat(opts.calidad || '0.82');
        blob = await new Promise(function (res) { canvas.toBlob(res, 'image/jpeg', calidad); });
        var intentos = 0;
        while (blob && blob.size > maxKB * 1024 && calidad > 0.5 && intentos < 4) {
          calidad -= 0.12; intentos++;
          blob = await new Promise(function (res) { canvas.toBlob(res, 'image/jpeg', calidad); });
        }
      }
      if (!blob) return file;
      var ext = tipo === 'image/png' ? 'png' : 'jpg';
      var base = (file.name || 'imagen').replace(/\.[^.]+$/, '');
      return new File([blob], base + '.' + ext, { type: tipo, lastModified: Date.now() });
    } catch (e) {
      return file; // ej. HEIC sin soporte: se sube el original y el server lo guarda tal cual
    }
  };

  async function procesarInput(input) {
    var archivos = Array.prototype.slice.call(input.files || []);
    if (!archivos.length) return;
    var zone = input.closest('[data-norm-zone]');
    var aviso = zone ? zone.querySelector('[data-norm-aviso]') : null;
    var opts = {
      max: input.dataset.max || '1600',
      maxkb: input.dataset.maxkb || '700',
      tipo: input.dataset.tipo || 'jpg',
      calidad: input.dataset.calidad || '0.82'
    };
    if (aviso) { aviso.textContent = 'Optimizando ' + archivos.length + ' imagen(es)…'; aviso.classList.remove('d-none'); }
    var out = [];
    for (var i = 0; i < archivos.length; i++) {
      out.push(await window.normalizarImagen(archivos[i], opts));
    }
    try {
      var dt = new DataTransfer();
      out.forEach(function (f) { dt.items.add(f); });
      input.files = dt.files; // el formulario sube los archivos ya optimizados
    } catch (e) { /* navegador viejo sin DataTransfer: sube los originales */ }
    if (aviso) {
      var tot = out.reduce(function (a, f) { return a + f.size; }, 0);
      aviso.textContent = archivos.length + ' imagen(es) listas: ' + Math.max(1, Math.round(tot / 1024)) + ' KB en total (se achican y comprimen automáticamente al subir).';
    }
  }

  document.addEventListener('change', function (e) {
    var input = e.target;
    if (input && input.matches && input.matches('input[type="file"][data-normalizar]') && !input.dataset.normProcesando) {
      input.dataset.normProcesando = '1';
      procesarInput(input).finally(function () { delete input.dataset.normProcesando; });
    }
  }, true);
})();
</script>
