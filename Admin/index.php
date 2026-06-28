<?php
/**
 * Admin Panel for User Management (ins6.md)
 * Location: /Admin/index.php
 */
session_start();

require_once dirname(__FILE__) . '/../src/Infrastructure/Database/DatabaseConnection.php';
require_once dirname(__FILE__) . '/../src/Infrastructure/Database/SystemLog.php';

$db = DatabaseConnection::getInstance();

// 1. Auth check
if (!isset($_SESSION['user'])) {
    header('Location: ../login');
    exit();
}

$currentUserId = (int)$_SESSION['user']['id'];
$resAdmin = mysqli_query($db, "SELECT `is_admin` FROM `biartet_users` WHERE `id` = $currentUserId AND `fecha_eliminacion` IS NULL LIMIT 1");
$adminRow = $resAdmin ? mysqli_fetch_assoc($resAdmin) : null;

if (!$adminRow || (int)$adminRow['is_admin'] !== 1) {
    $_SESSION['error_message'] = 'Acceso denegado. Solo administradores pueden ingresar al Panel Admin.';
    header('Location: ../');
    exit();
}

// 2. Action Handlers
$action = isset($_GET['action']) ? trim($_GET['action']) : '';
$msg_success = isset($_SESSION['admin_success']) ? $_SESSION['admin_success'] : null;
$msg_error = isset($_SESSION['admin_error']) ? $_SESSION['admin_error'] : null;
unset($_SESSION['admin_success']);
unset($_SESSION['admin_error']);

// Create / Save User
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($action === 'create' || $action === 'edit')) {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
    $nombre = isset($_POST['nombre_completo']) ? trim($_POST['nombre_completo']) : '';
    $email = isset($_POST['correo_electronico']) ? trim($_POST['correo_electronico']) : '';
    $dialview_user = isset($_POST['usuario_dialview']) ? trim($_POST['usuario_dialview']) : '';
    $dialview_pass = isset($_POST['contrasena_dialview']) ? trim($_POST['contrasena_dialview']) : '';
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;
    
    // validation
    if (empty($nombre) || empty($email) || empty($dialview_user) || empty($dialview_pass)) {
        $_SESSION['admin_error'] = 'Todos los campos son obligatorios.';
        header('Location: ' . ($id ? '?action=show_edit&id=' . $id : '?action=show_create'));
        exit();
    }
    
    $nombre_esc = mysqli_real_escape_string($db, $nombre);
    $email_esc = mysqli_real_escape_string($db, $email);
    $dv_user_esc = mysqli_real_escape_string($db, $dialview_user);
    $dv_pass_esc = mysqli_real_escape_string($db, $dialview_pass);
    
    if ($id) {
        // Edit Mode
        // Check duplicate email
        $chk = mysqli_query($db, "SELECT `id` FROM `biartet_users` WHERE `username` = '$email_esc' AND `id` != $id LIMIT 1");
        if ($chk && mysqli_num_rows($chk) > 0) {
            $_SESSION['admin_error'] = 'El correo electrónico ya está registrado por otro usuario.';
            header('Location: ?action=show_edit&id=' . $id);
            exit();
        }
        
        $sql = "UPDATE `biartet_users` SET 
            `username` = '$email_esc', 
            `nombre_completo` = '$nombre_esc', 
            `usuario_dialview` = '$dv_user_esc', 
            `contrasena_dialview` = '$dv_pass_esc', 
            `is_admin` = $is_admin,
            `fecha_actualizacion` = NOW() 
            WHERE `id` = $id";
            
        if (mysqli_query($db, $sql)) {
            SystemLog::write("Actualizó usuario en Panel Admin: " . $email);
            $_SESSION['admin_success'] = 'Usuario actualizado con éxito.';
            header('Location: index.php');
            exit();
        } else {
            $_SESSION['admin_error'] = 'Error al actualizar usuario en la base de datos: ' . mysqli_error($db);
            header('Location: ?action=show_edit&id=' . $id);
            exit();
        }
    } else {
        // Create Mode
        $pass_raw = isset($_POST['password']) ? trim($_POST['password']) : '';
        if (empty($pass_raw)) {
            $_SESSION['admin_error'] = 'La contraseña de ingreso web es obligatoria.';
            header('Location: ?action=show_create');
            exit();
        }
        
        // Check duplicate email
        $chk = mysqli_query($db, "SELECT `id` FROM `biartet_users` WHERE `username` = '$email_esc' LIMIT 1");
        if ($chk && mysqli_num_rows($chk) > 0) {
            $_SESSION['admin_error'] = 'El correo electrónico ya está registrado.';
            header('Location: ?action=show_create');
            exit();
        }
        
        $pass_hash = sha1($pass_raw);
        $sql = "INSERT INTO `biartet_users` 
            (`username`, `password`, `is_admin`, `nombre_completo`, `usuario_dialview`, `contrasena_dialview`, `fecha_creacion`, `fecha_actualizacion`) 
            VALUES ('$email_esc', '$pass_hash', $is_admin, '$nombre_esc', '$dv_user_esc', '$dv_pass_esc', NOW(), NOW())";
            
        if (mysqli_query($db, $sql)) {
            SystemLog::write("Creó usuario en Panel Admin: " . $email);
            $_SESSION['admin_success'] = 'Usuario creado con éxito.';
            header('Location: index.php');
            exit();
        } else {
            $_SESSION['admin_error'] = 'Error al insertar usuario en la base de datos: ' . mysqli_error($db);
            header('Location: ?action=show_create');
            exit();
        }
    }
}

