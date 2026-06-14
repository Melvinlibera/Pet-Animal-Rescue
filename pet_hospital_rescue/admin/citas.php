<?php
/**
 * GESTIÓN DE CITAS - PANEL ADMINISTRATIVO
 *
 * Funcionalidad:
 * - Listar todas las citas médicas del sistema
 * - Mostrar información completa: mascota, veterinario, especialidad, fecha, hora, estado
 * - Filtrar citas por estado (pendiente, confirmada, cancelada)
 * - Cambiar estado de citas (confirmar, cancelar)
 * - Ver detalles completos de cada cita
 *
 * Estados de cita:
 * - pendiente: Cita agendada pero no confirmada
 * - confirmada: Cita confirmada por administrador o veterinario
 * - cancelada: Cita cancelada por mascota, veterinario o admin
 *
 * Información mostrada por cita:
 * - ID único de la cita
 * - Nombre del mascota (usuario)
 * - Nombre del veterinario asignado
 * - Especialidad médica
 * - Fecha y hora de la cita
 * - Estado actual de la cita
 * - Fecha de creación del registro
 *
 * Operaciones disponibles:
 * - Cambiar estado de cita mediante AJAX
 * - Filtrado visual por estado
 * - Vista ordenada por fecha (más recientes primero)
 *
 * Relaciones consultadas:
 * - citas ↔ usuarios (id_usuario)
 * - citas ↔ veterinarios (id_veterinario)
 * - citas ↔ especialidades (id_especialidad)
 *
 * Seguridad:
 * - Validación de sesión y rol de administrador
 * - Prepared statements en consultas
 * - Control de acceso a operaciones sensibles
 */

session_start();
require_once("../config/sesiones.php");
require_once("../config/db.php");
require_once("../config/respuestas.php");

verificarSesionAdmin();

$csrf_token = obtenerTokenCSRF();

