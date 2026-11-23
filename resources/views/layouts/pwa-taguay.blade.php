{{-- App instalable (PWA) "App Taguay": se incluye en el <head> de layouts/app.
     - manifest-taguay.json: nombre, íconos y colores de la app instalada.
     - apple-*: equivalentes para iPhone/iPad (Safari no lee todo el manifest).
     - instalar-taguay.js: muestra el botón "Instalar App Taguay" en el login. --}}
<meta name="theme-color" content="#198754">
<link rel="manifest" href="{{ asset('manifest-taguay.json') }}">
<link rel="icon" type="image/png" href="{{ asset('icons/favicon.png') }}">
<link rel="apple-touch-icon" href="{{ asset('icons/icon-180.png') }}">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="Taguay">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<script src="{{ asset('js/instalar-taguay.js') }}" defer></script>
