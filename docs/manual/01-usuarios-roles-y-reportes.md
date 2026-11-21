# Usuarios, roles y reportes

Este capítulo explica cómo se definen los accesos de cada persona en el sistema Taguay y cómo administrarlos.

## 1. Dos cosas distintas: roles y reportes

Cada usuario tiene dos tipos de acceso, que se configuran por separado:

| Tipo | Qué define | ¿Cuántos puede tener? |
|---|---|---|
| **Roles** | Qué puede **operar**: menús, cargas y pantallas de trabajo | Uno o varios |
| **Reportes** | Qué **tarjetas de reportes** ve en la pantalla de inicio (Home) | Ninguno, uno o varios |

Así, una persona de campo puede cargar estados de lotes sin ver información económica. Y alguien de dirección puede ver reportes sin poder cargar nada.

## 2. Roles

| Rol | Qué puede hacer |
|---|---|
| **Administrador** | Ve y administra todo: usuarios, ABM (establecimientos, cultivos, campañas, lotes, estados de cultivo, rindes), importación de facturas, todos los reportes y todos los registros de todos los usuarios. Es el único que puede **eliminar** registros de Estados por Lote. |
| **Agrícola** | Usa la **Carga Rápida** (botón *Carga Estado*) y **Estados por Lote**: carga, ve y edita **solo sus propios registros**. También accede a **Lluvias**. |
| **Ganadero** | Accede al menú **Ganadería** (Haciendas). |
| **Comercial** | Accede al menú **Comerciales**: Contratos, Compras y Calificación de Créditos. |

Un usuario puede tener varios roles. Por ejemplo, alguien con **Agrícola + Ganadero** ve ambos menús.

## 3. Reportes del Home

| Reporte | Tarjetas que muestra en el Home |
|---|---|
| **Agrícola** | Margen Bruto y Cosecha |
| **Ganadería** | Tablero de Ganadería |
| **Flujo de Fondo** | Flujo de Fondo |

El **Administrador ve todos los reportes** aunque no estén tildados.

## 4. La pantalla de inicio (Home)

- Arriba aparecen las **tarjetas de los reportes** que el usuario tiene habilitados.
- Si el usuario tiene el rol **Agrícola**, debajo aparece un botón negro **"Estados por Lote"**. Al tocarlo se abre una tabla con los últimos 10 movimientos:
  - El administrador ve los movimientos de todos.
  - El resto ve **solo sus propios movimientos**.
- Esa tabla aparece **abierta** cuando el usuario no tiene ningún reporte (por ejemplo, alguien de campo que solo carga datos). Si tiene al menos un reporte, aparece **cerrada** y se abre con el botón.
- Desde ahí hay accesos directos a **Carga Rápida** y a **Ver todos**.

## 5. Cómo crear o editar un usuario

> Solo el **Administrador** puede administrar usuarios.

1. Ir a **Usuarios** (`/users`).
2. Tocar **Crear Usuario**, o **Editar** en un usuario existente.
3. Completar nombre y email.
4. Tildar los **Roles** que correspondan.
5. Tildar los **Reportes en el Home** que debe ver.
6. Guardar.

### Contraseña al editar

Al editar un usuario, la contraseña **no se cambia** salvo que se active el interruptor **"Cambiar contraseña"**. Recién ahí aparecen los campos para escribir la nueva clave y confirmarla. Si no se activa, el usuario sigue con la contraseña que ya tenía.

### Protecciones

- Un administrador no puede quitarse a sí mismo el rol de Administrador.
- Nadie puede eliminar su propio usuario.

## 6. Ejemplos de configuración

| Persona | Roles | Reportes |
|---|---|---|
| Encargado de campo que carga estados | Agrícola | — |
| Responsable agrícola que también analiza resultados | Agrícola | Agrícola |
| Encargado de hacienda | Ganadero | Ganadería |
| Área comercial | Comercial | — |
| Dirección (solo consulta) | — | Agrícola, Ganadería, Flujo de Fondo |
| Administración del sistema | Administrador | (ve todo) |

## 7. Listado de usuarios

En **Usuarios** se puede:

- Buscar por nombre o email.
- Filtrar por **Rol** o por **Reporte**.
- Ordenar por nombre o email tocando el encabezado de la columna.

La tabla muestra los roles de cada usuario (Administrador en negro, el resto en verde) y los reportes que tiene habilitados.
