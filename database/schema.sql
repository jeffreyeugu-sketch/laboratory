-- ============================================================
-- SISTEMA DE LABORATORIO CLÍNICO
-- Script de Creación de Base de Datos
-- MariaDB 10.6+
-- ============================================================

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS laboratorio_clinico
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE laboratorio_clinico;

-- ============================================================
-- MÓDULO: CONFIGURACIÓN Y CATÁLOGOS BASE
-- ============================================================

-- Tabla: sucursales
CREATE TABLE sucursales (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(10) NOT NULL UNIQUE COMMENT 'Código de 2 dígitos para folios',
    nombre VARCHAR(100) NOT NULL,
    nombre_corto VARCHAR(50),
    direccion VARCHAR(255),
    ciudad VARCHAR(100),
    estado VARCHAR(100),
    codigo_postal VARCHAR(10),
    telefono VARCHAR(20),
    email VARCHAR(100),
    responsable VARCHAR(150),
    activo BOOLEAN DEFAULT TRUE,
    es_matriz BOOLEAN DEFAULT FALSE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_activo (activo)
) ENGINE=InnoDB;

-- Tabla: areas (Áreas del laboratorio)
CREATE TABLE areas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(20) NOT NULL UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    color VARCHAR(7) COMMENT 'Color hex para UI',
    orden INT DEFAULT 0,
    activo BOOLEAN DEFAULT TRUE,
    INDEX idx_activo (activo)
) ENGINE=InnoDB;

-- Tabla: formas_pago
CREATE TABLE formas_pago (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(10) NOT NULL UNIQUE COMMENT 'Clave SAT para facturación',
    nombre VARCHAR(100) NOT NULL,
    requiere_referencia BOOLEAN DEFAULT FALSE,
    requiere_banco BOOLEAN DEFAULT FALSE,
    activo BOOLEAN DEFAULT TRUE,
    orden_display INT DEFAULT 0,
    INDEX idx_activo (activo)
) ENGINE=InnoDB;

-- Tabla: metodologias
CREATE TABLE metodologias (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    activo BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB;

-- Tabla: tipos_muestra
CREATE TABLE tipos_muestra (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    requiere_condiciones_especiales BOOLEAN DEFAULT FALSE,
    condiciones_especiales TEXT,
    activo BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB;

-- ============================================================
-- MÓDULO: USUARIOS Y PERMISOS
-- ============================================================

-- Tabla: usuarios
CREATE TABLE usuarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    nombres VARCHAR(100) NOT NULL,
    apellido_paterno VARCHAR(100) NOT NULL,
    apellido_materno VARCHAR(100),
    email VARCHAR(150) UNIQUE,
    telefono VARCHAR(20),
    cedula_profesional VARCHAR(50),
    firma_digital VARCHAR(255) COMMENT 'Ruta a imagen de firma',
    sucursal_id INT UNSIGNED NOT NULL,
    area_id INT UNSIGNED COMMENT 'Área asignada para químicos',
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_ultimo_acceso TIMESTAMP NULL,
    intentos_fallidos_login INT DEFAULT 0,
    bloqueado BOOLEAN DEFAULT FALSE,
    requiere_cambio_password BOOLEAN DEFAULT TRUE,
    fecha_expiracion_password DATE,
    notas TEXT,
    FOREIGN KEY (sucursal_id) REFERENCES sucursales(id),
    FOREIGN KEY (area_id) REFERENCES areas(id),
    INDEX idx_username (username),
    INDEX idx_activo (activo)
) ENGINE=InnoDB;

-- Tabla: roles
CREATE TABLE roles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT,
    es_sistema BOOLEAN DEFAULT FALSE COMMENT 'No se puede eliminar',
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabla: permisos
CREATE TABLE permisos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    modulo VARCHAR(50) NOT NULL,
    accion VARCHAR(50) NOT NULL,
    clave VARCHAR(100) NOT NULL UNIQUE COMMENT 'formato: modulo.accion',
    nombre_display VARCHAR(150) NOT NULL,
    descripcion TEXT,
    es_critico BOOLEAN DEFAULT FALSE,
    grupo VARCHAR(50) COMMENT 'Para agrupar en UI',
    INDEX idx_modulo (modulo)
) ENGINE=InnoDB;

-- Tabla: rol_permisos
CREATE TABLE rol_permisos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    rol_id INT UNSIGNED NOT NULL,
    permiso_id INT UNSIGNED NOT NULL,
    FOREIGN KEY (rol_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permiso_id) REFERENCES permisos(id) ON DELETE CASCADE,
    UNIQUE KEY uk_rol_permiso (rol_id, permiso_id)
) ENGINE=InnoDB;

