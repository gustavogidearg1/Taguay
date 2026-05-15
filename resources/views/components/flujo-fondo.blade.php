@extends('layouts.app')
@section('title', 'Flujo de fondo')

  {{-- Favicon --}}
  <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

<style>

    html,
    body {
        width: 100%;
        height: 100%;
        margin: 0;
        padding: 0;
        overflow: hidden;
        font-family: 'Roboto', sans-serif;
        background: #f4f6f9;
    }

    /* CONTENEDOR POWER BI */
    .iframe-container {
        width: 100vw;
        height: 100vh;
    }

    .iframe-container iframe {
        width: 100%;
        height: 100%;
        border: none;
    }

    /* MENU FLOTANTE */
.floating-menu {

    position: fixed;

    top: 15px;
    left: 15px;

    z-index: 1000;

    display: flex;
    flex-direction: column;

    gap: 6px;

    padding: 10px;

    border-radius: 14px;

    background: rgba(255, 255, 255, 0.15);

    backdrop-filter: blur(14px);

    -webkit-backdrop-filter: blur(14px);

    box-shadow:
        0 4px 18px rgba(0, 0, 0, 0.18);

    border:
        1px solid rgba(255, 255, 255, 0.20);
}

    /* BOTONES MATERIAL */
    .mat-btn {

    display: flex;
    align-items: center;
    gap: 8px;

    min-width: 140px;

    padding: 8px 10px;

    border: none;
    border-radius: 10px;

    background: #ffffff;

    color: #333;

    font-size: 12px;
    font-weight: 600;

    text-decoration: none;

    cursor: pointer;

    transition: all 0.25s ease;

    box-shadow:
        0 2px 6px rgba(0, 0, 0, 0.10);
}

    /* HOVER */
    .mat-btn:hover {

        transform:
            translateY(-2px);

        background:
            #f7f7f7;

        box-shadow:
            0 8px 20px rgba(0, 0, 0, 0.18);
    }

    /* ICONOS */
.mat-btn .icon {

    font-size: 14px;

    width: 16px;

    text-align: center;
}

    /* EFECTO CLICK */
    .mat-btn:active {
        transform: scale(0.98);
    }

    /* RESPONSIVE */
    @media (max-width: 768px) {

        .floating-menu {

            top: 10px;
            left: 10px;

            padding: 12px;

            gap: 8px;
        }

        .mat-btn {

            min-width: 190px;

            padding: 12px 14px;

            font-size: 13px;
        }
    }

    /* IMPRESIÓN */
@media print {
    .floating-menu,
    header,
    nav,
    footer,
    .navbar,
    .sidebar {
        display: none !important;
    }

    .iframe-container {
        width: 100vw !important;
        height: 100vh !important;
    }
}

</style>

{{-- MENU FLOTANTE --}}
<div class="floating-menu">

    {{-- VOLVER --}}
    <a href="{{ route('home') }}" class="mat-btn">

        <span class="icon">
            ⬅
        </span>

        <span>
            Volver
        </span>

    </a>

    {{-- IMPRIMIR --}}
    <button
        class="mat-btn"
        onclick="imprimirReporte()">

        <span class="icon">
            🖨
        </span>

        <span>
            Imprimir / PDF
        </span>

    </button>

    {{-- PANTALLA COMPLETA --}}
    <button
        class="mat-btn"
        onclick="pantallaCompleta()">

        <span class="icon">
            ⛶
        </span>

        <span>
            Pantalla completa
        </span>

    </button>

</div>


{{-- POWER BI --}}
<div class="iframe-container">
    <iframe
        id="powerbiFrame"
        title="Flujo de Fondo"
        src="https://app.powerbi.com/view?r=eyJrIjoiMWQ2MmMwNzMtZjkyMC00OTczLWE5YWQtNTdiNWJiMWRmNzU2IiwidCI6ImZmZDgyMjAxLWJjNzUtNDA5OS05MjkzLWRlNDdiMzkyNmM5YiIsImMiOjR9"
        allowFullScreen="true">
    </iframe>
</div>

<script>

    /*
    |--------------------------------------------------------------------------
    | IMPRIMIR / PDF
    |--------------------------------------------------------------------------
    */

    function imprimirReporte() {

        window.print();
    }

    /*
    |--------------------------------------------------------------------------
    | PANTALLA COMPLETA
    |--------------------------------------------------------------------------
    */

    function pantallaCompleta() {

        let iframe = document.getElementById('powerbiFrame');

        if (iframe.requestFullscreen) {

            iframe.requestFullscreen();

        } else if (iframe.webkitRequestFullscreen) {

            iframe.webkitRequestFullscreen();

        } else if (iframe.msRequestFullscreen) {

            iframe.msRequestFullscreen();
        }
    }

</script>
