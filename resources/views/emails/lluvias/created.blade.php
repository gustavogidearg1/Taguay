@component('mail::message')
# Nueva lluvia registrada

@component('mail::panel')
**Fecha:** {{ $fecha }}
**Hora:** {{ $hora }}
**Establecimiento:** {{ $lluvia->establecimiento->nombre ?? '—' }}
**Milímetros:** **{{ number_format($lluvia->mm,1,',','.') }} mm**
**Fuente:** {{ ucfirst($lluvia->fuente) }}

@isset($lluvia->estacion_nombre)
**Estación:** {{ $lluvia->estacion_nombre }}
@endisset
@isset($lluvia->observador)
**Observador:** {{ $lluvia->observador }}
@endisset
@isset($lluvia->comentario)
**Comentario:** {{ $lluvia->comentario }}
@endisset
@endcomponent

@component('mail::button', ['url' => route('lluvias.show', $lluvia), 'color' => 'primary'])
Ver registro
@endcomponent

Gracias,
{{ config('app.name') }}
@endcomponent
