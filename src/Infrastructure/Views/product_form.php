<?php
/**
 * View: Product Create / Edit Form
 */
$id = isset($product['id']) ? $product['id'] : null;
$nombre = isset($product['nombre']) ? $product['nombre'] : '';
$descripcion = isset($product['descripcion']) ? $product['descripcion'] : '';
$precio_moneda_local = isset($product['precio_moneda_local']) ? $product['precio_moneda_local'] : 0.00;
$imagen = isset($product['imagen']) ? $product['imagen'] : '';
?>
<div class="glass-card" style="max-width: 700px; margin: 1.5rem auto;">
    <h2 class="gradient-text" style="margin-top: 0; margin-bottom: 1.5rem;">
        <?php echo $id ? '📦 Editar Producto' : '📦 Registrar Nuevo Producto'; ?>
    </h2>

    <form action="products/save" method="POST" enctype="multipart/form-data">
        <?php if ($id): ?>
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <input type="hidden" name="existing_imagen" value="<?php echo htmlspecialchars($imagen); ?>">
        <?php endif; ?>

        <div class="form-group">
            <label class="form-label" for="nombre">Nombre del Producto *</label>
            <input type="text" id="nombre" name="nombre" class="form-control" value="<?php echo htmlspecialchars($nombre); ?>" required placeholder="Ej: Pastilla para dolor, Membrecía Premium">
            <?php if (isset($errors['nombre'])): ?>
                <div class="error-text"><?php echo htmlspecialchars($errors['nombre']); ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label class="form-label" for="descripcion">Descripción del Producto</label>
            <textarea id="descripcion" name="descripcion" class="form-control" style="min-height: 120px;" placeholder="Detalles o especificaciones del producto..."><?php echo htmlspecialchars($descripcion); ?></textarea>
            <?php if (isset($errors['descripcion'])): ?>
                <div class="error-text"><?php echo htmlspecialchars($errors['descripcion']); ?></div>
            <?php endif; ?>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <div class="form-group">
                <label class="form-label" for="precio_moneda_local">Precio (Moneda Local) *</label>
                <input type="number" step="0.01" id="precio_moneda_local" name="precio_moneda_local" class="form-control" value="<?php echo $precio_moneda_local; ?>" required min="0" placeholder="0.00">
                <?php if (isset($errors['precio_moneda_local'])): ?>
                    <div class="error-text"><?php echo htmlspecialchars($errors['precio_moneda_local']); ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label class="form-label" for="imagen">Imagen Ilustrativa</label>
                <input type="file" id="imagen" name="imagen" class="form-control" accept="image/*" style="padding: 0.55rem 1rem;">
                <?php if (!empty($imagen)): ?>
                    <div style="margin-top: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                        <img src="<?php echo htmlspecialchars($imagen); ?>" alt="Miniatura" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                        <span style="font-size: 0.8rem; color: rgba(255,255,255,0.5);">Imagen actual guardada</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php 
        $isAdminUser = isset($_SESSION['user']['is_admin']) && (int)$_SESSION['user']['is_admin'] === 1;
        if ($isAdminUser): 
            $db = DatabaseConnection::getInstance();
            $groupsRes = mysqli_query($db, "SELECT * FROM `biartet_grupos` ORDER BY `nombre` ASC");
            $productGroupId = isset($product) && isset($product['grupo_id']) ? $product['grupo_id'] : null;
            if ($id && $productGroupId === null) {
                $pQuery = mysqli_query($db, "SELECT `grupo_id` FROM `biartet_productos` WHERE `id` = $id LIMIT 1");
                $pRow = $pQuery ? mysqli_fetch_assoc($pQuery) : null;
                $productGroupId = $pRow ? $pRow['grupo_id'] : null;
            }
        ?>
            <!-- Grupo de Producto (ins7.md) -->
            <div class="form-group" style="margin-top: 1rem;">
                <label class="form-label" for="grupo_id">Grupo del Producto (Para organizar la lista de productos por usuario)</label>
                <select class="form-control" id="grupo_id" name="grupo_id">
                    <option value="">Global / Todos los Grupos</option>
                    <?php if ($groupsRes): ?>
                        <?php while ($g = mysqli_fetch_assoc($groupsRes)): ?>
                            <option value="<?php echo $g['id']; ?>" <?php echo (int)$productGroupId === (int)$g['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($g['nombre']); ?>
                            </option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </div>
        <?php endif; ?>

        <div style="display: flex; gap: 1rem; justify-content: flex-end; border-top: 1px solid rgba(255,255,255,0.08); padding-top: 1.5rem; margin-top: 1.5rem;">
            <a href="products" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary"><?php echo $id ? 'Actualizar Producto' : 'Guardar Producto'; ?></button>
        </div>
    </form>
</div>