-- Tabla: usuario_roles
CREATE TABLE usuario_roles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT UNSIGNED NOT NULL,
    rol_id INT UNSIGNED NOT NULL,
    fecha_asignacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    asignado_por INT UNSIGNED,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (rol_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (asignado_por) REFERENCES usuarios(id) ON DELETE SET NULL,
    UNIQUE KEY uk_usuario_rol (usuario_id, rol_id)
) ENGINE=InnoDB;

-- Tabla: usuario_permisos_extra
CREATE TABLE usuario_permisos_extra (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT UNSIGNED NOT NULL,
    permiso_id INT UNSIGNED NOT NULL,
    tipo ENUM('conceder', 'revocar') NOT NULL,
    fecha_asignacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    asignado_por INT UNSIGNED,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (permiso_id) REFERENCES permisos(id) ON DELETE CASCADE,
    FOREIGN KEY (asignado_por) REFERENCES usuarios(id) ON DELETE SET NULL,
    UNIQUE KEY uk_usuario_permiso (usuario_id, permiso_id)
) ENGINE=InnoDB;

-- Tabla: sesiones_activas
CREATE TABLE sesiones_activas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT UNSIGNED NOT NULL,
    token VARCHAR(255) NOT NULL UNIQUE,
    ip_address VARCHAR(45),
    user_agent TEXT,
    fecha_inicio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_ultimo_acceso TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    fecha_expiracion TIMESTAMP NOT NULL,
    activa BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_token (token),
    INDEX idx_usuario (usuario_id)
) ENGINE=InnoDB;

-- ============================================================
-- MÓDULO: PACIENTES
-- ============================================================

-- Tabla: pacientes
CREATE TABLE pacientes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    expediente VARCHAR(20) NOT NULL UNIQUE COMMENT 'Número de expediente único',
    nombres VARCHAR(100) NOT NULL,
    apellido_paterno VARCHAR(100) NOT NULL,
    apellido_materno VARCHAR(100),
    fecha_nacimiento DATE NOT NULL,
    sexo ENUM('M', 'F', 'O') NOT NULL COMMENT 'M=Masculino, F=Femenino, O=Otro',
    telefono VARCHAR(20),
    celular VARCHAR(20),
    email VARCHAR(150),
    direccion VARCHAR(255),
    codigo_postal VARCHAR(10),
    ciudad VARCHAR(100),
    estado VARCHAR(100),
    ocupacion VARCHAR(100),
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    sucursal_registro_id INT UNSIGNED NOT NULL COMMENT 'Sucursal donde se dio de alta',
    activo BOOLEAN DEFAULT TRUE,
    notas TEXT,
    FOREIGN KEY (sucursal_registro_id) REFERENCES sucursales(id),
    INDEX idx_expediente (expediente),
    INDEX idx_nombres (nombres, apellido_paterno),
    INDEX idx_fecha_nacimiento (fecha_nacimiento),
    INDEX idx_activo (activo)
) ENGINE=InnoDB;

-- ============================================================
-- MÓDULO: CATÁLOGOS DE ESTUDIOS Y PRECIOS
-- ============================================================

-- Tabla: listas_precios
CREATE TABLE listas_precios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    tipo ENUM('particular', 'empresa', 'convenio', 'medico') NOT NULL,
    activa BOOLEAN DEFAULT TRUE,
    fecha_vigencia_inicio DATE,
    fecha_vigencia_fin DATE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_activa (activa)
) ENGINE=InnoDB;

-- Tabla: companias
CREATE TABLE companias (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('empresa', 'convenio', 'aseguradora') NOT NULL,
    nombre VARCHAR(200) NOT NULL,
    nombre_comercial VARCHAR(200),
    rfc VARCHAR(20),
    direccion VARCHAR(255),
    ciudad VARCHAR(100),
    estado VARCHAR(100),
    telefono VARCHAR(20),
    email VARCHAR(150),
    contacto_nombre VARCHAR(150),
    contacto_telefono VARCHAR(20),
    lista_precio_id INT UNSIGNED,
    descuento_porcentaje DECIMAL(5,2) DEFAULT 0.00,
    dias_credito INT DEFAULT 0,
    limite_credito DECIMAL(10,2),
    requiere_autorizacion BOOLEAN DEFAULT FALSE,
    formato_resultado_especial BOOLEAN DEFAULT FALSE,
    activo BOOLEAN DEFAULT TRUE,
    notas TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (lista_precio_id) REFERENCES listas_precios(id),
    INDEX idx_nombre (nombre),
    INDEX idx_activo (activo)
) ENGINE=InnoDB;

