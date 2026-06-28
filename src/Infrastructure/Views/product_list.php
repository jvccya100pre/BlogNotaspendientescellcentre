<?php
/**
 * View: Product Catalog List
 */
?>
<div class="glass-card" style="margin-top: 1rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 class="gradient-text" style="margin: 0 0 0.25rem 0;">Catálogo de Productos</h1>
            <p style="font-size: 0.9rem; color: rgba(255,255,255,0.6);">Gestione los productos disponibles para campañas de ventas.</p>
        </div>
        <a href="products/create" class="btn btn-primary">+ Agregar Producto</a>
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

    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th style="width: 80px;">Imagen</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <?php if (isset($_SESSION['user']['is_admin']) && (int)$_SESSION['user']['is_admin'] === 1): ?>
                        <th>Grupo</th>
                    <?php endif; ?>
                    <th style="width: 150px; text-align: right;">Precio (Moneda Local)</th>
                    <th style="width: 160px; text-align: center;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="<?php echo (isset($_SESSION['user']['is_admin']) && (int)$_SESSION['user']['is_admin'] === 1) ? 6 : 5; ?>" style="text-align: center; color: rgba(255,255,255,0.4); padding: 2rem;">
                            No hay productos registrados en el catálogo.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($products as $prod): ?>
                        <tr>
                            <td>
                                <?php if (!empty($prod['imagen'])): ?>
                                    <img src="<?php echo htmlspecialchars($prod['imagen']); ?>" alt="Producto" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1);">
                                <?php else: ?>
                                    <div style="width: 50px; height: 50px; background: rgba(255,255,255,0.05); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; color: rgba(255,255,255,0.3);">
                                        📦
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td style="font-weight: 600; color: var(--highlight-color);">
                                <?php echo htmlspecialchars($prod['nombre']); ?>
                            </td>
                            <td style="color: rgba(255,255,255,0.8); font-size: 0.9rem; max-width: 400px; white-space: nowrap; overflow: hidden; text-transform: none; text-overflow: ellipsis;">
                                <?php echo htmlspecialchars($prod['descripcion']); ?>
                            </td>
                            <?php if (isset($_SESSION['user']['is_admin']) && (int)$_SESSION['user']['is_admin'] === 1): ?>
                                <td>
                                    <?php if (!empty($prod['grupo_nombre'])): ?>
                                        <span class="badge badge-exito-pedido" style="background: rgba(59, 130, 246, 0.2); color: #93c5fd; border: 1px solid rgba(59, 130, 246, 0.3);">
                                            👥 <?php echo htmlspecialchars($prod['grupo_nombre']); ?>
                                        </span>
                                    <?php else: ?>
                                        <span style="color: rgba(255,255,255,0.4); font-size: 0.85rem;">Global</span>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                            <td style="text-align: right; font-weight: 700; color: #10b981;">
                                $<?php echo number_format((double)$prod['precio_moneda_local'], 2); ?>
                            </td>
                            <td>
                                <div class="action-buttons" style="justify-content: center;">
                                    <a href="products/edit?id=<?php echo $prod['id']; ?>" class="btn btn-secondary action-btn" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;">Editar</a>
                                    <a href="products/delete?id=<?php echo $prod['id']; ?>" class="btn btn-secondary btn-danger action-btn" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;" onclick="return confirm('¿Está seguro de que desea eliminar este producto del catálogo?');">Eliminar</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
