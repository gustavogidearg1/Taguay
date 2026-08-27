@extends('layouts.app')

@section('title', 'Ingresar')

@section('content')

<style>

    html,
body{
    margin: 0;
    padding: 0;
    overflow-x: hidden;
}

    :root{
        --tg-green: #0c5a35;
        --tg-green-dark: #084428;
        --tg-green-soft: #eaf3ec;
        --tg-border: #d8e1d9;
        --tg-text: #1f2d1f;
        --tg-muted: #5e6b61;
        --tg-shadow: 0 12px 35px rgba(0,0,0,.10);
        --tg-radius: 22px;
    }

.auth-page{
    height: calc(100vh - 70px);
    min-height: 0;

    display: flex;
    align-items: center;
    justify-content: center;

    padding: 12px 16px;

    box-sizing: border-box;
    overflow: hidden;

    background:
        linear-gradient(
            rgba(255,255,255,.90),
            rgba(255,255,255,.94)
        ),
        url('{{ asset('storage/images/home-bg.jpg') }}')
        center/cover no-repeat;
}

    .auth-container{
        width: 100%;
        max-width: 470px;
    }

    .auth-card{
        background: rgba(255,255,255,.97);
        border: 1px solid var(--tg-border);
        border-radius: var(--tg-radius);
        box-shadow: var(--tg-shadow);
        overflow: hidden;
    }

    .auth-header{
        text-align: center;
        padding: 26px 30px 18px;
    }

    .auth-logo{
        height: 58px;
        width: auto;
        margin-bottom: 14px;
    }

    .auth-title{
        color: var(--tg-green-dark);
        font-size: 1.9rem;
        font-weight: 800;
        margin-bottom: 5px;
    }

    .auth-subtitle{
        color: var(--tg-muted);
        font-size: .98rem;
        margin-bottom: 12px;
    }

    .auth-divider{
        width: 48px;
        height: 3px;
        background: #5baa72;
        border-radius: 30px;
        margin: 0 auto;
    }

    .auth-body{
        padding: 16px 30px 28px;
    }

    .auth-label{
        font-size: .9rem;
        font-weight: 700;
        color: var(--tg-text);
        margin-bottom: 6px;
    }

    .auth-input-wrap{
        position: relative;
    }

    .auth-input-icon{
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #7c897f;
        z-index: 5;
    }

    .auth-input{
        height: 52px;
        padding-left: 44px;
        border-radius: 11px;
        border: 1px solid #d7dfd9;
        background-color: #fff;
        transition: border-color .2s ease, box-shadow .2s ease;
    }

    .auth-input:focus{
        border-color: #5baa72;
        box-shadow: 0 0 0 .20rem rgba(91,170,114,.14);
    }

    .password-input{
        padding-right: 48px;
    }

    .password-toggle{
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        border: 0;
        background: transparent;
        color: #6f7c72;
        padding: 6px;
        z-index: 6;
    }

    .password-toggle:hover{
        color: var(--tg-green);
    }

    .auth-options{
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        margin: 18px 0;
        font-size: .9rem;
    }

    .auth-link{
        color: var(--tg-green);
        font-weight: 600;
        text-decoration: none;
    }

    .auth-link:hover{
        color: var(--tg-green-dark);
        text-decoration: underline;
    }

    .form-check-input:checked{
        background-color: var(--tg-green);
        border-color: var(--tg-green);
    }

    .btn-auth{
        width: 100%;
        min-height: 50px;
        border: none;
        border-radius: 11px;

        display: flex;
        align-items: center;
        justify-content: center;
        gap: 9px;

        background: linear-gradient(
            90deg,
            var(--tg-green),
            var(--tg-green-dark)
        );

        color: white;
        font-weight: 700;
        transition: all .2s ease;
    }

    .btn-auth:hover{
        color: white;
        transform: translateY(-1px);
        filter: brightness(1.05);
        box-shadow: 0 7px 18px rgba(12,90,53,.18);
    }

    .btn-auth:disabled{
        opacity: .7;
    }

    .auth-footer{
        border-top: 1px solid #edf0ed;
        background: #fafcfa;
        padding: 14px 20px;

        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;

        color: var(--tg-muted);
        font-size: .85rem;
    }

    .auth-footer img{
        width: 24px;
        height: 24px;
        object-fit: contain;
    }

    .auth-status{
        border: 1px solid #badcc4;
        background: #edf8f0;
        color: #225f36;
        border-radius: 10px;
        font-size: .9rem;
    }

    .invalid-feedback{
        font-size: .85rem;
    }

    @media (max-width: 576px){

        .auth-page{
            padding: 15px;
            align-items: flex-start;
        }

        .auth-card{
            margin-top: 20px;
        }

        .auth-header{
            padding: 22px 20px 14px;
        }

        .auth-body{
            padding: 14px 20px 22px;
        }

        .auth-title{
            font-size: 1.6rem;
        }

        .auth-logo{
            height: 50px;
        }

        .auth-options{
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }
    }