-- Tabla: parametro_tipos
CREATE TABLE parametro_tipos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(50) NOT NULL UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT
) ENGINE=InnoDB;

-- Tabla: estudios
CREATE TABLE estudios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo_interno VARCHAR(50) NOT NULL UNIQUE,
    codigo_loinc VARCHAR(50) COMMENT 'Código LOINC estándar',
    nombre VARCHAR(200) NOT NULL,
    nombre_corto VARCHAR(100),
    area_id INT UNSIGNED NOT NULL,
    activo BOOLEAN DEFAULT TRUE,
    tipo_muestra_id INT UNSIGNED,
    volumen_requerido VARCHAR(50),
    metodologia_id INT UNSIGNED,
    dias_proceso INT DEFAULT 1,
    indicaciones_paciente TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (area_id) REFERENCES areas(id),
    FOREIGN KEY (tipo_muestra_id) REFERENCES tipos_muestra(id),
    FOREIGN KEY (metodologia_id) REFERENCES metodologias(id),
    INDEX idx_codigo_interno (codigo_interno),
    INDEX idx_codigo_loinc (codigo_loinc),
    INDEX idx_nombre (nombre),
    INDEX idx_area (area_id),
    INDEX idx_activo (activo)
) ENGINE=InnoDB;

-- Tabla: estudio_sinonimos
CREATE TABLE estudio_sinonimos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    estudio_id INT UNSIGNED NOT NULL,
    sinonimo VARCHAR(200) NOT NULL,
    FOREIGN KEY (estudio_id) REFERENCES estudios(id) ON DELETE CASCADE,
    INDEX idx_sinonimo (sinonimo)
) ENGINE=InnoDB;

-- Tabla: estudio_parametros
CREATE TABLE estudio_parametros (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    estudio_id INT UNSIGNED NOT NULL,
    codigo VARCHAR(50) NOT NULL,
    nombre VARCHAR(200) NOT NULL,
    nombre_corto VARCHAR(100),
    tipo_parametro_id INT UNSIGNED NOT NULL,
    unidad_medida VARCHAR(50),
    orden INT DEFAULT 0,
    formula TEXT COMMENT 'Fórmula para parámetros calculados',
    decimales INT DEFAULT 2,
    obligatorio BOOLEAN DEFAULT TRUE,
    mostrar_en_reporte BOOLEAN DEFAULT TRUE,
    activo BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (estudio_id) REFERENCES estudios(id) ON DELETE CASCADE,
    FOREIGN KEY (tipo_parametro_id) REFERENCES parametro_tipos(id),
    INDEX idx_estudio (estudio_id),
    INDEX idx_orden (orden)
) ENGINE=InnoDB;

-- Tabla: parametro_opciones
CREATE TABLE parametro_opciones (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    parametro_id INT UNSIGNED NOT NULL,
    valor VARCHAR(100) NOT NULL,
    descripcion VARCHAR(255),
    orden INT DEFAULT 0,
    activo BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (parametro_id) REFERENCES estudio_parametros(id) ON DELETE CASCADE,
    INDEX idx_parametro (parametro_id)
) ENGINE=InnoDB;

-- Tabla: parametro_valores_referencia
CREATE TABLE parametro_valores_referencia (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    parametro_id INT UNSIGNED NOT NULL,
    sexo ENUM('M', 'F') COMMENT 'NULL = aplica para ambos',
    edad_min INT COMMENT 'Edad mínima en meses',
    edad_max INT COMMENT 'Edad máxima en meses, NULL = sin límite',
    valor_min DECIMAL(15,4),
    valor_max DECIMAL(15,4),
    valor_texto TEXT COMMENT 'Para valores cualitativos',
    valor_critico_min DECIMAL(15,4),
    valor_critico_max DECIMAL(15,4),
    notas TEXT,
    FOREIGN KEY (parametro_id) REFERENCES estudio_parametros(id) ON DELETE CASCADE,
    INDEX idx_parametro (parametro_id)
) ENGINE=InnoDB;

-- Tabla: estudio_indicaciones
CREATE TABLE estudio_indicaciones (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    estudio_id INT UNSIGNED NOT NULL,
    indicacion TEXT NOT NULL,
    tipo ENUM('preparacion', 'muestra', 'observacion') NOT NULL,
    orden INT DEFAULT 0,
    FOREIGN KEY (estudio_id) REFERENCES estudios(id) ON DELETE CASCADE,
    INDEX idx_estudio (estudio_id)
) ENGINE=InnoDB;

-- Tabla: perfiles
CREATE TABLE perfiles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) NOT NULL UNIQUE,
    nombre VARCHAR(200) NOT NULL,
    descripcion TEXT,
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_codigo (codigo),
    INDEX idx_activo (activo)
) ENGINE=InnoDB;

