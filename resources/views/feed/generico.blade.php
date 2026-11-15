{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<propiedades portal="{{ config('app.name') }}" generado="{{ now()->toIso8601String() }}">
@foreach($propiedades as $p)
  <propiedad>
    <id>{{ $p->id }}</id>
    <url>{{ $p->inmobiliaria ? route('propiedad.show', [$p->inmobiliaria->slug, $p->id]) : '' }}</url>
    <titulo>{{ $p->titulo }}</titulo>
    <descripcion>{{ $p->descripcion }}</descripcion>
    <operacion>{{ $p->tipo_operacion }}</operacion>
    <tipo>{{ \App\Models\Inmueble::tiposPropiedad()[$p->tipo_propiedad] ?? $p->tipo_propiedad }}</tipo>
    <precio moneda="{{ $p->moneda ?? 'ARS' }}">{{ number_format((float) $p->precio, 2, '.', '') }}</precio>
    <direccion>{{ $p->direccion }}</direccion>
    <ciudad>{{ $p->ciudad }}</ciudad>
    <provincia>{{ $p->inmobiliaria->provincia ?? '' }}</provincia>
    @php $c = array_map('trim', explode(',', (string) $p->coordenadas)); @endphp
    <latitud>{{ isset($c[0]) && is_numeric($c[0]) ? $c[0] : '' }}</latitud>
    <longitud>{{ isset($c[1]) && is_numeric($c[1]) ? $c[1] : '' }}</longitud>
    <superficie_cubierta>{{ $p->superficie_cubierta }}</superficie_cubierta>
    <ambientes>{{ $p->ambientes ?? '' }}</ambientes>
    <habitaciones>{{ $p->habitaciones }}</habitaciones>
    <banos>{{ $p->banos }}</banos>
    <cocheras>{{ $p->cocheras ?? '' }}</cocheras>
    <fotos>
@foreach(($p->fotos ?? []) as $foto)
      <foto>{{ asset('images/fotosinmuebles/' . $foto) }}</foto>
@endforeach
    </fotos>
    <inmobiliaria>{{ $p->inmobiliaria->nombre_comercial ?? '' }}</inmobiliaria>
  </propiedad>
@endforeach
</propiedades>
