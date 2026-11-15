{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<avisos>
@foreach($propiedades as $p)
  <aviso>
    <id_inmobiliaria>{{ $p->inmobiliaria->id ?? '' }}</id_inmobiliaria>
    <id_aviso>{{ $p->id }}</id_aviso>
    <url>{{ $p->inmobiliaria ? route('propiedad.show', [$p->inmobiliaria->slug, $p->id]) : '' }}</url>
    <titulo>{{ $p->titulo }}</titulo>
    <descripcion>{{ $p->descripcion }}</descripcion>
    <tipo_operacion>{{ $p->tipo_operacion }}</tipo_operacion>
    <tipo_propiedad>{{ \App\Models\Inmueble::tiposPropiedad()[$p->tipo_propiedad] ?? $p->tipo_propiedad }}</tipo_propiedad>
    <precio>{{ number_format((float) $p->precio, 2, '.', '') }}</precio>
    <moneda>{{ $p->moneda ?? 'ARS' }}</moneda>
    <direccion>{{ $p->direccion }}</direccion>
    <ciudad>{{ $p->ciudad }}</ciudad>
    <provincia>{{ $p->inmobiliaria->provincia ?? '' }}</provincia>
    @php $c = array_map('trim', explode(',', (string) $p->coordenadas)); @endphp
    <latitud>{{ isset($c[0]) && is_numeric($c[0]) ? $c[0] : '' }}</latitud>
    <longitud>{{ isset($c[1]) && is_numeric($c[1]) ? $c[1] : '' }}</longitud>
    <superficie_cubierta>{{ $p->superficie_cubierta }}</superficie_cubierta>
    <ambientes>{{ $p->ambientes ?? '' }}</ambientes>
    <dormitorios>{{ $p->habitaciones }}</dormitorios>
    <banos>{{ $p->banos }}</banos>
    <cocheras>{{ $p->cocheras ?? '' }}</cocheras>
    <expensas>{{ $p->expensas ?? '' }}</expensas>
    <fotos>
@foreach(($p->fotos ?? []) as $foto)
      <foto><url_foto>{{ asset('images/fotosinmuebles/' . $foto) }}</url_foto></foto>
@endforeach
    </fotos>
    <contacto>
      <nombre>{{ $p->inmobiliaria->nombre_comercial ?? '' }}</nombre>
      <email>{{ $p->inmobiliaria->email ?? '' }}</email>
      <telefono>{{ $p->inmobiliaria->telefono ?? '' }}</telefono>
    </contacto>
  </aviso>
@endforeach
</avisos>