-- Tabla: perfil_estudios
CREATE TABLE perfil_estudios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    perfil_id INT UNSIGNED NOT NULL,
    estudio_id INT UNSIGNED NOT NULL,
    orden INT DEFAULT 0,
    FOREIGN KEY (perfil_id) REFERENCES perfiles(id) ON DELETE CASCADE,
    FOREIGN KEY (estudio_id) REFERENCES estudios(id) ON DELETE CASCADE,
    UNIQUE KEY uk_perfil_estudio (perfil_id, estudio_id)
) ENGINE=InnoDB;

-- Tabla: estudio_precios
CREATE TABLE estudio_precios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    estudio_id INT UNSIGNED NOT NULL,
    lista_precio_id INT UNSIGNED NOT NULL,
    precio_base DECIMAL(10,2) NOT NULL,
    moneda VARCHAR(3) DEFAULT 'MXN',
    FOREIGN KEY (estudio_id) REFERENCES estudios(id) ON DELETE CASCADE,
    FOREIGN KEY (lista_precio_id) REFERENCES listas_precios(id) ON DELETE CASCADE,
    UNIQUE KEY uk_estudio_lista (estudio_id, lista_precio_id)
) ENGINE=InnoDB;

-- ============================================================
-- MÓDULO: ÓRDENES Y FOLIOS
-- ============================================================

-- Tabla: folios_control
CREATE TABLE folios_control (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sucursal_id INT UNSIGNED NOT NULL,
    fecha DATE NOT NULL,
    ultimo_consecutivo INT UNSIGNED NOT NULL DEFAULT 0,
    FOREIGN KEY (sucursal_id) REFERENCES sucursales(id),
    UNIQUE KEY uk_sucursal_fecha (sucursal_id, fecha)
) ENGINE=InnoDB;

-- Tabla: ordenes
CREATE TABLE ordenes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    folio VARCHAR(20) NOT NULL UNIQUE COMMENT 'Formato: YYYYMMDDSSNNNN',
    paciente_id INT UNSIGNED NOT NULL,
    sucursal_id INT UNSIGNED NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_toma_muestra TIMESTAMP NULL,
    usuario_registro_id INT UNSIGNED NOT NULL,
    procedencia_tipo ENUM('particular', 'empresa', 'medico') NOT NULL,
    procedencia_id INT UNSIGNED COMMENT 'ID de compañía o médico',
    lista_precio_id INT UNSIGNED NOT NULL,
    medico_solicitante VARCHAR(255),
    diagnostico TEXT,
    subtotal DECIMAL(10,2) DEFAULT 0.00,
    descuento_monto DECIMAL(10,2) DEFAULT 0.00,
    descuento_porcentaje DECIMAL(5,2) DEFAULT 0.00,
    cargo_monto DECIMAL(10,2) DEFAULT 0.00,
    cargo_descripcion VARCHAR(255),
    total DECIMAL(10,2) DEFAULT 0.00,
    total_pagado DECIMAL(10,2) DEFAULT 0.00,
    saldo DECIMAL(10,2) DEFAULT 0.00,
    estatus ENUM('registrada', 'en_proceso', 'parcial', 'validada', 'liberada', 'entregada', 'cancelada') DEFAULT 'registrada',
    estatus_pago ENUM('pendiente', 'parcial', 'pagado', 'credito') DEFAULT 'pendiente',
    prioridad ENUM('normal', 'urgente', 'stat') DEFAULT 'normal',
    notas TEXT,
    fecha_cancelacion TIMESTAMP NULL,
    motivo_cancelacion TEXT,
    usuario_cancelacion_id INT UNSIGNED,
    FOREIGN KEY (paciente_id) REFERENCES pacientes(id),
    FOREIGN KEY (sucursal_id) REFERENCES sucursales(id),
    FOREIGN KEY (usuario_registro_id) REFERENCES usuarios(id),
    FOREIGN KEY (lista_precio_id) REFERENCES listas_precios(id),
    FOREIGN KEY (usuario_cancelacion_id) REFERENCES usuarios(id),
    INDEX idx_folio (folio),
    INDEX idx_paciente (paciente_id),
    INDEX idx_sucursal (sucursal_id),
    INDEX idx_fecha_registro (fecha_registro),
    INDEX idx_estatus (estatus),
    INDEX idx_estatus_pago (estatus_pago)
) ENGINE=InnoDB;

