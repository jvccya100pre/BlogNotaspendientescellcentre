<?php
/**
 * View: Create Memorandum Form (Admin Only)
 */
$titulo = isset($memo['titulo']) ? $memo['titulo'] : '';
$contenido = isset($memo['contenido']) ? $memo['contenido'] : '';
?>
<div class="glass-card" style="max-width: 750px; margin: 1.5rem auto;">
    <h2 class="gradient-text" style="margin-top: 0; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
        📢 Redactar Nuevo Memorándum
    </h2>

    <?php if (isset($errors['global'])): ?>
        <div class="alert alert-error">
            <span>❌</span> <?php echo htmlspecialchars($errors['global']); ?>
        </div>
    <?php endif; ?>

    <form action="memorandums/save" method="POST" enctype="multipart/form-data">
        
        <div class="form-group">
            <label class="form-label" for="titulo">Título del Comunicado *</label>
            <input type="text" id="titulo" name="titulo" class="form-control" value="<?php echo htmlspecialchars($titulo); ?>" required placeholder="Ej: Nuevo protocolo de atención telefónica, Cambio de horarios">
            <?php if (isset($errors['titulo'])): ?>
                <div class="error-text"><?php echo htmlspecialchars($errors['titulo']); ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label class="form-label" for="contenido">Mensaje o Contenido del Comunicado *</label>
            <textarea id="contenido" name="contenido" class="form-control" style="min-height: 200px; resize: vertical; line-height: 1.6;" required placeholder="Escriba aquí el contenido detallado del memorándum..."><?php echo htmlspecialchars($contenido); ?></textarea>
            <?php if (isset($errors['contenido'])): ?>
                <div class="error-text"><?php echo htmlspecialchars($errors['contenido']); ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label class="form-label" for="imagen">Adjuntar Fotografía / Imagen Ilustrativa</label>
            <input type="file" id="imagen" name="imagen" class="form-control" accept="image/*" style="padding: 0.55rem 1rem;">
        </div>

        <div style="display: flex; gap: 1rem; justify-content: flex-end; border-top: 1px solid rgba(255,255,255,0.08); padding-top: 1.5rem; margin-top: 1.5rem;">
            <a href="memorandums" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Enviar Comunicado 📢</button>
        </div>
    </form>
</div>
