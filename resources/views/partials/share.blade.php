@php
  $shareUrl = $shareUrl ?? url()->current();
  $shareTitulo = $shareTitulo ?? config('app.name');
  $dark = $dark ?? false;
  $shareTexto = rawurlencode($shareTitulo . ' - ' . $shareUrl);
@endphp
<style>
  .share-row { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
  .share-label { font-size: 13px; font-weight: 600; margin-right: 2px; }
  .share-btn {
    width: 36px; height: 36px; border-radius: 999px;
    display: inline-flex; align-items: center; justify-content: center;
    background: #f3f4f6; color: #475467; border: 1px solid #e5e7eb;
    cursor: pointer; transition: all .15s ease; font-size: 15px; text-decoration: none;
  }
  @if($dark)
  .share-label { color: rgba(255,255,255,.85); }
  .share-btn { background: rgba(255,255,255,.14); color: #fff; border-color: rgba(255,255,255,.25); }
  @endif
  .share-wa:hover { background: #25D366 !important; border-color: #25D366 !important; color: #fff !important; }
  .share-fb:hover { background: #1877F2 !important; border-color: #1877F2 !important; color: #fff !important; }
  .share-x:hover { background: #000 !important; border-color: #000 !important; color: #fff !important; }
  .share-in:hover { background: #0A66C2 !important; border-color: #0A66C2 !important; color: #fff !important; }
  .share-mail:hover { background: #6366f1 !important; border-color: #6366f1 !important; color: #fff !important; }
  .share-copy:hover { background: #6366f1 !important; border-color: #6366f1 !important; color: #fff !important; }
  .copy-feedback { font-size: 12.5px; font-weight: 600; color: #059669; }
  @if($dark)
  .copy-feedback { color: #a7f3d0; }
  @endif
</style>
<div class="share-row">
  <span class="share-label"><i class="fas fa-share-alt me-1"></i> Compartir</span>
  <a class="share-btn share-wa" href="https://wa.me/?text={{ $shareTexto }}" target="_blank" rel="noopener" title="Compartir por WhatsApp"><i class="fab fa-whatsapp"></i></a>
  <a class="share-btn share-fb" href="https://www.facebook.com/sharer/sharer.php?u={{ rawurlencode($shareUrl) }}" target="_blank" rel="noopener" title="Compartir en Facebook"><i class="fab fa-facebook-f"></i></a>
  <a class="share-btn share-x" href="https://twitter.com/intent/tweet?text={{ rawurlencode($shareTitulo) }}&url={{ rawurlencode($shareUrl) }}" target="_blank" rel="noopener" title="Compartir en X"><i class="fab fa-x-twitter"></i></a>
  <a class="share-btn share-in" href="https://www.linkedin.com/sharing/share-offsite/?url={{ rawurlencode($shareUrl) }}" target="_blank" rel="noopener" title="Compartir en LinkedIn"><i class="fab fa-linkedin-in"></i></a>
  <a class="share-btn share-mail" href="mailto:?subject={{ rawurlencode($shareTitulo) }}&body={{ $shareTexto }}" title="Enviar por email"><i class="fas fa-envelope"></i></a>
  <button type="button" class="share-btn share-copy btn-copy-link" data-url="{{ $shareUrl }}" title="Copiar enlace"><i class="fas fa-link"></i></button>
  <span class="copy-feedback d-none ms-1">¡Enlace copiado!</span>
</div>
<script>
if (!window.__shareInit) {
  window.__shareInit = true;
  document.addEventListener('click', function (e) {
    var btn = e.target.closest('.btn-copy-link');
    if (!btn) { return; }
    var url = btn.getAttribute('data-url');
    var feedback = btn.parentElement.querySelector('.copy-feedback');
    function ok() {
      if (feedback) {
        feedback.classList.remove('d-none');
        setTimeout(function () { feedback.classList.add('d-none'); }, 2000);
      }
    }
    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(url).then(ok).catch(function () { fallback(); });
    } else {
      fallback();
    }
    function fallback() {
      var ta = document.createElement('textarea');
      ta.value = url;
      document.body.appendChild(ta);
      ta.select();
      try { document.execCommand('copy'); ok(); } catch (err) {}
      document.body.removeChild(ta);
    }
  });
}
</script>