-- Tabla: orden_estudios
CREATE TABLE orden_estudios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    orden_id INT UNSIGNED NOT NULL,
    estudio_id INT UNSIGNED NOT NULL,
    codigo_estudio VARCHAR(50) NOT NULL COMMENT 'Snapshot del código',
    nombre_estudio VARCHAR(200) NOT NULL COMMENT 'Snapshot del nombre',
    precio_unitario DECIMAL(10,2) NOT NULL,
    cantidad INT DEFAULT 1,
    descuento_porcentaje DECIMAL(5,2) DEFAULT 0.00,
    subtotal DECIMAL(10,2) NOT NULL,
    muestra_tomada BOOLEAN DEFAULT FALSE,
    fecha_muestra TIMESTAMP NULL,
    observaciones_muestra TEXT,
    estatus ENUM('pendiente', 'capturado', 'validado', 'liberado') DEFAULT 'pendiente',
    fecha_captura TIMESTAMP NULL,
    usuario_captura_id INT UNSIGNED,
    fecha_validacion TIMESTAMP NULL,
    usuario_validacion_id INT UNSIGNED,
    fecha_liberacion TIMESTAMP NULL,
    usuario_liberacion_id INT UNSIGNED,
    cancelado BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (orden_id) REFERENCES ordenes(id) ON DELETE CASCADE,
    FOREIGN KEY (estudio_id) REFERENCES estudios(id),
    FOREIGN KEY (usuario_captura_id) REFERENCES usuarios(id),
    FOREIGN KEY (usuario_validacion_id) REFERENCES usuarios(id),
    FOREIGN KEY (usuario_liberacion_id) REFERENCES usuarios(id),
    INDEX idx_orden (orden_id),
    INDEX idx_estudio (estudio_id),
    INDEX idx_estatus (estatus)
) ENGINE=InnoDB;

-- Tabla: orden_indicaciones
CREATE TABLE orden_indicaciones (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    orden_id INT UNSIGNED NOT NULL,
    estudio_id INT UNSIGNED NOT NULL,
    indicacion TEXT NOT NULL,
    tipo ENUM('preparacion', 'muestra', 'observacion') NOT NULL,
    orden_impresion INT DEFAULT 0,
    FOREIGN KEY (orden_id) REFERENCES ordenes(id) ON DELETE CASCADE,
    FOREIGN KEY (estudio_id) REFERENCES estudios(id),
    INDEX idx_orden (orden_id)
) ENGINE=InnoDB;

-- ============================================================
-- MÓDULO: RESULTADOS
-- ============================================================

-- Tabla: orden_resultados
CREATE TABLE orden_resultados (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    orden_estudio_id INT UNSIGNED NOT NULL,
    parametro_id INT UNSIGNED NOT NULL,
    valor_numerico DECIMAL(15,4),
    valor_texto TEXT,
    valor_opcion_id INT UNSIGNED COMMENT 'Para opciones múltiples',
    unidad_medida VARCHAR(50),
    valor_referencia_min DECIMAL(15,4) COMMENT 'Snapshot',
    valor_referencia_max DECIMAL(15,4) COMMENT 'Snapshot',
    valor_referencia_texto TEXT COMMENT 'Snapshot',
    fuera_rango BOOLEAN DEFAULT FALSE,
    valor_critico BOOLEAN DEFAULT FALSE,
    metodo_captura ENUM('manual', 'interfaz', 'calculado') DEFAULT 'manual',
    equipo_id INT UNSIGNED COMMENT 'Si fue por interfaz',
    fecha_captura TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    usuario_captura_id INT UNSIGNED NOT NULL,
    fecha_modificacion TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    usuario_modificacion_id INT UNSIGNED,
    observaciones TEXT,
    FOREIGN KEY (orden_estudio_id) REFERENCES orden_estudios(id) ON DELETE CASCADE,
    FOREIGN KEY (parametro_id) REFERENCES estudio_parametros(id),
    FOREIGN KEY (valor_opcion_id) REFERENCES parametro_opciones(id),
    FOREIGN KEY (usuario_captura_id) REFERENCES usuarios(id),
    FOREIGN KEY (usuario_modificacion_id) REFERENCES usuarios(id),
    INDEX idx_orden_estudio (orden_estudio_id),
    INDEX idx_parametro (parametro_id)
) ENGINE=InnoDB;

