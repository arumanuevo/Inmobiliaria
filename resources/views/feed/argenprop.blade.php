{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<inmuebles>
@foreach($propiedades as $p)
  <inmueble>
    <id>{{ $p->id }}</id>
    <url>{{ $p->inmobiliaria ? route('propiedad.show', [$p->inmobiliaria->slug, $p->id]) : '' }}</url>
    <titulo>{{ $p->titulo }}</titulo>
    <descripcion>{{ $p->descripcion }}</descripcion>
    <operacion>{{ $p->tipo_operacion === 'venta' ? 'Venta' : 'Alquiler' }}</operacion>
    <tipo>{{ \App\Models\Inmueble::tiposPropiedad()[$p->tipo_propiedad] ?? $p->tipo_propiedad }}</tipo>
    <precio>{{ number_format((float) $p->precio, 2, '.', '') }}</precio>
    <moneda>{{ $p->moneda ?? 'ARS' }}</moneda>
    <direccion>{{ $p->direccion }}</direccion>
    <ciudad>{{ $p->ciudad }}</ciudad>
    <provincia>{{ $p->inmobiliaria->provincia ?? '' }}</provincia>
    @php $c = array_map('trim', explode(',', (string) $p->coordenadas)); @endphp
    <latitud>{{ isset($c[0]) && is_numeric($c[0]) ? $c[0] : '' }}</latitud>
    <longitud>{{ isset($c[1]) && is_numeric($c[1]) ? $c[1] : '' }}</longitud>
    <superficie>{{ $p->superficie_cubierta }}</superficie>
    <ambientes>{{ $p->ambientes ?? '' }}</ambientes>
    <dormitorios>{{ $p->habitaciones }}</dormitorios>
    <banos>{{ $p->banos }}</banos>
    <cocheras>{{ $p->cocheras ?? '' }}</cocheras>
    <expensas>{{ $p->expensas ?? '' }}</expensas>
    <imagenes>
@foreach(($p->fotos ?? []) as $foto)
      <imagen>{{ asset('images/fotosinmuebles/' . $foto) }}</imagen>
@endforeach
    </imagenes>
    <inmobiliaria>
      <nombre>{{ $p->inmobiliaria->nombre_comercial ?? '' }}</nombre>
      <email>{{ $p->inmobiliaria->email ?? '' }}</email>
      <telefono>{{ $p->inmobiliaria->telefono ?? '' }}</telefono>
    </inmobiliaria>
  </inmueble>
@endforeach
</inmuebles>