// Change Password Handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'change_password') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $new_pass = isset($_POST['new_password']) ? trim($_POST['new_password']) : '';
    
    if (empty($new_pass) || strlen($new_pass) < 4) {
        $_SESSION['admin_error'] = 'La contraseña debe tener al menos 4 caracteres.';
        header('Location: ?action=show_password&id=' . $id);
        exit();
    }
    
    $pass_hash = sha1($new_pass);
    $pass_esc = mysqli_real_escape_string($db, $new_pass);
    
    // Also sync the dialview password as requested ("El administrador podra cambiar la contraseña... y esta sera la de dialview y web")
    $sql = "UPDATE `biartet_users` SET `password` = '$pass_hash', `contrasena_dialview` = '$pass_esc', `fecha_actualizacion` = NOW() WHERE `id` = $id";
    if (mysqli_query($db, $sql)) {
        // Fetch user email
        $resMail = mysqli_query($db, "SELECT `username` FROM `biartet_users` WHERE `id` = $id LIMIT 1");
        $mailRow = $resMail ? mysqli_fetch_assoc($resMail) : null;
        $userMail = $mailRow ? $mailRow['username'] : 'ID: ' . $id;

        SystemLog::write("Cambió contraseña para usuario: " . $userMail);
        $_SESSION['admin_success'] = 'Contraseña cambiada con éxito.';
        header('Location: index.php');
        exit();
    } else {
        $_SESSION['admin_error'] = 'Error al actualizar contraseña: ' . mysqli_error($db);
        header('Location: ?action=show_password&id=' . $id);
        exit();
    }
}

// Delete (Soft Delete) Handler
if ($action === 'delete') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    // Fetch email
    $resMail = mysqli_query($db, "SELECT `username` FROM `biartet_users` WHERE `id` = $id LIMIT 1");
    $mailRow = $resMail ? mysqli_fetch_assoc($resMail) : null;
    $userMail = $mailRow ? $mailRow['username'] : '';

    if ($id === $currentUserId) {
        $_SESSION['admin_error'] = 'No puede eliminarse a sí mismo.';
    } else {
        $sql = "UPDATE `biartet_users` SET `fecha_eliminacion` = NOW(), `fecha_actualizacion` = NOW() WHERE `id` = $id";
        if (mysqli_query($db, $sql)) {
            SystemLog::write("Eliminó usuario (soft-delete): " . $userMail);
            $_SESSION['admin_success'] = 'Usuario eliminado con éxito (soft delete).';
        } else {
            $_SESSION['admin_error'] = 'Error al eliminar usuario: ' . mysqli_error($db);
        }
    }
    header('Location: index.php');
    exit();
}

// Restore User Handler (Bonus)
if ($action === 'restore') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    $resMail = mysqli_query($db, "SELECT `username` FROM `biartet_users` WHERE `id` = $id LIMIT 1");
    $mailRow = $resMail ? mysqli_fetch_assoc($resMail) : null;
    $userMail = $mailRow ? $mailRow['username'] : '';

    $sql = "UPDATE `biartet_users` SET `fecha_eliminacion` = NULL, `fecha_actualizacion` = NOW() WHERE `id` = $id";
    if (mysqli_query($db, $sql)) {
        SystemLog::write("Restauró usuario: " . $userMail);
        $_SESSION['admin_success'] = 'Usuario restaurado con éxito.';
    } else {
        $_SESSION['admin_error'] = 'Error al restaurar usuario: ' . mysqli_error($db);
    }
    header('Location: index.php');
    exit();
}

