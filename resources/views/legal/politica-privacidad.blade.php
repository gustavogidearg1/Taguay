{{-- Política de privacidad pública (sin login). La exige Meta para publicar la app de WhatsApp.
     BORRADOR: debe revisarlo la empresa antes de publicarlo. --}}
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Política de privacidad · Taguay</title>
    <link rel="icon" type="image/png" href="{{ asset('icons/favicon.png') }}">
    <style>
        :root { --verde: #198754; --verde-osc: #0b4d2c; --texto: #1f2a24; --gris: #5f6b64; --fondo: #f4f7f5; }
        * { box-sizing: border-box; }
        body { margin: 0; background: var(--fondo); color: var(--texto); font: 16px/1.65 "Segoe UI", system-ui, -apple-system, Roboto, Arial, sans-serif; }
        .encabezado { background: var(--verde-osc); color: #fff; padding: 28px 16px; }
        .encabezado .contenido { max-width: 860px; margin: 0 auto; display: flex; align-items: center; gap: 16px; }
        .encabezado img { height: 48px; background: #fff; border-radius: 10px; padding: 6px 10px; }
        .encabezado h1 { margin: 0; font-size: 1.6rem; }
        .encabezado p { margin: 2px 0 0; opacity: .85; }
        main { max-width: 860px; margin: 24px auto; background: #fff; border-radius: 16px; padding: 28px 24px; box-shadow: 0 4px 18px rgba(0,0,0,.06); }
        h2 { color: var(--verde-osc); font-size: 1.2rem; margin: 28px 0 8px; }
        h2:first-child { margin-top: 0; }
        ul { padding-left: 20px; }
        a { color: var(--verde); }
        .nota { color: var(--gris); font-size: .9rem; }
        footer { text-align: center; color: var(--gris); font-size: .85rem; padding: 8px 16px 32px; }
        @media (max-width: 576px) { main { margin: 16px; padding: 20px 16px; } }
    </style>
</head>
<body>
    <header class="encabezado">
        <div class="contenido">
            <img src="{{ asset('storage/images/logo-taguay.png') }}" alt="Taguay">
            <div>
                <h1>Política de privacidad</h1>
                <p>Sistema Taguay y App Taguay (incluida la carga por WhatsApp)</p>
            </div>
        </div>
    </header>

    <main>
        <h2>1. Quiénes somos</h2>
        <p>
            Esta política explica cómo <strong>Taguay</strong> trata los datos personales en el sistema de gestión
            <strong>sistema.taguay.com.ar</strong>, la <strong>App Taguay</strong> y el canal de WhatsApp
            <strong>Taguay Campo</strong>, que el personal de la empresa usa para registrar el avance de las tareas de campo.
        </p>

        <h2>2. Qué datos recopilamos</h2>
        <ul>
            <li><strong>Datos de la cuenta:</strong> nombre, correo electrónico y rol de los usuarios autorizados.</li>
            <li><strong>Datos de WhatsApp:</strong> número de teléfono, nombre de perfil y el contenido de los mensajes que se envían al canal Taguay Campo.</li>
            <li><strong>Datos operativos del avance de campo:</strong> establecimiento, lote, cultivo, cantidades, superficie, patente del camión, nombre del chofer, observaciones, fotos y, si el usuario lo comparte, la ubicación.</li>
        </ul>

        <h2>3. Para qué los usamos</h2>
        <ul>
            <li>Registrar y consultar el avance de las tareas agrícolas de la empresa.</li>
            <li>Identificar qué usuario realizó cada carga.</li>
            <li>Responder los mensajes que los usuarios envían al canal de WhatsApp.</li>
        </ul>
        <p>No usamos los datos con fines publicitarios ni los vendemos.</p>

        <h2>4. Con quién los compartimos</h2>
        <p>
            Los datos se usan solo dentro de Taguay. Para el canal de WhatsApp intervienen los servicios de
            <strong>Meta Platforms</strong> (WhatsApp Business Platform), que transmiten los mensajes según sus propias
            condiciones y políticas. También se alojan en nuestro proveedor de hosting.
        </p>

        <h2>5. Cuánto tiempo los guardamos</h2>
        <p>
            Los registros de avance de campo se conservan mientras sean necesarios para la gestión de la empresa.
            Los registros técnicos de mensajes de WhatsApp se eliminan automáticamente a los 30 días.
        </p>

        <h2>6. Seguridad</h2>
        <p>
            El acceso al sistema requiere usuario y contraseña, cada persona ve solo lo que su rol permite y las
            comunicaciones viajan cifradas (https).
        </p>

        <h2 id="eliminacion-de-datos">7. Tus derechos y eliminación de datos</h2>
        <p>
            Podés pedir acceder, corregir o eliminar tus datos personales. Para eso, escribí a
            <a href="mailto:soporte@taguay.com.ar">soporte@taguay.com.ar</a> indicando tu nombre y el número de teléfono
            o correo con el que usás el sistema. Respondemos dentro de los 10 días hábiles.
        </p>
        <p class="nota">
            En Argentina, la Agencia de Acceso a la Información Pública es el órgano de control de la Ley 25.326
            de Protección de los Datos Personales.
        </p>

        <h2>8. Cambios</h2>
        <p>Si modificamos esta política, publicaremos la nueva versión en esta misma página.</p>

        <p class="nota">Última actualización: 23 de noviembre de 2026.</p>
    </main>

    <footer>&copy; {{ date('Y') }} Taguay</footer>
</body>
</html>
