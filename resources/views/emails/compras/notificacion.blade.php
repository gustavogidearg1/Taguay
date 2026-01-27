@component('mail::message')
# Compra {{ $action }}

**Código:** {{ $compra->id }}
**Fecha:** {{ optional($compra->fecha)->format('d/m/Y') }}
**Organización:** {{ $compra->organizacion?->name }}
**Campaña:** {{ $compra->campania?->name }}

@component('mail::table')
| Producto | Cantidad | Unidad | Precio | Subtotal |
|---|---:|---|---:|---:|
@foreach($compra->subCompras as $s)
| {{ $s->producto?->name }} | {{ $s->cantidad }} | {{ $s->unidad?->corta ?? $s->unidad?->name }} | {{ $s->precio }} | {{ $s->sub_total }} |
@endforeach
@endcomponent

Gracias,
{{ config('app.name') }}
@endcomponent