</style>


<div class="auth-page">

    <div class="auth-container">

        <div class="auth-card">

            {{-- Encabezado --}}
            <div class="auth-header">

                <img
                    src="{{ asset('storage/images/logo-taguay.png') }}"
                    class="auth-logo"
                    alt="Taguay"
                >

                <h1 class="auth-title">
                    Bienvenido
                </h1>

                <p class="auth-subtitle">
                    Ingresa tus datos para acceder al sistema
                </p>

                <div class="auth-divider"></div>

            </div>


            {{-- Formulario --}}
            <div class="auth-body">

                {{-- Estado de sesión --}}
                @if (session('status'))
                    <div class="alert auth-status" role="alert">
                        {{ session('status') }}
                    </div>
                @endif


                <form
                    method="POST"
                    action="{{ route('login') }}"
                    id="loginForm"
                    novalidate
                >

                    @csrf


                    {{-- EMAIL --}}
                    <div class="mb-3">

                        <label for="email" class="auth-label">
                            Email
                        </label>

                        <div class="auth-input-wrap">

                            <i class="bi bi-envelope auth-input-icon"></i>

                            <input
                                id="email"
                                type="email"
                                class="form-control auth-input
                                @error('email') is-invalid @enderror"

                                name="email"
                                value="{{ old('email') }}"

                                required
                                autocomplete="email"
                                autofocus

                                placeholder="nombre@empresa.com"
                            >

                        </div>

                        @error('email')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- PASSWORD --}}
                    <div class="mb-2">

                        <label for="password" class="auth-label">
                            Contraseña
                        </label>

                        <div class="auth-input-wrap">

                            <i class="bi bi-lock auth-input-icon"></i>

                            <input
                                id="password"
                                type="password"

                                class="form-control auth-input password-input
                                @error('password') is-invalid @enderror"

                                name="password"

                                required
                                autocomplete="current-password"

                                placeholder="Ingresa tu contraseña"
                            >

                            <button
                                type="button"
                                class="password-toggle"
                                id="togglePassword"
                                aria-label="Mostrar contraseña"
                            >

                                <i class="bi bi-eye"></i>

                            </button>

                        </div>

                        @error('password')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- Recordar + Olvide --}}
                    <div class="auth-options">

                        <div class="form-check">

                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="remember"
                                id="remember"
                                {{ old('remember') ? 'checked' : '' }}
                            >

                            <label
                                class="form-check-label"
                                for="remember"
                            >
                                Recordarme
                            </label>

                        </div>


                        @if (Route::has('password.request'))

                            <a
                                class="auth-link"
                                href="{{ route('password.request') }}"
                            >
                                ¿Olvidaste tu contraseña?
                            </a>

                        @endif

                    </div>


                    {{-- INGRESAR --}}
                    <button
                        type="submit"
                        class="btn-auth"
                        id="btnSubmit"
                    >

                        <span
                            class="spinner-border spinner-border-sm d-none"
                            role="status"
                            aria-hidden="true"
                        ></span>

                        <span>
                            Ingresar
                        </span>

                        <span>
                            &rarr;
                        </span>

                    </button>

                </form>

            </div>


            {{-- Footer --}}
            <div class="auth-footer">

                <span>
                    &copy; {{ date('Y') }}
                    {{ config('app.name', 'Taguay') }}
                </span>

                <img
                    src="{{ asset('storage/images/ImgLogoCircular-SF.png') }}"
                    alt="Taguay"
                >

            </div>

        </div>

    </div>

</div>


<script>

    /* Mostrar / ocultar contraseña */

    const togglePassword =
        document.getElementById('togglePassword');

    const password =
        document.getElementById('password');


    if (togglePassword && password) {

        togglePassword.addEventListener('click', function () {

            const visible =
                password.type === 'text';

            password.type =
                visible ? 'password' : 'text';


            this.innerHTML =
                visible
                    ? '<i class="bi bi-eye"></i>'
                    : '<i class="bi bi-eye-slash"></i>';


            this.setAttribute(
                'aria-label',
                visible
                    ? 'Mostrar contraseña'
                    : 'Ocultar contraseña'
            );

        });

    }


    /* Spinner al enviar */

    const loginForm =
        document.getElementById('loginForm');

    if (loginForm) {

        loginForm.addEventListener('submit', function () {

            const btn =
                document.getElementById('btnSubmit');

            if (!btn) return;

            btn.disabled = true;

            const spinner =
                btn.querySelector('.spinner-border');

            if (spinner) {
                spinner.classList.remove('d-none');
            }

        });

    }

</script>

@endsection
