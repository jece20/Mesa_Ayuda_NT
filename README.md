# Sistema de Mesa de Ayuda

Un sistema completo de gestión de tickets de soporte técnico con roles diferenciados (cliente, técnico, administrador).

## Características

- **Sistema de Autenticación**: Login y registro de usuarios.
- **Roles Diferenciados**: Cliente, Técnico y Administrador con permisos específicos.
- **Gestión de Tickets**: Crear, ver y responder tickets.
- **Asignación de Tickets (Nuevo)**: Los administradores pueden asignar tickets específicos a los técnicos.
- **Flujo de Trabajo por Rol (Mejorado)**: Los técnicos solo ven los tickets que les han sido asignados, asegurando un flujo de trabajo ordenado.
- **Registro con Roles (Nuevo)**: El formulario de registro ahora permite seleccionar un rol (Cliente, Técnico, Administrador).
- **Dashboard Personalizado**: Cada rol tiene su propio panel con estadísticas y acciones relevantes.
- **Sistema de Respuestas**: Conversaciones en tiempo real dentro de cada ticket.
- **Logs de Auditoría Mejorados**: El sistema ahora guarda el **nombre** del técnico cuando se le asigna un ticket, para un mejor seguimiento.
- **Filtros Avanzados**: Búsqueda por estado, prioridad y categoría.
- **Gestión de Usuarios**: Administración completa de usuarios para el rol de admin.
- **Diseño Moderno y Responsivo**: Interfaz profesional con un fondo degradado y adaptable a dispositivos móviles.

## Requisitos del Sistema

- PHP 7.4 o superior
- MySQL 5.7 o superior
- Servidor web (Apache/Nginx)
- XAMPP, WAMP o similar

## Instalación

### 1. Configuración de la Base de Datos

1.  Importa el archivo `mesa_ayuda2.sql` en tu servidor MySQL. Este archivo contiene la estructura de tablas, vistas, **procedimientos almacenados actualizados** y datos de ejemplo.
2.  **Importante**: Si ya tenías la base de datos, ejecuta el script para actualizar el procedimiento `AsignarTecnico` que se te proporcionó para habilitar el log con el nombre del técnico.

### 2. Configuración de la Conexión

1.  Abre `conexion.php`.
2.  Modifica las credenciales de la base de datos:
    ```php
    $host = 'localhost';
    $dbname = 'mesa_ayuda2';
    $username = 'root';
    $password = '';
    ```

### 3. Configuración del Servidor Web

1.  Coloca todos los archivos en tu directorio web (htdocs en XAMPP).
2.  Abre la aplicación en tu navegador.

## Funcionalidades por Rol

### Cliente
- Crear tickets de soporte.
- Ver el estado y las respuestas de sus propios tickets.
- Dashboard con estadísticas personales.

### Técnico
- Dashboard con métricas de trabajo sobre **sus tickets asignados**.
- Ver y gestionar **únicamente** los tickets que le son asignados por un administrador.
- Responder y cambiar el estado de sus tickets.

### Administrador
- **Visión Global**: Ver todos los tickets del sistema.
- **Asignación de Tickets**: Asignar cualquier ticket a un técnico específico desde la vista de detalle del ticket.
- **Gestión Completa de Usuarios**: Crear, editar y eliminar usuarios y sus roles.
- Dashboard con estadísticas globales del sistema.

## Usuarios de Prueba

El sistema incluye usuarios de prueba predefinidos en `mesa_ayuda2.sql`:

-   🔵 **Cliente**: juan@correo.com / 123
-   🟡 **Técnico**: ana@correo.com / 123
-   🔴 **Admin**: admin@correo.com / 123
-   *(Y más usuarios de ejemplo...)*

## Personalización

### Colores y Estilos
Los colores principales y el fondo de la aplicación se pueden modificar en la parte superior del archivo `estilos.css`.

```css
/* Ejemplo de variables de color en :root */
:root {
    --primary-blue: #2563eb;
    --secondary-blue: #1d4ed8;
    /* ... más colores */
}

/* Estilo del fondo principal */
body {
    background: linear-gradient(135deg, #c2e9fb 0%, #a1c4fd 100%);
    /* ... */
}
```

---

**Desarrollado para facilitar la gestión de soporte técnico**
