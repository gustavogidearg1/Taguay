@extends('layouts.app')

@section('title', 'Editar Avance de Campo')

@section('content')
<div class="page-wrap">
    <div class="card mat-card">
        <div class="card-header header-mint py-3">
            <div class="mat-header">
                <h3 class="mat-title">
                    <i class="fa-solid fa-pen me-2"></i> Editar avance de campo
                </h3>
            </div>
        </div>

        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    Revisá los campos marcados y volvé a intentar.
                </div>
            @endif

<form id="lote-estado-form"
      action="{{ route('lote-estados.update', $registro) }}"
      method="POST"
      enctype="multipart/form-data">
    @method('PUT')
    @include('abm.lote_estados._form')
</form>
        </div>
    </div>
</div>
@endsection
