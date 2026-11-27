# Tutorial: cargar Avance de Campo por WhatsApp

Guía paso a paso para conectar un número de WhatsApp al sistema Taguay usando la **WhatsApp Business Platform (Cloud API de Meta)**. Sirve para hacerlo desde cero o para repetirlo (por ejemplo, con otro número).

> Las pantallas de Meta cambian seguido. Si un botón no se llama exactamente igual, buscá el más parecido; los conceptos son los mismos.

---

## 0. Conceptos que hay que tener claros

| Concepto | Qué es |
|---|---|
| **WhatsApp Business (app)** | La app del celular. **No** se puede conectar a un sistema. |
| **WhatsApp Business Platform / Cloud API** | El servicio de Meta para que un programa mande y reciba mensajes. Es lo que usamos. |
| **Portfolio comercial** (Business Manager) | La "empresa" dentro de Meta. Agrupa apps, números y usuarios. |
| **App de Meta** | El "contenedor" técnico donde se activa WhatsApp y se obtienen las llaves. |
| **Webhook** | Dirección de nuestro sistema a la que Meta **avisa** cada mensaje: `https://sistema.taguay.com.ar/webhook/whatsapp` |
| **Graph API** | Dirección de Meta a la que **nuestro sistema** le pide enviar mensajes. |
| **Token de acceso** | La llave para enviar mensajes. El de prueba dura 24 horas; en producción se usa uno permanente. |
| **Verify token** | Un texto secreto que **inventamos nosotros**. Meta lo manda al verificar el webhook para probar que la dirección es nuestra. |
| **App secret** | Clave de la app. Meta firma cada aviso con ella; así detectamos avisos falsos. |
| **Ventana de 24 h** | Cuando alguien nos escribe, podemos responderle libremente durante 24 horas. |

### Cómo viaja un mensaje

```
Celular ──"avance"──▶ Meta ──webhook (POST)──▶ Laravel: /webhook/whatsapp
Celular ◀──respuesta── Meta ◀──Graph API────── Laravel: WhatsAppCliente
```

### Archivos del sistema que intervienen

| Archivo | Para qué |
|---|---|
| `routes/web.php` | Rutas `GET` y `POST /webhook/whatsapp` (sin login). |
| `bootstrap/app.php` | Excluye `webhook/whatsapp` del control CSRF. |
| `app/Http/Controllers/WhatsAppWebhookController.php` | Verifica el webhook, valida la firma y recibe los mensajes. |
| `app/Services/WhatsApp/WhatsAppCliente.php` | Envía mensajes: texto, botones y listas. |
| `config/services.php` | Lee las variables `WHATSAPP_*` del `.env`. |
| `config/logging.php` | Canal `whatsapp`: todo queda registrado en `storage/logs/whatsapp-AAAA-MM-DD.log`. |

---

## 1. Crear la cuenta de desarrollador de Meta

1. Entrá a **Meta for Developers**: <https://developers.facebook.com/>
2. Tocá **Iniciar sesión** con tu cuenta de Facebook. Conviene una cuenta personal tuya; después se pueden sumar otros administradores.
3. Si es la primera vez, tocá **Comenzar / Registrarse como desarrollador**: aceptá los términos y verificá tu teléfono o email.

## 2. Crear el portfolio comercial (empresa) en Meta

1. Entrá a **Meta Business Suite**: <https://business.facebook.com/>
2. Creá un **portfolio comercial** (puede figurar como "Crear cuenta" o "Crear portfolio"):
   - Nombre: **Taguay** (o la razón social).
   - Tu nombre y un email de la empresa.
3. Más adelante, para pasar a producción, se hace la **verificación del negocio** desde la *Configuración del negocio* (pide documentación de la empresa). **Para las pruebas no hace falta.**

> También se puede crear el portfolio durante el paso 3, cuando Meta lo pide.

## 3. Crear la app y activar WhatsApp

1. Entrá a **Mis apps**: <https://developers.facebook.com/apps>
2. Tocá **Crear app**.
3. Caso de uso: elegí **"Conectarte con clientes a través de WhatsApp"**. Si no aparece, elegí **Otro** y tipo **Negocios**.
4. Nombre de la app: **Taguay Campo**. Email de contacto: el de la empresa.
5. Vinculala al **portfolio comercial Taguay** del paso 2.
6. En el panel de la app, buscá el producto **WhatsApp** y tocá **Configurar**. Aceptá las condiciones.

## 4. Número de prueba y tu celular

