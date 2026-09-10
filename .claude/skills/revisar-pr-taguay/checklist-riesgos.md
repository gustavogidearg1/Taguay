# Checklist de riesgos — Taguay

Lista de control para revisar cambios antes de mergear. Cada ítem se marca
✅ cumplido / ❌ no cumplido / ⚠️ a revisar / N/A.

## Seguridad

1. **Sin secretos en el diff.** Ninguna API key, contraseña, token, secret ni
   string de conexión hardcodeado en `.php`, `.js`, `.blade.php`, ni `.env` /
   `.sql` versionados. Los valores sensibles van en `.env` (ignorado) y se leen
   por `config/`.
2. **`env()` solo dentro de `config/`.** En controladores, modelos, servicios o
   vistas se usa `config('...')`, nunca `env('...')` directo (rompe con
   `php artisan config:cache`).
3. **Autorización en rutas nuevas.** Toda ruta agregada tiene el middleware que
   corresponde (`auth`, `role:admin`, `permission:ver_*`) y no queda accesible
   sin login por descuido.
4. **Inputs validados.** Cada `store`/`update` valida con `$request->validate()`
   o un Form Request. No hay `Model::create($request->all())` sin `$fillable`
   acotado ni asignación masiva de campos sensibles (`role_id`, `user_id`, etc.).
5. **Salida escapada en Blade.** Datos de usuario se imprimen con `{{ }}`. Todo
   `{!! !!}` sobre contenido no confiable está justificado.
6. **Archivos subidos.** Se valida tipo MIME y tamaño; se guardan con
   `Storage::` (disco `local`/privado), no bajo `public/` sin control.
7. **Sin debug olvidado.** No quedan `dd()`, `dump()`, `var_dump()`, `ray()`,
   `logger()->debug()` de prueba ni `console.log` en el diff.
8. **SQL seguro.** Sin `DB::raw()` / `whereRaw()` concatenando input del usuario;
   se usan bindings.

## Base de datos y migraciones

9. **Migración reversible.** Tiene `down()` que revierte lo que hace `up()`.
10. **Sin pérdida de datos.** Al agregar columnas a tablas con datos, son
    `nullable()` o tienen `default()`. Los `dropColumn` / `dropTable` /
    renombres tienen plan y respaldo.
11. **Índices y claves foráneas.** Columnas nuevas de relación (`*_id`) tienen
    índice y FK con `onDelete` definido.
12. **Sin N+1.** Los listados que iteran relaciones usan `with()` / `load()`.
    Revisar `index()` de controladores y `@foreach` en vistas.
13. **El esquema queda representado en migraciones**, no solo en la base local
    ni en un `.sql` suelto.

## Consistencia con el proyecto (CLAUDE.md)

14. **Idioma.** Comentarios de código, textos de UI, mensajes de commit y
    documentación en español.
15. **Colores de marca.** Primario `#198754`, secundario `#0d6efd`, usados de
    forma consistente en botones, enlaces y acentos. Sin hex sueltos que
    reintroduzcan el azul por defecto de Bootstrap como color principal.
16. **Estilo visual.** Jerarquía, espaciado y elevación al estilo Angular
    Material; se reutilizan las clases/patrones existentes (`.mat-card`, etc.).
17. **Estructura.** Se respeta la nomenclatura y las carpetas actuales
    (`app/Http/Controllers/Abm/`, `resources/views/abm/...`, etc.).
18. **Dependencias.** No se agregan paquetes de Composer ni npm sin aviso
    explícito. `composer.json` / `package.json` sin cambios inesperados.
19. **Nada generado ni de terceros modificado.** Sin cambios en `vendor/`,
    `node_modules/`, `public/build/` a mano.
20. **Assets compilados.** Si el diff toca `resources/sass/` o `resources/js/`,
    se corrió `npm run build` y `public/build/manifest.json` quedó al día
    (o se aclara que el deploy lo compila).

## General

21. **Sin romper contratos.** Cambios en firmas de métodos, nombres de rutas
    (`route('...')`), o variables que las vistas esperan (`compact(...)`) están
    acompañados de la actualización de todos los usos.
22. **Manejo de errores.** Llamadas externas (envío de mail, HTTP a OpenAI /
    finneg, generación de PDF) están en `try/catch` con `Log::error` y no
    rompen el flujo principal si fallan.
23. **Destinatarios por configuración.** Emails de notificación (CC/TO) salen de
    `config`/`.env`, no hardcodeados en el controlador.
24. **Tests.** Si se agrega lógica de negocio, hay test que la cubra, o se
    documenta por qué no.
25. **Sin código muerto.** No se suma (ni se deja) controladores de prueba,
    vistas huérfanas, rutas duplicadas ni bloques comentados grandes.
26. **Alcance del PR.** Los cambios corresponden a lo que dice el título/objetivo;
    no hay refactors o reformateos masivos mezclados que dificulten la revisión.