-- Tabla: orden_estudio_validaciones
CREATE TABLE orden_estudio_validaciones (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    orden_estudio_id INT UNSIGNED NOT NULL,
    tipo_validacion ENUM('tecnica', 'medica') NOT NULL,
    usuario_id INT UNSIGNED NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    observaciones TEXT,
    firma_digital VARCHAR(255) COMMENT 'Hash de la firma',
    FOREIGN KEY (orden_estudio_id) REFERENCES orden_estudios(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    INDEX idx_orden_estudio (orden_estudio_id)
) ENGINE=InnoDB;

-- ============================================================
-- MÓDULO: MICROBIOLOGÍA
-- ============================================================

-- Tabla: catalogo_microorganismos
CREATE TABLE catalogo_microorganismos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre_cientifico VARCHAR(200) NOT NULL,
    nombre_comun VARCHAR(200),
    tipo ENUM('bacteria', 'hongo', 'parasito', 'virus') NOT NULL,
    gram ENUM('positivo', 'negativo', 'NA') COMMENT 'Para bacterias',
    activo BOOLEAN DEFAULT TRUE,
    frecuente BOOLEAN DEFAULT FALSE COMMENT 'Para mostrar primero',
    INDEX idx_nombre (nombre_cientifico),
    INDEX idx_frecuente (frecuente)
) ENGINE=InnoDB;

-- Tabla: catalogo_antibioticos
CREATE TABLE catalogo_antibioticos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    familia VARCHAR(100) COMMENT 'Betalactámicos, Quinolonas, etc.',
    abreviatura VARCHAR(10),
    activo BOOLEAN DEFAULT TRUE,
    orden INT DEFAULT 0,
    INDEX idx_familia (familia)
) ENGINE=InnoDB;

-- Tabla: resultado_cultivo
CREATE TABLE resultado_cultivo (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    orden_estudio_id INT UNSIGNED NOT NULL,
    tipo_muestra VARCHAR(100),
    aspecto_muestra TEXT,
    desarrollo_microbiano ENUM('no', 'si', 'escaso', 'moderado', 'abundante'),
    dias_incubacion INT,
    fecha_siembra DATE,
    fecha_lectura DATE,
    observaciones_generales TEXT,
    conclusion TEXT,
    usuario_captura_id INT UNSIGNED NOT NULL,
    fecha_captura TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    validado BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (orden_estudio_id) REFERENCES orden_estudios(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_captura_id) REFERENCES usuarios(id),
    INDEX idx_orden_estudio (orden_estudio_id)
) ENGINE=InnoDB;

-- Tabla: cultivo_microorganismos
CREATE TABLE cultivo_microorganismos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    resultado_cultivo_id INT UNSIGNED NOT NULL,
    microorganismo_id INT UNSIGNED NOT NULL,
    cantidad ENUM('escaso', 'moderado', 'abundante'),
    ufc_ml VARCHAR(50) COMMENT 'Unidades formadoras de colonias',
    orden INT DEFAULT 0,
    observaciones TEXT,
    FOREIGN KEY (resultado_cultivo_id) REFERENCES resultado_cultivo(id) ON DELETE CASCADE,
    FOREIGN KEY (microorganismo_id) REFERENCES catalogo_microorganismos(id),
    INDEX idx_cultivo (resultado_cultivo_id)
) ENGINE=InnoDB;

-- Tabla: antibiograma
CREATE TABLE antibiograma (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cultivo_microorganismo_id INT UNSIGNED NOT NULL,
    antibiotico_id INT UNSIGNED NOT NULL,
    sensibilidad ENUM('S', 'I', 'R') NOT NULL COMMENT 'S=Sensible, I=Intermedio, R=Resistente',
    halo_mm INT COMMENT 'Diámetro del halo en mm',
    metodo VARCHAR(100) COMMENT 'Kirby-Bauer, dilución, etc.',
    observaciones TEXT,
    FOREIGN KEY (cultivo_microorganismo_id) REFERENCES cultivo_microorganismos(id) ON DELETE CASCADE,
    FOREIGN KEY (antibiotico_id) REFERENCES catalogo_antibioticos(id),
    INDEX idx_cultivo_microorganismo (cultivo_microorganismo_id)
) ENGINE=InnoDB;

-- ============================================================
-- MÓDULO: PAGOS
-- ============================================================

-- Tabla: pagos
CREATE TABLE pagos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    orden_id INT UNSIGNED NOT NULL,
    folio_pago VARCHAR(30) NOT NULL UNIQUE COMMENT 'Formato: PAG-YYYYMMDDSSNNNN',
    fecha_pago TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    monto DECIMAL(10,2) NOT NULL,
    forma_pago_id INT UNSIGNED NOT NULL,
    referencia VARCHAR(100),
    banco VARCHAR(100),
    usuario_registro_id INT UNSIGNED NOT NULL,
    sucursal_id INT UNSIGNED NOT NULL,
    notas TEXT,
    cancelado BOOLEAN DEFAULT FALSE,
    fecha_cancelacion TIMESTAMP NULL,
    motivo_cancelacion TEXT,
    usuario_cancelacion_id INT UNSIGNED,
    recibo_impreso BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (orden_id) REFERENCES ordenes(id),
    FOREIGN KEY (forma_pago_id) REFERENCES formas_pago(id),
    FOREIGN KEY (usuario_registro_id) REFERENCES usuarios(id),
    FOREIGN KEY (sucursal_id) REFERENCES sucursales(id),
    FOREIGN KEY (usuario_cancelacion_id) REFERENCES usuarios(id),
    INDEX idx_orden (orden_id),
    INDEX idx_folio (folio_pago),
    INDEX idx_fecha (fecha_pago)
) ENGINE=InnoDB;

