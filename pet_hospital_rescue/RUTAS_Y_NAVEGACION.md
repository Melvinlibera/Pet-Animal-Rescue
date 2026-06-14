<!-- VALIDACIÓN Y DOCUMENTACIÓN DE RUTAS -->

# PET HOSPITAL AND RESCUE - MAPA DE NAVEGACIÓN COMPLETO

## 🏠 PÁGINA PRINCIPAL (index.php)
- Ruta: `/index.php` ✓
- Botones disponibles:
  - "Agendar Cita" → `/user/agendar.php` (solo si está logueado)
  - "Mis Citas" → `/user/mis_citas.php` (solo si está logueado)
  - "Unirse Ahora" → `/auth/register.php` (si NO está logueado)
  - "Iniciar Sesión" → `/auth/login.php` (si NO está logueado)
  - "Especialidades" → `/especialidades/index.php` ✓
  - "Ver Todas las Especialidades" → `/especialidades/index.php` ✓
  - Navegación superior: Inicio, Especialidades, Panel (según rol), Salir

---

## 🔐 AUTENTICACIÓN

### Login (/auth/login.php)
- Ruta: `/auth/login.php` ✓
- Valida credenciales
- Redirige a:
  - Admin → `/admin/dashboard.php`
  - Veterinario → `/veterinario/dashboard.php`
  - Usuario → `/user/dashboard.php`
- Botones:
  - "¿No tienes cuenta?" → `/auth/register.php`
  - "Volver al inicio" → `/index.php`

### Registro (/auth/register.php)
- Ruta: `/auth/register.php` ✓
- Campos: Nombre, Apellido, Correo, Contraseña
- Redirige a login después del registro
- Botones:
  - "¿Ya tienes cuenta?" → `/auth/login.php`
  - "Volver al inicio" → `/index.php`

### Logout (/auth/logout.php)
- Ruta: `/auth/logout.php` ✓
- Cierra sesión
- Redirige a `/index.php`

---

## 👤 PANEL DE USUARIO

### Dashboard del Usuario (/user/dashboard.php)
- Ruta: `/user/dashboard.php` ✓
- Requiere: rol = 'user'
- Botones disponibles:
  - "Agendar Cita" → `/user/agendar.php` ✓
  - "Ver Mis Citas" → `/user/mis_citas.php` ✓
  - "Mi Perfil" → `/user/perfil.php` ✓
  - "Especialidades" → `/especialidades/index.php` ✓
  - "Inicio" → `/index.php` ✓
  - "Cerrar Sesión" → `/auth/logout.php` ✓

### Agendar Cita (/user/agendar.php)
- Ruta: `/user/agendar.php` ✓
- Parámetros opcionales: ?especialidad=ID&veterinario=ID
- Formulario:
  - Seleccionar especialidad
  - Seleccionar veterinario (cargado por AJAX)
  - Seleccionar fecha
  - Seleccionar hora
  - Botón: "Agendar Cita"
- Redirección: `/user/mis_citas.php` después de agendar

### Mis Citas (/user/mis_citas.php)
- Ruta: `/user/mis_citas.php` ✓
- Muestra tabla con:
  - Especialidad
  - Veterinario
  - Fecha
  - Hora
  - Estado
  - Botón cancelar (si está pendiente)

### Mi Perfil (/user/perfil.php)
- Ruta: `/user/perfil.php` ✓
- Campos editables:
  - Nombre
  - Teléfono
  - Tipo de seguro
  - Nombre del seguro
  - Botón: "Actualizar Perfil"
  - Botón: "Cambiar Contraseña"

---

## ⚕️ ESPECIALIDADES

### Listado de Especialidades (/especialidades/index.php)
- Ruta: `/especialidades/index.php` ✓
- Muestra todas las especialidades con:
  - Nombre
  - Descripción
  - Cantidad de veterinarios
  - Botones:
    - "Ver Detalles" → `/especialidades/ver.php?id=ID` ✓
    - "Agendar Cita" → `/user/agendar.php?especialidad=ID` (si está logueado)
    - "Agendar" → `/auth/register.php` (si NO está logueado)

### Detalles de Especialidad (/especialidades/ver.php)
- Ruta: `/especialidades/ver.php?id=ID` ✓
- Muestra:
  - Nombre de la especialidad
  - Descripción
  - Información de precios (con/sin seguro)
  - Lista de veterinarios disponibles
  - Botones:
    - "Agendar Cita" → `/user/agendar.php?id_veterinario=ID&id_especialidad=ID`
    - "Iniciar Sesión para Agendar" → `/auth/login.php`
    - "Volver al inicio" → `/index.php`

