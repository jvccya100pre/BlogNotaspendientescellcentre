<?php
// Pre-populate values if editing
$id = '';
$nombre = '';
$charla_saludo = '';
$charla_desarrollo = '';
$charla_cierre = '';
$estado = 1;
$items = array();

if (isset($campaign) && $campaign !== null) {
    $id = htmlspecialchars($campaign->id);
    $nombre = htmlspecialchars($campaign->nombre);
    $charla_saludo = htmlspecialchars($campaign->charla_saludo);
    $charla_desarrollo = htmlspecialchars($campaign->charla_desarrollo);
    $charla_cierre = htmlspecialchars($campaign->charla_cierre);
    $estado = (int)$campaign->estado;
    $items = $campaign->items;
}
?>

<div class="glass-card" style="max-width: 900px; margin: 0 auto;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 1rem;">
        <div>
            <h2 class="gradient-text" style="margin-bottom:0.25rem;"><?php echo htmlspecialchars($title); ?></h2>
            <p style="font-size:0.9rem; color:rgba(255,255,255,0.6);">
                <?php if($id): ?>
                    Modificando campaña: <strong style="color:var(--highlight-color);"><?php echo $nombre; ?></strong>
                <?php else: ?>
                    Defina los detalles de la nueva campaña y sus discursos comerciales
                <?php endif; ?>
            </p>
        </div>
        <a href="campaigns" class="btn btn-secondary">
            <svg style="width:18px;height:18px;fill:currentColor" viewBox="0 0 24 24">
                <path d="M21,11H6.83L10.41,7.41L9,6L3,12L9,18L10.41,16.59L6.83,13H21V11Z" />
            </svg>
            <span>Volver</span>
        </a>
    </div>

    <?php if (isset($errors['global'])): ?>
        <div class="alert alert-error">
            <svg style="width:20px;height:20px;fill:currentColor" viewBox="0 0 24 24">
                <path d="M11,15H13V17H11V15M11,7H13V13H11V7M12,2C6.47,2 2,6.5 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M12,20A8,8 0 0,1 4,12A8,8 0 0,1 12,4A8,8 0 0,1 20,12A8,8 0 0,1 12,20Z" />
            </svg>
            <span><?php echo htmlspecialchars($errors['global']); ?></span>
        </div>
    <?php endif; ?>

    <form action="campaigns/save" method="POST" id="campaign-form">
        <?php if($id): ?>
            <input type="hidden" name="id" value="<?php echo $id; ?>">
        <?php endif; ?>

        <div class="form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
            <!-- Nombre -->
            <div class="form-group" style="grid-column: span 1;">
                <label class="form-label" for="nombre">Nombre de la Campaña *</label>
                <input class="form-control" type="text" id="nombre" name="nombre" value="<?php echo $nombre; ?>" required placeholder="Ej: Campaña Salud / Dolor de Cabeza">
                <?php if (isset($errors['nombre'])): ?>
                    <div class="error-text"><?php echo htmlspecialchars($errors['nombre']); ?></div>
                <?php endif; ?>
            </div>

            <!-- Estado -->
            <div class="form-group" style="grid-column: span 1;">
                <label class="form-label" for="estado">Estado *</label>
                <select class="form-control" id="estado" name="estado" required>
                    <option value="1" <?php echo $estado === 1 ? 'selected' : ''; ?>>Activo (Visible en Dashboard)</option>
                    <option value="0" <?php echo $estado === 0 ? 'selected' : ''; ?>>Oculto (Inactivo)</option>
                </select>
            </div>

            <!-- Charla 1: Saludo -->
            <div class="form-group" style="grid-column: span 2;">
                <label class="form-label" for="charla_saludo">Saludo de Venta (Charla 1 - Max 2000 caracteres) *</label>
                <textarea class="form-control" id="charla_saludo" name="charla_saludo" maxlength="2000" rows="4" required placeholder="Texto inicial de saludo y presentación de productos..."><?php echo $charla_saludo; ?></textarea>
                <div style="font-size:0.8rem; text-align:right; color:rgba(255,255,255,0.4);" id="saludo-char-counter">0 / 2000</div>
                <?php if (isset($errors['charla_saludo'])): ?>
                    <div class="error-text"><?php echo htmlspecialchars($errors['charla_saludo']); ?></div>
                <?php endif; ?>
            </div>

            <!-- Charla 2: Desarrollo -->
            <div class="form-group" style="grid-column: span 2;">
                <label class="form-label" for="charla_desarrollo">Desarrollo de Venta (Charla 2) *</label>
                <textarea class="form-control" id="charla_desarrollo" name="charla_desarrollo" rows="6" required placeholder="Cuerpo o argumento comercial, detalles del producto..."><?php echo $charla_desarrollo; ?></textarea>
            </div>

            <!-- Charla 3: Cierre -->
            <div class="form-group" style="grid-column: span 2;">
                <label class="form-label" for="charla_cierre">Cierre de Venta (Charla 3) *</label>
                <textarea class="form-control" id="charla_cierre" name="charla_cierre" rows="4" required placeholder="Conclusión del discurso de ventas y llamada a la acción/cierre..."><?php echo $charla_cierre; ?></textarea>
            </div>
        </div>

        <!-- Section: Items/Products List (Up to 30) -->
        <div style="border-top: 1px solid rgba(255,255,255,0.08); padding-top: 1.5rem; margin-bottom: 2rem;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
                <h3 class="gradient-text" style="margin:0;">Lista de Productos / Ítems a Vender (Hasta 30)</h3>
                <button type="button" id="btn-add-item" class="btn btn-secondary" style="padding: 0.4rem 0.8rem; font-size:0.85rem;">
                    + Añadir Producto
                </button>
            </div>
            
            <div class="table-responsive">
                <table class="custom-table" id="items-table">
                    <thead>
                        <tr>
                            <th>Nombre del Producto *</th>
                            <th style="width: 150px;">Precio (USD) *</th>
                            <th style="width: 150px;">Comisión Venta (USD) *</th>
                            <th style="width: 150px;">Premio Extra Día (USD) *</th>
                            <th style="width: 80px; text-align: center;">Quitar</th>
                        </tr>
                    </thead>
                    <tbody id="items-tbody">
                        <!-- Table rows populated dynamically -->
                    </tbody>
                </table>
            </div>
        </div>

        <div style="display: flex; gap: 1rem; justify-content: flex-end; border-top: 1px solid rgba(255,255,255,0.08); padding-top: 1.5rem;">
            <a href="campaigns" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Guardar Campaña</button>
        </div>
    </form>
