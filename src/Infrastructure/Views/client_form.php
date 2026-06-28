<?php
// Pre-populate values if editing
$id = '';
$identificador_unico = '';
$nombre = '';
$telefono = '';
$direccion = '';
$estado_id = '';
$municipio_id = '';
$ciudad_id = '';
$archivo_adjunto = '';
$estado_llamada = 'Pendiente';
$observacion = '';
$lapso_tiempo = '';
$lapso_dias = '';

if (isset($client) && $client !== null) {
    $id = htmlspecialchars($client->id);
    $identificador_unico = htmlspecialchars($client->identificador_unico);
    $nombre = htmlspecialchars($client->nombre);
    $telefono = htmlspecialchars($client->telefono);
    $direccion = htmlspecialchars($client->direccion);
    $estado_id = htmlspecialchars($client->estado_id);
    $municipio_id = htmlspecialchars($client->municipio_id);
    $ciudad_id = htmlspecialchars($client->ciudad_id);
    $archivo_adjunto = htmlspecialchars($client->archivo_adjunto);
    $estado_llamada = htmlspecialchars($client->estado_llamada);
    $observacion = htmlspecialchars($client->observacion);
    $lapso_tiempo = htmlspecialchars($client->lapso_tiempo);
    $lapso_dias = htmlspecialchars($client->lapso_dias);
}

// Lists for select boxes (Paso 10)
$estados = array(
    'Pendiente',
    'Exito pedido pendiente'
);
?>

