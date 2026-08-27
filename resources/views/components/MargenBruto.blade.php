@extends('layouts.app')
@section('title', 'Margen Bruto')

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
    justify-content: center;

    gap: 6px;

    width: 42px;
    height: 42px;

    padding: 0;

    border: none;
    border-radius: 12px;

    background: #ffffff;

    color: #333;

    font-size: 16px;
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

    .mobile-hide {
        display: none !important;
    }

    .floating-menu {
        top: auto;
        left: auto;

        right: 10px;
        bottom: 10px;

        transform: none;

        flex-direction: row;

        padding: 4px;
        gap: 0;

        border-radius: 10px;

        background: rgba(255,255,255,.88);

        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);

        box-shadow:
            0 4px 12px rgba(0,0,0,.15);
    }

    .mat-btn {
        width: 36px;
        height: 36px;

        min-width: 36px;

        padding: 0;

        font-size: 14px;

        border-radius: 8px;
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

    <a
        href="{{ route('home') }}"
        class="mat-btn"
        title="Volver">
        ⬅
    </a>

    <button
        class="mat-btn mobile-hide"
        onclick="imprimirReporte()"
        title="Imprimir">
        🖨
    </button>

    <button
        class="mat-btn mobile-hide"
        onclick="pantallaCompleta()"
        title="Pantalla completa">
        ⛶
    </button>

</div>

{{-- POWER BI --}}
<div class="iframe-container">
  <!-- Boton de retroceso -->
    <iframe
        id="powerbiFrame"
            title="Margen Bruto V6"
            src="https://app.powerbi.com/view?r=eyJrIjoiYWY0YjI5MjQtYTkzYi00ZDg0LWIwYzYtNjI0OGE5Nzc0NDYzIiwidCI6ImZmZDgyMjAxLWJjNzUtNDA5OS05MjkzLWRlNDdiMzkyNmM5YiIsImMiOjR9"
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
