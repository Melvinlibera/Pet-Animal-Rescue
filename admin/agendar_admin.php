<?php
session_start();
require_once("../config/sesiones.php");
require_once("../config/db.php");
require_once("../config/respuestas.php");

verificarSesionAdmin();

$csrf_token = obtenerTokenCSRF();

if($_POST){
    validarTokenPost();
    
    $id_usuario = $_POST['usuario'];
    $id_veterinario = $_POST['veterinario'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];

    $stmt = $pdo->prepare("INSERT INTO citas (id_usuario, id_veterinario, fecha, hora, estado) VALUES (?,?,?,?,?)");
    $stmt->execute([$id_usuario,$id_veterinario,$fecha,$hora,'pendiente']);
    
    registrarLog('agendar_cita_admin', ['usuario' => $id_usuario, 'veterinario' => $id_veterinario]);

    echo "<p style='color:green'>Cita agendada correctamente</p>";
}
$usuarios = $pdo->query("SELECT id,nombre FROM usuarios")->fetchAll(PDO::FETCH_ASSOC);
$especialidades = $pdo->query("SELECT * FROM especialidades")->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Agendar Cita (Admin)</h2>
<form method="POST">
<input type="hidden" name="csrf_token" value="<?php echo esc($csrf_token); ?>">
<select name="usuario" required>
<option value="">Selecciona Usuario</option>
<?php foreach($usuarios as $u): ?>
<option value="<?php echo esc($u['id']);?>"><?php echo esc($u['nombre']);?></option>
<?php endforeach;?>
</select>

<select name="especialidad" id="especialidad" required>
<option value="">Selecciona Especialidad</option>
<?php foreach($especialidades as $e): ?>
<option value="<?php echo $e['id'];?>"><?php echo $e['nombre'];?></option>
<?php endforeach;?>
</select>

<select name="veterinario" id="veterinario" required>
<option value="">Selecciona Veterinario</option>
</select>

<input type="date" name="fecha" required>
<input type="time" name="hora" required>
<button type="submit">Agendar</button>
</form>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
document.getElementById('especialidad').addEventListener('change', function(){
    var espId = this.value;
    var veterinarioSelect = document.getElementById('veterinario');
    veterinarioSelect.innerHTML = "<option value=''>Cargando...</option>";

    const formData = new FormData();
    formData.append('id_especialidad', espId);

    axios.post('ajax/get_veterinarios.php', formData)
        .then(res=>{
            var docs = res.data;
            veterinarioSelect.innerHTML = "<option value=''>Selecciona Veterinario</option>";
            docs.forEach(d=>{
                var opt = document.createElement('option');
                opt.value = d.id;
                opt.textContent = d.nombre;
                veterinarioSelect.appendChild(opt);
            });
        }).catch(err=>{
            veterinarioSelect.innerHTML="<option value=''>Error al cargar</option>";
        });
});
</script>