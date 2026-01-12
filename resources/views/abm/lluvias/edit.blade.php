@extends('layouts.app')
@section('title','Editar lluvia')
@section('content')
<div class="container">
  <h1 class="mb-3">Editar lluvia</h1>
  <form method="POST" action="{{ route('lluvias.update', $lluvia) }}" enctype="multipart/form-data" class="row g-3">
    @csrf @method('PUT')
    @include('abm.lluvias._form')
    <div class="col-12 d-flex justify-content-end">
      <a href="{{ route('lluvias.show',$lluvia) }}" class="btn btn-outline-secondary me-2">Ver</a>
      <button class="btn btn-primary">Actualizar</button>
    </div>
  </form>
</div>
@endsection
