@extends('layouts.app')

@section('content')
<div class="container-fluid">

  <div class="card mat-card">
    <div class="card-header mat-header d-flex align-items-center">
      <h3 class="mat-title mb-0">
        <i class="fa-solid fa-file-import me-2"></i> Importar Facturas con IA
      </h3>
    </div>

    <div class="card-body">

      {{-- MENSAJES --}}
      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif

      @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
      @endif

      @if($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach($errors->all() as $e)
              <li>{{ $e }}</li>
            @endforeach
          </ul>
        </div>
      @endif


      {{-- =========================
        SUBIR PDF
      ========================= --}}
      <div class="card mat-card mb-3">
        <div class="card-header mat-header d-flex align-items-center">
          <h5 class="mat-title mb-0">
            <i class="fa-solid fa-upload me-2"></i> Subir Facturas PDF
          </h5>
        </div>

        <div class="card-body">

          <form action="{{ route('invoices.upload') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row g-3 align-items-end">

              <div class="col-md-8">
                <label class="form-label">Seleccionar archivos</label>
                <input type="file" name="pdfs[]" multiple accept="application/pdf" class="form-control">
              </div>

              <div class="col-md-4 d-flex">
                <button type="submit" class="btn btn-primary btn-mat w-100">
                  <i class="fa-solid fa-cloud-upload-alt me-1"></i> Subir
                </button>
              </div>

            </div>

          </form>

        </div>
      </div>


      {{-- =========================
        PROCESAR IA
      ========================= --}}
      <div class="card mat-card mb-3">
        <div class="card-header mat-header d-flex align-items-center">
          <h5 class="mat-title mb-0">
            <i class="fa-solid fa-robot me-2"></i> Procesar con IA
          </h5>

          <button type="button" class="btn btn-success btn-mat ms-auto"
                  onclick="document.getElementById('formProcesar').submit();">
            <i class="fa-solid fa-play me-1"></i> Procesar PDFs
          </button>
        </div>

        <div class="card-body">

          <form id="formProcesar" action="{{ route('invoices.process') }}" method="POST">
            @csrf
          </form>

          <div class="text-muted">
            Se analizarán los PDFs y se generará un archivo estructurado automáticamente.
          </div>

        </div>
      </div>


      {{-- =========================
        DESCARGAR
      ========================= --}}
      <div class="card mat-card">
        <div class="card-header mat-header d-flex align-items-center">
          <h5 class="mat-title mb-0">
            <i class="fa-solid fa-download me-2"></i> Exportar Resultado
          </h5>

          <a href="{{ route('invoices.download') }}" class="btn btn-outline-primary btn-mat ms-auto">
            <i class="fa-solid fa-file-csv me-1"></i> Descargar CSV
          </a>
        </div>

        <div class="card-body text-muted">
          Descargá el archivo generado con los datos procesados por IA.
        </div>
      </div>

    </div>
  </div>

</div>
@endsection
