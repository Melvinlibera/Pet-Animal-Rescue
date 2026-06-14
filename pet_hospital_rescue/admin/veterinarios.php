<?php
/**
 * GESTIÓN DE DOCTORES - PANEL ADMINISTRATIVO
 *
 * Funcionalidad:
 * - Listar todos los veterinarios registrados en el sistema
 * - Mostrar información: nombre del veterinario y especialidad médica
 * - Agregar nuevos veterinarios con enlace automático a especialidad
 * - Editar información del veterinario y cambiar especialidad
 * - Eliminar veterinarios del sistema
 *
 * Relaciones importantes:
 * - Cada veterinario está asociado a UNA especialidad médica
 * - Los veterinarios son usuarios del sistema (rol = 'veterinario')
 * - La relación veterinario-especialidad es crítica para el agendamiento
 *
 * Formularios incluidos:
 * - Modal de agregar veterinario: nombre, cédula, correo, especialidad
 * - Modal de editar veterinario: nombre y especialidad editable
 * - Funciones AJAX para operaciones CRUD
 *
 * Proceso de creación de veterinario:
 * 1. Se crea el usuario con rol 'veterinario'
 * 2. Se crea el registro en tabla veterinarios
 * 3. Se enlaza automáticamente con la especialidad seleccionada
 * 4. Contraseña por defecto: 123456 (debe cambiarse al primer login)
 *
 * Validaciones implementadas:
 * - Campos obligatorios en formularios
 * - Formato de cédula dominicana (11 dígitos)
 * - Correo electrónico válido y único
 * - Especialidad debe existir en el sistema
 * - Contraseña mínimo 6 caracteres
 *
 * Seguridad:
*/

session_start();
require_once '../config/sesiones.php';
require_once '../config/db.php';
require_once '../config/respuestas.php';

verificarSesionAdmin();

$csrf_token = obtenerTokenCSRF();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo esc($csrf_token); ?>">
    <title>Gestionar Veterinarioes - Admin</title>
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
        <h2>Veterinarioes</h2>
        <button class="btn-add" onclick="abrirModalAdd()"><i class='bx bx-plus'></i> Agregar Veterinario</button>
        <?php
        // Obtener todos los veterinarios y su especialidad usando los helpers db_* recién creados
        $veterinarios = db_fetch_all("SELECT d.id, u.nombre, u.apellido, u.cedula, u.telefono, u.correo, u.genero, u.rol, e.nombre AS especialidad, d.id_especialidad FROM veterinarioes d LEFT JOIN usuarios u ON d.id_usuario = u.id LEFT JOIN especialidades e ON d.id_especialidad = e.id ORDER BY d.id DESC");
        ?>
        <table>
            <tr>
                <th>Nombre</th>
                <th>Cédula</th>
                <th>Teléfono</th>
                <th>Correo</th>
                <th>Género</th>
                <th>Rol</th>
                <th>Especialidad</th>
                <th>Acciones</th>
            </tr>
            <?php foreach($veterinarios as $d): ?>
            <tr>
                <td><?= htmlspecialchars($d['nombre'] . ' ' . ($d['apellido'] ?? '')) ?></td>
                <td><?= htmlspecialchars($d['cedula'] ?? '-') ?></td>
                <td><?= htmlspecialchars($d['telefono'] ?? '-') ?></td>
                <td><?= htmlspecialchars($d['correo'] ?? '-') ?></td>
                <td><?= htmlspecialchars($d['genero'] ?? '-') ?></td>
                <td><?= htmlspecialchars($d['rol'] ?? '-') ?></td>
                <td><?= htmlspecialchars($d['especialidad'] ?? '-') ?></td>
                <td>
                    <button onclick="abrirModalEdit(
                        <?= $d['id'] ?>,
                        '<?= addslashes($d['nombre'] . ' ' . ($d['apellido'] ?? '')) ?>',
                        '<?= addslashes($d['cedula'] ?? '') ?>',
                        '<?= addslashes($d['telefono'] ?? '') ?>',
                        '<?= addslashes($d['correo'] ?? '') ?>',
                        '<?= addslashes($d['genero'] ?? '') ?>',
                        '<?= addslashes($d['rol'] ?? '') ?>',
                        <?= $d['id_especialidad'] ?>
                    )"><i class='bx bx-edit'></i></button>
                    <button onclick="eliminarVeterinario(<?= $d['id'] ?>)"><i class='bx bx-trash'></i></button>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>

