# Avance de Campo

El **Avance de Campo** es la sección donde se registra, desde el celular y en el campo, cada movimiento de un lote: siembra, madurez, cosecha, camiones, etc.

- **Cargar Avance** abre el formulario para registrar un movimiento nuevo.
- **Avance de Campo** es el listado de todos los movimientos cargados.

**Quién puede usarla:** usuarios con rol **Agrícola** y el **Administrador**.

## 1. Cómo cargar un avance

- Botón verde **Cargar Avance** del menú superior.
- Desde el Home: botón negro **Avance de Campo → Cargar Avance**.
- Desde el listado **Avance de Campo**: botón **Cargar Avance**.

## 2. Campos del formulario

| Campo | Obligatorio | Detalle |
|---|---|---|
| **Campaña** | Sí | Viene elegida la campaña activa más reciente. |
| **Cultivo** | Sí | Lista del ABM de Cultivos. Solo aparecen los cultivos con **Filtro Power BI = Sí**. Al elegir el lote, se completa solo con el cultivo del lote si todavía no elegiste otro. |
| **Establecimiento** | Sí | Filtra la lista de lotes. |
| **Lote** | Sí | Se puede elegir de la lista o con el botón **Buscar**. |
| **Estado** | Sí | Viene elegido el estado marcado como *por defecto* en ABM → Agrícola → Estados de Cultivo. |
| **Fecha** | Sí | Viene con la fecha de hoy. |
| Producción (Tn), Superficie (Ha) | No | Si cargás las dos, el **Rinde** se calcula solo. |
| Lluvia, Humedad | No | |
| Chasis, Chofer, Número / CTG | No | Datos del camión. |
| **Propietario** | — | Viene **tildado**: el registro es propio (Taguay). Si lo destildás, se registra como de **socio**. |
| Empresa | — | Viene tildado. |
| Silo | — | |
| Localización | No | Botón desplegable para tomar la ubicación GPS del celular. |

## 3. Cultivos que aparecen en la lista

La lista de **Cultivo** muestra solo los cultivos marcados con **Filtro Power BI = Sí** en *ABM → Agrícola → Cultivo*. Para que un cultivo no aparezca, editalo y destildá esa opción.

Si un cultivo se desmarca después de haber sido usado, los registros que ya lo tenían lo conservan. Al editarlos, sigue apareciendo en la lista.

## 4. Listado de Avance de Campo

Menú **Agrícola → Avance de Campo**.

- El **Administrador** ve los registros de todos los usuarios. El resto ve **solo los suyos**.
- Columnas: Fecha, Lote, Establecimiento, Campaña, Cultivo, **Propiedad** (*Propio* o *Socio*), Estado, Usuario y Acciones.
- Acciones:
  - **Ver** y **Editar:** sobre los registros propios. El Administrador, sobre todos.
  - **Eliminar:** solo el Administrador.
- Si el Administrador edita el registro de otra persona, el registro sigue figurando a nombre de quien lo cargó.

## 5. Registros anteriores a este cambio

Los registros cargados antes de que existiera el campo **Cultivo** tomaron automáticamente el cultivo de su lote. Todos quedaron como **Propietario** (propio).
