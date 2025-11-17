# Sistema de Laboratorio Clínico

Sistema completo de gestión para laboratorio clínico desarrollado en PHP, MariaDB, HTML5, CSS3 y JavaScript.

## 📋 Características Principales

- ✅ **Gestión Multi-sucursal** con folios independientes
- ✅ **Gestión de Pacientes** con expediente único
- ✅ **Órdenes de Servicio** con múltiples estudios
- ✅ **Captura de Resultados** por área con interfaz especial para microbiología
- ✅ **Sistema de Pagos** con soporte para pagos parciales y múltiples formas de pago
- ✅ **Catálogos Configurables** (estudios, precios, parámetros, valores de referencia)
- ✅ **Sistema de Roles y Permisos** granular y configurable
- ✅ **Auditoría** completa de acciones
- ✅ **Generación de Documentos** (etiquetas, órdenes, recibos, resultados)

## 🔧 Requisitos del Sistema

- **PHP** 8.0 o superior
- **MariaDB** 10.6 o superior (o MySQL 8.0+)
- **Apache** 2.4+ con mod_rewrite habilitado
- **Extensiones PHP requeridas:**
  - PDO
  - PDO_MySQL
  - mbstring
  - json
  - openssl
  - session
  - GD (para generación de códigos de barras)

## 📦 Instalación

### Paso 1: Clonar o descargar el proyecto

```bash
# Descargar el proyecto al directorio del servidor web
cd /var/www/html  # o la ruta de tu servidor
# Colocar los archivos del proyecto aquí
```

### Paso 2: Configurar la Base de Datos

```bash
# 1. Crear la base de datos
mysql -u root -p
```

```sql
-- En el prompt de MySQL:
CREATE DATABASE laboratorio_clinico CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

```bash
# 2. Importar el esquema
mysql -u root -p laboratorio_clinico < database/schema.sql
```

### Paso 3: Configurar el archivo .env

```bash
# Copiar el archivo de ejemplo
cp .env.example .env

# Editar el archivo .env con tus configuraciones
nano .env
```

Ajustar los valores:
```
DB_HOST=localhost
DB_PORT=3306
DB_NAME=laboratorio_clinico
DB_USER=tu_usuario
DB_PASS=tu_contraseña

BASE_URL=http://localhost/laboratorio-clinico/public
```

### Paso 4: Configurar permisos

```bash
# Dar permisos de escritura a los directorios necesarios
chmod -R 755 storage/
chmod -R 755 public/uploads/
chmod -R 755 storage/logs/
chmod -R 755 storage/temp/
```

### Paso 5: Configurar Apache

Asegurarse de que el archivo `.htaccess` esté en el directorio `public/` y que `mod_rewrite` esté habilitado:

```bash
# Habilitar mod_rewrite en Apache
sudo a2enmod rewrite

# Reiniciar Apache
sudo systemctl restart apache2
```

### Paso 6: Acceder al sistema

Abrir en el navegador:
```
http://localhost/laboratorio-clinico/public
```

**Credenciales iniciales:**
- Usuario: `admin`
- Contraseña: `admin123`

⚠️ **IMPORTANTE:** Cambiar la contraseña inmediatamente después del primer inicio de sesión.

## 📁 Estructura del Proyecto

```
laboratorio-clinico/
│
├── config/                 # Archivos de configuración
│   ├── app.php
│   ├── database.php
│   └── constants.php
│
├── core/                   # Clases core del sistema
│   ├── Auth.php           # Autenticación
│   ├── Controller.php     # Controlador base
│   ├── Database.php       # Conexión a BD
│   └── Model.php          # Modelo base
│
├── controllers/            # Controladores de la aplicación
│   ├── AuthController.php
│   ├── PacienteController.php
│   ├── OrdenController.php
│   ├── ResultadoController.php
│   └── ...
│
├── models/                 # Modelos de datos
│   ├── Paciente.php
│   ├── Orden.php
│   ├── Estudio.php
│   └── ...
│
├── views/                  # Vistas HTML/PHP
│   ├── layouts/           # Layouts principales
│   ├── auth/              # Vistas de autenticación
│   ├── pacientes/         # Vistas de pacientes
│   ├── ordenes/           # Vistas de órdenes
│   └── resultados/        # Vistas de resultados
│
├── public/                 # Directorio público (DocumentRoot)
│   ├── index.php          # Punto de entrada
│   ├── .htaccess
│   └── assets/            # Recursos estáticos
│       ├── css/
│       ├── js/
│       └── img/
│
├── helpers/                # Funciones helper
│   └── functions.php
│
├── storage/                # Archivos de almacenamiento
│   ├── logs/              # Logs del sistema
│   └── temp/              # Archivos temporales
│
└── database/               # Scripts de base de datos
    └── schema.sql         # Esquema completo
