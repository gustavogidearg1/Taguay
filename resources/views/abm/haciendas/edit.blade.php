@extends('layouts.app')

@section('content')
<div class="container">
  <h1 class="mb-3">Editar Hacienda #{{ $entry->id }}</h1>

  @if (session('ok'))
    <div class="alert alert-success">{{ session('ok') }}</div>
  @endif
  @if ($errors->any())
    <div class="alert alert-danger">
      <strong>Revisá estos campos:</strong>
      <ul class="mb-0">
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="post" action="{{ route('haciendas.update', $entry->id) }}">
    @method('PUT')
    @include('abm.haciendas._form', ['entry' => $entry])
  </form>
</div>
@endsection
