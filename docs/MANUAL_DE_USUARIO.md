# Manual de usuario — ITAL FIAT

Sistema de gestión de repuestos y stock para **ITAL FIAT (Formosa, Argentina)**.

Este manual explica cómo instalar el sistema, qué puede hacer cada rol y cómo usar cada pantalla.

---

## 1. ¿Qué es el sistema?

Es una aplicación web desarrollada con **Laravel** que permite:

- Administrar el catálogo de repuestos (código, marca, modelo, precio, stock).
- Controlar el stock con entradas y salidas e historial de movimientos.
- Generar pedidos a proveedores y recibirlos para reponer stock.
- Emitir reportes de inventario, movimientos y pedidos en **PDF** y **Excel**.
- Gestionar usuarios con tres roles: administrador, vendedor y depósito.

---

## 2. Requisitos técnicos

| Componente | Versión mínima |
|---|---|
| PHP | 8.3 |
| Extensiones PHP | `dom`, `mbstring`, `xml`, `zip`, `gd` |
| Composer | 2.x |
| MySQL / MariaDB | 8.0 / 10.4 |
| Node.js + npm | 18+ |

> `gd` es necesaria para exportar a Excel. Si falta, Composer no instala `maatwebsite/excel`.

---

## 3. Instalación local

```bash
# 1. Clonar el repositorio
git clone https://github.com/serg2336/Proyecto-final-TFI.git
cd Proyecto-final-TFI

# 2. Instalar dependencias de PHP
composer install

# 3. Configurar el entorno
copy .env.example .env
php artisan key:generate

# 4. Crear la base de datos y cargar datos de prueba
php artisan migrate --seed

# 5. Compilar los estilos y scripts
npm install
npm run build

# 6. Levantar el servidor
php artisan serve
```

En `.env` hay que configurar la conexión a MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ital_fiat
DB_USERNAME=root
DB_PASSWORD=
```

La aplicación queda disponible en `http://127.0.0.1:8000`.

### Usuarios de prueba

| Rol | Correo | Contraseña |
|---|---|---|
| Administrador | `admin@italfiat.com` | `password` |
| Vendedor | `vendedor@italfiat.com` | `password` |
| Depósito | `deposito@italfiat.com` | `password` |

---

## 4. Roles y permisos

| Función | Administrador | Vendedor | Depósito |
|---|:---:|:---:|:---:|
| Ver dashboard | Sí | Sí | Sí |
| Ver productos | Sí | Sí | Sí |
| Crear y editar productos | Sí | Sí | Sí |
| Eliminar productos | Sí | No | No |
| Ver stock e historial | Sí | Sí | Sí |
| Registrar entradas y salidas | Sí | No | Sí |
| Pedir al proveedor desde Stock | Sí | No | Sí |
| Ver y gestionar pedidos | Sí | No | Sí |
| Ventas y clientes | Sí | Sí | No |
| Reportes (PDF / Excel) | Sí | No | No |
| Gestión de usuarios | Sí | No | No |

Cada rol ingresa directamente a su pantalla principal:

- **Administrador** → Dashboard.
- **Vendedor** → Ventas.
- **Depósito** → Stock.

---

## 5. Inicio de sesión

1. Abrir la aplicación e ingresar correo y contraseña.
2. El botón del **ojo** muestra u oculta la contraseña.
3. Si no recuerda la contraseña, usar **¿Olvidaste tu contraseña?** y seguir el enlace del correo.
4. Al iniciar sesión, el sistema redirige según el rol.

Para salir, usar **SALIR** en la barra superior.

---

## 6. Dashboard

Muestra el resumen general del inventario:

- **Total Productos**: cantidad de repuestos cargados.
- **Stock Crítico (Alertas)**: productos con stock igual o menor al mínimo.
- **Repuestos a Reponer**: listado de los más urgentes.
- **Últimos Movimientos de Stock**: las últimas entradas y salidas.

> Las tarjetas «Pedidos Pendientes» y «Ventas del Mes» están previstas para la HU7 (Dashboard dinámico).

---

## 7. Productos

Ruta: **Productos**.

### Buscar
El buscador filtra por código, descripción, marca o modelo.

### Crear un producto
1. Presionar **Nuevo producto**.
2. Completar: código, descripción, marca, modelo, precio, stock y stock mínimo.
3. Guardar. El código no puede repetirse.

### Editar
Usar **Editar** en la fila del producto y guardar los cambios.

### Eliminar
Solo el **administrador** ve el botón **Eliminar**. El sistema pide confirmación.

> El **stock mínimo** es el umbral de reposición: cuando el stock baja de ese valor, el sistema muestra alertas.

---

## 8. Stock

Ruta: **Stock**.

### Alertas en rojo
Si hay productos con stock bajo o agotado, aparece un aviso rojo con el detalle y la cantidad de unidades disponibles. La fila del producto también se resalta en rojo y el estado se muestra como:

