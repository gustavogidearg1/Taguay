// Botón "Instalar App Taguay" (PWA). Mismo enfoque que App Producción.
// - Android / Chrome / Edge: el navegador avisa con "beforeinstallprompt" que la app se puede
//   instalar; recién ahí mostramos el botón, y al tocarlo abrimos el diálogo del navegador.
// - iPhone / iPad: Safari no tiene ese evento; mostramos cómo instalar a mano (Compartir).
// - Ya instalada (abierta como app): no mostramos el botón y sí el distintivo "App Taguay".
(function () {
    const instalada = window.matchMedia('(display-mode: standalone)').matches
        || window.navigator.standalone === true;

    const boton = document.querySelector('[data-instalar-taguay]');
    const guiaIos = document.querySelector('[data-ios-taguay]');
    const distintivo = document.querySelector('[data-app-taguay]');

    const esIos = /iPad|iPhone|iPod/.test(navigator.userAgent)
        || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);

    if (instalada) {
        if (distintivo) distintivo.hidden = false;
        return;
    }

    if (esIos && guiaIos) guiaIos.hidden = false;

    if (!boton) return;

    let aviso = null;

    window.addEventListener('beforeinstallprompt', function (evento) {
        evento.preventDefault();   // evita el cartel automático: lo abrimos con nuestro botón
        aviso = evento;
        boton.hidden = false;
    });

    boton.addEventListener('click', async function () {
        if (!aviso) return;
        const actual = aviso;
        aviso = null;
        boton.hidden = true;
        await actual.prompt();
    });

    window.addEventListener('appinstalled', function () {
        aviso = null;
        boton.hidden = true;
    });
})();