1. En el menú de la app: **WhatsApp → Configuración de la API** (*API Setup*).
2. Vas a ver:
   - **De (From):** un **número de prueba** que regala Meta.
   - **Identificador del número de teléfono** (*Phone number ID*): copialo, es `WHATSAPP_PHONE_NUMBER_ID`.
   - **Identificador de la cuenta de WhatsApp Business** (*WABA ID*): anotalo.
   - **Token de acceso temporal:** tocá **Generar token**. Es `WHATSAPP_TOKEN` y **dura 24 horas**.
3. En **Para (To)** tocá **Administrar lista de números** y agregá **tu celular** con código de país (Argentina: `+54 9 …`). Te llega un código por WhatsApp: ingresalo.
4. Tocá **Enviar mensaje**: te tiene que llegar un "Hello World" de Meta a tu WhatsApp. ✅
5. **Respondé ese mensaje** desde tu celular (cualquier texto). Así se abre la ventana de 24 h para que el sistema te pueda contestar.

> Se pueden registrar hasta **5 celulares** de prueba.

## 5. La "Clave secreta de la app"

1. En el menú de la app: **Configuración de la app → Básica**.
2. En **Clave secreta de la app** tocá **Mostrar** (pide tu contraseña de Facebook).
3. Copiala: es `WHATSAPP_APP_SECRET`.

## 6. Inventar el verify token

Inventá un texto largo y difícil de adivinar, sin espacios. Por ejemplo, con un generador de contraseñas, unos 30 caracteres. Es `WHATSAPP_VERIFY_TOKEN`. Lo vas a escribir en dos lugares: en el `.env` y en Meta (paso 9).

## 7. Subir el código al hosting

Subí estos archivos con el Administrador de archivos de cPanel:

- `app/Http/Controllers/WhatsAppWebhookController.php` *(nuevo)*
- `app/Services/WhatsApp/WhatsAppCliente.php` *(nuevo; crear la carpeta `WhatsApp`)*
- `config/services.php`
- `config/logging.php`
- `routes/web.php`
- `bootstrap/app.php`

No hay SQL ni `npm run build`.

## 8. Cargar las llaves en el `.env` del hosting

1. En cPanel → Administrador de archivos → carpeta `sistema.taguay.com.ar`.
   El `.env` es un archivo oculto: **Configuración** (arriba a la derecha) → tildar **Mostrar archivos ocultos**.
2. Editá el `.env` y agregá al final:

```env
# WhatsApp (Cloud API de Meta) - ver docs/whatsapp/tutorial-whatsapp.md
WHATSAPP_TOKEN=pegá_acá_el_token_de_acceso
WHATSAPP_PHONE_NUMBER_ID=pegá_acá_el_identificador_del_número
WHATSAPP_VERIFY_TOKEN=pegá_acá_el_texto_que_inventaste
WHATSAPP_APP_SECRET=pegá_acá_la_clave_secreta_de_la_app
WHATSAPP_API_VERSION=v27.0
```

   `WHATSAPP_API_VERSION`: usá la versión que figura en los ejemplos de *Configuración de la API* (por ejemplo `v27.0`).
3. Guardá y abrí en el navegador **`https://sistema.taguay.com.ar/limpiarcache.php`**, para que Laravel lea el `.env` nuevo. Tiene que decir "Cachés limpiadas".

> ⚠️ El `.env` **nunca** se sube a GitHub ni se pega en un chat: tiene las llaves.

### Control rápido antes de ir a Meta

Abrí esta dirección, reemplazando `TU_VERIFY_TOKEN` por el tuyo:

```
https://sistema.taguay.com.ar/webhook/whatsapp?hub.mode=subscribe&hub.verify_token=TU_VERIFY_TOKEN&hub.challenge=12345
```

- Si muestra **`12345`**: el webhook funciona. ✅
- Si dice **"Token de verificación inválido"**: el token no coincide con el `.env`, o falta limpiar la caché.

## 9. Configurar el webhook en Meta

1. En la app: **WhatsApp → Configuración** (*Configuration*).
2. En **Webhook** tocá **Editar**:
   - **URL de devolución de llamada:** `https://sistema.taguay.com.ar/webhook/whatsapp`
   - **Token de verificación:** el mismo `WHATSAPP_VERIFY_TOKEN`.
3. Tocá **Verificar y guardar**. Meta llama a tu sistema; si responde bien, queda guardado. ✅
4. En **Campos del webhook** (*Webhook fields*), buscá **`messages`** y tocá **Suscribirse**.

> **Pantalla nueva de Meta (2026):** la configuración está en *Casos de uso → Personalizar → "Conectar en WhatsApp"*, con elegir **"Integrar con la API"** (no "Convertirte en socio"):
> - *Paso 1. Pruébalo*: número de prueba, **Phone Number ID**, **Generar token** y la lista de números destinatarios.
> - *Paso 2: Configuración de producción*: **Configurar webhooks** (URL y token de verificación). Los otros puntos de ese paso (registrar número, pago) son para la línea real.