- **Sin stock** (rojo oscuro): 0 unidades.
- **Stock bajo** (rojo): stock igual o menor al mínimo.
- **Disponible** (verde): stock por encima del mínimo.

### Registrar una entrada o salida
1. Presionar **Movimiento** en la fila del producto.
2. Elegir **Entrada** o **Salida**.
3. Indicar la cantidad y, opcionalmente, el motivo (por ejemplo: «Compra a proveedor» o «Venta de mostrador»).
4. Confirmar. El stock se actualiza automáticamente.

> No se puede registrar una salida mayor al stock disponible.

### Pedir al proveedor
En los productos con stock bajo, el botón **Pedir al proveedor** genera un pedido con la cantidad necesaria para alcanzar el mínimo. El pedido queda en estado **Pendiente** hasta que se reciba.

### Historial
Debajo de la tabla se listan los últimos movimientos con fecha, producto, tipo, cantidad y motivo.

---

## 9. Pedidos a proveedores

Ruta: **Pedidos** (administrador y depósito).

### Nuevo pedido
1. Presionar **Nuevo pedido**.
2. Elegir el repuesto, la cantidad y, si se quiere, el proveedor.
3. Guardar. El pedido se crea en estado **Pendiente**.

### Recibir un pedido
1. Ubicar el pedido pendiente.
2. Presionar **Recibir pedido** y confirmar.
3. El estado pasa a **Recibido** y la cantidad se suma al stock del producto.

> Recibir dos veces el mismo pedido no duplica el stock: el sistema ignora la segunda recepción.

### Filtros
Se puede filtrar por texto (producto, código o proveedor), por proveedor y por estado (pendiente, recibido, cancelado).

---

## 10. Reportes

Ruta: **Reportes** (solo administrador).

### Filtro por fechas
Elegir **Desde** y **Hasta** y presionar **Aplicar filtro**. Las fechas se aplican a movimientos y pedidos. El inventario muestra siempre la situación actual.

### Indicadores
- Productos en el catálogo.
- Unidades en stock.
- Valor del inventario (precio × stock).
- Productos bajo mínimo.

### Reportes disponibles
| Reporte | Contenido |
|---|---|
| Inventario | Todos los productos con precio, stock, estado y valorización |
| Movimientos de stock | Entradas y salidas del período, con totales |
| Pedidos a proveedores | Pedidos del período con estado y proveedor |

Cada reporte tiene dos botones:

- **Exportar a PDF**: descarga un archivo listo para imprimir.
- **Exportar a Excel**: descarga un archivo `.xlsx` con los datos.

Los archivos se nombran con el rango de fechas elegido, por ejemplo `reporte-inventario-20261101-20261130.pdf`.

---

## 11. Usuarios

Ruta: **Usuarios** (solo administrador).

Permite crear, editar y eliminar usuarios, y asignarles uno de los tres roles. El correo es único y la contraseña se guarda encriptada.

---

## 12. Flujos típicos

### Reponer un producto agotado
1. En **Stock**, detectar el producto con alerta roja.
2. Presionar **Pedir al proveedor** (o crear el pedido desde **Pedidos**).
3. Cuando llega la mercadería, en **Pedidos** presionar **Recibir pedido**.
4. Verificar en **Stock** que la alerta desapareció.

### Alta de un repuesto nuevo
1. Ir a **Productos** → **Nuevo producto**.
2. Cargar código, descripción, marca, modelo, precio, stock inicial y stock mínimo.
3. Guardar y verificar que aparece en **Stock**.

### Cierre de mes
1. Ir a **Reportes** y elegir el rango del mes.
2. Exportar el **inventario** y los **movimientos** en Excel para contabilidad.
3. Exportar el reporte de **pedidos** en PDF para archivo.

---

## 13. Problemas frecuentes

| Problema | Solución |
|---|---|
| «These credentials do not match our records» | Verificar correo y contraseña. Usar el ojo para revisar lo escrito. |
| Error 419 al enviar un formulario | La sesión expiró: recargar la página e iniciar sesión otra vez. |
| No aparece el botón Eliminar | Solo el rol administrador puede eliminar productos. |
| El PDF no descarga | Verificar que la carpeta `storage` tenga permisos de escritura. |
| Composer falla con `ext-gd` | Habilitar la extensión `gd` en `php.ini` y reinstalar dependencias. |
| El stock no cambia | Revisar el historial de movimientos: puede haberse registrado una salida. |
| Los estilos se ven sin diseño | Ejecutar `npm run build` o `npm run dev`. |

---

## 14. Glosario

- **Stock**: unidades disponibles de un producto.
- **Stock mínimo**: cantidad a partir de la cual se considera que hay que reponer.
- **Movimiento**: entrada o salida de unidades del depósito.
- **Pedido**: solicitud de reposición a un proveedor.
- **Valorización**: precio unitario multiplicado por el stock.
- **PDF / Excel**: formatos de exportación de los reportes.

---

*ITAL FIAT · Sistema de gestión de repuestos · Formosa, Argentina*