// 3. Render HTML
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración de Usuarios</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            padding-top: 20px;
        }
        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Brand / Header -->
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; margin-bottom:2rem; gap:1rem;">
            <div>
                <h1 class="gradient-text" style="margin:0 0 0.25rem 0;">👥 Panel Admin - Usuarios</h1>
                <p style="font-size:0.9rem; color:rgba(255,255,255,0.6);">Gestión global de los usuarios de la aplicación web y credenciales Dialview.</p>
            </div>
            <div style="display:flex; gap:0.5rem;">
                <a href="../" class="btn btn-secondary">◀ Volver a la App</a>
                <?php if ($action !== 'show_create'): ?>
                    <a href="?action=show_create" class="btn btn-primary">+ Registrar Usuario</a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Feedback Alert Messages -->
        <?php if ($msg_success): ?>
            <div class="alert alert-success">
                <span>✔</span> <?php echo htmlspecialchars($msg_success); ?>
            </div>
        <?php endif; ?>
        <?php if ($msg_error): ?>
            <div class="alert alert-error">
                <span>❌</span> <?php echo htmlspecialchars($msg_error); ?>
            </div>
        <?php endif; ?>

        <?php if ($action === 'show_create' || $action === 'show_edit'): 
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            $user = null;
            if ($id) {
                $res = mysqli_query($db, "SELECT * FROM `biartet_users` WHERE `id` = $id LIMIT 1");
                $user = $res ? mysqli_fetch_assoc($res) : null;
            }
        ?>
            <!-- Form Card -->
            <div class="glass-card" style="max-width: 700px; margin: 0 auto;">
                <h2 class="gradient-text" style="margin-top:0; margin-bottom:1.5rem;">
                    <?php echo $id ? '✏ Editar Usuario' : '➕ Registrar Nuevo Usuario'; ?>
                </h2>
                
                <form action="?action=<?php echo $id ? 'edit' : 'create'; ?>" method="POST">
                    <?php if ($id): ?>
                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label class="form-label" for="nombre_completo">Nombre Completo *</label>
                        <input type="text" id="nombre_completo" name="nombre_completo" class="form-control" value="<?php echo $user ? htmlspecialchars($user['nombre_completo']) : ''; ?>" required placeholder="Ej: John Doe">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="correo_electronico">Correo Electrónico (Email de Ingreso) *</label>
                        <input type="email" id="correo_electronico" name="correo_electronico" class="form-control" value="<?php echo $user ? htmlspecialchars($user['username']) : ''; ?>" required placeholder="Ej: john@gmail.com">
                    </div>

                    <?php if (!$id): ?>
                        <div class="form-group">
                            <label class="form-label" for="password">Contraseña de Ingreso Web *</label>
                            <input type="text" id="password" name="password" class="form-control" required placeholder="Defina la clave de acceso web inicial" value="5841263">
                        </div>
                    <?php endif; ?>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem;">
                        <div class="form-group">
                            <label class="form-label" for="usuario_dialview">Usuario de Software Dialview *</label>
                            <input type="text" id="usuario_dialview" name="usuario_dialview" class="form-control" value="<?php echo $user ? htmlspecialchars($user['usuario_dialview']) : ''; ?>" required placeholder="Ej: john_dv">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="contrasena_dialview">Contraseña de Software Dialview *</label>
                            <input type="text" id="contrasena_dialview" name="contrasena_dialview" class="form-control" value="<?php echo $user ? htmlspecialchars($user['contrasena_dialview']) : ''; ?>" required placeholder="Ej: dv1234">
                        </div>
                    </div>

                    <div class="form-group" style="margin-top: 1rem;">
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-size: 0.95rem; font-weight: 600;">
                            <input type="checkbox" name="is_admin" <?php echo ($user && (int)$user['is_admin'] === 1) ? 'checked' : ''; ?> style="width: 18px; height: 18px; cursor: pointer;">
                            Es Administrador (Privilegios globales)
                        </label>
                    </div>

                    <div style="display: flex; gap: 1rem; justify-content: flex-end; border-top: 1px solid rgba(255,255,255,0.08); padding-top: 1.5rem; margin-top: 2rem;">
                        <a href="index.php" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary"><?php echo $id ? 'Guardar Cambios' : 'Registrar Usuario'; ?></button>
                    </div>
                </form>
            </div>

        <?php elseif ($action === 'show_password'): 
            $id = (int)$_GET['id'];
            $res = mysqli_query($db, "SELECT `id`, `username` FROM `biartet_users` WHERE `id` = $id LIMIT 1");
            $user = $res ? mysqli_fetch_assoc($res) : null;
        ?>
            <!-- Change Password Card -->
            <div class="glass-card" style="max-width: 500px; margin: 0 auto;">
                <h2 class="gradient-text" style="margin-top:0; margin-bottom:1.5rem;">🔑 Cambiar Contraseña</h2>
                <p style="font-size:0.9rem; color:rgba(255,255,255,0.6); margin-bottom:1.5rem;">
                    Usuario: <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                </p>
                
                <form action="?action=change_password" method="POST">
                    <input type="hidden" name="id" value="<?php echo $id; ?>">

                    <div class="form-group">
                        <label class="form-label" for="new_password">Nueva Contraseña *</label>
                        <input type="text" id="new_password" name="new_password" class="form-control" required placeholder="Escriba la nueva contraseña" style="padding:0.65rem 1rem;">
                        <span style="font-size:0.75rem; color:rgba(255,255,255,0.5); display:block; margin-top:0.25rem;">Esta contraseña se sincronizará también como la clave de acceso Dialview.</span>
                    </div>

                    <div style="display: flex; gap: 1rem; justify-content: flex-end; border-top: 1px solid rgba(255,255,255,0.08); padding-top: 1.5rem; margin-top: 2rem;">
                        <a href="index.php" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Cambiar Contraseña</button>
                    </div>
                </form>
            </div>

        <?php else: 
            // Main user listing
            $res = mysqli_query($db, "SELECT * FROM `biartet_users` ORDER BY `fecha_creacion` DESC");
            $users = array();
            if ($res) {
                while ($row = mysqli_fetch_assoc($res)) {
                    $users[] = $row;
                }
            }
        ?>
            <!-- Users Table Grid -->
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Nombre Completo</th>
                            <th>Correo / Web Login</th>
                            <th>Usuario Dialview</th>
                            <th>Clave Dialview (Plana)</th>
                            <th>Rol</th>
                            <th>Fechas de Auditoría</th>
                            <th style="width: 250px; text-align: center;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 2rem; color: rgba(255,255,255,0.4);">
                                    No hay usuarios registrados en el sistema.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $u): 
                                $isDeleted = $u['fecha_eliminacion'] !== null;
                            ?>
                                <tr style="<?php echo $isDeleted ? 'opacity: 0.5; background: rgba(239, 68, 68, 0.05);' : ''; ?>">
                                    <td>
                                        <div style="font-weight:600; color:<?php echo $isDeleted ? 'rgba(255,255,255,0.4)' : 'var(--highlight-color)'; ?>;">
                                            <?php echo htmlspecialchars($u['nombre_completo']); ?>
                                        </div>
                                        <?php if ($isDeleted): ?>
                                            <span class="badge badge-descompuesto" style="font-size:0.65rem; margin-top:0.25rem;">Eliminado / Inactivo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span style="font-weight: 500; font-family: monospace;"><?php echo htmlspecialchars($u['username']); ?></span>
                                    </td>
                                    <td style="font-family: monospace;"><?php echo htmlspecialchars($u['usuario_dialview']); ?></td>
                                    <td style="font-family: monospace; font-weight: bold; color: #10b981;"><?php echo htmlspecialchars($u['contrasena_dialview']); ?></td>
                                    <td>
                                        <span class="badge <?php echo (int)$u['is_admin'] === 1 ? 'badge-pendiente' : 'badge-llamar-de-nuevo'; ?>" style="font-size: 0.75rem;">
                                            <?php echo (int)$u['is_admin'] === 1 ? 'ADMIN' : 'VENDEDOR'; ?>
                                        </span>
                                    </td>
                                    <td style="font-size: 0.8rem; color: rgba(255,255,255,0.6); line-height: 1.4;">
                                        <div>🌱 Reg: <?php echo date('d/m/Y H:i', strtotime($u['fecha_creacion'])); ?></div>
                                        <div>✏ Mod: <?php echo date('d/m/Y H:i', strtotime($u['fecha_actualizacion'])); ?></div>
                                        <?php if ($isDeleted): ?>
                                            <div style="color: #ef4444;">❌ Del: <?php echo date('d/m/Y H:i', strtotime($u['fecha_eliminacion'])); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="action-buttons" style="justify-content: center;">
                                            <?php if (!$isDeleted): ?>
                                                <a href="?action=show_edit&id=<?php echo $u['id']; ?>" class="btn btn-secondary action-btn" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;" title="Editar Datos">Editar</a>
                                                <a href="?action=show_password&id=<?php echo $u['id']; ?>" class="btn btn-secondary action-btn" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;" title="Cambiar Contraseña">🔑 Clave</a>
                                                <?php if ($u['id'] !== $currentUserId): ?>
                                                    <a href="?action=delete&id=<?php echo $u['id']; ?>" class="btn btn-secondary btn-danger action-btn" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;" title="Eliminar Usuario" onclick="return confirm('¿Está seguro de que desea eliminar a este usuario del sistema?');">Eliminar</a>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <a href="?action=restore&id=<?php echo $u['id']; ?>" class="btn btn-secondary action-btn" style="padding: 0.4rem 0.8rem; font-size: 0.85rem; background: rgba(16, 185, 129, 0.2); color: #6ee7b7; border-color: rgba(16, 185, 129, 0.4);" title="Restaurar Usuario" onclick="return confirm('¿Desea restaurar a este usuario y reactivar su cuenta?');">Reactivar</a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
