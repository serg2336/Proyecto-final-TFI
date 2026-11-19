# Lista de Requerimientos — Sistema ITAL FIAT Repuestos

**Proyecto Final TFI · Trabajo Final Integrador**

| | |
|---|---|
| **Fecha** | 18/11/2026 |
| **Rama / commit** | `develop` @ `3fa5e65` |
| **Stack** | PHP 8.3 · Laravel 13 · MySQL (`ital_fiat`) · Tailwind CSS · Vite |
| **Estado de la suite** | 112 tests en verde (grupo `stress`: 6) |
| **Prioridades** | **Imprescindible** = requisito de aprobación · **Deseable** = valor agregado |
| **Estados** | ✅ Implementado · ⛔ Pendiente |

---

## 1. Alcance del sistema

Sistema web de gestión de repuestos automotrices para el taller **ITAL FIAT**, con:

1. **Acceso y roles** (administrador · vendedor · depósito) sobre módulos protegidos.
2. **Gestión de usuarios** (solo administrador).
3. **Catálogo de productos** con stock, búsqueda y filtros.
4. **Control de stock** con historial de entradas y salidas.
5. **Pedidos de reposición** a proveedores con ciclo de estados.
6. **Directorio de proveedores**.
7. **Dashboard** con indicadores del inventario.
8. **Módulos pendientes de integración**: clientes, ventas, reportes (hoy vistas estáticas).

---

## 2. Requerimientos funcionales

### A. Acceso, autenticación y roles (HU1)

| ID | Requerimiento | Prioridad | Estado | Evidencia |
|----|---------------|-----------|--------|-----------|
| RF-01 | Iniciar sesión con email y contraseña, con mensaje claro ante credenciales inválidas | Imprescindible | ✅ | `LoginTest` |
| RF-02 | Cerrar sesión | Imprescindible | ✅ | `AuthenticationTest` |
| RF-03 | Recuperación de contraseña por email | Deseable | ✅ | `PasswordResetTest` |
| RF-04 | Registro de nuevos usuarios (habilitado) | Deseable | ✅ | `RegistrationTest` |
| RF-05 | Tres roles (`administrador`, `vendedor`, `deposito`); el rol define el home y los módulos accesibles | Imprescindible | ✅ | `UserManagementTest::test_cada_rol_accede_solo_a_sus_modulos` |
| RF-06 | Autorización por middleware en cada ruta (`role:…`) denegando el acceso no permitido | Imprescindible | ✅ | `routes/web.php`, `EnsureUserHasRole` |

### B. Gestión de usuarios (solo administrador)

| ID | Requerimiento | Prioridad | Estado | Evidencia |
|----|---------------|-----------|--------|-----------|
| RF-07 | Listado de usuarios con buscador y filtro por rol | Imprescindible | ✅ | `UserManagementTest` |
| RF-08 | Alta y edición con validación de email único, rol válido y contraseña (opcional al editar) | Imprescindible | ✅ | `UserManagementTest` |
| RF-09 | Baja con protecciones: no eliminarse a sí mismo ni dejar el sistema sin administradores | Imprescindible | ✅ | `UserManagementTest` |
| RF-10 | Perfil propio: editar nombre, email y contraseña | Deseable | ✅ | `ProfileTest` |

### C. Catálogo de productos (HU3)

| ID | Requerimiento | Prioridad | Estado | Evidencia |
|----|---------------|-----------|--------|-----------|
| RF-11 | Listado con búsqueda por código / descripción / modelo y filtros por marca y modelo | Imprescindible | ✅ | `ProductoTest` |
| RF-12 | Alta con código único y validaciones (precio ≥ 0, stock ≥ 0, campos obligatorios) | Imprescindible | ✅ | `ProductoTest` |
| RF-13 | Edición que conserva el código propio; al cambiar el stock registra el movimiento equivalente | Imprescindible | ✅ | `ProductoTest::test_editar_el_stock_registra_el_movimiento_correspondiente` |
| RF-14 | Baja de productos únicamente para administrador | Imprescindible | ✅ | `ProductoTest`, middleware `role:administrador` |
| RF-15 | Estados visibles de stock (sin stock / stock bajo / OK) en los listados | Deseable | ✅ | `ProductoTest`, `stock.blade.php` |

### D. Control de stock (HU4)