-- Tabla: pago_formas_multiple
CREATE TABLE pago_formas_multiple (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pago_id INT UNSIGNED NOT NULL,
    forma_pago_id INT UNSIGNED NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    referencia VARCHAR(100),
    banco VARCHAR(100),
    FOREIGN KEY (pago_id) REFERENCES pagos(id) ON DELETE CASCADE,
    FOREIGN KEY (forma_pago_id) REFERENCES formas_pago(id),
    INDEX idx_pago (pago_id)
) ENGINE=InnoDB;

-- ============================================================
-- MÓDULO: AUDITORÍA
-- ============================================================

-- Tabla: auditoria
CREATE TABLE auditoria (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT UNSIGNED,
    accion VARCHAR(100) NOT NULL,
    modulo VARCHAR(50) NOT NULL,
    entidad_tipo VARCHAR(50),
    entidad_id INT UNSIGNED,
    datos_anteriores JSON,
    datos_nuevos JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    sucursal_id INT UNSIGNED,
    fecha_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    notas TEXT,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    FOREIGN KEY (sucursal_id) REFERENCES sucursales(id),
    INDEX idx_usuario (usuario_id),
    INDEX idx_fecha (fecha_hora),
    INDEX idx_modulo (modulo),
    INDEX idx_accion (accion)
) ENGINE=InnoDB;

-- ============================================================
-- DATOS INICIALES
-- ============================================================

-- Insertar sucursal matriz
INSERT INTO sucursales (codigo, nombre, nombre_corto, es_matriz, activo) VALUES
('01', 'Matriz', 'MTZ', TRUE, TRUE);

-- Insertar áreas básicas
INSERT INTO areas (codigo, nombre, color, orden, activo) VALUES
('QC', 'Química Clínica', '#3498db', 1, TRUE),
('HEM', 'Hematología', '#e74c3c', 2, TRUE),
('INMU', 'Inmunología', '#9b59b6', 3, TRUE),
('MICRO', 'Microbiología', '#2ecc71', 4, TRUE),
('URIAN', 'Urianálisis', '#f39c12', 5, TRUE),
('COPR', 'Coprología', '#95a5a6', 6, TRUE);

-- Insertar formas de pago
INSERT INTO formas_pago (clave, nombre, requiere_referencia, requiere_banco, activo, orden_display) VALUES
('01', 'Efectivo', FALSE, FALSE, TRUE, 1),
('02', 'Cheque', TRUE, TRUE, TRUE, 2),
('03', 'Transferencia', TRUE, TRUE, TRUE, 3),
('04', 'Tarjeta Débito', TRUE, FALSE, TRUE, 4),
('05', 'Tarjeta Crédito', TRUE, FALSE, TRUE, 5),
('99', 'Por Definir', FALSE, FALSE, TRUE, 99);

-- Insertar tipos de parámetros
INSERT INTO parametro_tipos (clave, nombre, descripcion) VALUES
('numerico', 'Numérico', 'Valores numéricos (glucosa, hemoglobina, etc.)'),
('texto', 'Texto Libre', 'Texto libre para observaciones'),
('opcion_multiple', 'Opción Múltiple', 'Lista de opciones predefinidas'),
('tabla', 'Tabla', 'Resultados tabulares'),
('cultivo', 'Cultivo', 'Específico para microbiología');

-- Insertar tipos de muestra básicos
INSERT INTO tipos_muestra (nombre, descripcion, activo) VALUES
('Sangre - Suero', 'Muestra de sangre para obtención de suero', TRUE),
('Sangre - Plasma', 'Muestra de sangre con anticoagulante', TRUE),
('Sangre - Total', 'Sangre completa sin separar', TRUE),
('Orina', 'Orina para análisis general o cultivo', TRUE),
('Heces', 'Materia fecal para análisis', TRUE),
('Exudado Faríngeo', 'Muestra de garganta', TRUE),
('Exudado Uretral', 'Muestra de uretra', TRUE),
('Esputo', 'Muestra de secreción bronquial', TRUE);

