# Manual de Usuario — Sistema ITAL FIAT Repuestos

**Proyecto Final TFI · Trabajo Final Integrador**

| | |
|---|---|
| **Fecha** | 18/11/2026 |
| **Versión del sistema** | `develop` @ `3fa5e65` |
| **Dirigido a** | Administradores, vendedores y personal de depósito del taller ITAL FIAT |
| **Complementos** | *Lista de Requerimientos* y *Modelo de Base de Datos* (documentos del proyecto) |

---

## 1. Introducción

El **Sistema de Gestión de Repuestos ITAL FIAT** permite administrar el catálogo de repuestos,
el stock del depósito, los pedidos a proveedores y los usuarios del taller desde un navegador web.

Este manual describe, paso a paso, cómo realizar cada operación del sistema y qué puede hacer
cada rol. Cuando una funcionalidad todavía no está disponible, se indica expresamente como
**pendiente**.

---

## 2. Requisitos técnicos

- Navegador web actualizado: **Chrome, Edge, Firefox o Safari** (últimas dos versiones).
- Conexión a internet o red interna donde esté publicado el sistema.
- Resolución de pantalla recomendada: 1280 × 720 o superior (la interfaz se adapta a pantallas
  chicas).

---

## 3. Acceso al sistema

### 3.1 Credenciales de demostración

Para la entrega del proyecto, la base incluye tres usuarios de prueba:

| Rol | Email | Contraseña |
|-----|-------|------------|
| Administrador | `admin@italfiat.com` | `password` |
| Vendedor | `vendedor@italfiat.com` | `password` |
| Depósito | `deposito@italfiat.com` | `password` |

> En un entorno real, el **administrador** es quien crea y habilita las cuentas (ver §11).

### 3.2 Iniciar sesión

1. Abrí el navegador en la dirección del sistema.
2. Completá **Correo electrónico** y **Contraseña**.
3. Hacé clic en **Iniciar sesión**.
4. Si las credenciales son correctas, entrás al **Dashboard** según tu rol.

**Errores comunes:**

| Mensaje / situación | Causa | Solución |
|---|---|---|
| "Estas credenciales no coinciden con nuestros registros" | Email o contraseña incorrectos | Verificá mayúsculas/minúsculas y usá las credenciales de §3.1 |
| "El campo correo electrónico es obligatorio" | Dejaste el email vacío | Completá ambos campos |
| Acceso denegado a un módulo | Tu rol no tiene permiso | Consultá la matriz de §13 |

### 3.3 Cuentas y contraseñas

- Las cuentas las **crea el administrador** desde el módulo Usuarios (§11).
- Si olvidaste tu contraseña, pedile al administrador que la restablezca desde la edición del
  usuario. *(El enlace "¿Olvidaste tu contraseña?" de la pantalla de acceso se habilitará
  próximamente.)*
- Cada usuario puede cambiar su propia contraseña desde su perfil (§12).

### 3.4 Cerrar sesión

Hacé clic en el botón **Salir**, arriba a la derecha. Si trabajás en una computadora compartida,
cerrá sesión siempre que termines.

---

## 4. Interfaz general

### 4.1 Barra superior

| Elemento | Descripción |
|---|---|
| **IF · ITAL FIAT — Repuestos & Stock** | Marca del sistema; al hacer clic vuelve al Dashboard |
| **Menú** | Muestra solo los módulos a los que tu rol tiene acceso: Dashboard · Productos · Stock · Pedidos · Ventas · Clientes · Reportes · Usuarios |
| **Tu nombre + rol** | Iniciales en el círculo, nombre completo y rol (Administrador / Vendedor / Depósito) |
| **Salir** | Cierra la sesión |

El módulo activo se resalta en el menú.

### 4.2 Pie de página

"ITAL FIAT · Formosa, Argentina — Sistema de gestión de repuestos".

---

## 5. Dashboard (Panel de control)

Es la pantalla inicial. Muestra el estado general del negocio:

| Indicador | Qué significa |
|---|---|
| **Total Productos** | Cantidad de repuestos distintos cargados en el catálogo |
| **Stock Crítico (Alertas)** | Productos cuyo stock está en el mínimo o por debajo |
| **Pedidos Pendientes** | *(Pendiente: hoy muestra un valor fijo; se conectará a la BD)* |
| **Ventas del Mes** | *(Pendiente: hoy muestra un valor fijo; se habilitará con el módulo de ventas)* |
| **Últimos Movimientos de Stock** | Las 5 últimas entradas/salidas con producto, tipo, cantidad y fecha relativa ("hace 2 horas") |
| **Repuestos a Reponer** | Hasta 5 productos con stock bajo, con las unidades que quedan y el mínimo configurado |

**Buenas prácticas:** revisá el panel al inicio de cada jornada y priorizá los repuestos del
panel "a reponer".

---

## 6. Productos (catálogo)

Accesible para **todos los roles**. Administrador, vendedor y depósito pueden crear y editar;
solo el administrador puede eliminar.

