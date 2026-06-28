<?php
/**
 * View: Memorandums / Announcements list
 */
?>
<div class="glass-card" style="margin-top: 1rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 class="gradient-text" style="margin: 0 0 0.25rem 0; display: flex; align-items: center; gap: 0.5rem;">
                📢 Memorándums y Comunicados
            </h1>
            <p style="font-size: 0.9rem; color: rgba(255,255,255,0.6);">Bandeja oficial de avisos y notificaciones de administración.</p>
        </div>
        <?php if ($isAdmin): ?>
            <a href="memorandums/create" class="btn btn-primary">📢 Enviar Comunicado</a>
        <?php endif; ?>
    </div>

    <?php if (isset($success)): ?>
        <div class="alert alert-success">
            <span>✔</span> <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <?php if (empty($memos)): ?>
        <div style="background: rgba(255,255,255,0.02); border: 1px dashed rgba(255,255,255,0.1); border-radius: 12px; padding: 4rem; text-align: center; color: rgba(255,255,255,0.4);">
            <div style="font-size: 3rem; margin-bottom: 1rem;">📢</div>
            <h4>Sin Memorándums Recientes</h4>
            <p style="font-size: 0.9rem; margin-top: 0.25rem;">No se han publicado comunicados el día de hoy.</p>
        </div>
    <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <?php foreach ($memos as $memo): ?>
                <div style="background: rgba(70, 8, 9, 0.2); border: 1px solid rgba(255, 255, 255, 0.08); border-left: 4px solid var(--highlight-color); border-radius: 12px; padding: 1.5rem; transition: transform 0.2s ease, border-color 0.2s ease; position: relative; overflow: hidden;" class="memo-card">
                    
                    <div style="display: flex; gap: 1.5rem; flex-wrap: wrap;">
                        <?php if (!empty($memo['imagen'])): ?>
                            <div style="flex: 0 0 150px; max-width: 150px;">
                                <img src="<?php echo htmlspecialchars($memo['imagen']); ?>" alt="Adjunto" style="width: 100%; height: 120px; object-fit: cover; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1);">
                            </div>
                        <?php endif; ?>

                        <div style="flex: 1; min-width: 250px;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem; flex-wrap: wrap; gap: 0.5rem;">
                                <h3 style="color: var(--highlight-color); margin: 0; font-size: 1.25rem;"><?php echo htmlspecialchars($memo['titulo']); ?></h3>
                                <span style="font-size: 0.8rem; color: rgba(255,255,255,0.5); background: rgba(255,255,255,0.06); padding: 0.2rem 0.6rem; border-radius: 4px;">
                                    📅 <?php echo date('d/m/Y H:i', strtotime($memo['fecha_creacion'])); ?>
                                </span>
                            </div>
                            <div style="color: rgba(255,255,255,0.85); font-size: 0.95rem; line-height: 1.6; white-space: pre-wrap; word-break: break-word;">
                                <?php echo htmlspecialchars($memo['contenido']); ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.memo-card:hover {
    transform: translateX(4px);
    border-color: rgba(255, 255, 255, 0.15) !important;
    background: rgba(70, 8, 9, 0.3) !important;
}
</style>