| ID | Requerimiento | Prioridad | Estado | Evidencia |
|----|---------------|-----------|--------|-----------|
| RF-16 | Vista de stock ordenada por criticidad con estados por producto | Imprescindible | ✅ | `MovimientoStockTest` |
| RF-17 | Registro de entradas y salidas (administrador y depósito) | Imprescindible | ✅ | `MovimientoStockTest` |
| RF-18 | Validaciones: cantidad entera positiva y la salida no puede superar el stock disponible | Imprescindible | ✅ | `MovimientoStockTest::test_no_se_puede_sacar_mas_stock_del_disponible` |
| RF-19 | Historial de movimientos con fecha, producto, tipo, cantidad y motivo | Imprescindible | ✅ | `MovimientoStockTest` |
| RF-20 | Trazabilidad: todo cambio de stock (alta, edición, recepción de pedido) deja un movimiento | Imprescindible | ✅ | `ProductoTest`, `PedidoController::recibir` |

### E. Pedidos a proveedores (HU5)

| ID | Requerimiento | Prioridad | Estado | Evidencia |
|----|---------------|-----------|--------|-----------|
| RF-21 | Alta de pedido (producto, cantidad, proveedor, observaciones) para administrador y depósito | Imprescindible | ✅ | `PedidoTest` |
| RF-22 | Ciclo de estados pendiente → recibido / cancelado; al recibir suma stock y genera la entrada | Imprescindible | ✅ | `PedidoTest` |
| RF-23 | Listado con filtros (búsqueda, estado, proveedor) e indicadores: total, pendientes, recibidos, unidades pedidas | Imprescindible | ✅ | `pedidos.blade.php` |

### F. Proveedores

| ID | Requerimiento | Prioridad | Estado | Evidencia |
|----|---------------|-----------|--------|-----------|
| RF-24 | Alta de proveedor con nombre único y datos de contacto opcionales | Imprescindible | ✅ | `ProveedorTest` |

### G. Dashboard

| ID | Requerimiento | Prioridad | Estado | Evidencia |
|----|---------------|-----------|--------|-----------|
| RF-25 | Indicadores reales: total de productos y stock crítico (consultados a la BD) | Imprescindible | ✅ | `ProductoTest::test_dashboard_usa_datos_reales` |
| RF-26 | Últimos movimientos de stock y panel de "repuestos a reponer" | Imprescindible | ✅ | `ProductoTest`, `StressTest` |
| RF-27 | Tarjeta "Pedidos pendientes" con el conteo real de la BD | Imprescindible | ⛔ | Hoy valor fijo `5` en `dashboard.blade.php` |
| RF-28 | Tarjeta "Ventas del mes" con la suma real del mes en curso | Imprescindible | ⛔ | Hoy valor fijo `$450.000`; requiere la tabla `ventas` |

### H. Clientes

| ID | Requerimiento | Prioridad | Estado | Evidencia |
|----|---------------|-----------|--------|-----------|
| RF-29 | Directorio de clientes (nombre / razón social, CUIT-DNI, teléfono, saldo de cuenta corriente) con búsqueda por nombre, CUIT o DNI | Imprescindible | ⛔ | `clientes.blade.php` es una vista estática sin BD ni modelo |

### I. Ventas

| ID | Requerimiento | Prioridad | Estado | Evidencia |
|----|---------------|-----------|--------|-----------|
| RF-30 | Registro de venta con ticket único (formato T-00125), cliente, vendedor, método de pago (efectivo, transferencia, tarjeta, cuenta corriente) e ítems | Imprescindible | ⛔ | `ventas.blade.php` estática; sin tabla `ventas` |
| RF-31 | Al concretar la venta: descontar el stock de cada producto y registrar la salida correspondiente | Imprescindible | ⛔ | Depende de RF-30 |
| RF-32 | Historial de ventas con filtros por fecha y cliente | Deseable | ⛔ | `ventas.blade.php` (filtros sin acción) |
| RF-33 | Cuenta corriente: los cobros/pagos actualizan el saldo del cliente | Deseable | ⛔ | Requiere `clientes.saldo_cta_cte` |

### J. Reportes

| ID | Requerimiento | Prioridad | Estado | Evidencia |
|----|---------------|-----------|--------|-----------|
| RF-34 | Reporte de inventario (listado con valorización `stock × precio`) con exportación a PDF y Excel | Deseable | ⛔ | `reportes.blade.php`: botones sin acción |
| RF-35 | Reporte de ventas por período, vendedor y productos más vendidos, con exportación | Deseable | ⛔ | `reportes.blade.php`: botones sin acción |

---

## 3. Requerimientos no funcionales

