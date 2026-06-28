<?php
/**
 * View: Configure Daily Prizes for Campaign Items
 */
?>
<div class="glass-card" style="margin-top: 1rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 class="gradient-text" style="margin: 0 0 0.25rem 0;">Control de Premios Diarios</h1>
            <p style="font-size: 0.9rem; color: rgba(255,255,255,0.6);">Establezca premios adicionales por la venta de productos específicos para una fecha determinada.</p>
        </div>
    </div>

    <!-- Date & Group selector panel -->
    <div style="background: rgba(255, 255, 255, 0.03); padding: 1.25rem; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.06); margin-bottom: 2rem; display: flex; align-items: center; gap: 1.5rem; flex-wrap: wrap;">
        <div>
            <label class="form-label" for="selected_fecha" style="margin-bottom: 0.25rem; font-weight: bold; color: var(--highlight-color);">Seleccione la Fecha:</label>
            <input type="date" id="selected_fecha" class="form-control" value="<?php echo htmlspecialchars($fecha); ?>" style="width: 200px; padding: 0.5rem 1rem;" onchange="changeFilter()">
        </div>
        <div>
            <label class="form-label" for="selected_grupo" style="margin-bottom: 0.25rem; font-weight: bold; color: var(--highlight-color);">Seleccione el Grupo:</label>
            <select id="selected_grupo" class="form-control" style="width: 250px; padding: 0.5rem 1rem;" onchange="changeFilter()">
                <option value="0" <?php echo (int)$grupo_id === 0 ? 'selected' : ''; ?>>Global / Todos (Sin Grupo)</option>
                <?php
                $db = DatabaseConnection::getInstance();
                $groupsRes = mysqli_query($db, "SELECT * FROM `biartet_grupos` ORDER BY `nombre` ASC");
                if ($groupsRes) {
                    while ($g = mysqli_fetch_assoc($groupsRes)) {
                        echo '<option value="' . $g['id'] . '" ' . ((int)$grupo_id === (int)$g['id'] ? 'selected' : '') . '>' . htmlspecialchars($g['nombre']) . '</option>';
                    }
                }
                ?>
            </select>
        </div>
    </div>

    <?php if (isset($success)): ?>
        <div class="alert alert-success">
            <span>✔</span> <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="alert alert-error">
            <span>❌</span> <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form action="daily-prizes/save" method="POST">
        <input type="hidden" name="fecha" value="<?php echo htmlspecialchars($fecha); ?>">
        <input type="hidden" name="grupo_id" value="<?php echo htmlspecialchars($grupo_id); ?>">

        <div class="table-responsive" style="margin-bottom: 1.5rem;">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Campaña</th>
                        <th>Producto / Item a Vender</th>
                        <th style="width: 250px; text-align: center;">Premio Extra para esta Fecha (USD) *</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($items)): ?>
                        <tr>
                            <td colspan="3" style="text-align: center; color: rgba(255,255,255,0.4); padding: 2rem;">
                                No hay productos o ítems activos en ninguna campaña para configurar premios.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td style="color: rgba(255,255,255,0.6); font-size: 0.9rem;">
                                    <?php echo htmlspecialchars($item['campana_nombre']); ?>
                                </td>
                                <td style="font-weight: 600; color: #fff;">
                                    <?php echo htmlspecialchars($item['nombre_producto']); ?>
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                                        <span style="color: rgba(255,255,255,0.6); font-weight: bold;">$</span>
                                        <input type="number" step="0.01" name="premios[<?php echo $item['id']; ?>]" class="form-control" value="<?php echo number_format((double)$item['premio_hoy'], 2, '.', ''); ?>" min="0" style="width: 150px; text-align: right; padding: 0.5rem 0.75rem;">
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (!empty($items)): ?>
            <div style="display: flex; justify-content: flex-end; border-top: 1px solid rgba(255,255,255,0.08); padding-top: 1.5rem;">
                <button type="submit" class="btn btn-primary" style="padding: 0.65rem 1.5rem;">Guardar Premios para la Fecha</button>
            </div>
        <?php endif; ?>
    </form>
</div>

<script>
function changeFilter() {
    var fecha = document.getElementById('selected_fecha').value;
    var grupo = document.getElementById('selected_grupo').value;
    window.location.href = 'daily-prizes?fecha=' + encodeURIComponent(fecha) + '&grupo_id=' + encodeURIComponent(grupo);
}
</script>