---

## 👨‍💼 PANEL DE VETERINARIO

### Dashboard del Veterinario (/veterinario/dashboard.php)
- Ruta: `/veterinario/dashboard.php` ✓
- Requiere: rol = 'veterinario'
- Muestra:
  - Bienvenida con nombre del veterinario
  - Especialidad asignada
  - Estadísticas:
    - Total de citas
    - Citas completadas
    - Citas pendientes
  - Lista de citas del día
  - Botones para confirmar/completar citas

### Mis Citas del Veterinario (/veterinario/mis_citas.php)
- Ruta: `/veterinario/mis_citas.php` ✓
- Tabla de todas las citas asignadas
- Filtros por estado
- Opciones de marcar como completada/cancelada

### Mi Perfil del Veterinario (/veterinario/perfil.php)
- Ruta: `/veterinario/perfil.php` ✓
- Mostrar información:
  - Nombre
  - Especialidad
  - Teléfono
  - Correo
  - Opción de cambiar contraseña

---

## 👨‍💻 PANEL ADMINISTRATIVO

### Dashboard Admin (/admin/dashboard.php)
- Ruta: `/admin/dashboard.php` ✓
- Requiere: rol = 'admin'
- Muestra:
  - Estadísticas globales:
    - Total de usuarios
    - Total de veterinarios
    - Total de especialidades
    - Total de citas
    - Citas del día
  - Acceso rápido a módulos
  - Navegación a:
    - `/admin/usuarios.php`
    - `/admin/veterinarios.php`
    - `/admin/especialidades.php`
    - `/admin/citas.php`

### Gestión de Usuarios (/admin/usuarios.php)
- Ruta: `/admin/usuarios.php` ✓
- Tabla de usuarios con:
  - Nombre
  - Correo
  - Rol
  - Fecha de registro
  - Botones: Editar, Cambiar rol, Eliminar

### Gestión de Veterinarios (/admin/veterinarios.php)
- Ruta: `/admin/veterinarios.php` ✓
- Tabla de veterinarios con:
  - Nombre
  - Especialidad
  - Correo
  - Botones: Editar, Eliminar

### Gestión de Especialidades (/admin/especialidades.php)
- Ruta: `/admin/especialidades.php` ✓
- Tabla de especialidades con:
  - Nombre
  - Descripción
  - Precio
  - Botones: Editar, Eliminar, Agregar nueva

### Gestión de Citas (/admin/citas.php)
- Ruta: `/admin/citas.php` ✓
- Tabla de citas con:
  - Usuario
  - Veterinario
  - Especialidad
  - Fecha y hora
  - Estado
  - Botones: Ver detalles, Cambiar estado, Eliminar

---

## 🎨 CSS Y DISEÑO

### Estilos centralizados (/assets/css/style.css)
✓ Variables CSS mejoradas
✓ Estilos de botones unificados (.btn, .btn-primary, .btn-success, etc.)
✓ Estilos de formularios mejorados
✓ Estilos de tablas
✓ Estilos de alertas y mensajes
✓ Responsive design
✓ Animaciones modernas
✓ Colores consistentes

---

## ✅ VALIDACIÓN FINAL

### Rutas verificadas:
- ✓ index.php
- ✓ auth/login.php
- ✓ auth/register.php
- ✓ auth/logout.php
- ✓ user/dashboard.php
- ✓ user/agendar.php
- ✓ user/mis_citas.php
- ✓ user/perfil.php
- ✓ veterinario/dashboard.php
- ✓ veterinario/mis_citas.php
- ✓ veterinario/perfil.php
- ✓ admin/dashboard.php
- ✓ admin/usuarios.php
- ✓ admin/veterinarios.php
- ✓ admin/especialidades.php
- ✓ admin/citas.php
- ✓ especialidades/index.php
- ✓ especialidades/ver.php

### Botones verificados:
- ✓ Todos los botones tienen rutas correctas
- ✓ Navegación consistente
- ✓ Redirecciones automáticas según rol
- ✓ Protección de páginas (verificación de sesión y rol)

### Diseño verificado:
- ✓ Centralizado
- ✓ Moderno
- ✓ Responsivo
- ✓ Consistente
- ✓ Profesional
- ✓ Con animaciones suaves

---

Última actualización: 13 de junio de 2026
Versión: 2.0 - Mejorada y centralizada
