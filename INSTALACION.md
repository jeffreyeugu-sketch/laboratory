# Guía de Instalación Rápida

## 🚀 Instalación en 5 Minutos

### 1. Requisitos Previos
✅ PHP 8.0+ instalado  
✅ MariaDB 10.6+ instalado  
✅ Apache con mod_rewrite habilitado  

### 2. Instalación

```bash
# 1. Crear la base de datos
mysql -u root -p
```

```sql
CREATE DATABASE laboratorio_clinico CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit;
```

```bash
# 2. Importar el esquema
mysql -u root -p laboratorio_clinico < database/schema.sql

# 3. Configurar credenciales
cp .env.example .env
nano .env  # Editar DB_USER y DB_PASS

# 4. Dar permisos (Linux/Mac)
chmod -R 755 storage/
chmod -R 755 public/uploads/

# 5. Configurar Apache
# Asegurarse de que el DocumentRoot apunte a la carpeta public/
# O usar un virtual host
```

### 3. Configuración de Apache (Opción 1 - VirtualHost)

Crear archivo: `/etc/apache2/sites-available/laboratorio.conf`

```apache
<VirtualHost *:80>
    ServerName laboratorio.local
    DocumentRoot /var/www/laboratorio-clinico/public

    <Directory /var/www/laboratorio-clinico/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/laboratorio_error.log
    CustomLog ${APACHE_LOG_DIR}/laboratorio_access.log combined
</VirtualHost>
```

```bash
# Habilitar el sitio
sudo a2ensite laboratorio.conf
sudo systemctl reload apache2

# Agregar a /etc/hosts
sudo nano /etc/hosts
# Agregar: 127.0.0.1  laboratorio.local
```

### 4. Configuración de Apache (Opción 2 - Subdirectorio)

Si instalaste en `/var/www/html/laboratorio-clinico`:

```bash
# El sistema debería funcionar automáticamente en:
# http://localhost/laboratorio-clinico/public
```

Asegurarse de que en `.env`:
```
BASE_URL=http://localhost/laboratorio-clinico/public
```

### 5. Acceder al Sistema

```
URL: http://laboratorio.local  (o http://localhost/laboratorio-clinico/public)
Usuario: admin
Password: admin123
```

⚠️ **CAMBIAR INMEDIATAMENTE EL PASSWORD**

## ✅ Verificación Post-Instalación

1. **Verificar conexión a BD:**
   - Login exitoso = BD conectada ✅

2. **Verificar permisos:**
   ```bash
   # Debe poder escribir en estos directorios
   ls -la storage/logs/
   ls -la public/uploads/
   ```

3. **Verificar mod_rewrite:**
   - Las URLs deben funcionar sin index.php
   - Si ves errores 404, verificar `.htaccess` y `mod_rewrite`

## 🔧 Solución Rápida de Problemas

### Error: "No se pudo conectar a la base de datos"
```bash
# Verificar credenciales en .env
cat .env

# Verificar que MariaDB esté corriendo
sudo systemctl status mariadb

# Verificar que la BD exista
mysql -u root -p -e "SHOW DATABASES LIKE 'laboratorio_clinico';"
```

### Error: Rutas no funcionan (404)
```bash
# Verificar mod_rewrite
sudo a2enmod rewrite
sudo systemctl restart apache2

# Verificar .htaccess
cat public/.htaccess
```

### Error: "Permission denied" al escribir logs
```bash
# Dar permisos correctos
sudo chown -R www-data:www-data storage/
sudo chmod -R 755 storage/
```

## 📋 Checklist de Instalación

- [ ] Base de datos creada
- [ ] Esquema importado
- [ ] Archivo .env configurado
- [ ] Permisos de escritura establecidos
- [ ] Apache configurado y reiniciado
- [ ] Login funciona correctamente
- [ ] Password de admin cambiado

## 🎯 Próximos Pasos

Después de la instalación:

1. **Cambiar password del admin**
2. **Configurar datos de la empresa**
   - Ir a Catálogos > Sucursales
   - Editar sucursal Matriz con datos reales

3. **Crear usuarios**
   - Ir a Usuarios > Crear Usuario
   - Asignar roles apropiados

4. **Configurar catálogos**
   - Áreas del laboratorio
   - Estudios y parámetros
   - Listas de precios

5. **Realizar prueba completa:**
   - Crear un paciente de prueba
   - Registrar una orden
   - Capturar resultados
   - Generar reportes

## 💡 Tips

- Hacer backup de la BD antes de cambios importantes
- Revisar logs regularmente en `storage/logs/`
- En producción, configurar HTTPS
- Cambiar `LOG_LEVEL=error` en producción
