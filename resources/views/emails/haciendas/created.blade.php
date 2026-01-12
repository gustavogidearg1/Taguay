@component('mail::message')
# Nueva Hacienda cargada

Se registró una nueva Hacienda.

@component('mail::panel')
**ID:** {{ $hacienda->id }}  
**Cliente:** {{ $hacienda->cliente }}  
**Consignatario:** {{ $hacienda->consignatario ?: '—' }}  
**Vendedor:** {{ $hacienda->vendedor ?: '—' }}  
**Categoría:** {{ $hacienda->categoria?->nombre ?: '—' }}  
**Establecimiento:** {{ $hacienda->establecimiento?->nombre }} @if($hacienda->establecimiento?->ubicacion) — {{ $hacienda->establecimiento->ubicacion }} @endif  
**Destino:** {{ $hacienda->destino ?: '—' }}

**Cantidad:** {{ number_format($hacienda->cantidad ?? 0, 1, ',', '.') }}  
**Peso vivo (-8%):** {{ $hacienda->peso_vivo_menos_8 !== null ? number_format($hacienda->peso_vivo_menos_8, 1, ',', '.') : '—' }}  
**Subtotal Peso vivo x Cantidad:** {{ number_format($subtotal, 1, ',', '.') }}
@endcomponent

Gracias,  
{{ config('app.name') }}
@endcomponent
