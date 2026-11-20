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


    /* AYUDA: COMO COPIAR A EXCEL */
    .help-overlay {
        display: none;
        position: fixed;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        z-index: 2000;
        background: rgba(0, 0, 0, 0.45);
        align-items: center;
        justify-content: center;
        padding: 16px;
    }

    .help-overlay.open {
        display: flex;
    }

    .help-box {
        position: relative;
        background: #ffffff;
        color: #333;
        border-radius: 14px;
        width: 100%;
        max-width: 760px;
        max-height: 90vh;
        overflow-y: auto;
        padding: 22px 26px;
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.30);
        font-size: 15px;
        line-height: 1.5;
    }

    .help-box h2 {
        margin: 0 0 12px 0;
        font-size: 20px;
        color: #17503D;
    }

    .help-box ol {
        margin: 0 0 12px 0;
        padding-left: 22px;
    }

    .help-box li {
        margin-bottom: 8px;
    }

    .help-box .help-note {
        background: #f4f6f9;
        border-left: 4px solid #D6540A;
        border-radius: 6px;
        padding: 10px 14px;
        margin: 12px 0;
        font-size: 14px;
    }

    .help-box video {
        width: 100%;
        border-radius: 8px;
        margin-top: 8px;
        background: #000;
    }

    .help-close {
        position: absolute;
        top: 10px;
        right: 12px;
        width: 32px;
        height: 32px;
        border: none;
        border-radius: 8px;
        background: #f1f1f1;
        font-size: 18px;
        cursor: pointer;
    }

    .help-close:hover {
        background: #e2e2e2;
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
    .help-overlay,
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

    <button
        class="mat-btn mobile-hide"
        onclick="abrirAyudaExcel()"
        title="Cómo copiar tablas a Excel">
        📋
    </button>

</div>

{{-- AYUDA: COMO COPIAR A EXCEL --}}
<div class="help-overlay" id="helpExcel" onclick="if (event.target === this) cerrarAyudaExcel()">
    <div class="help-box" role="dialog" aria-modal="true" aria-labelledby="helpExcelTitulo">

        <button class="help-close" onclick="cerrarAyudaExcel()" title="Cerrar">✕</button>

        <h2 id="helpExcelTitulo">Cómo copiar una tabla a Excel</h2>

        <ol>
            <li>
                Pasá el mouse sobre la tabla y tocá el ícono <strong>Modo de enfoque</strong>
                (cuadradito con flecha, arriba a la derecha de la tabla). La tabla se agranda.
            </li>
            <li>
                Hacé <strong>clic derecho</strong> sobre cualquier celda de la tabla y elegí
                <strong>Copia &rarr; Copiar selección</strong>.
                (<em>Copiar valor</em> copia una sola celda.)
            </li>
            <li>
                Abrí Excel, hacé clic en la celda <strong>A1</strong> y pegá con <strong>Ctrl + V</strong>.
            </li>
            <li>
                Para salir del modo de enfoque, tocá <strong>Volver al informe</strong> (arriba a la izquierda;
                el menú flotante tapa parte del botón, hacé clic en la parte que se ve).
            </li>
        </ol>

        <div class="help-note">
            <strong>Tené en cuenta:</strong>
            <ul style="margin: 6px 0 0 0; padding-left: 20px;">
                <li>La fila <strong>Total</strong> no se copia: si la necesitás, sumala en Excel.</li>
                <li>Las celdas vacías llegan como <strong>(En blanco)</strong>. Para limpiarlas: Ctrl + L,
                    buscá <em>(En blanco)</em> y reemplazá por nada.</li>
                <li>Esto copia de a una tabla por vez. Para otra tabla, repetí los pasos.</li>
            </ul>
        </div>

        <video controls preload="none" id="helpExcelVideo">
            <source src="{{ asset('videos/copiar-a-excel.mp4') }}" type="video/mp4">
        </video>

    </div>
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

    /*
    |--------------------------------------------------------------------------
    | AYUDA: COMO COPIAR A EXCEL
    |--------------------------------------------------------------------------
    */

    function abrirAyudaExcel() {

        document.getElementById('helpExcel').classList.add('open');
    }

    function cerrarAyudaExcel() {

        document.getElementById('helpExcel').classList.remove('open');

        let video = document.getElementById('helpExcelVideo');

        if (video) {

            video.pause();
        }
    }

    document.addEventListener('keydown', function (e) {

        if (e.key === 'Escape') {

            cerrarAyudaExcel();
        }
    });

</script>
