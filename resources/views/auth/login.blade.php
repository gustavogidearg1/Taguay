@extends('layouts.app')

@section('title', 'Ingresar')

@section('content')
<style>
    .auth-bg {
        min-height: 70vh;
        display: grid;
        place-items: center;
    }
    .auth-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,.08);
        overflow: hidden;
    }
    .auth-card .card-header {
        background: #A8E6A1; /* tu naranja */
        color: #fff;
    }
    .form-floating > label { color: #6c757d; }
    .input-icon {
        position: absolute; left: .75rem; top: 50%; transform: translateY(-50%);
        pointer-events: none; opacity: .6;
    }
    .ps-icon { padding-left: 2.2rem !important; }
</style>

<div class="auth-bg">
    <div class="col-11 col-sm-9 col-md-7 col-lg-5">
        <div class="card auth-card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <img src="{{ asset('storage/images/logo-taguay.png') }}" alt="Logo" height="36">

                </div>

                                <div class="d-flex align-items-center gap-2">

                    <span class="fw-semibold">Bienvenido</span>
                </div>


            </div>

            <div class="card-body p-4">

                {{-- Mensaje de estado de sesión (por ejemplo, link de reset enviado) --}}
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" id="loginForm" novalidate>
                    @csrf

                    {{-- Email --}}
                    <div class="mb-3 position-relative">
                        <i class="bi bi-envelope input-icon"></i>
                        <div class="form-floating">
                            <input id="email" type="email"
                                   class="form-control ps-icon @error('email') is-invalid @enderror"
                                   name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                                   placeholder="nombre@empresa.com">
                            <label for="email">Email</label>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Password --}}
                    <div class="mb-3 position-relative">
                        <i class="bi bi-lock input-icon"></i>
                        <div class="form-floating">
                            <input id="password" type="password"
                                   class="form-control ps-icon @error('password') is-invalid @enderror"
                                   name="password" required autocomplete="current-password"
                                   placeholder="••••••••">
                            <label for="password">Contraseña</label>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="button" class="btn btn-sm btn-outline-secondary mt-2" id="togglePassword">
                            <i class="bi bi-eye"></i> Mostrar
                        </button>
                    </div>
                    
                                        {{-- Submit --}}
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary" id="btnSubmit">
                            <span class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true"></span>
                            Ingresar
                        </button>
                    </div>
                    
                    <hr>

                    {{-- Remember + Forgot --}}
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                   {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">Recordarme</label>
                        </div>

                        @if (Route::has('password.request'))
                            <a class="link-primary" href="{{ route('password.request') }}">
                                ¿Olvidaste tu contraseña?
                            </a>
                        @endif
                    </div>


                </form>

            </div>
            <div class="card-footer text-center small text-muted">
                © {{ date('Y') }} {{ config('app.name', 'Taguay') }}
                <img src="{{ asset('storage/images/ImgLogoCircular-SF.png') }}" alt="Logo" width="30" height="30">
            </div>
        </div>
    </div>
</div>

{{-- JS: toggle contraseña + spinner al enviar --}}
<script>
document.getElementById('togglePassword').addEventListener('click', function () {
    const pwd = document.getElementById('password');
    const icon = this.querySelector('i');
    const isText = pwd.type === 'text';
    pwd.type = isText ? 'password' : 'text';
    this.innerHTML = (isText ? '<i class="bi bi-eye"></i> Mostrar' : '<i class="bi bi-eye-slash"></i> Ocultar');
});

document.getElementById('loginForm').addEventListener('submit', function () {
    const btn = document.getElementById('btnSubmit');
    btn.disabled = true;
    btn.querySelector('.spinner-border').classList.remove('d-none');
});
</script>
@endsection