| ID | Requerimiento | Prioridad | Estado | Evidencia |
|----|---------------|-----------|--------|-----------|
| RNF-01 | **Seguridad**: contraseñas con bcrypt, formularios protegidos con CSRF, validación en servidor y autorización por rol en cada ruta | Imprescindible | ✅ | Breeze + `EnsureUserHasRole` |
| RNF-02 | **Integridad de datos**: claves foráneas con reglas de borrado, unicidades (`productos.codigo`, `proveedores.nombre`, `users.email`) y `stock = entradas − salidas` verificado por test | Imprescindible | ✅ | Migraciones + `StressTest` |
| RNF-03 | **Rendimiento de listados**: respuesta < 5 s con 2000 productos y 1000 movimientos en productos, stock y dashboard | Imprescindible | ✅ | `StressTest` (umbrales 5 s) |
| RNF-04 | **Carga masiva**: alta de 2000 productos en < 10 s | Deseable | ✅ | `StressTest::test_la_carga_masiva…` |
| RNF-05 | **Escalabilidad de consultas**: cantidad de queries constante ante aumento de volumen (sin N+1); < 15 queries por request de listado | Imprescindible | ✅ | `StressTest` (100 vs 2000 productos) |
| RNF-06 | **Testing automatizado**: suite completa en verde (112 tests) y grupo de estrés ejecutable independiente (`php artisan test --group=stress`) | Imprescindible | ✅ | `tests/Feature/StressTest.php` |
| RNF-07 | **Usabilidad**: interfaz responsive en español, estados vacíos descriptivos y mensajes de éxito/error en cada operación | Deseable | ✅ | Vistas Blade + Tailwind |
| RNF-08 | **Auditoría de stock**: cada movimiento registra qué cambió, cuánto, cuándo y por qué (motivo) | Imprescindible | ✅ | Tabla `movimientos` |
| RNF-09 | **Entorno reproducible**: migraciones + seeders recrean la base desde cero con usuarios demo (`admin@`, `vendedor@`, `deposito@italfiat.com` / `password`) | Imprescindible | ✅ | `DatabaseSeeder` |

---

## 4. Requerimientos de integración (próxima iteración)

Conectan los módulos pendientes (RF-27 a RF-35) y las mejoras de modelo detalladas en
*Modelo de Base de Datos* (§3 y §5 del documento generado):

| ID | Requerimiento | Prioridad |
|----|---------------|-----------|
| RIN-01 | Migraciones `clientes`, `ventas` y `detalle_ventas` (con índices en `fecha_venta` y `cliente_id`) | Imprescindible |
| RIN-02 | Modelos `Cliente`, `Venta` y `DetalleVenta` con relaciones | Imprescindible |
| RIN-03 | `VentaController` con alta transaccional (venta + detalle + descuento de stock + movimiento) y numeración de ticket | Imprescindible |
| RIN-04 | Activar `ClienteController` (hoy existe sin ruta real) | Imprescindible |
| RNF/RIN-05 | Cambiar la baja de productos a borrado lógico cuando exista historial de ventas (hoy `CASCADE` borra movimientos y pedidos) | Imprescindible |
| RIN-06 | Opcional: FK `pedidos.proveedor_id` → `proveedores.id` (hoy texto libre) | Deseable |
| RIN-07 | Opcional: tabla `detalle_pedidos` para pedidos con múltiples ítems | Deseable |
| RIN-08 | Opcional: tabla `marcas` normalizada (`productos.marca_id`) | Deseable |

---

## 5. Criterios de aceptación globales

1. `php artisan test` → **112/112 en verde**.
2. `php artisan test --group=stress` → **6/6 en verde**, respetando los umbrales (< 5 s por listado, < 10 s por carga, ≤ 15 queries).
3. `php artisan migrate:fresh --seed` → recrea la base completa con los 3 usuarios demo funcionando.
4. Cada rol accede solo a sus módulos (matriz validada por test).
5. Ningún número visible en el dashboard debe ser fijo: **hoy quedan 2 pendientes (RF-27 y RF-28)**.
6. Todo módulo nuevo llega con sus feature tests y, si renderiza listados, con su test de estrés en el grupo `stress`.

---

## 6. Trazabilidad historias de usuario ↔ ramas

| HU / módulo | Rama de origen | Integración |
|-------------|----------------|-------------|
| HU1 · Auth y roles | `feature/login-auth`, `feature/breeze-login`, `frontend/hu1-auth` | → `main` (fix de integración) |
| HU3 · Productos | `hu3-productos` | PR #7 → `develop` |
| HU4 · Stock | `hu4-stock` | PR #8 → `develop` |
| HU5 · Pedidos | `frontend/hu5-pedidos` | PR #10 → `develop` |
| Proveedores | `feature/proveedores` | PR #11 → `develop` |
| Stress tests | `feature/stress-tests` | PR #12 → `develop` |
| Revisión de stress | `feature/stress-tests-review` | merge directo → `develop` (`3fa5e65`) |
