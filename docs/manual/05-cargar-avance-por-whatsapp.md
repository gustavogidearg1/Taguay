# Cargar Avance de Campo por WhatsApp

Además de la App Taguay, el Avance de Campo se puede cargar **chateando con el número de WhatsApp de Taguay Campo**. El sistema va haciendo las preguntas y la carga queda guardada igual que si se hiciera desde la app.

## 1. Quién puede usarlo

- Usuarios con rol **Agrícola** (o Administrador)…
- …que tengan su **Teléfono WhatsApp** cargado en el usuario.

Si escribe un número que no está registrado, el sistema responde que no está habilitado.

### Cómo habilita el Administrador a una persona

1. **Usuarios** → **Editar** el usuario (o crear uno nuevo).
2. Completar **Teléfono WhatsApp** con código de país, por ejemplo `+54 9 3534 191741`. También se acepta `3534 191741`: el sistema agrega el `+54 9`.
3. Verificar que tenga el rol **Agrícola**.
4. **Actualizar.** En el listado de usuarios aparece el teléfono en verde debajo del email.

## 2. Cómo se carga

1. Escribirle **avance** (o cualquier mensaje y tocar **📋 Cargar avance**) al número de Taguay Campo.
2. El sistema completa solo la **campaña** actual, la **fecha** de hoy y el **estado** (el marcado *por defecto*, hoy Cosecha).
3. Responder las preguntas:

| Pregunta | Cómo se responde |
|---|---|
| 🏡 Establecimiento | Tocar **Ver establecimientos** y elegir. Si son muchos, la última opción es **➡️ Ver más**. |
| 🌱 Lote | Tocar **Ver lotes** y elegir. Se ven las hectáreas y el cultivo de cada lote. |
| 🌾 Cultivo | Tocar el cultivo del lote, u **Otro cultivo** para elegir de la lista. |
| ⚖️ Cantidad (kg) | Escribir los **kilos**: `15020` o `15.020`. |
| 📐 Superficie (ha) | Escribir las hectáreas (`15`) o tocar **Saltear**. El **rinde** se calcula solo (Tn/ha). |
| 🚛 Datos del camión | **Sí, agregar** → patente, chofer, número/CTG, humedad y si la carga es **propia** o **de socio**. Cada dato se puede **Saltear**. **No** → sigue. |
| 📷 / 📍 Foto y ubicación | Opcional. Mandar una foto (📎 › Cámara, hasta 2) o la ubicación (📎 › Ubicación › *Enviar tu ubicación actual*). Después tocar **➡️ Seguir**. |

4. El sistema muestra el **resumen** con todos los datos:
   - **✅ Guardar** → queda registrado y responde con el número de carga.
   - **✏️ Corregir** → elegir qué dato cambiar. Después vuelve al resumen.
   - **❌ Cancelar** → no se guarda nada.

## 3. Comandos útiles

| Escribir | Hace |
|---|---|
| `avance` | Empieza una carga nueva (descarta la que estaba a medias). |
| `cancelar` | Cancela la carga en curso. |

Si una carga queda sin terminar por más de **2 horas**, se descarta sola.

## 4. Dónde se ven las cargas

Las cargas hechas por WhatsApp aparecen en **Avance de Campo** igual que las de la app, a nombre del usuario dueño del teléfono. En **Observación** dicen *"Cargado por WhatsApp."*. Se pueden **Ver** y **Editar** desde la app como cualquier otra carga.
