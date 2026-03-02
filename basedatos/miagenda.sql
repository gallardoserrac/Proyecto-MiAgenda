
CREATE DATABASE IF NOT EXISTS miagenda 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE miagenda;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    fecha_nacimiento DATE,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultima_sesion TIMESTAMP NULL,
    rol ENUM('alumno', 'profesor') NOT NULL DEFAULT 'alumno',
    idioma_preferido VARCHAR(5) DEFAULT 'es',
    activo BOOLEAN DEFAULT TRUE,
    INDEX idx_email (email),
    INDEX idx_usuario (usuario),
    INDEX idx_rol (rol)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE idiomas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(5) NOT NULL UNIQUE,
    nombre VARCHAR(50) NOT NULL,
    nombre_nativo VARCHAR(50),
    activo BOOLEAN DEFAULT TRUE,
    es_default BOOLEAN DEFAULT FALSE,
    INDEX idx_codigo (codigo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE traducciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    idioma_id INT NOT NULL,
    clave VARCHAR(100) NOT NULL,
    texto TEXT NOT NULL,
    categoria VARCHAR(50) DEFAULT 'general',
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (idioma_id) REFERENCES idiomas(id) ON DELETE CASCADE,
    UNIQUE KEY unique_idioma_clave (idioma_id, clave),
    INDEX idx_clave (clave),
    INDEX idx_categoria (categoria)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE tareas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    creado_por INT,
    titulo VARCHAR(100) NOT NULL,
    descripcion TEXT,
    fecha DATE NOT NULL,
    hora TIME,
    prioridad ENUM('alta', 'media', 'baja') DEFAULT 'media',
    completada BOOLEAN DEFAULT FALSE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (creado_por) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_usuario_fecha (usuario_id, fecha),
    INDEX idx_completada (completada),
    INDEX idx_fecha (fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE kanban (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    creado_por INT,
    titulo VARCHAR(100) NOT NULL,
    descripcion TEXT,
    columna ENUM('pendiente', 'en_progreso', 'completado') DEFAULT 'pendiente',
    prioridad ENUM('alta', 'media', 'baja') DEFAULT 'media',
    fecha_limite DATE,
    orden INT DEFAULT 0,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (creado_por) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_usuario_columna (usuario_id, columna),
    INDEX idx_usuario_prioridad (usuario_id, prioridad)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE asignaturas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    color VARCHAR(7) DEFAULT '#007bff',
    profesor VARCHAR(100),
    aula VARCHAR(50),
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario (usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE actividades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    asignatura_id INT,
    titulo VARCHAR(150) NOT NULL,
    descripcion TEXT,
    tipo ENUM('tarea', 'examen', 'trabajo', 'entrega', 'otro') DEFAULT 'tarea',
    fecha_entrega DATE,
    completada BOOLEAN DEFAULT FALSE,
    nota DECIMAL(4,2),
    prioridad ENUM('alta', 'media', 'baja') DEFAULT 'media',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (asignatura_id) REFERENCES asignaturas(id) ON DELETE SET NULL,
    INDEX idx_usuario (usuario_id),
    INDEX idx_asignatura (asignatura_id),
    INDEX idx_tipo (tipo),
    INDEX idx_completada (completada),
    INDEX idx_fecha_entrega (fecha_entrega)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE exportaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    tipo ENUM('pdf', 'csv') NOT NULL,
    contenido TEXT,
    fecha_exportacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario (usuario_id),
    INDEX idx_tipo (tipo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO idiomas (codigo, nombre, nombre_nativo, activo, es_default) VALUES
('es', 'Español', 'Español', TRUE, TRUE),
('en', 'Inglés', 'English', TRUE, FALSE);

INSERT INTO traducciones (idioma_id, clave, texto, categoria) VALUES
(1, 'nav.inicio', 'Inicio', 'navegacion'),
(1, 'nav.calendario', 'Calendario', 'navegacion'),
(1, 'nav.actividades', 'Actividades', 'navegacion'),
(1, 'nav.exportar', 'Exportar', 'navegacion'),
(1, 'nav.logros', 'Logros', 'navegacion'),
(1, 'nav.login', 'Iniciar Sesión', 'navegacion'),
(1, 'nav.registro', 'Registrarse', 'navegacion'),
(1, 'nav.logout', 'Cerrar Sesión', 'navegacion'),
(1, 'form.email', 'Correo Electrónico', 'formulario'),
(1, 'form.password', 'Contraseña', 'formulario'),
(1, 'form.usuario', 'Usuario', 'formulario'),
(1, 'form.enviar', 'Enviar', 'formulario'),
(1, 'form.cancelar', 'Cancelar', 'formulario'),
(1, 'mensaje.bienvenido', '¡Bienvenido de nuevo!', 'mensajes'),
(1, 'mensaje.error', 'Ha ocurrido un error', 'mensajes'),
(1, 'mensaje.exito', 'Operación exitosa', 'mensajes');

INSERT INTO traducciones (idioma_id, clave, texto, categoria) VALUES
(2, 'nav.inicio', 'Home', 'navegacion'),
(2, 'nav.calendario', 'Calendar', 'navegacion'),
(2, 'nav.actividades', 'Activities', 'navegacion'),
(2, 'nav.exportar', 'Export', 'navegacion'),
(2, 'nav.logros', 'Achievements', 'navegacion'),
(2, 'nav.login', 'Login', 'navegacion'),
(2, 'nav.registro', 'Register', 'navegacion'),
(2, 'nav.logout', 'Logout', 'navegacion'),
(2, 'form.email', 'Email', 'formulario'),
(2, 'form.password', 'Password', 'formulario'),
(2, 'form.usuario', 'Username', 'formulario'),
(2, 'form.enviar', 'Submit', 'formulario'),
(2, 'form.cancelar', 'Cancel', 'formulario'),
(2, 'mensaje.bienvenido', 'Welcome back!', 'mensajes'),
(2, 'mensaje.error', 'An error occurred', 'mensajes'),
(2, 'mensaje.exito', 'Operation successful', 'mensajes');

INSERT INTO usuarios (usuario, email, password, rol, idioma_preferido, activo) VALUES
('profesor1', 'profe1@miagenda.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'profesor', 'es', TRUE);

INSERT INTO usuarios (usuario, email, password, rol, idioma_preferido, activo) VALUES
('profesor2', 'profe2@miagenda.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'profesor', 'es', TRUE);

CREATE OR REPLACE VIEW v_tareas_pendientes AS
SELECT 
    u.id AS usuario_id,
    u.usuario,
    COUNT(t.id) AS pendientes
FROM usuarios u
LEFT JOIN tareas t ON u.id = t.usuario_id AND t.completada = FALSE
WHERE u.rol = 'alumno' AND u.activo = TRUE
GROUP BY u.id, u.usuario;

CREATE OR REPLACE VIEW v_tareas_semana AS
SELECT 
    u.id AS usuario_id,
    u.usuario,
    COUNT(t.id) AS completadas_semana
FROM usuarios u
LEFT JOIN tareas t ON u.id = t.usuario_id 
    AND t.completada = TRUE 
    AND t.fecha_actualizacion >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
WHERE u.rol = 'alumno' AND u.activo = TRUE
GROUP BY u.id, u.usuario;

CREATE OR REPLACE VIEW v_examenes_proximos AS
SELECT 
    u.id AS usuario_id,
    u.usuario,
    COUNT(a.id) AS examenes_proximos
FROM usuarios u
LEFT JOIN actividades a ON u.id = a.usuario_id 
    AND a.tipo = 'examen' 
    AND a.fecha_entrega BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
WHERE u.rol = 'alumno' AND u.activo = TRUE
GROUP BY u.id, u.usuario;

CREATE OR REPLACE VIEW v_promedio_completadas AS
SELECT 
    u.id AS usuario_id,
    u.usuario,
    ROUND(
        (COUNT(CASE WHEN t.completada = TRUE THEN 1 END) / COUNT(t.id)) * 100, 
        2
    ) AS porcentaje_completadas
FROM usuarios u
LEFT JOIN tareas t ON u.id = t.usuario_id
WHERE u.rol = 'alumno' AND u.activo = TRUE
GROUP BY u.id, u.usuario;