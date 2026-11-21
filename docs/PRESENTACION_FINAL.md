# Presentación final — ITAL FIAT

Guion de la presentación del TFI. Cada diapositiva incluye el contenido sugerido y las notas para exponer.

**Duración estimada:** 12 a 15 minutos + demo.

---

## Diapositiva 1 — Portada

**Contenido**
- Título: *Sistema de gestión de repuestos ITAL FIAT*
- Subtítulo: Trabajo Final Integrador
- Integrantes: Sergio Soria, Lía Barcos, Ramiro Corrales
- Materia, carrera, año

**Notas**
> Somos el grupo 8 y desarrollamos un sistema web para administrar el catálogo, el stock y las compras de repuestos de ITAL FIAT, en Formosa.

---

## Diapositiva 2 — Problema

**Contenido**
- El control de repuestos se hace en planillas y de forma manual.
- No hay alertas cuando un repuesto se agota.
- Los pedidos a proveedores se pierden o se duplican.
- No existen reportes confiables para tomar decisiones.

**Notas**
> El problema central es la falta de trazabilidad: no se sabe con certeza qué hay, qué falta y qué se pidió.

---

## Diapositiva 3 — Solución

**Contenido**
- Sistema web con roles y permisos.
- Catálogo único de repuestos con stock y stock mínimo.
- Alertas visuales en rojo cuando hay que reponer.
- Pedidos a proveedores con recepción que actualiza el stock.
- Reportes en PDF y Excel.

**Notas**
> La solución cubre el ciclo completo: alta del repuesto, control de stock, reposición y reporte.

---

## Diapositiva 4 — Alcance funcional (HUs)

**Contenido**

| HU | Funcionalidad | Estado |
|---|---|---|
| HU1 | Login y roles | ✅ |
| HU2 | Gestión de usuarios | ✅ |
| HU3 | ABM de productos | ✅ |
| HU4 | Control de stock | ✅ |
| HU5 | Pedidos a proveedores | ✅ |
| HU6 | Reportes | ✅ |
| HU7 | Dashboard dinámico | 🔄 |
| HU8 | Cierre y despliegue | 🔄 |

**Notas**
> Ocho historias de usuario; seis cerradas y dos en curso.

---

## Diapositiva 5 — Stack tecnológico

**Contenido**
- **Backend:** Laravel 13 (PHP 8.3)
- **Base de datos:** MySQL
- **Frontend:** Blade + Tailwind CSS
- **Autenticación:** Laravel Breeze
- **PDF:** barryvdh/laravel-dompdf
- **Excel:** maatwebsite/excel
- **Testing:** PHPUnit
- **Control de versiones:** Git + GitHub
- **Gestión:** Jira (Scrum)

**Notas**
> Elegimos Laravel por su ecosistema, su sistema de migraciones y la seguridad que trae de fábrica.

---

## Diapositiva 6 — Arquitectura

**Contenido**
- Patrón MVC.
- Rutas protegidas por middleware `auth` y `role`.
- Controladores por dominio: Productos, Stock, Pedidos, Reportes, Usuarios.
- Vistas Blade reutilizables (`layouts/app`).
- Migraciones y seeders para reproducir la base.

**Diagrama sugerido**
```
Navegador → Rutas → Middleware de rol → Controlador → Modelo → MySQL
                                              ↓
                                          Vistas Blade
```

**Notas**
> El middleware de rol es la pieza clave: cada usuario ve solo lo que le corresponde.

---

## Diapositiva 7 — Roles y permisos

**Contenido**

| Función | Admin | Vendedor | Depósito |
|---|:---:|:---:|:---:|
| Productos (alta y edición) | ✅ | ✅ | ✅ |
| Eliminar productos | ✅ | ❌ | ❌ |
| Movimientos de stock | ✅ | ❌ | ✅ |
| Pedidos | ✅ | ❌ | ✅ |
| Reportes | ✅ | ❌ | ❌ |
| Usuarios | ✅ | ❌ | ❌ |