### 6.1 Listado, búsqueda y filtros

- **Buscar**: por código, descripción o modelo (ej.: `REP-004`, `filtro`, `palio`).
- **Filtros**: por **marca** y por **modelo** (desplegables con los valores ya cargados).
- Cada fila muestra código, descripción, marca/modelo, precio de venta, stock con su estado y
  las acciones disponibles.

### 6.2 Crear un producto

1. En **Productos**, hacé clic en **➕ Nuevo Producto**.
2. Completá el formulario:

| Campo | Obligatorio | Indicaciones |
|---|---|---|
| Código del Repuesto | Sí | Debe ser **único** en todo el sistema (ej.: `REP-004`) |
| Descripción | Sí | Nombre comercial (ej.: `Filtro de Aire`) |
| Marca | Sí | Seleccioná entre las marcas disponibles (Fiat, Volkswagen, Ford y las que ya existan) |
| Modelo Compatible | No | Ej.: `Palio / Siena` |
| Precio de Venta ($) | Sí | Número con hasta 2 decimales, no negativo |
| Stock Inicial | Sí | Unidades disponibles al crear (0 o más) |
| Stock Mínimo | No | Umbral de alerta (por defecto 5) |

3. Hacé clic en **Guardar Producto**.
4. Si el stock inicial es mayor a 0, el sistema registra automáticamente un **movimiento de
   entrada** con motivo "Stock inicial".

**Errores posibles:** código ya utilizado (usá otro), campos obligatorios vacíos o precios
negativos — el sistema muestra el detalle junto al campo.

### 6.3 Editar un producto

1. En la fila del producto, hacé clic en **Editar**.
2. Modificá los campos y guardá.
3. Si cambiaste el **stock**, el sistema genera solo un movimiento (entrada si subió, salida si
   bajó) con motivo "Ajuste desde edición". Si no cambiaste el stock, no se registra nada.

### 6.4 Eliminar un producto

- **Solo administrador**: botón **Eliminar** en la fila (con confirmación).
- ⚠️ Al eliminar un producto se borran también sus movimientos y pedidos asociados. Verificá
  que no necesites el historial antes de confirmar.

### 6.5 Estados de stock en las listas

| Etiqueta | Significado |
|---|---|
| **Sin stock** (rojo) | Quedan 0 unidades |
| **Stock bajo** (amarillo) | Quedan unidades pero llegaste al stock mínimo |
| **N un.** (verde) | Por encima del mínimo |

---

## 7. Stock (depósito)

Accesible para **todos los roles**; el formulario de movimientos solo lo ven administrador y
depósito.

### 7.1 Ver las existencias

- La lista muestra todos los repuestos **ordenados de menor a mayor stock** (los críticos
  primero), con código, descripción, marca/modelo y unidades.
- Se destacan los estados *Sin stock* y *Stock bajo*, e incluye la fecha de última actualización.

### 7.2 Registrar una entrada (recepción de mercadería)

1. Andá a **Stock** y hacé clic en el ícono de acción del producto → **Registrar movimiento**.
2. Tipo: **Entrada**.
3. **Cantidad**: número entero mayor a 0.
4. **Motivo** (opcional): ej. `Compra a proveedor`.
5. **Guardar**. El stock del producto se incrementa al instante.

### 7.3 Registrar una salida (venta o uso)

Mismos pasos que la entrada, pero elegí **Salida**.

**Validaciones:**

- La cantidad debe ser un entero positivo.
- **No se puede sacar más stock del disponible**: si intentás descontar más unidades de las
  que hay, el sistema bloquea el movimiento y muestra "No hay suficiente stock disponible".

### 7.4 Historial de movimientos

Debajo de la lista, **Historial de movimientos** muestra los últimos 10 movimientos con:

Fecha · Producto · Tipo (Entrada verde / Salida rojo) · Cantidad (+/-) · Motivo.

> Cada cambio de stock — alta de producto, edición de stock o recepción de pedido — deja su
> movimiento en el historial. Si el producto fue eliminado, figura como "Producto eliminado".

---

## 8. Pedidos de reposición

Módulo de **administrador y depósito**.

### 8.1 Indicadores y filtros

- Tarjetas: **Pedidos** (total), **Pendientes**, **Recibidos**, **Unidades pedidas** (en pedidos
  pendientes).
- Filtros: **buscar** (por código, descripción o proveedor), por **estado** y por **proveedor**.

### 8.2 Crear un pedido

1. Andá a **Pedidos** → **➕ Nuevo pedido**.
2. Completá:

| Campo | Obligatorio | Indicaciones |
|---|---|---|
| Repuesto | Sí | Seleccioná el producto; el desplegable muestra código, descripción y stock actual |
| Cantidad | Sí | Entero mayor a 0 |
| Proveedor | No | Texto libre con autocompletado de proveedores usados (ej.: `Repuestos del Norte S.A.`) |

