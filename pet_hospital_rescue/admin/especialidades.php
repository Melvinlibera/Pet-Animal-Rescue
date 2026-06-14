<?php
session_start();
require_once("../config/sesiones.php");
require_once("../config/db.php");
require_once("../config/respuestas.php");

// Verificar que sea admin
verificarSesionAdmin();

// Generar token CSRF
$csrf_token = obtenerTokenCSRF();

$especialidades = db_fetch_all("SELECT * FROM especialidades ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo esc($csrf_token); ?>">
    <title>Especialidades - Admin</title>
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
        input, textarea {
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
<?php include_once('../includes/floating_theme_toggle.php'); ?>
<?php include('sidebar.php'); ?>
<div class="main">
    <div class="card">
        <h2>Especialidades</h2>
        <button class="btn-add" onclick="abrirAdd()"><i class='bx bx-plus'></i> Agregar Especialidad</button>
        <table>
            <tr>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Precio</th>
                <th>Acciones</th>
            </tr>
            <?php foreach($especialidades as $e): ?>
            <tr>
                <td><?= esc($e['nombre']) ?></td>
                <td><?= esc($e['descripcion']) ?></td>
                <td>$<?= esc($e['precio']) ?></td>
                <td>
                    <button onclick="abrirEdit(<?= esc($e['id']) ?>,'<?= esc($e['nombre']) ?>','<?= esc($e['descripcion']) ?>',<?= esc($e['precio']) ?>)"><i class='bx bx-edit'></i></button>
                    <button onclick="eliminar(<?= esc($e['id']) ?>)"><i class='bx bx-trash'></i></button>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>

<!-- MODAL AGREGAR ESPECIALIDAD -->
<div id="modalAdd" class="modal">
    <div class="modal-content">
        <span class="close" onclick="cerrarAdd()">&times;</span>
        <h3>Agregar Especialidad</h3>
        <form id="formAdd">
            <input type="text" name="nombre" placeholder="Nombre" required>
            <textarea name="descripcion" placeholder="Descripción" required></textarea>
            <input type="number" name="precio" placeholder="Precio" step="0.01" required>
            <button type="submit">Agregar</button>
        </form>
    </div>
</div>

<!-- MODAL EDITAR ESPECIALIDAD -->
<div id="modalEdit" class="modal">
    <div class="modal-content">
        <span class="close" onclick="cerrarEdit()">&times;</span>
        <h3>Editar Especialidad</h3>
        <form id="formEdit">
            <input type="hidden" id="idEdit" name="id">
            <input type="text" id="nombreEdit" name="nombre" placeholder="Nombre" required>
            <textarea id="descripcionEdit" name="descripcion" placeholder="Descripción" required></textarea>
            <input type="number" id="precioEdit" name="precio" placeholder="Precio" step="0.01" required>
            <button type="submit">Actualizar</button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
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

// ====================================
// MODAL: AGREGAR ESPECIALIDAD
// ====================================
function abrirAdd() {
    document.getElementById('modalAdd').style.display = 'flex';
}

function cerrarAdd() {
    document.getElementById('modalAdd').style.display = 'none';
    document.getElementById('formAdd').reset();
}

// ====================================
// MODAL: EDITAR ESPECIALIDAD
// ====================================
function abrirEdit(id, nombre, descripcion, precio) {
    document.getElementById('idEdit').value = id;
    document.getElementById('nombreEdit').value = nombre;
    document.getElementById('descripcionEdit').value = descripcion;
    document.getElementById('precioEdit').value = precio;
    document.getElementById('modalEdit').style.display = 'flex';
}

function cerrarEdit() {
    document.getElementById('modalEdit').style.display = 'none';
    document.getElementById('formEdit').reset();
}

// ====================================
// ELIMINAR ESPECIALIDAD
// ====================================
function eliminar(id) {
    if (confirm('¿Estás seguro de que deseas eliminar esta especialidad?')) {
        const formData = new FormData();
        formData.append('id', id);
        
        axios.post('./ajax/eliminar_especialidad.php', formData)
            .then(res => {
                if (res.data.success) {
                    alert('Especialidad eliminada');
                    location.reload();
                } else {
                    alert('Error: ' + res.data.message);
                }
            })
            .catch(err => {
                alert('Error al eliminar');
                console.error(err);
            });
    }
}

// ====================================
// FORMULARIO: AGREGAR ESPECIALIDAD
// ====================================
const formAdd = document.getElementById('formAdd');
if(formAdd){
    formAdd.onsubmit = e => {
        e.preventDefault();
        axios.post('./ajax/agregar_especialidad.php', new FormData(formAdd))
            .then(res => {
                if(res.data.success) {
                    alert('Especialidad agregada correctamente');
                    cerrarAdd();
                    location.reload();
                } else {
                    alert('Error: ' + res.data.message);
                }
            })
            .catch(err => {
                alert('Error al agregar');
                console.error(err);
            });
    }
}

// ====================================
// FORMULARIO: EDITAR ESPECIALIDAD
// ====================================
const formEdit = document.getElementById('formEdit');
if(formEdit){
    formEdit.onsubmit = e => {
        e.preventDefault();
        axios.post('./ajax/editar_especialidad.php', new FormData(formEdit))
            .then(res => {
                if(res.data.success) {
                    alert('Especialidad actualizada correctamente');
                    cerrarEdit();
                    location.reload();
                } else {
                    alert('Error: ' + res.data.message);
                }
            })
            .catch(err => {
                alert('Error al actualizar');
                console.error(err);
            });
    }
}

// ====================================
// CERRAR MODAL AL HACER CLICK FUERA
// ====================================
window.onclick = e => {
    const modalAdd = document.getElementById('modalAdd');
    const modalEdit = document.getElementById('modalEdit');
    
    if(e.target === modalAdd) {
        cerrarAdd();
    }
    if(e.target === modalEdit) {
        cerrarEdit();
    }
}
</script>

<!-- Tema oscuro -->
<?php include_once('../includes/floating_theme_toggle.php'); ?>

</body>
</html>