<div class="glass-card" style="max-width: 800px; margin: 0 auto;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 1rem;">
        <div>
            <h2 class="gradient-text" style="margin-bottom:0.25rem;"><?php echo htmlspecialchars($title); ?></h2>
            <p style="font-size:0.9rem; color:rgba(255,255,255,0.6);">
                <?php if($identificador_unico): ?>
                    Modificando registro: <strong style="color:var(--highlight-color);"><?php echo $identificador_unico; ?></strong>
                <?php else: ?>
                    Complete los datos para agregar un nuevo registro
                <?php endif; ?>
            </p>
        </div>
        <a href="./" class="btn btn-secondary">
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

    <form action="clients/save" method="POST" enctype="multipart/form-data">
        <?php if($id): ?>
            <input type="hidden" name="id" value="<?php echo $id; ?>">
        <?php endif; ?>

        <div class="form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <!-- Nombre -->
            <div class="form-group" style="grid-column: span 1;">
                <label class="form-label" for="nombre">Nombre Completo *</label>
                <input class="form-control" type="text" id="nombre" name="nombre" value="<?php echo $nombre; ?>" required placeholder="Nombre del cliente">
                <?php if (isset($errors['nombre'])): ?>
                    <div class="error-text"><?php echo htmlspecialchars($errors['nombre']); ?></div>
                <?php endif; ?>
            </div>

            <!-- Teléfono -->
            <div class="form-group" style="grid-column: span 1;">
                <label class="form-label" for="telefono">Número de Teléfono *</label>
                <input class="form-control" type="text" id="telefono" name="telefono" value="<?php echo $telefono; ?>" required placeholder="Ej: +584123456789">
                <?php if (isset($errors['telefono'])): ?>
                    <div class="error-text"><?php echo htmlspecialchars($errors['telefono']); ?></div>
                <?php endif; ?>
            </div>

            <!-- Estado (Venezuela) (Paso 4) -->
            <div class="form-group" style="grid-column: span 2; display:grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                <div>
                    <label class="form-label" for="estado_id">Estado *</label>
                    <select id="estado_id" name="estado_id" required>
                        <option value="">-- Seleccionar --</option>
                        <?php foreach ($estadosList as $estObj): ?>
                            <option value="<?php echo $estObj['id']; ?>" <?php echo ($estado_id == $estObj['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($estObj['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['estado_id'])): ?>
                        <div class="error-text"><?php echo htmlspecialchars($errors['estado_id']); ?></div>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="form-label" for="municipio_id">Municipio *</label>
                    <select id="municipio_id" name="municipio_id" required <?php echo empty($municipiosList) ? 'disabled' : ''; ?>>
                        <option value="">-- Seleccionar --</option>
                        <?php foreach ($municipiosList as $muniObj): ?>
                            <option value="<?php echo $muniObj['id']; ?>" <?php echo ($municipio_id == $muniObj['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($muniObj['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['municipio_id'])): ?>
                        <div class="error-text"><?php echo htmlspecialchars($errors['municipio_id']); ?></div>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="form-label" for="ciudad_id">Ciudad/Sector *</label>
                    <select id="ciudad_id" name="ciudad_id" required <?php echo empty($ciudadesList) ? 'disabled' : ''; ?>>
                        <option value="">-- Seleccionar --</option>
                        <?php foreach ($ciudadesList as $cityObj): ?>
                            <option value="<?php echo $cityObj['id']; ?>" <?php echo ($ciudad_id == $cityObj['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cityObj['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['ciudad_id'])): ?>
                        <div class="error-text"><?php echo htmlspecialchars($errors['ciudad_id']); ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Dirección -->
            <div class="form-group" style="grid-column: span 2;">
                <label class="form-label" for="direccion">Dirección *</label>
                <textarea class="form-control" id="direccion" name="direccion" required placeholder="Dirección detallada de domicilio o entrega"><?php echo $direccion; ?></textarea>
                <?php if (isset($errors['direccion'])): ?>
                    <div class="error-text"><?php echo htmlspecialchars($errors['direccion']); ?></div>
                <?php endif; ?>
            </div>

            <!-- Campaña y Producto (Paso Campaña) -->
            <div class="form-group" style="grid-column: span 2; display:grid; grid-template-columns: 1fr 1fr 100px; gap: 1rem;">
                <div>
                    <label class="form-label" for="campana_id">Campaña de Venta</label>
                    <select id="campana_id" name="campana_id">
                        <option value="">-- Seleccionar Campaña --</option>
                        <?php foreach ($campaignsList as $camp): ?>
                            <option value="<?php echo $camp->id; ?>" <?php echo (isset($client) && $client->campana_id == $camp->id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($camp->nombre); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="form-label" for="campana_item_id">Producto a Vender</label>
                    <select id="campana_item_id" name="campana_item_id">
                        <option value="">-- Seleccionar Producto --</option>
                        <?php foreach ($campaignItemsList as $item): ?>
                            <option value="<?php echo $item->id; ?>" <?php echo (isset($client) && $client->campana_item_id == $item->id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($item->nombre_producto) . " ($" . $item->precio . ")"; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="form-label" for="cantidad_items">Cantidad</label>
                    <input class="form-control" type="number" id="cantidad_items" name="cantidad_items" value="<?php echo isset($client) ? htmlspecialchars($client->cantidad_items) : 1; ?>" min="1" required style="padding: 0.75rem 1rem;">
                </div>
            </div>

            <!-- Estado de Llamada -->
            <div class="form-group" style="grid-column: span 1;">
                <label class="form-label" for="estado_llamada">Estado de Llamada *</label>
                <select class="form-control" id="estado_llamada" name="estado_llamada" required>
                    <?php foreach ($estados as $est): ?>
                        <option value="<?php echo $est; ?>" <?php echo ($estado_llamada == $est) ? 'selected' : ''; ?>>
                            <?php echo $est; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['estado_llamada'])): ?>
                    <div class="error-text"><?php echo htmlspecialchars($errors['estado_llamada']); ?></div>
                <?php endif; ?>
            </div>

            <!-- Lapsos (Día / Hora) -->
            <div class="form-group" style="grid-column: span 1;">
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                    <div>
                        <label class="form-label" for="lapso_dias">Llamar en (Fecha/Almanaque) *</label>
                        <input class="form-control" type="date" id="lapso_dias" name="lapso_dias" value="<?php echo !empty($lapso_dias) ? $lapso_dias : date('Y-m-d'); ?>" required style="padding: 0.65rem 1rem;">
                        <?php if (isset($errors['lapso_dias'])): ?>
                            <div class="error-text"><?php echo htmlspecialchars($errors['lapso_dias']); ?></div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label class="form-label" for="lapso_tiempo">Llamar en (Hora Exacta) *</label>
                        <input class="form-control" type="time" id="lapso_tiempo" name="lapso_tiempo" value="<?php echo !empty($lapso_tiempo) ? $lapso_tiempo : date('H:i'); ?>" required style="padding: 0.65rem 1rem;">
                        <?php if (isset($errors['lapso_tiempo'])): ?>
                            <div class="error-text"><?php echo htmlspecialchars($errors['lapso_tiempo']); ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Imagen / Archivo Adjunto (Paso 6) -->
            <div class="form-group" style="grid-column: span 2;">
                <label class="form-label" for="archivo_adjunto">Imagen o Archivo Adjunto *</label>
                <input type="hidden" name="existing_archivo_adjunto" value="<?php echo $archivo_adjunto; ?>">
                <input class="form-control" type="file" id="archivo_adjunto" name="archivo_adjunto" <?php echo empty($archivo_adjunto) ? 'required' : ''; ?> accept="image/*,application/pdf,application/zip">
                <?php if($archivo_adjunto): ?>
                    <div style="margin-top: 0.5rem; font-size: 0.85rem;">
                        <span>Archivo actual: </span>
                        <a href="<?php echo $archivo_adjunto; ?>" target="_blank" style="color: var(--highlight-color); text-decoration: underline; font-weight: 500;"><?php echo basename($archivo_adjunto); ?></a>
                    </div>
                <?php endif; ?>
                <?php if (isset($errors['archivo_adjunto'])): ?>
                    <div class="error-text"><?php echo htmlspecialchars($errors['archivo_adjunto']); ?></div>
                <?php endif; ?>
            </div>

            <!-- Observación -->
            <div class="form-group" style="grid-column: span 2; margin-bottom: 2rem;">
                <label class="form-label" for="observacion">Observación *</label>
                <textarea class="form-control" id="observacion" name="observacion" required placeholder="Escriba los detalles y observaciones obligatorias sobre la llamada..."><?php echo $observacion; ?></textarea>
                <?php if (isset($errors['observacion'])): ?>
                    <div class="error-text"><?php echo htmlspecialchars($errors['observacion']); ?></div>
                <?php endif; ?>
            </div>
        </div>

        <button class="btn btn-primary" type="submit" style="width:100%;">
            <svg style="width:20px;height:20px;fill:currentColor" viewBox="0 0 24 24">
                <path d="M15,9H5V5H15M12,19A3,3 0 0,1 9,16A3,3 0 0,1 12,13A3,3 0 0,1 15,16A3,3 0 0,1 12,19M17,3H5C3.89,3 3,3.9 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V7L17,3Z" />
            </svg>
            <span>Guardar Registro</span>
        </button>
    </form>