-- Insertar lista de precios general
INSERT INTO listas_precios (nombre, descripcion, tipo, activa) VALUES
('General', 'Lista de precios para público general', 'particular', TRUE);

-- Insertar roles básicos
INSERT INTO roles (nombre, descripcion, es_sistema, activo) VALUES
('Superusuario', 'Control total del sistema', TRUE, TRUE),
('Administrador', 'Gestión operativa y configuración', TRUE, TRUE),
('Químico Supervisor', 'Personal técnico con capacidad de supervisión', TRUE, TRUE),
('Químico Estándar', 'Personal técnico de captura', TRUE, TRUE),
('Recepcionista', 'Personal de atención al público', TRUE, TRUE);

-- Insertar permisos básicos (muestra)
INSERT INTO permisos (modulo, accion, clave, nombre_display, descripcion, grupo) VALUES
-- Pacientes
('pacientes', 'crear', 'pacientes.crear', 'Crear Pacientes', 'Permite registrar nuevos pacientes', 'Pacientes'),
('pacientes', 'ver', 'pacientes.ver', 'Ver Pacientes', 'Permite ver información de pacientes', 'Pacientes'),
('pacientes', 'editar', 'pacientes.editar', 'Editar Pacientes', 'Permite modificar datos de pacientes', 'Pacientes'),
('pacientes', 'eliminar', 'pacientes.eliminar', 'Eliminar Pacientes', 'Permite eliminar pacientes', 'Pacientes'),

-- Órdenes
('ordenes', 'crear', 'ordenes.crear', 'Crear Órdenes', 'Permite registrar nuevas órdenes', 'Órdenes'),
('ordenes', 'ver', 'ordenes.ver', 'Ver Órdenes', 'Permite ver órdenes', 'Órdenes'),
('ordenes', 'editar', 'ordenes.editar', 'Editar Órdenes', 'Permite modificar órdenes', 'Órdenes'),
('ordenes', 'cancelar', 'ordenes.cancelar', 'Cancelar Órdenes', 'Permite cancelar órdenes', 'Órdenes'),
('ordenes', 'imprimir', 'ordenes.imprimir', 'Imprimir Documentos', 'Permite imprimir etiquetas, órdenes y recibos', 'Órdenes'),

-- Resultados
('resultados', 'capturar', 'resultados.capturar', 'Capturar Resultados', 'Permite capturar resultados de estudios', 'Resultados'),
('resultados', 'editar', 'resultados.editar', 'Editar Resultados', 'Permite modificar resultados', 'Resultados'),
('resultados', 'validar', 'resultados.validar', 'Validar Técnicamente', 'Permite validación técnica', 'Resultados'),
('resultados', 'liberar', 'resultados.liberar', 'Liberar Resultados', 'Permite liberación médica', 'Resultados'),

-- Pagos
('pagos', 'registrar', 'pagos.registrar', 'Registrar Pagos', 'Permite registrar pagos', 'Pagos'),
('pagos', 'ver', 'pagos.ver', 'Ver Pagos', 'Permite ver historial de pagos', 'Pagos'),
('pagos', 'cancelar', 'pagos.cancelar', 'Cancelar Pagos', 'Permite cancelar pagos', 'Pagos'),

-- Catálogos
('catalogos', 'gestionar', 'catalogos.gestionar', 'Gestionar Catálogos', 'Permite administrar catálogos del sistema', 'Catálogos'),

-- Usuarios
('usuarios', 'crear', 'usuarios.crear', 'Crear Usuarios', 'Permite crear nuevos usuarios', 'Usuarios'),
('usuarios', 'ver', 'usuarios.ver', 'Ver Usuarios', 'Permite ver usuarios', 'Usuarios'),
('usuarios', 'editar', 'usuarios.editar', 'Editar Usuarios', 'Permite modificar usuarios', 'Usuarios'),
('usuarios', 'eliminar', 'usuarios.eliminar', 'Eliminar Usuarios', 'Permite eliminar usuarios', 'Usuarios');

-- Crear usuario superadmin inicial (password: admin123)
-- IMPORTANTE: Cambiar password en producción
INSERT INTO usuarios (username, password_hash, nombres, apellido_paterno, apellido_materno, email, sucursal_id, activo, requiere_cambio_password) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador', 'Sistema', '', 'admin@laboratorio.com', 1, TRUE, TRUE);

-- Asignar rol de superusuario al admin
INSERT INTO usuario_roles (usuario_id, rol_id, asignado_por) VALUES
(1, 1, 1);

-- ============================================================
-- FIN DEL SCRIPT
-- ============================================================