## 9 bis. Publicar la app (obligatorio para recibir mensajes reales)

Mientras la app está **"Sin publicar"**, Meta **solo** entrega al webhook los mensajes de prueba disparados desde el panel (botón *Probar* al lado de `messages`). Los mensajes que se escriben desde un celular, aunque sea el del administrador, **no llegan** hasta que se publica la app.

1. En la app: **Configuración de la app → Básica**. Completá:
   - **URL de la política de privacidad:** `https://sistema.taguay.com.ar/politica-de-privacidad`
   - **URL de instrucciones de eliminación de datos:** `https://sistema.taguay.com.ar/politica-de-privacidad#eliminacion-de-datos`
   - **Categoría:** *Negocios y páginas* (o la más parecida).
   - **Ícono de la app (1024 × 1024):** `docs/whatsapp/icono-app-1024.png`
   - **Guardar cambios.**
2. Menú izquierdo → **Publicar** → revisá que no queden requisitos pendientes → **Publicar**.
3. El estado pasa de "Sin publicar" a **"Publicada"**. Publicar no hace la app visible para cualquiera: solo la saca del modo de desarrollo.

> La página de privacidad está en `resources/views/legal/politica-privacidad.blade.php` (ruta `/politica-de-privacidad`). Es un borrador: la empresa debe revisar el texto.

## 9 ter. Suscribir la cuenta de WhatsApp a la app (obligatorio)

Suscribir **la app** al campo `messages` (paso 9) no alcanza: también hay que suscribir **la cuenta de WhatsApp** a la app. Si no se hace, el botón *Probar* del panel llega, pero los mensajes reales no.

1. Abrí el **Explorador de la API Graph**: <https://developers.facebook.com/tools/explorer/>
2. A la derecha: **App de Meta → Taguay Campo**. En **Token de acceso**, pegá el token del usuario del sistema.
3. Método **POST**, dirección `ID_DE_LA_CUENTA_WHATSAPP/subscribed_apps`. El ID es el *Identificador de la cuenta de WhatsApp* que se ve en *Paso 1. Pruébalo*. → **Enviar** → tiene que responder `{"success": true}`.
4. Para verificar: método **GET**, misma dirección → **Enviar** → en `data` tiene que aparecer **Taguay Campo**.

> El token del usuario del sistema necesita tener asignados **los dos activos**: la app y la cuenta de WhatsApp (*Usuarios del sistema → Asignar activos*).

## 10. ¡Probar!

Desde tu celular, escribile **"hola"** al número de prueba. El sistema tiene que responder:

> ¡Hola Gustavo! Soy el sistema Taguay 🌾
> Recibí tu mensaje: "hola".

Todo queda registrado en `storage/logs/whatsapp-AAAA-MM-DD.log` (se ve desde el Administrador de archivos):

- `ENTRANTE`: lo que llegó.
- `SALIENTE`: lo que se respondió y qué contestó Meta.

---

## Problemas frecuentes