</div>

<!-- Cascading dropdown logic + Tom Select (Paso 4) -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    var estadoSelect = document.getElementById('estado_id');
    var municipioSelect = document.getElementById('municipio_id');
    var ciudadSelect = document.getElementById('ciudad_id');

    // Init Tom Select
    var tsEstado = new TomSelect('#estado_id', {
        create: false,
        placeholder: 'Seleccione Estado...',
        controlInput: '<input class="ts-control-search">',
        onChange: function(val) {
            updateMunicipios(val);
        }
    });

    var tsMunicipio = new TomSelect('#municipio_id', {
        create: false,
        placeholder: 'Seleccione Municipio...',
        controlInput: '<input class="ts-control-search">' ,
        onChange: function(val) {
            updateCiudades(val);
        }
    });

    var tsCiudad = new TomSelect('#ciudad_id', {
        create: false,
        placeholder: 'Seleccione Ciudad/Sector...',
        controlInput: '<input class="ts-control-search">'
    });

    // Campaigns cascading select
    var tsCampana = new TomSelect('#campana_id', {
        create: false,
        placeholder: 'Seleccione Campaña...',
        onChange: function(val) {
            updateCampaignItems(val);
        }
    });

    var tsCampanaItem = new TomSelect('#campana_item_id', {
        create: false,
        placeholder: 'Seleccione Producto...'
    });

    function updateCampaignItems(campaignId) {
        tsCampanaItem.clear();
        tsCampanaItem.clearOptions();
        tsCampanaItem.disable();

        if (!campaignId) return;

        fetch('api/campaigns/items?campaign_id=' + campaignId)
            .then(function(res) { return res.json(); })
            .then(function(data) {
                var options = data.map(function(item) {
                    return { value: item.id, text: item.nombre_producto + ' ($' + item.precio + ')' };
                });
                tsCampanaItem.addOptions(options);
                tsCampanaItem.enable();
            });
    }

    function updateMunicipios(estadoId) {
        tsMunicipio.clear();
        tsMunicipio.clearOptions();
        tsMunicipio.disable();

        tsCiudad.clear();
        tsCiudad.clearOptions();
        tsCiudad.disable();

        if (!estadoId) return;

        fetch('api/locations?type=municipios&estado_id=' + estadoId)
            .then(function(res) { return res.json(); })
            .then(function(data) {
                var options = data.map(function(item) {
                    return { value: item.id, text: item.nombre };
                });
                tsMunicipio.addOptions(options);
                tsMunicipio.enable();
            });
    }

    function updateCiudades(municipioId) {
        tsCiudad.clear();
        tsCiudad.clearOptions();
        tsCiudad.disable();

        if (!municipioId) return;

        fetch('api/locations?type=ciudades&municipio_id=' + municipioId)
            .then(function(res) { return res.json(); })
            .then(function(data) {
                var options = data.map(function(item) {
                    return { value: item.id, text: item.nombre };
                });
                tsCiudad.addOptions(options);
                tsCiudad.enable();
            });
    }
});
</script>
