# Sistema de Mesa de Ayuda

Un sistema completo de gestión de tickets de soporte técnico con roles diferenciados (cliente, técnico, administrador).

## Características

- **Sistema de Autenticación**: Login y registro de usuarios
- **Roles Diferenciados**: Cliente, Técnico y Administrador
- **Gestión de Tickets**: Crear, ver y responder tickets
- **Dashboard Personalizado**: Cada rol tiene su propio panel
- **Sistema de Respuestas**: Conversaciones en tiempo real
- **Filtros Avanzados**: Búsqueda por estado, prioridad y categoría
- **Gestión de Usuarios**: Administración completa (solo admin)
- **Diseño Responsivo**: Interfaz moderna y profesional

## Requisitos del Sistema

- PHP 7.4 o superior
- MySQL 5.7 o superior
- Servidor web (Apache/Nginx)
- XAMPP, WAMP o similar

## Instalación

### 1. Configuración de la Base de Datos

**Opción A: Base de Datos Mejorada (Recomendada)**
1. Importa el archivo `mesa_ayuda2.sql` en tu servidor MySQL
2. Este archivo crea una base de datos completa con tablas, vistas, procedimientos y datos de ejemplo

**Opción B: Base de Datos Básica**
1. Importa el archivo `mesa_ayuda.sql` en tu servidor MySQL
2. Base de datos simple con funcionalidades básicas

### 2. Configuración de la Conexión

1. Abre `conexion.php`
2. Modifica las credenciales de la base de datos según tu elección:

   **Para mesa_ayuda2 (Recomendado):**
   ```php
   $host = 'localhost';
   $dbname = 'mesa_ayuda2';
   $username = 'root';
   $password = '';
   ```

   **Para mesa_ayuda (Básico):**
   ```php
   $host = 'localhost';
   $dbname = 'mesa_ayuda';
   $username = 'root';
   $password = '';
   ```

### 3. Configuración del Servidor Web

1. Coloca todos los archivos en tu directorio web (htdocs en XAMPP)
2. Asegúrate de que PHP tenga permisos de escritura

## Estructura de Archivos

```
MesaAyuda_Final/
├── conexion.php              # Conexión a la base de datos
├── estilos.css               # Estilos CSS personalizados
├── index.php                 # Página principal (redirección)
├── login.php                 # Formulario de login
├── registro.php              # Formulario de registro
├── dashboard.php             # Dashboard principal
├── crear_ticket.php          # Crear nuevos tickets
├── ver_ticket.php            # Ver y responder tickets
├── gestion_usuarios.php      # Gestión de usuarios (admin)
├── todos_tickets.php         # Lista de todos los tickets
├── logout.php                # Cerrar sesión
├── mesa_ayuda.sql            # Base de datos básica
├── mesa_ayuda2.sql           # Base de datos mejorada (RECOMENDADA)
├── test_conexion.php         # Archivo de prueba de conexión
├── INSTRUCCIONES_PHPMYADMIN.md # Guía de instalación
└── README.md                 # Este archivo
```

## Usuarios de Prueba

El sistema incluye usuarios de prueba predefinidos:

### **Base de Datos mesa_ayuda2 (Recomendada):**
- 🔵 **Cliente**: juan@correo.com / 123
- 🟡 **Técnico**: ana@correo.com / 123
- 🟡 **Técnico**: carlos@correo.com / 123
- 🔵 **Cliente**: maria@correo.com / 123
- 🔴 **Admin**: admin@correo.com / 123
- 🔵 **Cliente**: pedro@correo.com / 123
- 🟡 **Técnico**: laura@correo.com / 123

### **Base de Datos mesa_ayuda (Básica):**
- **Cliente**: juan@correo.com / 123
- **Técnico**: ana@correo.com / 123  
- **Administrador**: admin@correo.com / 123

## Funcionalidades por Rol

### Cliente
- Crear tickets de soporte
- Ver sus propios tickets
- Responder a tickets existentes
- Dashboard con estadísticas personales

### Técnico
- Ver tickets pendientes
- Responder y resolver tickets
- Cambiar estado de tickets
- Dashboard con métricas de trabajo

### Administrador
- Gestión completa de usuarios
- Ver todos los tickets del sistema
- Cambiar roles de usuarios
- Estadísticas del sistema completo

## Características Técnicas

- **Seguridad**: Validación de sesiones y roles
- **Base de Datos**: Consultas preparadas (prepared statements)
- **Frontend**: Bootstrap 5 + CSS personalizado
- **Responsive**: Diseño adaptable a móviles
- **Validación**: Validación del lado del servidor
- **Manejo de Errores**: Mensajes informativos para el usuario

## Personalización

### Colores
Los colores principales se pueden modificar en `estilos.css`:
```css
:root {
    --primary-blue: #2563eb;
    --secondary-blue: #1d4ed8;
    --accent-blue: #3b82f6;
    /* ... más colores */
}
```

### Categorías de Tickets
Modifica las opciones en `crear_ticket.php`:
```php
<option value="General">General</option>
<option value="Software">Software</option>
<option value="Hardware">Hardware</option>
<!-- Agregar más categorías aquí -->
```

## Solución de Problemas

### Error de Conexión
- Verifica que MySQL esté ejecutándose
- Confirma las credenciales en `conexion.php`
- Asegúrate de que la base de datos `mesa_ayuda` exista

### Página en Blanco
- Verifica los logs de error de PHP
- Confirma que todas las dependencias estén instaladas
- Verifica permisos de archivos

### Problemas de Sesión
- Verifica que las cookies estén habilitadas
- Confirma que `session_start()` esté en todos los archivos necesarios

## Mejoras Futuras

- Sistema de notificaciones por email
- Adjuntar archivos a tickets
- Reportes y estadísticas avanzadas
- API REST para integración externa
- Sistema de calificaciones
- Base de conocimientos (KB)

## Soporte

Para soporte técnico o preguntas sobre el sistema, contacta al administrador del sistema.

## Licencia

Este proyecto es de uso libre para fines educativos y comerciales.

---

**Desarrollado con ❤️ para facilitar la gestión de soporte técnico**