<!-- ================= MODAL AGREGAR ================= -->
<!-- ================= MODAL EDITAR ================= -->
<div id="modalEdit" class="modal">
    <div class="modal-content">
        <span class="close" onclick="cerrarModalEdit()">&times;</span>
        <h3>Editar Veterinario</h3>
        <form id="formEditVeterinario">
            <input type="hidden" id="editId" name="id">
            <div class="form-row">
                <input type="text" id="editNombre" name="nombre" placeholder="Nombre" required>
                <input type="text" id="editApellido" name="apellido" placeholder="Apellido" required>
            </div>
            <input type="text" id="editCedula" name="cedula" placeholder="Cédula (XXX-XXXXXXX-X)" required>
            <input type="tel" id="editTelefono" name="telefono" placeholder="Teléfono (10 dígitos)" required>
            <input type="email" id="editCorreo" name="correo" placeholder="Correo electrónico" required>
            <select id="editGenero" name="genero" required>
                <option value="">Género</option>
                <option value="masculino">Masculino</option>
                <option value="femenino">Femenino</option>
            </select>
            <select id="editRol" name="rol" required>
                <option value="veterinario">Veterinario</option>
                <option value="admin">Administrador</option>
            </select>
            <select id="editEspecialidad" name="id_especialidad" required>
                <option value="">Especialidad</option>
                <?php
                $especialidades = db_fetch_all("SELECT id, nombre FROM especialidades ORDER BY nombre ASC");
                foreach($especialidades as $esp){
                    echo '<option value="'.htmlspecialchars($esp['id']).'">'.htmlspecialchars($esp['nombre']).'</option>';
                }
                ?>
            </select>
            <input type="password" id="editPassword" name="password" placeholder="Nueva contraseña (opcional)">
            <input type="password" id="editConfirmPassword" name="confirm_password" placeholder="Confirmar contraseña">
            <button type="submit" class="btn-add">Guardar Cambios</button>
        </form>
    </div>
</div>
<div id="modalAdd" class="modal">
    <div class="modal-content">
        <span class="close" onclick="cerrarModalAdd()">&times;</span>
        <h3>Agregar Veterinario</h3>

        <form id="formAddVeterinario">
            <div class="form-row">
                <input type="text" name="nombre" placeholder="Nombre" required>
                <input type="text" name="apellido" placeholder="Apellido" required>
            </div>
            <input type="text" name="cedula" placeholder="Cédula (XXX-XXXXXXX-X)" required>
            <input type="tel" name="telefono" placeholder="Teléfono (10 dígitos)" required>
            <input type="email" name="correo" placeholder="Correo electrónico" required>
            <select name="genero" required>
                <option value="">Selecciona Género</option>
                <option value="masculino">Masculino</option>
                <option value="femenino">Femenino</option>
            </select>
            <select name="rol" required>
                <option value="">Selecciona Rol</option>
                <option value="user">Usuario</option>
                <option value="veterinario">Veterinario</option>
                <option value="admin">Admin</option>
            </select>
            <div class="form-row">
                <div class="password-field">
                    <input type="password" name="password" id="addVeterinarioPassword" placeholder="Contraseña (mínimo 6 caracteres)" required>
                    <button type="button" class="eye-btn" onclick="togglePassword('addVeterinarioPassword')" title="Mostrar/Ocultar contraseña"><i class='bx bx-hide'></i></button>
                </div>
                <div class="password-field">
                    <input type="password" name="confirm_password" id="addVeterinarioConfirmPassword" placeholder="Confirmar contraseña" required>
                    <button type="button" class="eye-btn" onclick="togglePassword('addVeterinarioConfirmPassword')" title="Mostrar/Ocultar contraseña"><i class='bx bx-hide'></i></button>
                </div>
            </div>
            <select name="id_especialidad" required>
                <option value="">Selecciona Especialidad</option>
                <?php
                $especialidades = db_fetch_all("SELECT * FROM especialidades");
                foreach($especialidades as $e){
                    echo "<option value='{$e['id']}'>{$e['nombre']}</option>";
                }
                ?>
            </select>

            <button type="submit">Agregar</button>
        </form>
    </div>
</div>

