<?php
$db = DatabaseConnection::getInstance();
$groupNames = array();
$grpRes = mysqli_query($db, "SELECT `id`, `nombre` FROM `biartet_grupos`");
if ($grpRes) {
    while ($gRow = mysqli_fetch_assoc($grpRes)) {
        $groupNames[$gRow['id']] = $gRow['nombre'];
    }
}
?>
<div class="glass-card">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; margin-bottom:1.5rem; gap:1rem;">
        <div>
            <h1 class="gradient-text" style="margin-bottom:0.25rem;">Campañas de Ventas</h1>
            <p style="font-size:0.9rem; color:rgba(255,255,255,0.6);">Gestione las campañas de ventas y los discursos de llamada</p>
        </div>
        <div style="display:flex; gap:1rem; align-items:center;">
            <a href="campaigns/create" class="btn btn-primary">
                <svg style="width:20px;height:20px;fill:currentColor" viewBox="0 0 24 24">
                    <path d="M19,13H13V19H11V13H5V11H11V5H13V11H19V13Z" />
                </svg>
                <span>Nueva Campaña</span>
            </a>
            <a href="./" class="btn btn-secondary">Volver al Inicio</a>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if (isset($success) && $success !== null): ?>
        <div class="alert alert-success">
            <svg style="width:20px;height:20px;fill:currentColor" viewBox="0 0 24 24">
                <path d="M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2M12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4M11,16.5L7.5,13L8.91,11.59L11,13.67L15.09,9.58L16.5,11L11,16.5Z" />
            </svg>
            <span><?php echo htmlspecialchars($success); ?></span>
        </div>
    <?php endif; ?>

    <?php if (isset($error) && $error !== null): ?>
        <div class="alert alert-error">
            <svg style="width:20px;height:20px;fill:currentColor" viewBox="0 0 24 24">
                <path d="M11,15H13V17H11V15M11,7H13V13H11V7M12,2C6.47,2 2,6.5 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M12,20A8,8 0 0,1 4,12A8,8 0 0,1 12,4A8,8 0 0,1 20,12A8,8 0 0,1 12,20Z" />
            </svg>
            <span><?php echo htmlspecialchars($error); ?></span>
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <?php if (empty($campaigns)): ?>
            <div style="padding:3rem; text-align:center; color:rgba(255,255,255,0.4);">
                <svg style="width:64px;height:64px;fill:currentColor;margin-bottom:1rem;" viewBox="0 0 24 24">
                    <path d="M19,19H5V8H19M19,3H18V1H16V3H8V1H6V3H5C3.89,3 3,3.9 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5A2,2 0 0,0 19,3Z" />
                </svg>
                <p>No hay campañas registradas.</p>
            </div>
        <?php else: ?>
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Campaña</th>
                        <th>Saludo (Charla 1)</th>
                        <th>Desarrollo (Charla 2)</th>
                        <th>Cierre (Charla 3)</th>
                        <?php if (isset($_SESSION['user']['is_admin']) && (int)$_SESSION['user']['is_admin'] === 1): ?>
                            <th>Grupo</th>
                        <?php endif; ?>
                        <th>Productos (Ítems)</th>
                        <th>Estado</th>
                        <th style="width: 120px; text-align: center;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($campaigns as $camp): ?>
                        <tr>
                            <td style="font-weight:600; color:var(--highlight-color);">
                                <?php echo htmlspecialchars($camp->nombre); ?>
                            </td>
                            <td style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?php echo htmlspecialchars($camp->charla_saludo); ?>">
                                <?php echo htmlspecialchars($camp->charla_saludo); ?>
                            </td>
                            <td style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?php echo htmlspecialchars($camp->charla_desarrollo); ?>">
                                <?php echo htmlspecialchars($camp->charla_desarrollo); ?>
                            </td>
                            <td style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?php echo htmlspecialchars($camp->charla_cierre); ?>">
                                <?php echo htmlspecialchars($camp->charla_cierre); ?>
                            </td>
                            <?php if (isset($_SESSION['user']['is_admin']) && (int)$_SESSION['user']['is_admin'] === 1): ?>
                                <td>
                                    <?php 
                                    $grpName = ($camp->grupo_id && isset($groupNames[$camp->grupo_id])) ? $groupNames[$camp->grupo_id] : null;
                                    if ($grpName): 
                                    ?>
                                        <span class="badge badge-exito-pedido" style="background: rgba(59, 130, 246, 0.2); color: #93c5fd; border: 1px solid rgba(59, 130, 246, 0.3);">
                                            👥 <?php echo htmlspecialchars($grpName); ?>
                                        </span>
                                    <?php else: ?>
                                        <span style="color: rgba(255,255,255,0.4); font-size: 0.85rem;">Global</span>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                            <td>
                                <span class="badge badge-llamar-de-nuevo">
                                    <?php echo count($camp->items); ?> ítems
                                </span>
                            </td>
                            <td>
                                <span class="badge <?php echo $camp->estado ? 'badge-exito-pedido' : 'badge-descompuesto'; ?>">
                                    <?php echo $camp->estado ? 'Activo' : 'Oculto'; ?>
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <div class="action-buttons">
                                    <a href="campaigns/edit?id=<?php echo $camp->id; ?>" class="btn btn-secondary action-btn" title="Editar">
                                        <svg style="width:16px;height:16px;fill:currentColor" viewBox="0 0 24 24">
                                            <path d="M20.71,7.04C21.1,6.65 21.1,6 20.71,5.63L18.37,3.29C18,2.9 17.35,2.9 16.96,3.29L15.12,5.12L18.87,8.87M3,17.25V21H6.75L17.81,9.93L14.07,6.19L3,17.25Z" />
                                        </svg>
                                    </a>
                                    <a href="campaigns/delete?id=<?php echo $camp->id; ?>" class="btn btn-danger action-btn" title="Eliminar" onclick="return confirm('¿Está seguro de que desea eliminar esta campaña? Se borrarán sus productos asociados.');">
                                        <svg style="width:16px;height:16px;fill:currentColor" viewBox="0 0 24 24">
                                            <path d="M19,4H15.5L14.5,3H9.5L8.5,4H5V6H19M6,19A2,2 0 0,0 8,21H16A2,2 0 0,0 18,19V7H6V19Z" />
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
