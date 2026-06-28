<?php
/**
 * View: System Logs List
 */
?>
<div class="glass-card" style="margin-top: 1rem;">
    <div style="margin-bottom: 2rem;">
        <h1 class="gradient-text" style="margin: 0 0 0.25rem 0;">Historial de Logs del Sistema</h1>
        <p style="font-size: 0.9rem; color: rgba(255,255,255,0.6);">Registro cronológico de acciones y operaciones realizadas en la plataforma.</p>
    </div>

    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th style="width: 180px;">Fecha y Hora</th>
                    <th style="width: 150px;">Usuario</th>
                    <th>Acción Realizada</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($logs)): ?>
                    <tr>
                        <td colspan="3" style="text-align: center; color: rgba(255,255,255,0.4); padding: 2rem;">
                            No hay registros de logs disponibles.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td style="font-size: 0.85rem; color: rgba(255,255,255,0.6); font-family: monospace;">
                                📅 <?php echo date('d/m/Y H:i:s', strtotime($log['fecha_hora'])); ?>
                            </td>
                            <td style="font-weight: bold; color: var(--highlight-color);">
                                👤 <?php echo htmlspecialchars($log['usuario']); ?>
                            </td>
                            <td style="color: rgba(255,255,255,0.9); font-size: 0.95rem;">
                                <?php echo htmlspecialchars($log['accion']); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
