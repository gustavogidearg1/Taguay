@extends('layouts.app')

@section('title', 'Nuevo Estado por Lote')

@section('content')
<div class="page-wrap">
    <div class="card mat-card">
        <div class="card-header header-mint py-3">
            <div class="mat-header">
                <h3 class="mat-title">
                    <i class="fa-solid fa-plus me-2"></i> Nuevo estado por lote
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
      action="{{ route('lote-estados.store') }}"
      method="POST"
      enctype="multipart/form-data">
    @include('abm.lote_estados._form')
</form>
        </div>
    </div>
</div>
@endsection