3. **Guardar pedido**. Nace en estado **pendiente**.

### 8.3 Estados y recepción

| Estado | Significado | Acción |
|---|---|---|
| **Pendiente** | Pedido realizado, esperando mercadería | Botón **Recibir** |
| **Recibido** | La mercadería ingresó | Al recibir: **suma el stock** del producto y registra la **entrada** correspondiente |
| **Cancelado** | No se concretó | Sin efectos sobre el stock |

---

## 9. Ventas y Clientes (vista informativa)

Los módulos **Ventas** y **Clientes** (visibles para administrador y vendedor) muestran hoy
**datos de ejemplo** mientras se completa su integración con la base de datos:

- **Ventas**: historial de tickets (número, fecha, cliente, método de pago, total) — la
  registración de ventas reales está **pendiente** (RF-30 a RF-33 de la Lista de Requerimientos).
- **Clientes**: directorio con nombre/razón social, CUIT-DNI, teléfono y saldo de cuenta
  corriente — la carga real de clientes está **pendiente** (RF-29).

Los botones de estos módulos aún no realizan operaciones.

---

## 10. Reportes

Módulo de **solo administrador**. Muestra dos tarjetas — **Reporte de Inventario** y **Reporte
de Ventas** — con sus botones de exportación a PDF y Excel **pendientes de conectar**
(RF-34 y RF-35).

---

## 11. Usuarios (solo administrador)

1. Andá a **Usuarios**.
2. **Buscar / filtrar**: por texto libre y por rol.
3. **Nuevo usuario**: nombre, email único, rol (Administrador / Vendedor / Depósito) y
   contraseña.
4. **Editar**: podés cambiar datos, rol y (opcional) contraseña; si no escribís una contraseña
   nueva, se conserva la actual.
5. **Eliminar**: con confirmación.

**Protecciones del sistema:**

- No podés eliminar **tu propio usuario**.
- No podés dejar el sistema **sin administradores**: no se permite degradar o eliminar al último
  administrador.
- El email debe ser único.

---

## 12. Tu perfil

En tu perfil podés actualizar tu **nombre**, tu **email** y tu **contraseña** (requiere la
contraseña actual). Se encuentra en la ruta `/profile` del sistema (enlace directo).

---

## 13. Matriz de permisos por rol

| Módulo / acción | Administrador | Vendedor | Depósito |
|---|:-:|:-:|:-:|
| Dashboard | ✅ | ✅ | ✅ |
| Productos: ver, buscar, crear, editar | ✅ | ✅ | ✅ |
| Productos: eliminar | ✅ | ❌ | ❌ |
| Stock: ver existencias e historial | ✅ | ✅ | ✅ |
| Stock: registrar entradas/salidas | ✅ | ❌ | ✅ |
| Pedidos: ver, crear, recibir | ✅ | ❌ | ✅ |
| Ventas / Clientes | ✅ | ✅ | ❌ |
| Reportes | ✅ | ❌ | ❌ |
| Usuarios: alta, edición, baja | ✅ | ❌ | ❌ |

*El menú superior oculta los módulos que tu rol no puede abrir; si ingresás directamente a una
dirección sin permiso, el sistema te redirige al Dashboard.*

---

## 14. Preguntas frecuentes

**¿Puedo recuperar mi contraseña solo?**
Por ahora no: pedile al administrador que la restablezca desde Usuarios → Editar.

**¿Por qué no veo el módulo de Pedidos?**
Solo lo ven administrador y depósito. Consultá la matriz de §13.

**Eliminé un producto por error y perdió sus movimientos.**
Sí: la baja es definitiva. Confirmá siempre en el diálogo antes de eliminar y usá la edición de
stock si solo querías ajustar unidades.

**Registré una salida de más.**
Creá una **entrada** de corrección con el motivo correspondiente; todo queda en el historial.

**El dashboard muestra valores fijos en "Pedidos pendientes" y "Ventas del Mes".**
Son los dos indicadores pendientes de conexión a la base (RF-27 y RF-28 del Lista de
Requerimientos).

---

## 15. Anexo — Estado de los módulos

| Módulo | Estado |
|---|---|
| Acceso, roles, usuarios, perfil | ✅ Funcional |
| Productos (alta, edición, filtros, baja) | ✅ Funcional |
| Stock (movimientos e historial) | ✅ Funcional |
| Pedidos a proveedores | ✅ Funcional |
| Dashboard (indicadores reales + movimientos) | ✅ Funcional (2 tarjetas pendientes) |
| Ventas / Clientes | ⛔ Vista informativa, integración pendiente |
| Reportes | ⛔ Pendiente |
| Recuperación de contraseña desde la UI | ⛔ Pendiente (habilitar enlace) |
| Módulo de proveedores con pantalla propia | ⛔ Pendiente (hoy se cargan como texto en los pedidos) |

---

*Manual de Usuario · Proyecto Final TFI — ITAL FIAT Repuestos · 18/11/2026 · develop (3fa5e65)*