**Notas**
> El vendedor se enfoca en la atención al cliente; el depósito, en el stock; el administrador supervisa todo.

---

## Diapositiva 8 — Demo en vivo (1/3): acceso y productos

**Guion**
1. Abrir el login y mostrar el botón del ojo.
2. Ingresar como **administrador**.
3. Mostrar el dashboard con los indicadores.
4. Ir a **Productos**: buscar, crear un repuesto y editarlo.

**Notas**
> Destacar la validación de código duplicado y los mensajes en español.

---

## Diapositiva 9 — Demo en vivo (2/3): stock y alertas

**Guion**
1. Ir a **Stock**.
2. Mostrar la **alerta roja** y la fila resaltada del producto bajo mínimo.
3. Registrar una **salida** y mostrar cómo cambia el estado a rojo.
4. Presionar **Pedir al proveedor**.

**Notas**
> La alerta roja es el corazón de la HU4: se ve de inmediato qué hay que reponer.

---

## Diapositiva 10 — Demo en vivo (3/3): pedidos y reportes

**Guion**
1. Ir a **Pedidos**, mostrar el pedido pendiente.
2. Presionar **Recibir pedido** y verificar que el stock sube.
3. Ir a **Reportes**, elegir el rango de fechas.
4. Descargar el **PDF** de inventario y el **Excel** de movimientos.

**Notas**
> El circuito completo: pedido → recepción → stock actualizado → reporte.

---

## Diapositiva 11 — Calidad y pruebas

**Contenido**
- **122 tests automatizados** con PHPUnit (473 assertions).
- Cobertura de autenticación, permisos, productos, stock, pedidos y reportes.
- Pruebas de stress con alto volumen de datos.
- Revisión manual en navegador de cada pantalla.

**Notas**
> Los tests nos permitieron refactorizar sin miedo y detectar regresiones antes de mergear.

---

## Diapositiva 12 — Despliegue

**Contenido**
- Repositorio en GitHub con ramas `main` y `develop`.
- Pull Requests revisados por el equipo.
- Entornos reproducibles con migraciones y seeders.
- Próximo paso: Docker y Azure (HU8).

**Notas**
> Todo cambio entra por Pull Request con tests en verde.

---

## Diapositiva 13 — Dificultades y aprendizajes

**Contenido**
- Integración de Breeze con el login propio y los roles.
- Conflictos de merge entre ramas paralelas.
- Migración de la base y datos de prueba.
- Diferencia entre entorno local y despliegue.

**Notas**
> Aprendimos a trabajar con ramas cortas, PRs pequeños y tests que respaldan cada cambio.

---

## Diapositiva 14 — Roadmap

**Contenido**
- HU7: dashboard con pedidos y ventas reales.
- Módulo de ventas y clientes.
- Docker + Azure para el despliegue final.
- Documentación de la API y colección de Postman.

**Notas**
> Lo que sigue está planificado en el Sprint 5.

---

## Diapositiva 15 — Cierre

**Contenido**
- Sistema funcional que resuelve el ciclo completo del repuesto.
- Código testeado y documentado.
- Trabajo en equipo con Scrum y Jira.
- **¡Gracias!** Preguntas.

**Notas**
> Cerrar con una frase: «Cada producto, en su lugar».

---

## Anexo — Checklist de la demo

- [ ] Servidor levantado (`php artisan serve`).
- [ ] Base migrada y seedeada.
- [ ] Assets compilados (`npm run build`).
- [ ] Usuario administrador disponible.
- [ ] Un producto con stock bajo para mostrar la alerta.
- [ ] Un pedido pendiente para recibir.
- [ ] Carpeta `storage` con permisos de escritura para las exportaciones.
- [ ] Pestañas del navegador preparadas (login, stock, pedidos, reportes).
