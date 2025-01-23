-- Tabla: alumnos
CREATE TABLE alumnos (
    alumno_id INT(11) NOT NULL AUTO_INCREMENT,
    matricula VARCHAR(20) NOT NULL,
    nombres VARCHAR(100) NOT NULL,
    direccion VARCHAR(255) DEFAULT NULL,
    telefono VARCHAR(15) DEFAULT NULL,
    grupo_id INT(11) NOT NULL,
    nivel_id INT(11) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    fecha_nacimiento DATE DEFAULT NULL,
    foto VARCHAR(255) DEFAULT NULL,
    fecha_registro TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    PRIMARY KEY (alumno_id),
    FOREIGN KEY (grupo_id) REFERENCES grupos(id_grupo) ON UPDATE RESTRICT,
    FOREIGN KEY (nivel_id) REFERENCES niveles(nivel_id) ON UPDATE CASCADE
);

-- Tabla: calificaciones
CREATE TABLE calificaciones (
    calificacion_id INT(11) NOT NULL AUTO_INCREMENT,
    alumno_id INT(11) NOT NULL,
    materia_id INT(11) NOT NULL,
    periodo_id INT(11) NOT NULL,
    rasgo_id INT(11) NOT NULL,
    calificacion DECIMAL(5,2) NOT NULL,
    observaciones TEXT DEFAULT NULL,
    fecha_registro TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    PRIMARY KEY (calificacion_id),
    FOREIGN KEY (alumno_id) REFERENCES alumnos(alumno_id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (materia_id) REFERENCES materias(materia_id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (periodo_id) REFERENCES periodos(periodo_id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (rasgo_id) REFERENCES rasgos(rasgo_id) ON UPDATE CASCADE ON DELETE CASCADE
);

-- Tabla: directores
CREATE TABLE directores (
    id INT(11) NOT NULL AUTO_INCREMENT,
    usuario_id INT(11) NOT NULL,
    nivel_id INT(11) NOT NULL,
    asignado_por INT(11) DEFAULT NULL,
    fecha_asignacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    PRIMARY KEY (id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (nivel_id) REFERENCES niveles(nivel_id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (asignado_por) REFERENCES usuarios(usuario_id) ON UPDATE CASCADE ON DELETE SET NULL
);

-- Tabla: grupos
CREATE TABLE grupos (
    id_grupo INT(11) NOT NULL AUTO_INCREMENT,
    nivel_id INT(11) NOT NULL,
    grado VARCHAR(20) NOT NULL,
    turno VARCHAR(20) NOT NULL,
    PRIMARY KEY (id_grupo),
    FOREIGN KEY (nivel_id) REFERENCES niveles(nivel_id) ON UPDATE CASCADE ON DELETE CASCADE
);

-- Tabla: grupo_materia
CREATE TABLE grupo_materia (
    id INT(11) NOT NULL AUTO_INCREMENT,
    grupo_id INT(11) NOT NULL,
    materia_id INT(11) NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (grupo_id) REFERENCES grupos(id_grupo) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (materia_id) REFERENCES materias(materia_id) ON UPDATE CASCADE ON DELETE CASCADE
);

-- Tabla: materias
CREATE TABLE materias (
    materia_id INT(11) NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    nivel_id INT(11) NOT NULL,
    PRIMARY KEY (materia_id),
    FOREIGN KEY (nivel_id) REFERENCES niveles(nivel_id) ON UPDATE RESTRICT ON DELETE CASCADE
);

-- Tabla: materia_rasgo
CREATE TABLE materia_rasgo (
    materia_rasgo_id INT(11) NOT NULL AUTO_INCREMENT,
    materia_id INT(11) NOT NULL,
    rasgo_id INT(11) NOT NULL,
    porcentaje DECIMAL(5,2) NOT NULL,
    PRIMARY KEY (materia_rasgo_id),
    FOREIGN KEY (materia_id) REFERENCES materias(materia_id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (rasgo_id) REFERENCES rasgos(rasgo_id) ON UPDATE CASCADE ON DELETE CASCADE
);

-- Tabla: niveles
CREATE TABLE niveles (
    nivel_id INT(11) NOT NULL AUTO_INCREMENT,
    nivel_nombre VARCHAR(50) NOT NULL,
    PRIMARY KEY (nivel_id)
);

-- Tabla: pagos
CREATE TABLE pagos (
    pago_id INT(11) NOT NULL AUTO_INCREMENT,
    alumno_id INT(11) NOT NULL,
    concepto ENUM('inscripcion', 'mensualidad', 'colegiatura') NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    fecha_pago DATE NOT NULL,
    descuento DECIMAL(10,2) DEFAULT 0.00,
    recargo DECIMAL(10,2) DEFAULT 0.00,
    total DECIMAL(10,2) GENERATED ALWAYS AS (monto - descuento + recargo) VIRTUAL,
    PRIMARY KEY (pago_id),
    FOREIGN KEY (alumno_id) REFERENCES alumnos(alumno_id) ON UPDATE RESTRICT ON DELETE CASCADE
);

-- Tabla: periodos
CREATE TABLE periodos (
    periodo_id INT(11) NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    activo TINYINT(1) DEFAULT 1,
    PRIMARY KEY (periodo_id)
);

-- Tabla: profesores
CREATE TABLE profesores (
    profesor_id INT(11) NOT NULL AUTO_INCREMENT,
    usuario_id INT(11) NOT NULL,
    especialidad VARCHAR(100) DEFAULT NULL,
    telefono VARCHAR(15) DEFAULT NULL,
    nivel_id INT(11) DEFAULT NULL,
    foto VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY (profesor_id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id) ON UPDATE RESTRICT ON DELETE CASCADE,
    FOREIGN KEY (nivel_id) REFERENCES niveles(nivel_id) ON UPDATE RESTRICT ON DELETE CASCADE
);

-- Tabla: profesor_materia
CREATE TABLE profesor_materia (
    profesor_id INT(11) NOT NULL,
    materia_id INT(11) NOT NULL,
    periodo_id INT(11) NOT NULL,
    PRIMARY KEY (profesor_id, materia_id, periodo_id),
    FOREIGN KEY (profesor_id) REFERENCES profesores(profesor_id) ON UPDATE RESTRICT ON DELETE CASCADE,
    FOREIGN KEY (materia_id) REFERENCES materias(materia_id) ON UPDATE RESTRICT ON DELETE CASCADE,
    FOREIGN KEY (periodo_id) REFERENCES periodos(periodo_id) ON UPDATE RESTRICT ON DELETE CASCADE
);

-- Tabla: profesor_nivel
CREATE TABLE profesor_nivel (
    id INT(11) NOT NULL AUTO_INCREMENT,
    profesor_id INT(11) NOT NULL,
    nivel_id INT(11) NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (profesor_id) REFERENCES profesores(profesor_id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (nivel_id) REFERENCES niveles(nivel_id) ON UPDATE CASCADE ON DELETE CASCADE
);

-- Tabla: rasgos
CREATE TABLE rasgos (
    rasgo_id INT(11) NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL,
    PRIMARY KEY (rasgo_id)
);

-- Tabla: roles
CREATE TABLE roles (
    rol_id INT(11) NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT DEFAULT NULL,
    fecha_creacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    PRIMARY KEY (rol_id)
);

-- Tabla: usuarios
CREATE TABLE usuarios (
    usuario_id INT(11) NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(100) NOT NULL,
    contrasena VARCHAR(255) NOT NULL,
    rol_id INT(11) DEFAULT NULL,
    fecha_creacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    PRIMARY KEY (usuario_id),
    FOREIGN KEY (rol_id) REFERENCES roles(rol_id) ON UPDATE CASCADE ON DELETE RESTRICT
);