</div>

<!-- JavaScript to manage dynamic rows & char count limit -->
<script>
(function() {
    var itemsContainer = document.getElementById('items-tbody');
    var addItemBtn = document.getElementById('btn-add-item');
    var saluteText = document.getElementById('charla_saludo');
    var saluteCounter = document.getElementById('saludo-char-counter');
    
    // Character counter for Saludo
    function updateCharCounter() {
        saluteCounter.textContent = saluteText.value.length + ' / 2000';
    }
    saluteText.addEventListener('input', updateCharCounter);
    updateCharCounter(); // Run initially

    // Array of initial items from PHP
    var initialItems = <?php echo json_encode($items); ?>;
    var rowCount = 0;

    function addRow(itemData) {
        if (rowCount >= 30) {
            alert('Límite alcanzado: solo se pueden agregar hasta 30 productos por campaña.');
            return;
        }

        var data = itemData || { nombre_producto: '', precio: 0.00, comision_venta: 0.00, premio_extra: 0.00 };
        var uniqueIndex = rowCount;

        var tr = document.createElement('tr');
        tr.id = 'item-row-' + uniqueIndex;
        tr.innerHTML = '<td>' +
            '  <input type="text" name="items[' + uniqueIndex + '][nombre_producto]" class="form-control" value="' + escapeHtml(data.nombre_producto) + '" required placeholder="Nombre producto">' +
            '</td>' +
            '<td>' +
            '  <input type="number" step="0.01" name="items[' + uniqueIndex + '][precio]" class="form-control" value="' + data.precio + '" required min="0">' +
            '</td>' +
            '<td>' +
            '  <input type="number" step="0.01" name="items[' + uniqueIndex + '][comision_venta]" class="form-control" value="' + data.comision_venta + '" required min="0">' +
            '</td>' +
            '<td>' +
            '  <input type="number" step="0.01" name="items[' + uniqueIndex + '][premio_extra]" class="form-control" value="' + data.premio_extra + '" required min="0">' +
            '</td>' +
            '<td style="text-align:center; vertical-align:middle;">' +
            '  <button type="button" class="btn btn-danger action-btn btn-remove-row" style="padding: 0.4rem; border-radius: 4px;" title="Eliminar Producto">&times;</button>' +
            '</td>';

        itemsContainer.appendChild(tr);

        // Bind delete button handler
        tr.querySelector('.btn-remove-row').addEventListener('click', function() {
            tr.remove();
            rowCount--;
            reindexRows();
        });

        rowCount++;
    }

    function reindexRows() {
        var rows = itemsContainer.querySelectorAll('tr');
        rowCount = 0;
        rows.forEach(function(row, idx) {
            row.id = 'item-row-' + idx;
            row.querySelectorAll('input').forEach(function(input) {
                var name = input.getAttribute('name');
                if (name) {
                    var updatedName = name.replace(/items\[\d+\]/, 'items[' + idx + ']');
                    input.setAttribute('name', updatedName);
                }
            });
            rowCount++;
        });
    }

    function escapeHtml(text) {
        if (!text) return '';
        return text
            .toString()
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // Populate initial items or insert 1 empty row
    if (initialItems && initialItems.length > 0) {
        initialItems.forEach(function(item) {
            addRow(item);
        });
    } else {
        addRow(); // Initial empty row
    }

    // Bind add button click
    addItemBtn.addEventListener('click', function() {
        addRow();
    });
})();
</script>
