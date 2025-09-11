# Sistema de Mesa de Ayuda

Un sistema completo para la gestión de tickets de soporte técnico, diseñado con roles de usuario específicos (cliente, técnico, administrador) para un flujo de trabajo eficiente y organizado.

## Características Principales

- **Sistema de Autenticación**: Login seguro y registro de clientes.
- **Roles y Permisos Claros**: Tres roles con paneles y capacidades distintas.
- **Gestión Completa de Tickets**: Creación, visualización, filtrado y seguimiento de tickets.
- **Asignación de Tareas**: Los clientes y administradores pueden asignar tickets a técnicos específicos.
- **Actualizaciones en Tiempo Real**: Los clientes ven los cambios de estado de sus tickets al instante, sin necesidad de recargar la página.
- **Paneles Personalizados (Dashboards)**: Cada rol tiene una vista principal con estadísticas y acciones rápidas relevantes para sus tareas.
- **Gestión de Usuarios Centralizada**: Los administradores tienen control total sobre la creación y gestión de cuentas de usuario.
- **Interfaz Moderna**: Diseño limpio, amigable y adaptable a dispositivos móviles.

---

## Funcionalidades por Rol

### 👤 Cliente
- **Registro**: Puede crear su propia cuenta desde la página de registro.
- **Crear Tickets**: Puede abrir un nuevo ticket de soporte, describiendo su problema y estableciendo una prioridad.
- **Asignación Opcional**: Al crear un ticket, puede asignarlo directamente a un técnico disponible o dejarlo "Sin Asignar" para que un administrador lo gestione.
- **Ver Mis Tickets**: Tiene una página dedicada (`mis_tickets.php`) para ver el historial completo de todos los tickets que ha creado, con opciones de filtrado.
- **Actualizaciones en Vivo**: Al ver un ticket, el estado se actualiza automáticamente si un técnico realiza un cambio.

### 🛠️ Técnico
- **Panel de Tareas**: Su dashboard principal le muestra un resumen de sus tickets activos.
- **Tickets Pendientes**: Cuenta con una página (`tickets_pendientes.php`) que funciona como su lista de tareas, mostrando únicamente los tickets que tiene asignados y que están en estado "Pendiente" o "En proceso".
- **Gestionar Tickets**: Puede ver los detalles de sus tickets asignados, responder al cliente y cambiar el estado del ticket (ej. de "En proceso" a "Resuelto").

### 👑 Administrador
- **Visión Global**: Tiene acceso total para ver y gestionar todos los tickets del sistema, sin importar quién los creó o quién está asignado.
- **Creación de Usuarios**: Desde el panel de "Gestión de Usuarios", puede crear nuevas cuentas de usuario, especialmente para **Técnicos** y otros administradores, asignándoles un rol directamente.
- **Asignación de Tickets**: Puede asignar cualquier ticket que esté "Sin Asignar" a un técnico disponible. Esta función es ideal para distribuir la carga de trabajo.
- **Gestión de Roles**: Puede cambiar el rol de cualquier usuario en cualquier momento.
- **Dashboard Global**: Ve estadísticas generales de todo el sistema, como el número total de tickets, usuarios y tickets pendientes.

---

## Detalles Técnicos

- **Actualizaciones en Tiempo Real**: La página `ver_ticket.php` utiliza JavaScript (AJAX) para consultar periódicamente el estado del ticket a través del endpoint `get_estado_ticket.php`. Esto permite una experiencia de usuario fluida y moderna.

## Archivos Creados en esta Versión

- `mis_tickets.php`: Página para que los clientes vean su historial de tickets.
- `tickets_pendientes.php`: Página para que los técnicos vean su carga de trabajo activa.
- `get_estado_ticket.php`: Endpoint interno para la funcionalidad de actualización en tiempo real.

## Instalación

1.  **Base de Datos**: Importa el archivo `mesa_ayuda2.sql` en tu servidor MySQL.
2.  **Conexión**: Abre `conexion.php` y modifica las credenciales de acceso a tu base de datos.
3.  **Servidor**: Coloca todos los archivos en el directorio de tu servidor web (ej. `htdocs` en XAMPP).
4.  **¡Listo!**: Abre la aplicación en tu navegador.

## Usuarios de Prueba

-   🔵 **Cliente**: `juan@correo.com` / `123`
-   🟡 **Técnico**: `ana@correo.com` / `123`
-   🔴 **Admin**: `admin@correo.com` / `123`

---

**Desarrollado para facilitar la gestión de soporte técnico.**
