@extends('layouts.app')

@section('title', 'Editar Rinde')

@section('content')
<div class="page-wrap">
    <div class="card mat-card">
        <div class="card-header header-mint py-3">
            <div class="mat-header">
                <h3 class="mat-title">
                    <i class="fa-solid fa-pen me-2"></i> Editar rinde
                </h3>
            </div>
        </div>

        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    Revisá los campos marcados y volvé a intentar.
                </div>
            @endif

            <form action="{{ route('rindes.update', $rinde) }}" method="POST">
                @method('PUT')
                @include('abm.rindes._form')
            </form>
        </div>
    </div>
</div>
@endsection
