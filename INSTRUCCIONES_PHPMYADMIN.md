# 📋 INSTRUCCIONES PARA EJECUTAR EN PHPMYADMIN

## 🚀 **PASO 1: Acceder a phpMyAdmin**
1. Abre tu navegador
2. Ve a: `http://localhost/phpmyadmin`
3. Inicia sesión con:
   - **Usuario:** `root`
   - **Contraseña:** (deja en blanco si no tienes)

## 🗄️ **PASO 2: Crear la Base de Datos**
1. En el panel izquierdo, haz clic en **"Nueva"**
2. En **"Nombre de la base de datos"** escribe: `mesa_ayuda2`
3. Selecciona **"utf8mb4_unicode_ci"** en la colación
4. Haz clic en **"Crear"**

## 📝 **PASO 3: Ejecutar el Script SQL**
1. Selecciona la base de datos `mesa_ayuda2` en el panel izquierdo
2. Haz clic en la pestaña **"SQL"**
3. Copia TODO el contenido del archivo `mesa_ayuda2.sql`
4. Pega el contenido en el área de texto
5. Haz clic en **"Continuar"**

## ✅ **PASO 4: Verificar la Creación**
Después de ejecutar el script, deberías ver:

### **Tablas Creadas:**
- ✅ `usuarios` - Usuarios del sistema
- ✅ `tickets` - Tickets de soporte
- ✅ `respuestas` - Respuestas a tickets
- ✅ `categorias` - Categorías de tickets
- ✅ `prioridades` - Niveles de prioridad
- ✅ `estados` - Estados de tickets
- ✅ `logs_tickets` - Historial de cambios
- ✅ `vista_tickets_completa` - Vista de tickets
- ✅ `vista_estadisticas_usuarios` - Estadísticas por usuario
- ✅ `vista_estadisticas_generales` - Estadísticas generales

### **Usuarios de Prueba Creados:**
- 🔵 **Cliente:** `juan@correo.com` / `123`
- 🟡 **Técnico:** `ana@correo.com` / `123`
- 🟡 **Técnico:** `carlos@correo.com` / `123`
- 🔵 **Cliente:** `maria@correo.com` / `123`
- 🔴 **Admin:** `admin@correo.com` / `123`
- 🔵 **Cliente:** `pedro@correo.com` / `123`
- 🟡 **Técnico:** `laura@correo.com` / `123`

### **Datos de Ejemplo:**
- ✅ 5 tickets de ejemplo
- ✅ 6 respuestas de ejemplo
- ✅ 5 logs de ejemplo
- ✅ 8 categorías predefinidas
- ✅ 4 niveles de prioridad
- ✅ 5 estados de tickets

## 🔧 **PASO 5: Probar la Conexión**
1. Ve a tu aplicación: `http://localhost/MesaAyuda_Final/`
2. Intenta iniciar sesión con:
   - **Email:** `juan@correo.com`
   - **Contraseña:** `123`

## 🚨 **SOLUCIÓN DE PROBLEMAS**

### **Error: "Base de datos no existe"**
- Verifica que hayas creado la base de datos `mesa_ayuda2`
- Asegúrate de que el nombre esté exactamente igual

### **Error: "Acceso denegado"**
- Verifica que estés usando `root` como usuario
- Si tienes contraseña, actualiza `conexion.php`

### **Error: "Tabla no existe"**
- Verifica que hayas ejecutado TODO el script SQL
- Asegúrate de que no haya errores en la ejecución

### **Error: "Credenciales incorrectas"**
- Verifica que los usuarios se hayan creado correctamente
- Ejecuta: `SELECT * FROM usuarios;` en SQL

## 📊 **VERIFICACIÓN FINAL**
Ejecuta esta consulta para verificar todo:

```sql
-- Verificar usuarios
SELECT nombre, correo, rol FROM usuarios;

-- Verificar tickets
SELECT COUNT(*) as total_tickets FROM tickets;

-- Verificar respuestas
SELECT COUNT(*) as total_respuestas FROM respuestas;

-- Verificar estadísticas
SELECT * FROM vista_estadisticas_generales;
```

## 🎯 **RESULTADO ESPERADO**
- **Total usuarios:** 7
- **Total tickets:** 5
- **Total respuestas:** 6
- **Base de datos:** Funcionando correctamente

---

## 🚀 **¡LISTO PARA USAR!**
Una vez completados todos los pasos, tu sistema de mesa de ayuda estará funcionando con la nueva base de datos `mesa_ayuda2`.

**¡Disfruta tu nueva mesa de ayuda mejorada! 🎉**
