@extends('layouts.app')

@section('title', 'Taguay')

@section('content')

<style>
    :root{
        --tg-green: #0c5a35;
        --tg-green-dark: #084428;
        --tg-green-soft: #eaf3ec;
        --tg-border: #d8e1d9;
        --tg-text: #1f2d1f;
        --tg-muted: #5e6b61;
        --tg-shadow: 0 10px 30px rgba(0,0,0,.10);
        --tg-radius: 22px;
    }

    .home-page{
        min-height: calc(100vh - 80px);
        background:
            linear-gradient(rgba(255,255,255,.88), rgba(255,255,255,.92)),
            url('{{ asset('storage/images/home-bg.jpg') }}') center/cover no-repeat;
            min-height: calc(100vh - 70px);
            padding: 5px 0 5px;
    }

    .home-header{
        text-align: center;
        margin-bottom: 22px;
    }

    .home-title{
        font-size: clamp(1.8rem, 2.5vw, 2.8rem);
        font-weight: 800;
        color: var(--tg-green);
        margin-bottom: 4px;
    }

    .home-subtitle{
        font-size: 1.1rem;
        color: var(--tg-muted);
        margin-bottom: 10px;
    }

    .home-divider{
        width: 55px;
        height: 4px;
        background: #5baa72;
        border-radius: 30px;
        margin: 0 auto;
    }

    .module-card{
        height: 100%;
        background: rgba(255,255,255,.96);
        border: 1px solid var(--tg-border);
        border-radius: var(--tg-radius);
        box-shadow: var(--tg-shadow);
        padding: 28px 24px 22px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .module-card:hover{
        transform: translateY(-6px);
        box-shadow: 0 16px 40px rgba(0,0,0,.14);
    }

    .module-card.disabled-card{
        opacity: .88;
        background: #f8f8f8;
    }

    .module-icon-wrap{
        width: 140px;
        height: 140px;
        margin: 0 auto 20px;
        border-radius: 50%;
        background: var(--tg-green-soft);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .module-icon{
        width: 88%;
        height: auto;
        object-fit: contain;
    }

    .module-title{
        text-align: center;
        font-size: 2rem;
        font-weight: 800;
        color: var(--tg-green-dark);
        margin-bottom: 10px;
    }

    .module-line{
    width: 38px;
    height: 3px;
        border-radius: 30px;
        background: #5baa72;
        margin: 0 auto 10px;
    }

    .module-description{
        text-align: center;
        color: var(--tg-text);
        font-size: .95rem;
         min-height: 42px;
        margin-bottom: 14px;
    }

    .btn-module{
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        background: linear-gradient(90deg, var(--tg-green), var(--tg-green-dark));
        color: #fff !important;
        font-weight: 700;
        border-radius: 12px;
        padding: 10px 16px;
        text-decoration: none;
        border: none;
        transition: all .2s ease;
        margin-bottom: 12px;
    }

    .btn-module:hover{
        filter: brightness(1.05);
        transform: translateY(-1px);
        color: #fff !important;
        text-decoration: none;
    }

    .btn-module.disabled{
        background: #b8c1bb;
        cursor: not-allowed;
        pointer-events: none;
    }

    .module-footer-note{
        border-top: 1px solid #e8ece8;
        padding-top: 10px;
        color: var(--tg-muted);
        font-size: .88rem;
        text-align: left;
        min-height: 38px;
    }

    .warning-mini{
        margin-top: 10px;
        font-size: .92rem;
        color: #9b6b00;
        background: #fff7db;
        border: 1px solid #f0d98a;
        border-radius: 10px;
        padding: 10px 12px;
        text-align: center;
    }

    .feature-strip{
        margin-top: 34px;
        background: rgba(255,255,255,.96);
        border: 1px solid var(--tg-border);
        border-radius: 20px;
        box-shadow: var(--tg-shadow);
        padding: 22px 18px;
    }

    .feature-item{
        height: 100%;
        padding: 8px 16px;
    }

    .feature-title{
        font-size: 1.15rem;
        font-weight: 800;
        color: var(--tg-text);
        margin-bottom: 6px;
    }

    .feature-text{
        color: var(--tg-muted);
        margin-bottom: 0;
        line-height: 1.45;
    }

    .home-footer{
        text-align: center;
        color: #666;
        margin-top: 12px;
        font-size: .85rem;
    }

    @media (max-width: 991px){
        .module-title{
            font-size: 1.7rem;
        }

        .module-description{
            min-height: auto;
        }
    }

    @media (max-width: 576px){
        .home-page{
            padding-top: 22px;
        }

        .module-card{
            padding: 22px 18px;
        }

        .module-icon-wrap{
            width: 118px;
            height: 118px;
        }

        .module-title{
            font-size: 1.55rem;
        }
    }
</style>

<div class="home-page">
    <div class="container-fluid">
        <div class="container">

            {{-- Encabezado --}}
            <div class="home-header">
                <h1 class="home-title">Bienvenido a Taguay</h1>
                <p class="home-subtitle">
                    Gestion, analisis y control de tu produccion en un solo lugar.
                </p>
                <div class="home-divider"></div>
            </div>

            {{-- Tarjetas principales --}}
            <div class="row g-4">

                {{-- Margen Bruto --}}
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="module-card">
                        <div>
                            <div class="module-icon-wrap">
                                <img
                                    src="{{ asset('storage/images/BtnMargenBruto.png') }}"
                                    class="module-icon"
                                    alt="Margen Bruto">
                            </div>

                            <h3 class="module-title">Margen Bruto</h3>
                            <div class="module-line"></div>

                            <p class="module-description">
                                Analisis del margen bruto por cultivo y campo.
                            </p>
                        </div>

                        <div>
                            <a href="{{ route('margen-bruto') }}" class="btn-module">
                                Ingresar <span>&rarr;</span>
                            </a>

                            <div class="module-footer-note">
                                Rentabilidad y desempe&ntilde;o de tus cultivos.
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Cosecha --}}
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="module-card">
                        <div>
                            <div class="module-icon-wrap">
                                <img
                                    src="{{ asset('storage/images/BtnCosecha.png') }}"
                                    class="module-icon"
                                    alt="Cosecha">
                            </div>

                            <h3 class="module-title">Cosecha</h3>
                            <div class="module-line"></div>

                            <p class="module-description">
                                Seguimiento de cosecha, rendimientos y calidad.
                            </p>
                        </div>

                        <div>
                            <a href="{{ route('cosecha') }}" class="btn-module">
                                Ingresar <span>&rarr;</span>
                            </a>

                            <div class="module-footer-note">
                                Controla cada etapa de tu produccion.
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Flujo de fondo --}}
                <div class="col-12 col-md-6 col-xl-4">
                    @if (Auth::user()->role_id !== 4)
                        <div class="module-card">
                            <div>
                                <div class="module-icon-wrap">
                                    <img
                                        src="{{ asset('storage/images/BtnFlujoFondo.png') }}"
                                        class="module-icon"
                                        alt="Flujo de fondo">
                                </div>

                                <h3 class="module-title">Flujo de fondo</h3>
                                <div class="module-line"></div>

                                <p class="module-description">
                                    Acceso al analisis financiero y flujo de fondos.
                                </p>
                            </div>

                            <div>
                                <a href="{{ route('flujo-fondo') }}" class="btn-module">
                                    Ingresar <span>&rarr;</span>
                                </a>

                                <div class="module-footer-note">
                                    Toma decisiones con informacion clara.
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="module-card disabled-card">
                            <div>
                                <div class="module-icon-wrap">
                                    <img
                                        src="{{ asset('storage/images/BtnFlujoFondo.png') }}"
                                        class="module-icon"
                                        alt="Flujo de fondo">
                                </div>

                                <h3 class="module-title">Flujo de fondo</h3>
                                <div class="module-line"></div>

                                <p class="module-description">
                                    Acceso al analisis financiero y flujo de fondos.
                                </p>
                            </div>

                            <div>
                                <span class="btn-module disabled">
                                    Solo lectura
                                </span>

                                <div class="warning-mini">
                                    Vista solamente para Administradores y Editores
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

            </div>

            {{-- Beneficios / información --}}


            {{-- Footer --}}
            <div class="home-footer">
                &copy; {{ date('Y') }} Taguay.
            </div>

        </div>
    </div>
</div>

@endsection