```

## 🔐 Sistema de Roles y Permisos

El sistema incluye los siguientes roles predefinidos:

### 1. Superusuario
- Control total del sistema
- Todos los permisos disponibles

### 2. Administrador
- Gestión operativa y configuración
- No puede modificar superusuarios

### 3. Químico Supervisor
- Registra órdenes
- Captura y valida resultados
- Libera resultados (validación médica)
- Puede revertir validaciones

### 4. Químico Estándar
- Captura resultados
- Valida técnicamente
- No puede liberar ni revertir validaciones
- Solo ve su área asignada

### 5. Recepcionista
- Registra pacientes y órdenes
- Registra pagos
- Imprime documentos
- No accede a captura de resultados

## 📊 Módulos del Sistema

### Gestión de Pacientes
- Registro con expediente único de 8 dígitos
- Búsqueda inteligente
- Historial de órdenes
- Detección de duplicados

### Gestión de Órdenes
- Folio único: `YYYYMMDDSSNNNN`
- Multi-estudio por orden
- Descuentos y cargos configurables
- Impresión de etiquetas, orden de trabajo y recibo

### Captura de Resultados
- **Interfaz Estándar:** Para química clínica, hematología, etc.
- **Interfaz Microbiología:** Cultivos y antibiogramas
- Validación por niveles (técnica y médica)
- Valores de referencia dinámicos por edad/sexo

### Sistema de Pagos
- Pagos parciales
- Múltiples formas de pago en una transacción
- Historial completo
- Recibos dinámicos

### Catálogos Configurables
- Estudios con parámetros
- Valores de referencia
- Listas de precios múltiples
- Sucursales y áreas
- Compañías y convenios

## 🔧 Configuración Avanzada

### Agregar una nueva sucursal

```sql
INSERT INTO sucursales (codigo, nombre, nombre_corto, activo) 
VALUES ('02', 'Sucursal Norte', 'SUC-N', 1);
```

### Crear un nuevo usuario

```php
// El password se hashea automáticamente
$password_hash = password_hash('contraseña', PASSWORD_DEFAULT);

INSERT INTO usuarios (username, password_hash, nombres, apellido_paterno, 
                     email, sucursal_id, activo) 
VALUES ('usuario1', '$password_hash', 'Juan', 'Pérez', 
        'juan@lab.com', 1, 1);
```

### Asignar rol a usuario

```sql
-- Asignar rol de Recepcionista (ID 5) al usuario
INSERT INTO usuario_roles (usuario_id, rol_id) 
VALUES (2, 5);
```

## 📈 Próximas Características (Roadmap)

- [ ] Facturación electrónica (CFDI 4.0)
- [ ] Interfaz con equipos de laboratorio
- [ ] Control de calidad interno
- [ ] Gestión de inventarios y reactivos
- [ ] Portal web para pacientes
- [ ] App móvil
- [ ] Reportes avanzados y dashboards
- [ ] API REST
- [ ] Integración con WhatsApp para notificaciones

## 🐛 Solución de Problemas

### Error "No se pudo conectar a la base de datos"
- Verificar credenciales en `.env`
- Comprobar que MariaDB esté corriendo
- Verificar que la base de datos exista

### Error 404 en las rutas
- Verificar que `mod_rewrite` esté habilitado
- Comprobar que el archivo `.htaccess` esté en `public/`
- Verificar la configuración de Apache (AllowOverride All)

### Problemas de permisos
```bash
# En Linux/Mac
sudo chown -R www-data:www-data storage/
sudo chown -R www-data:www-data public/uploads/

# Dar permisos de escritura
sudo chmod -R 755 storage/
sudo chmod -R 755 public/uploads/
```

## 📝 Notas Importantes

- **Seguridad:** Cambiar SIEMPRE el password del admin después de la instalación
- **Respaldos:** Hacer respaldos regulares de la base de datos
- **Logs:** Revisar los logs en `storage/logs/` para detectar problemas
- **PHP:** Asegurarse de que `display_errors` esté en `Off` en producción
- **HTTPS:** Usar HTTPS en producción para proteger datos sensibles

## 🤝 Soporte

Para reportar problemas o solicitar características:
1. Revisar la documentación
2. Verificar los logs del sistema
3. Contactar al equipo de desarrollo

## 📄 Licencia

Este sistema es propietario y está protegido por derechos de autor.

---

**Versión:** 1.0.0  
**Última actualización:** Octubre 2025