| Síntoma | Causa probable | Solución |
|---|---|---|
| "Verificar y guardar" falla en Meta | El verify token no coincide, o la caché vieja | Revisá el `.env`, abrí `limpiarcache.php` y probá el *control rápido* del paso 8 |
| **No aparece `ENTRANTE`** cuando escribís desde el celular (pero el botón *Probar* del panel sí llega) | La **cuenta de WhatsApp no está suscrita a la app** | En el [Explorador de la API Graph](https://developers.facebook.com/tools/explorer/), con la app Taguay Campo y el token: **POST** `ID_DE_LA_CUENTA_WHATSAPP/subscribed_apps` → `{"success": true}`. Se verifica con **GET** en la misma dirección. |
| Llega el mensaje (aparece `ENTRANTE` en el log) pero no la respuesta, y el log dice `401` | **El token de prueba venció** (dura 24 h) | Generá uno nuevo en *Configuración de la API* y actualizá `WHATSAPP_TOKEN` |
| El log dice error `131030` (*recipient not in allowed list*) | Tu número no está en la lista de prueba, o es un **celular argentino** (WhatsApp lo identifica con el `9`, pero Meta acepta enviarle en otro formato) | Si el número es argentino, **el sistema lo resuelve solo**: prueba `54…` sin el 9 y `54 + área + 15 + número`, y recuerda el formato que funciona (ver `WhatsAppCliente::formatosAlternativosArgentina`). Si no es argentino, agregalo en **Para (To)**. |
| El log dice error `131047` | Pasaron más de 24 h desde tu último mensaje | Escribile primero al número desde el celular |
| No aparece nada en el log | Webhook sin suscripción a `messages`, o archivos sin subir | Revisá el paso 9.4 y el paso 7 |
| El log dice "firma inválida" | `WHATSAPP_APP_SECRET` mal copiado | Copialo de nuevo del paso 5 y limpiá la caché |

Lista de códigos de error de Meta: <https://developers.facebook.com/docs/whatsapp/cloud-api/support/error-codes>

---

## Pasar a producción (más adelante)

1. **Número propio:** conviene un chip dedicado ("Taguay Campo"). Se agrega en *WhatsApp → Configuración de la API → Agregar número de teléfono*. Un número que ya se usa en la app de WhatsApp tiene que darse de baja de la app primero, salvo que se use la opción de "coexistencia" de Meta.
2. **Verificación del negocio** en el portfolio comercial.
3. **Token permanente:** en *Configuración del negocio → Usuarios del sistema*, crear un usuario del sistema con acceso a la app y generar un token sin vencimiento, con los permisos `whatsapp_business_messaging` y `whatsapp_business_management`. Reemplaza al `WHATSAPP_TOKEN` temporal.
4. **Método de pago:** solo hace falta si el sistema **inicia** conversaciones, con plantillas. Responder a quien escribe primero (dentro de las 24 h) hoy no tiene costo. Precios: <https://developers.facebook.com/docs/whatsapp/pricing>

## Documentación oficial

- Primeros pasos Cloud API: <https://developers.facebook.com/docs/whatsapp/cloud-api/get-started>
- Webhooks: <https://developers.facebook.com/docs/whatsapp/cloud-api/guides/set-up-webhooks>
- Mensajes interactivos (listas y botones): <https://developers.facebook.com/docs/whatsapp/cloud-api/messages/interactive-list-messages>

---

## Fases del proyecto

| Fase | Estado | Qué hace |
|---|---|---|
| 1. Cuenta, app y número de prueba en Meta | ✅ Hecho (23/11/2026) | Pasos 1 a 6 |
| 2. Webhook: recibir y responder un saludo | ✅ Hecho (27/11/2026): responde por WhatsApp | Pasos 7 a 10 |
| 3. Conversación guiada para cargar Avance de Campo | ✅ Código listo (27/11/2026) | Ver "Cómo funciona la carga" abajo |
| 4. Teléfono en el ABM de usuarios, ubicación y fotos | ✅ Incluido en la fase 3 | Cada encargado carga a su nombre |
| 5. Producción | Pendiente | Número real (línea nueva), verificación del negocio |

---

## Cómo funciona la carga (fase 3)

Manual para el usuario final: `docs/manual/05-cargar-avance-por-whatsapp.md`.

### Piezas del código

| Archivo | Qué hace |
|---|---|
| `app/Services/WhatsApp/ConversacionAvance.php` | **Máquina de estados** de la conversación: qué preguntar, cómo validar cada respuesta, el resumen, corregir y guardar en `lote_estados`. |
| `app/Models/WhatsAppSesion.php` + tabla `whatsapp_sesiones` | La "memoria": en qué **paso** está cada teléfono y qué **datos** ya respondió (JSON). La fila se borra al guardar o cancelar. |
| `users.telefono_whatsapp` | Teléfono del usuario (solo dígitos, ej. `5493534191741`). Identifica quién carga. Se completa en el ABM de Usuarios. |
| `WhatsAppWebhookController::recibir` | Ignora mensajes repetidos (Meta a veces reenvía) y le pasa cada mensaje a la conversación. |
| `WhatsAppCliente` | Envía texto, botones (máx. 3) y listas (máx. 10 opciones; con "➡️ Ver más" se pagina). Descarga las fotos. |

### Recorrido

```
avance → establecimiento → lote → cultivo → cantidad (kg) → superficie (ha)
       → ¿datos del camión? [patente → chofer → número → humedad → propia/socio]
       → ¿foto / ubicación? → resumen → Guardar | Corregir | Cancelar
```

Automáticos: campaña activa más reciente, fecha de hoy, estado *por defecto*, "Empresa" tildado (igual que la app) y la observación *"Cargado por WhatsApp."*.

### Base de datos en el hosting

Script: `database/sql/2026_11_27_whatsapp_carga.sql` (columna `telefono_whatsapp` y tabla `whatsapp_sesiones`).

### Límites de WhatsApp que condicionan el diseño

- Botones: máximo **3**, con títulos de hasta 20 caracteres.
- Listas: máximo **10** opciones en total, con títulos de 24 y descripciones de 72 caracteres.
- El sistema solo puede responder dentro de las **24 h** desde el último mensaje de la persona. En esta carga siempre escribe primero el usuario, así que no hay problema.