// Traer todas las citas con información relacionada
// JOIN múltiple para obtener nombres legibles en lugar de IDs
$citas = db_fetch_all("
    SELECT citas.*, usuarios.nombre AS usuario_nombre,
           veterinarioes.nombre AS veterinario_nombre,
           especialidades.nombre AS especialidad_nombre
    FROM citas
    JOIN usuarios ON citas.id_usuario = usuarios.id
    JOIN veterinarioes ON citas.id_veterinario = veterinarioes.id
    JOIN especialidades ON citas.id_especialidad = especialidades.id
    ORDER BY citas.fecha DESC, citas.hora DESC
");

// Datos para selects en posibles formularios de edición
$usuarios = db_fetch_all("SELECT id, nombre FROM usuarios");
$veterinarios = db_fetch_all("SELECT id, nombre FROM veterinarioes");
$especialidades = db_fetch_all("SELECT id, nombre FROM especialidades");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo esc($csrf_token); ?>">
    <title>Gestionar Citas - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #0a1f44;
            --secondary: #1e90ff;
            --accent: #00d4ff;
            --bg-light: #f8fafc;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--bg-light) 0%, #e3f0ff 100%);
            color: var(--primary);
            min-height: 100vh;
        }
        .main {
            margin-left: 260px;
            padding: 30px;
        }
        .card {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(12px);
            border-radius: 16px;
            padding: 28px;
            box-shadow: 0 4px 20px rgba(30, 144, 255, 0.08);
            border: 1.5px solid rgba(30, 144, 255, 0.1);
            margin-bottom: 30px;
        }
        .card h2 {
            font-size: 24px;
            font-weight: 800;
            margin-bottom: 18px;
        }
        .btn-add {
            background: linear-gradient(135deg, var(--secondary) 0%, var(--accent) 100%);
            color: #fff;
            padding: 12px 28px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            margin-bottom: 18px;
            cursor: pointer;
            transition: 0.2s;
            box-shadow: 0 2px 8px rgba(30,144,255,0.10);
        }
        .btn-add:hover {
            background: var(--primary);
            color: #fff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            background: rgba(255,255,255,0.95);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(30,144,255,0.07);
        }
        th, td {
            padding: 14px;
            border-bottom: 1px solid #e3f0ff;
            text-align: left;
        }
        th {
            background: #f0f4f8;
            color: var(--primary);
            font-weight: 700;
        }
        tr:last-child td {
            border-bottom: none;
        }
        button {
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            background: var(--primary);
            color: #fff;
            cursor: pointer;
            transition: 0.2s;
            margin-right: 5px;
        }
        button:hover {
            background: var(--secondary);
        }
        /* MODAL */
        .modal {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.6);
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        .modal-content {
            background: #fff;
            padding: 30px;
            border-radius: 16px;
            width: 400px;
            position: relative;
            box-shadow: 0 8px 32px rgba(30,144,255,0.13);
        }
        .close {
            position: absolute;
            top: 10px;
            right: 15px;
            cursor: pointer;
            font-size: 20px;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        @media (max-width: 900px) {
            .main { margin-left: 0; padding: 10px; }
        }
    </style>
</head>
<body>
<?php include('sidebar.php'); ?>
<div class="main">
    <div class="card">
        <h2>Gestionar Citas</h2>
        <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;">
            <div>
                <button class="btn-add" onclick="abrirAdd()"><i class='bx bx-plus'></i> Agregar Cita</button>
            </div>
            <div>
                <select id="filtroEstado" onchange="filtrarEstado()" style="padding:8px;border-radius:8px;">
                    <option value="">Todos los estados</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="confirmada">Confirmada</option>
                    <option value="cancelada">Cancelada</option>
                </select>
            </div>
        </div>
        <div style="overflow-x:auto;">
        <table id="tablaCitas">
            <thead>
            <tr>
                <th>Usuario</th>
                <th>Veterinario</th>
                <th>Especialidad</th>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($citas as $c): ?>
            <tr data-estado="<?= $c['estado'] ?>">
                <td><?= htmlspecialchars($c['usuario_nombre']) ?></td>
                <td><?= htmlspecialchars($c['veterinario_nombre']) ?></td>
                <td><?= htmlspecialchars($c['especialidad_nombre']) ?></td>
                <td><?= htmlspecialchars($c['fecha']) ?></td>
                <td><?= date('h:i A', strtotime($c['hora'])) ?></td>
                <td><span class="badge badge-<?= $c['estado'] ?>"><?= ucfirst($c['estado']) ?></span></td>
                <td>
                    <button title="Editar" onclick="abrirEdit(<?= $c['id'] ?>,'<?= $c['fecha'] ?>','<?= $c['hora'] ?>','<?= $c['estado'] ?>','<?= $c['id_usuario'] ?>','<?= $c['id_veterinario'] ?>','<?= $c['id_especialidad'] ?>')"><i class='bx bx-edit'></i></button>
                    <button title="Eliminar" onclick="eliminarCita(<?= $c['id'] ?>)"><i class='bx bx-trash'></i></button>
                    <?php if($c['estado']==='pendiente'): ?>
                        <button title="Confirmar" onclick="cambiarEstado(<?= $c['id'] ?>,'confirmada')"><i class='bx bx-check'></i></button>
                        <button title="Cancelar" onclick="cambiarEstado(<?= $c['id'] ?>,'cancelada')"><i class='bx bx-x'></i></button>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>

<!-- ================= MODAL AGREGAR ================= -->
<div id="modalAdd" class="modal">
    <div class="modal-content">
        <span class="close" onclick="cerrarAdd()">&times;</span>
        <h3>Agregar Cita</h3>
        <form id="formAdd">
            <select name="id_usuario" required>
                <option value="">Mascota</option>
                <?php foreach($usuarios as $u): ?>
                <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="id_veterinario" required>
                <option value="">Veterinario</option>
                <?php foreach($veterinarios as $d): ?>
                <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="id_especialidad" required>
                <option value="">Especialidad</option>
                <?php foreach($especialidades as $e): ?>
                <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
            <input type="date" name="fecha" required>
            <input type="time" name="hora" required>
            <button class="btn-add" type="submit">Guardar</button>
        </form>
    </div>
</div>

<!-- ================= MODAL EDITAR ================= -->
<div id="modalEdit" class="modal">
    <div class="modal-content">
        <span class="close" onclick="cerrarEdit()">&times;</span>
        <h3>Editar Cita</h3>
        <form id="formEdit">
            <input type="hidden" name="id" id="editId">
            <select name="id_usuario" id="editUsuario" required>
                <option value="">Mascota</option>
                <?php foreach($usuarios as $u): ?>
                <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="id_veterinario" id="editVeterinario" required>
                <option value="">Veterinario</option>
                <?php foreach($veterinarios as $d): ?>
                <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="id_especialidad" id="editEspecialidad" required>
                <option value="">Especialidad</option>
                <?php foreach($especialidades as $e): ?>
                <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
            <input type="date" name="fecha" id="editFecha" required>
            <input type="time" name="hora" id="editHora" required>
            <select name="estado" id="editEstado">
                <option value="pendiente">Pendiente</option>
                <option value="confirmada">Confirmada</option>
                <option value="cancelada">Cancelada</option>
            </select>
            <button class="btn-add" type="submit">Actualizar</button>
        </form>
    </div>
</div>
</body>
</html>



<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<style>
.badge-pendiente {background:#ffe082;color:#b26a00;padding:4px 10px;border-radius:8px;font-weight:600;}
.badge-confirmada {background:#b9f6ca;color:#00695c;padding:4px 10px;border-radius:8px;font-weight:600;}
.badge-cancelada {background:#ff8a80;color:#b71c1c;padding:4px 10px;border-radius:8px;font-weight:600;}
.toast {
    position: fixed;
    bottom: 30px;
    right: 30px;
    background: #1e90ff;
    color: #fff;
    padding: 16px 32px;
    border-radius: 12px;
    font-weight: 600;
    box-shadow: 0 2px 12px rgba(30,144,255,0.15);
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s, bottom 0.3s;
    z-index: 99999;
}
.toast.show {
    opacity: 1;
    bottom: 60px;
    pointer-events: auto;
}
@media (max-width: 600px) {
    .modal-content { width: 98vw !important; padding: 10px !important; }
    .main { padding: 2vw !important; }
    .card { padding: 10px !important; }
    table, th, td { font-size: 13px !important; }
    .btn-add { width: 100%; margin-bottom: 10px; }
}
</style>
<div id="toast" class="toast"></div>

<script>
// ====================================
// AGREGAR TOKEN CSRF A TODOS LOS AXIOS
// ====================================
const csrfMeta = document.querySelector('meta[name="csrf-token"]');
const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

axios.interceptors.request.use(config => {
    if (csrfToken && ['post', 'put', 'delete', 'patch'].includes(config.method)) {
        config.headers['X-CSRF-TOKEN'] = csrfToken;
        if (config.data instanceof FormData) {
            config.data.append('csrf_token', csrfToken);
        }
    }
    return config;
});

// MODALES Y FEEDBACK
const modalAdd = document.getElementById('modalAdd');
const modalEdit = document.getElementById('modalEdit');
const formAdd = document.getElementById('formAdd');
const formEdit = document.getElementById('formEdit');
const editId = document.getElementById('editId');
const editUsuario = document.getElementById('editUsuario');
const editVeterinario = document.getElementById('editVeterinario');
const editEspecialidad = document.getElementById('editEspecialidad');
const editFecha = document.getElementById('editFecha');
const editHora = document.getElementById('editHora');
const editEstado = document.getElementById('editEstado');
const toast = document.getElementById('toast');

function showToast(msg, color='#1e90ff') {
    toast.textContent = msg;
    toast.style.background = color;
    toast.classList.add('show');
    setTimeout(()=>toast.classList.remove('show'), 2500);
}

function abrirAdd(){
    modalAdd.style.display='flex';
    setTimeout(()=>modalAdd.querySelector('select, input').focus(), 100);
}
function cerrarAdd(){modalAdd.style.display='none'}

function abrirEdit(id,fecha,hora,estado,id_usuario,id_veterinario,id_especialidad){
        editId.value=id;
        editFecha.value=fecha;
        editHora.value=hora;
        editEstado.value=estado;
        editUsuario.value=id_usuario;
        editVeterinario.value=id_veterinario;
        editEspecialidad.value=id_especialidad;
        modalEdit.style.display='flex';
        setTimeout(()=>editFecha.focus(), 100);
}
function cerrarEdit(){modalEdit.style.display='none'}

// AGREGAR
formAdd.onsubmit=async e=>{
        e.preventDefault();
        const btn = formAdd.querySelector('button[type="submit"]');
        btn.disabled = true; btn.textContent = 'Guardando...';
        try {
            const r = await axios.post('ajax/agregar_cita.php', new FormData(formAdd));
            showToast(r.data.message, '#00b894');
            setTimeout(()=>location.reload(), 1200);
        } catch(err) {
            showToast('Error al agregar cita', '#d63031');
        } finally {
            btn.disabled = false; btn.textContent = 'Guardar';
        }
}

// EDITAR
formEdit.onsubmit=async e=>{
        e.preventDefault();
        const btn = formEdit.querySelector('button[type="submit"]');
        btn.disabled = true; btn.textContent = 'Actualizando...';
        try {
            const r = await axios.post('ajax/editar_cita.php', new FormData(formEdit));
            showToast(r.data.message, '#00b894');
            setTimeout(()=>location.reload(), 1200);
        } catch(err) {
            showToast('Error al editar cita', '#d63031');
        } finally {
            btn.disabled = false; btn.textContent = 'Actualizar';
        }
}

// ELIMINAR
async function eliminarCita(id){
        if(confirm("¿Desea eliminar esta cita?")){
            try {
                showToast('Eliminando...', '#636e72');
                const formData = new FormData();
                formData.append('id', id);
                const res = await axios.post('ajax/eliminar_cita.php', formData);
                showToast(res.data.message, '#00b894');
                setTimeout(()=>location.reload(), 1200);
            } catch(err) {
                showToast('Error al eliminar', '#d63031');
            }
        }
}

// CAMBIAR ESTADO
async function cambiarEstado(id, estado){
        try {
            showToast('Actualizando estado...', '#636e72');
            const formData = new FormData();
            formData.append('id', id);
            formData.append('estado', estado);
            const res = await axios.post('ajax/cambiar_estado_cita.php', formData);
            showToast(res.data.message, '#00b894');
            setTimeout(()=>location.reload(), 1200);
        } catch(err) {
            showToast('Error al cambiar estado', '#d63031');
        }
}

// FILTRAR POR ESTADO
function filtrarEstado(){
        const estado = document.getElementById('filtroEstado').value;
        const filas = document.querySelectorAll('#tablaCitas tbody tr');
        filas.forEach(fila=>{
                if(!estado || fila.getAttribute('data-estado')===estado){
                        fila.style.display='';
                }else{
                        fila.style.display='none';
                }
        });
}

// Cerrar modales con ESC
window.addEventListener('keydown', e=>{
    if(e.key==='Escape') { cerrarAdd(); cerrarEdit(); }
});

// Cerrar modal al hacer click fuera
[modalAdd,modalEdit].forEach(modal=>{
    modal.addEventListener('click',e=>{if(e.target===modal) modal.style.display='none';});
});
</script>

</body>
</html>