<!-- ================= MODAL EDITAR ================= -->
<div id="modalEdit" class="modal">
    <div class="modal-content">
        <span class="close" onclick="cerrarModalEdit()">&times;</span>
        <h3>Editar Veterinario</h3>

        <form id="formEditVeterinario">
            <input type="hidden" name="id" id="editId">
            <div class="form-row">
                <input type="text" name="nombre" id="editNombre" placeholder="Nombre" required>
                <input type="text" name="apellido" id="editApellido" placeholder="Apellido" required>
            </div>
            <input type="text" name="cedula" id="editCedula" placeholder="Cédula (XXX-XXXXXXX-X)" required>
            <input type="tel" name="telefono" id="editTelefono" placeholder="Teléfono (10 dígitos)" required>
            <input type="email" name="correo" id="editCorreo" placeholder="Correo electrónico" required>
            <select name="genero" id="editGenero" required>
                <option value="">Selecciona Género</option>
                <option value="masculino">Masculino</option>
                <option value="femenino">Femenino</option>
            </select>
            <select name="rol" id="editRol" required>
                <option value="">Selecciona Rol</option>
                <option value="user">Usuario</option>
                <option value="veterinario">Veterinario</option>
                <option value="admin">Admin</option>
            </select>
            <select name="id_especialidad" id="editEspecialidad" required>
                <option value="">Selecciona Especialidad</option>
                <?php
                $especialidades = db_fetch_all("SELECT * FROM especialidades");
                foreach($especialidades as $e){
                    echo "<option value='{$e['id']}'>{$e['nombre']}</option>";
                }
                ?>
            </select>
            <div class="form-row">
                <div class="password-field">
                    <input type="password" name="password" id="editVeterinarioPassword" placeholder="Nueva contraseña (opcional)">
                    <button type="button" class="eye-btn" onclick="togglePassword('editVeterinarioPassword')" title="Mostrar/Ocultar contraseña"><i class='bx bx-hide'></i></button>
                </div>
                <div class="password-field">
                    <input type="password" name="confirm_password" id="editVeterinarioConfirmPassword" placeholder="Confirmar contraseña">
                    <button type="button" class="eye-btn" onclick="togglePassword('editVeterinarioConfirmPassword')" title="Mostrar/Ocultar contraseña"><i class='bx bx-hide'></i></button>
                </div>
            </div>
            <button type="submit">Guardar cambios</button>
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

// MODALES
function abrirModalAdd(){ document.getElementById('modalAdd').style.display='flex'; }
function cerrarModalAdd(){ document.getElementById('modalAdd').style.display='none'; }

function abrirModalEdit(id, nombreCompleto, cedula, telefono, correo, genero, rol, idEsp){
    const parts = nombreCompleto.trim().split(' ');
    const apellido = parts.length > 1 ? parts.slice(1).join(' ') : '';
    const nombre = parts[0] || '';

    document.getElementById('editId').value = id;
    document.getElementById('editNombre').value = nombre;
    document.getElementById('editApellido').value = apellido;
    document.getElementById('editCedula').value = cedula;
    document.getElementById('editTelefono').value = telefono;
    document.getElementById('editCorreo').value = correo;
    document.getElementById('editGenero').value = genero;
    document.getElementById('editRol').value = rol;
    document.getElementById('editEspecialidad').value = idEsp;
    document.getElementById('modalEdit').style.display='flex';
}
function cerrarModalEdit(){ document.getElementById('modalEdit').style.display='none'; }

function togglePassword(inputId){
    const input = document.getElementById(inputId);
    const btn = input.nextElementSibling;
    if(!input) return;
    input.type = input.type === 'password' ? 'text' : 'password';
    btn.innerHTML = input.type === 'password' ? "<i class='bx bx-hide'></i>" : "<i class='bx bx-show'></i>";
}

// AGREGAR
document.getElementById('formAddVeterinario').addEventListener('submit', function(e){
    e.preventDefault();
    const password = this.password.value;
    const confirmPassword = this.confirm_password.value;

    if(password !== confirmPassword){
        alert('Las contraseñas no coinciden');
        return;
    }

    const form = new FormData(this);
    const fullName = `${form.get('nombre').trim()} ${form.get('apellido').trim()}`.trim();
    form.set('nombre', fullName);

    axios.post('ajax/agregar_veterinario.php', form)
    .then(res=>{
        alert(res.data.message);
        location.reload();
    }).catch(err=>console.error(err));
});

// EDITAR
document.getElementById('formEditVeterinario').addEventListener('submit', function(e){
    e.preventDefault();
    const password = this.password.value;
    const confirmPassword = this.confirm_password.value;

    if(password && password !== confirmPassword){
        alert('Las contraseñas no coinciden');
        return;
    }

    const form = new FormData(this);
    const fullName = `${form.get('nombre').trim()} ${form.get('apellido').trim()}`.trim();
    form.set('nombre', fullName);

    axios.post('ajax/editar_veterinario.php', form)
    .then(res=>{
        alert(res.data.message);
        location.reload();
    }).catch(err=>console.error(err));
});

// ELIMINAR
function eliminarVeterinario(id){
    if(confirm("¿Eliminar este veterinario?")){
        const formData = new FormData();
        formData.append('id', id);
        
        axios.post('ajax/eliminar_veterinario.php', formData)
        .then(res=>{
            alert(res.data.message);
            location.reload();
        }).catch(err=>console.error(err));
    }
}
</script>

</body>
</html>