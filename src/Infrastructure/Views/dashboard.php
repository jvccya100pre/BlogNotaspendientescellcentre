<div class="glass-card">
    <div
        style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; margin-bottom:1.5rem; gap:1rem;">
        <div>
            <h1 class="gradient-text" style="margin-bottom:0.25rem;">Total Clientes Pendientes</h1>
            <p style="font-size:0.9rem; color:rgba(255,255,255,0.6);">Gestión de llamadas y seguimiento de pendientes
            </p>
        </div>
        <div style="display:flex; gap:1rem; align-items:center;">
            <!-- Alarm bell trigger button -->
            <button type="button" id="btn_alarm_bell" class="btn btn-secondary"
                style="position:relative; display: inline-flex; align-items: center; justify-content: center; width: 42px; height: 42px; padding: 0; border-radius: 50%; cursor: pointer; transition: all 0.3s ease; transform-origin: 50% 0%;">
                <svg id="bell_icon_svg" style="width:24px;height:24px;fill:currentColor;" viewBox="0 0 24 24">
                    <path
                        d="M12,2A3,3 0 0,0 9,5V5.28C6.1,6.46 4,9.45 4,13V19L2,21V22H22V21L20,19V13C20,9.45 17.9,6.46 15,5.28V5A3,3 0 0,0 12,2M12,24A3,3 0 0,0 15,21H9A3,3 0 0,0 12,24Z" />
                </svg>
                <!-- Alarm count badge -->
                <span id="alarm_badge"
                    style="display:none; position:absolute; top:-4px; right:-4px; background:#ef4444; color:#fff; border-radius:50%; width:20px; height:20px; font-size:0.75rem; font-weight:bold; align-items:center; justify-content:center; border:2px solid #1a1a1a; line-height:1;">0</span>
            </button>

            <!-- Exito pedido pendiente Button (Paso 9) -->
            <button type="button" id="btn_exito_pedido" class="btn btn-highlight"
                style="display: inline-flex; align-items: center; gap: 0.5rem;">
                <svg style="width:20px;height:20px;fill:currentColor" viewBox="0 0 24 24">
                    <path d="M17,18L12,15.82L7,18V5H17M17,3H7A2,2 0 0,0 5,5V21L12,18L19,21V5C19,3.89 18.1,3 17,3Z" />
                </svg>
                <span>Exito pedido pendiente</span>
            </button>
            <a href="clients/create" class="btn btn-primary">
                <svg style="width:20px;height:20px;fill:currentColor" viewBox="0 0 24 24">
                    <path d="M19,13H13V19H11V13H5V11H11V5H13V11H19V13Z" />
                </svg>
                <span>Nuevo Registro</span>
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if (isset($success) && $success !== null): ?>
        <div class="alert alert-success">
            <svg style="width:20px;height:20px;fill:currentColor" viewBox="0 0 24 24">
                <path
                    d="M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2M12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4M11,16.5L7.5,13L8.91,11.59L11,13.67L15.09,9.58L16.5,11L11,16.5Z" />
            </svg>
            <span><?php echo htmlspecialchars($success); ?></span>
        </div>
    <?php endif; ?>

    <?php if (isset($error) && $error !== null): ?>
        <div class="alert alert-error">
            <svg style="width:20px;height:20px;fill:currentColor" viewBox="0 0 24 24">
                <path
                    d="M11,15H13V17H11V15M11,7H13V13H11V7M12,2C6.47,2 2,6.5 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M12,20A8,8 0 0,1 4,12A8,8 0 0,1 12,4A8,8 0 0,1 20,12A8,8 0 0,1 12,20Z" />
            </svg>
            <span><?php echo htmlspecialchars($error); ?></span>
        </div>
    <?php endif; ?>

    <!-- Dashboard Search and Date Filters (Paso 7) -->
    <div class="dashboard-actions"
        style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; margin-bottom:1.5rem;">
        <div class="search-box" style="flex:1; min-width:300px;">
            <input type="text" id="search_input" class="form-control"
                placeholder="Buscar por Nombre, Teléfono, ID o Detalles...">
        </div>

        <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
            <div class="date-filter-box" style="display: flex; align-items: center; gap: 0.5rem;">
                <label for="filter_date"
                    style="font-size: 0.9rem; color: rgba(255,255,255,0.7); white-space: nowrap;">Filtrar Fecha:</label>
                <input type="date" id="filter_date" class="form-control"
                    style="width: auto; max-width: 160px; padding: 0.4rem 0.8rem; font-size: 0.85rem;">
                <button type="button" id="clear_date" class="btn btn-secondary"
                    style="padding: 0.4rem 0.6rem; font-size: 0.8rem; height: 35px;"
                    title="Limpiar Fecha">&times;</button>
            </div>

            <form action="report" method="GET" class="report-box"
                style="margin-bottom:0; display:flex; align-items:center; gap:0.5rem;">
                <label for="report_date"
                    style="font-size: 0.9rem; color: rgba(255,255,255,0.7); white-space: nowrap;">Reporte del
                    día:</label>
                <input type="date" id="report_date" name="date" value="<?php echo date('Y-m-d'); ?>" required
                    style="padding:0.4rem 0.8rem; font-size: 0.85rem;">
                <button type="submit" class="btn btn-highlight"
                    style="padding: 0.4rem 1rem; font-size: 0.85rem; height: 35px;">
                    <svg style="width:16px;height:16px;fill:currentColor" viewBox="0 0 24 24">
                        <path d="M5,20H19V18H5V20M19,9H15V3H9V9H5L12,16L19,9Z" />
                    </svg>
                    <span>TXT</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Client List Table -->
    <div class="table-responsive">
        <?php if (empty($clients)): ?>
            <div style="padding:3rem; text-align:center; color:rgba(255,255,255,0.4);">
                <svg style="width:64px;height:64px;fill:currentColor;margin-bottom:1rem;" viewBox="0 0 24 24">
                    <path
                        d="M15.5,12C18,12 20,14 20,16.5C20,17.38 19.75,18.21 19.31,18.9L22.39,22L21,23.39L17.88,20.32C17.47,20.75 17,21 16.5,21C14,21 12,19 12,16.5C12,14 14,12 15.5,12M15.5,14A2.5,2.5 0 0,0 13,16.5A2.5,2.5 0 0,0 15.5,19A2.5,2.5 0 0,0 18,16.5A2.5,2.5 0 0,0 15.5,14M10,4A4,4 0 0,1 14,8C14,8.86 13.72,9.66 13.25,10.3C11.97,11.24 11.12,12.7 11.03,14.39C10.69,14.14 10.36,14 10,14A4,4 0 0,1 6,10A4,4 0 0,1 10,4M10,16C12.44,16 14.74,16.79 16.63,18.1L15.19,19.54C13.72,18.57 11.93,18 10,18C6.5,18 3.42,19.93 2,22.8C2,19 5.58,16 10,16Z" />
                </svg>
                <p>No hay registros de clientes pendientes.</p>
            </div>
        <?php else: ?>
            <table class="custom-table clients-table">
                <thead>
                    <tr>
                        <th>ID Único</th>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Ubicación y Dirección</th>
                        <th>Estado de Llamada</th>
                        <th>Lapsos (Día/Hora)</th>
                        <th style="width: 150px; text-align: center;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clients as $c):
                        // Map status to class badge
                        $badgeClass = 'badge-pendiente';
                        $statusNormalized = strtolower($c->estado_llamada);
                        if ($statusNormalized == 'exito pedido pendiente') {
                            $badgeClass = 'badge-exito-pedido';
                        }
                        ?>
                        <tr data-fecha-creacion="<?php echo date('Y-m-d', strtotime($c->fecha_creacion)); ?>"
                            data-fecha-creacion-full="<?php echo date('Y-m-d H:i:s', strtotime($c->fecha_creacion)); ?>"
                            data-fecha-creacion-timestamp="<?php echo strtotime($c->fecha_creacion); ?>"
                            data-posponer-hasta-timestamp="<?php echo !empty($c->posponer_hasta) ? strtotime($c->posponer_hasta) : 0; ?>"
                            data-lapso-tiempo="<?php echo htmlspecialchars($c->lapso_tiempo); ?>"
                            data-nombre="<?php echo htmlspecialchars($c->nombre); ?>"
                            data-id-unico="<?php echo htmlspecialchars($c->identificador_unico); ?>"
                            data-status="<?php echo htmlspecialchars($c->estado_llamada); ?>">

                            <td style="font-weight:600; color:var(--highlight-color);">
                                <?php echo htmlspecialchars($c->identificador_unico); ?></td>
                            <td style="font-weight:500;"><?php echo htmlspecialchars($c->nombre); ?></td>
                            <td><?php echo htmlspecialchars($c->telefono); ?></td>
                            <td>
                                <!-- Geographical location display (Paso 4) -->
                                <div style="font-size: 0.85rem; color: var(--highlight-color); margin-bottom: 0.25rem;">
                                    📍 <?php
                                    $estName = $repo->getEstadoName($c->estado_id);
                                    $muniName = $repo->getMunicipioName($c->municipio_id);
                                    $cityName = $repo->getCiudadName($c->ciudad_id);
                                    echo htmlspecialchars(implode(', ', array_filter(array($estName, $muniName, $cityName))));
                                    ?>
                                </div>
                                <div style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"
                                    title="<?php echo htmlspecialchars($c->direccion); ?>">
                                    <?php echo htmlspecialchars($c->direccion); ?>
                                </div>
                                <!-- File attachment display (Paso 6) -->
                                <?php if ($c->archivo_adjunto): ?>
                                    <div style="margin-top: 0.25rem; font-size: 0.8rem;">
                                        📎 <a href="<?php echo htmlspecialchars($c->archivo_adjunto); ?>" target="_blank"
                                            style="color: #a7f3d0; text-decoration: underline;"><?php echo basename($c->archivo_adjunto); ?></a>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span
                                    class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($c->estado_llamada); ?></span>
                            </td>
                            <td>
                                <div style="font-size:0.85rem; color:rgba(255,255,255,0.7);">
                                    <?php if ($c->lapso_dias): ?>
                                        <div>📅 <?php echo htmlspecialchars($c->lapso_dias); ?></div><?php endif; ?>
                                    <?php if ($c->lapso_tiempo): ?>
                                        <div>⏱️ <?php echo htmlspecialchars($c->lapso_tiempo); ?></div><?php endif; ?>
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <div class="action-buttons">
                                    <a href="clients/edit?id=<?php echo $c->id; ?>" class="btn btn-secondary action-btn"
                                        title="Editar">
                                        <svg style="width:16px;height:16px;fill:currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M20.71,7.04C21.1,6.65 21.1,6 20.71,5.63L18.37,3.29C18,2.9 17.35,2.9 16.96,3.29L15.12,5.12L18.87,8.87M3,17.25V21H6.75L17.81,9.93L14.07,6.19L3,17.25Z" />
                                        </svg>
                                    </a>
                                    <a href="clients/delete?id=<?php echo $c->id; ?>" class="btn btn-danger action-btn"
                                        title="Eliminar"
                                        onclick="return confirm('¿Está seguro de que desea eliminar este cliente?');">
                                        <svg style="width:16px;height:16px;fill:currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M19,4H15.5L14.5,3H9.5L8.5,4H5V6H19M6,19A2,2 0 0,0 8,21H16A2,2 0 0,0 18,19V7H6V19Z" />
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

<!-- Pedidos Registrados Table (Paso 9) -->
<div class="glass-card" style="margin-top: 2rem;">
    <div>
        <h2 class="gradient-text" style="margin-bottom:0.25rem;">Pedidos Registrados</h2>
        <p style="font-size:0.9rem; color:rgba(255,255,255,0.6); margin-bottom:1.5rem;">Registro de pedidos concretados
            exitosamente (Tabla biartet_pedido)</p>
    </div>

    <div class="table-responsive">
        <?php if (empty($orders)): ?>
            <div style="padding:3rem; text-align:center; color:rgba(255,255,255,0.4);">
                <svg style="width:64px;height:64px;fill:currentColor;margin-bottom:1rem;" viewBox="0 0 24 24">
                    <path
                        d="M19,19H5V8H19M19,3H18V1H16V3H8V1H6V3H5C3.89,3 3,3.9 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5A2,2 0 0,0 19,3Z" />
                </svg>
                <p>No hay pedidos registrados en la tabla.</p>
            </div>
        <?php else: ?>
            <table class="custom-table orders-table">
                <thead>
                    <tr>
                        <th>Orden #</th>
                        <th>Cliente</th>
                        <th>Teléfono</th>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Pago</th>
                        <th>Fecha Entrega</th>
                        <th>Nota</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $o): ?>
                        <tr data-fecha-creacion="<?php echo date('Y-m-d', strtotime($o['fecha_creacion'])); ?>">
                            <td style="font-weight:600; color:var(--highlight-color);"><?php echo htmlspecialchars($o['id']); ?>
                            </td>
                            <td style="font-weight:500;"><?php echo htmlspecialchars($o['cliente']); ?></td>
                            <td><?php echo htmlspecialchars($o['telefono']); ?></td>
                            <td><?php echo htmlspecialchars($o['producto']); ?></td>
                            <td style="font-weight: 500; color: #a7f3d0;"><?php echo htmlspecialchars($o['precio']); ?></td>
                            <td>
                                <span class="badge badge-exito-pedido"
                                    style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); color: #34d399;">
                                    <?php echo htmlspecialchars($o['pago']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($o['fecha']))); ?></td>
                            <td>
                                <div style="max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"
                                    title="<?php echo htmlspecialchars($o['nota']); ?>">
                                    <?php echo htmlspecialchars($o['nota']); ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Dialog for Exito pedido pendiente (Paso 9) -->
<div id="orderModal" class="modal-overlay"
    style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.85); z-index:9999; justify-content:center; align-items:center; backdrop-filter:blur(8px);">
    <div class="glass-card"
        style="width:100%; max-width:600px; max-height:90vh; overflow-y:auto; padding:2.5rem; border-radius:16px; position:relative;">
        <button type="button" id="closeOrderModal"
            style="position:absolute; right:20px; top:20px; background:none; border:none; color:rgba(255,255,255,0.6); font-size:2rem; cursor:pointer; line-height:1; outline:none;">&times;</button>
        <h2 class="gradient-text" style="margin-bottom:1.5rem;">Registrar Pedido Exitoso</h2>

        <!-- Alerts container inside modal -->
        <div id="orderModalAlert" class="alert" style="display:none; margin-bottom:1.5rem;"></div>

        <form id="orderForm">
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1.2rem;">
                <!-- Orden (Read-only / No colocar nada) -->
                <div class="form-group">
                    <label class="form-label" for="order_id">Orden (Auto)</label>
                    <input class="form-control" type="text" id="order_id" value="Automático" disabled
                        style="opacity:0.6;">
                </div>

                <!-- Cliente -->
                <div class="form-group">
                    <label class="form-label" for="order_cliente">Cliente *</label>
                    <input class="form-control" type="text" id="order_cliente" name="cliente" required
                        placeholder="Nombre del cliente">
                </div>

                <!-- Teléfono -->
                <div class="form-group">
                    <label class="form-label" for="order_telefono">Teléfono *</label>
                    <input class="form-control" type="text" id="order_telefono" name="telefono" required
                        placeholder="Ej: +584123456789">
                </div>

                <!-- Producto -->
                <div class="form-group">
                    <label class="form-label" for="order_producto">Producto *</label>
                    <input class="form-control" type="text" id="order_producto" name="producto" required
                        placeholder="Nombre del producto">
                </div>

                <!-- Precio -->
                <div class="form-group">
                    <label class="form-label" for="order_precio">Precio *</label>
                    <input class="form-control" type="text" id="order_precio" name="precio" required
                        placeholder="Ej: 45.50">
                </div>

                <!-- Pago -->
                <div class="form-group">
                    <label class="form-label" for="order_pago">Pago *</label>
                    <input class="form-control" type="text" id="order_pago" name="pago" required
                        placeholder="Ej: Zelle / Pago Móvil">
                </div>

                <!-- Fecha -->
                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label" for="order_fecha">Fecha de Entrega *</label>
                    <input class="form-control" type="datetime-local" id="order_fecha" name="fecha" required>
                </div>

                <!-- Dirección -->
                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label" for="order_direccion">Dirección *</label>
                    <textarea class="form-control" id="order_direccion" name="direccion" required
                        placeholder="Dirección de envío/entrega" style="min-height:80px;"></textarea>
                </div>

                <!-- Nota -->
                <div class="form-group" style="grid-column: span 2; margin-bottom:1.5rem;">
                    <label class="form-label" for="order_nota">Nota *</label>
                    <textarea class="form-control" id="order_nota" name="nota" required
                        placeholder="Instrucción de despacho" style="min-height:60px;">Llamar antes.</textarea>
                </div>
            </div>

            <button class="btn btn-primary" type="submit" style="width:100%;">
                <span>Registrar Cliente</span>
            </button>
        </form>
    </div>
</div>

<!-- Alarm Notification Centralized Modal (Paso 8, 15 y modificaciones) -->
<div id="alarmOverlay" class="modal-overlay"
    style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.85); z-index:9999; justify-content:center; align-items:center; backdrop-filter:blur(8px); -webkit-backdrop-filter:blur(8px);">
    <div class="glass-card"
        style="padding:2rem; border:1px solid #ef4444; box-shadow: 0 10px 35px rgba(239,68,68,0.3); border-radius:16px; background: rgba(30, 8, 9, 0.98); margin-bottom:0; width:95%; max-width:500px; position:relative; pointer-events:auto;">
        <!-- Close Button (X) -->
        <button id="closeAlarmModal"
            style="position:absolute; top:1.25rem; right:1.25rem; background:none; border:none; color:rgba(255,255,255,0.5); font-size:1.5rem; cursor:pointer; line-height:1; transition:color 0.2s;"
            onmouseover="this.style.color='#ef4444'"
            onmouseout="this.style.color='rgba(255,255,255,0.5)'">&times;</button>

        <div style="display:flex; align-items:center; gap:1rem; margin-bottom:1.5rem;">
            <svg class="alarm-bell-icon" style="width:36px;height:36px;fill:#ef4444;" viewBox="0 0 24 24">
                <path
                    d="M12,2A3,3 0 0,0 9,5V5.28C6.1,6.46 4,9.45 4,13V19L2,21V22H22V21L20,19V13C20,9.45 17.9,6.46 15,5.28V5A3,3 0 0,0 12,2M12,24A3,3 0 0,0 15,21H9A3,3 0 0,0 12,24Z" />
            </svg>
            <div>
                <h4 style="margin:0; color:#ef4444; font-size:1.3rem; font-weight:700;">Lapsos Cumplidos</h4>
                <p style="margin:0; font-size:0.9rem; color:rgba(255,255,255,0.6);">Alarma de llamadas vencidas</p>
            </div>
        </div>
        <div id="alarmList"
            style="max-height:250px; overflow-y:auto; font-size:0.95rem; margin-bottom:1.5rem; text-align:left; color:rgba(255,255,255,0.95); border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:1rem;">
            <!-- Triggered alarm items injected here -->
        </div>
        <button id="silenceAlarmBtn" class="btn btn-danger"
            style="width:100%; padding:0.75rem; font-size:1rem; font-weight:600; display:flex; justify-content:center; align-items:center; gap:0.5rem;">
            <svg style="width:20px;height:20px;fill:currentColor" viewBox="0 0 24 24">
                <path
                    d="M12,2a10,10 0 1,1 10,10A10,10 0 0,1 12,2m0,2a8,8 0 1,0 8,8A8,8 0 0,0 12,4m-1,8H7v-2h4V6h2v4h4v2h-4v4h-2V12Z" />
            </svg>
            <span>Silenciar Todas las Alarmas</span>
        </button>
    </div>
</div>

<!-- Scripts for Modal, Unified Filtering and Alarm Systems -->
<script>
    (function () {
        // ----------------------------------------------------
        // 1. Modal Logic (Paso 9)
        // ----------------------------------------------------
        var btnExitoPedido = document.getElementById('btn_exito_pedido');
        var orderModal = document.getElementById('orderModal');
        var closeOrderModal = document.getElementById('closeOrderModal');
        var orderForm = document.getElementById('orderForm');
        var orderModalAlert = document.getElementById('orderModalAlert');

        if (btnExitoPedido && orderModal) {
            btnExitoPedido.addEventListener('click', function () {
                // Clean alerts & form
                orderForm.reset();
                document.getElementById('order_nota').value = 'Llamar antes.';
                orderModalAlert.style.display = 'none';
                orderModalAlert.className = 'alert';
                orderModalAlert.innerHTML = '';

                // Show modal
                orderModal.style.display = 'flex';
            });
        }

        if (closeOrderModal && orderModal) {
            closeOrderModal.addEventListener('click', function () {
                orderModal.style.display = 'none';
            });
        }

        if (orderForm) {
            orderForm.addEventListener('submit', function (e) {
                e.preventDefault();

                // Collect fields
                var cliente = document.getElementById('order_cliente').value.trim();
                var telefono = document.getElementById('order_telefono').value.trim();
                var producto = document.getElementById('order_producto').value.trim();
                var precio = document.getElementById('order_precio').value.trim();
                var pago = document.getElementById('order_pago').value.trim();
                var fecha = document.getElementById('order_fecha').value.trim();
                var direccion = document.getElementById('order_direccion').value.trim();
                var nota = document.getElementById('order_nota').value.trim();

                // Frontend validation (Paso 11)
                if (!cliente || !telefono || !producto || !precio || !pago || !fecha || !direccion || !nota) {
                    showModalAlert('error', 'Error: Todos los campos son obligatorios.');
                    return;
                }

                // Submit via AJAX
                var params = 'cliente=' + encodeURIComponent(cliente) +
                    '&telefono=' + encodeURIComponent(telefono) +
                    '&producto=' + encodeURIComponent(producto) +
                    '&precio=' + encodeURIComponent(precio) +
                    '&pago=' + encodeURIComponent(pago) +
                    '&fecha=' + encodeURIComponent(fecha) +
                    '&direccion=' + encodeURIComponent(direccion) +
                    '&nota=' + encodeURIComponent(nota);

                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'orders/save', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function () {
                    var res = {};
                    try {
                        res = JSON.parse(xhr.responseText);
                    } catch (err) {
                        res = { status: 'error', message: 'Respuesta del servidor no válida.' };
                    }

                    if (xhr.status === 200 && res.status === 'success') {
                        showModalAlert('success', res.message);

                        // Hide modal and refresh page after 1.5s
                        setTimeout(function () {
                            orderModal.style.display = 'none';
                            window.location.reload();
                        }, 1500);
                    } else {
                        showModalAlert('error', res.message || 'Ocurrió un error al guardar el pedido.');
                    }
                };
                xhr.send(params);
            });
        }

        function showModalAlert(type, message) {
            if (!orderModalAlert) return;
            orderModalAlert.className = 'alert alert-' + (type === 'success' ? 'success' : 'error');
            orderModalAlert.style.display = 'flex';
            orderModalAlert.innerHTML = '<svg style="width:20px;height:20px;fill:currentColor" viewBox="0 0 24 24"><path d="M11,15H13V17H11V15M11,7H13V13H11V7M12,2C6.47,2 2,6.5 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M12,20A8,8 0 0,1 4,12A8,8 0 0,1 12,4A8,8 0 0,1 20,12A8,8 0 0,1 12,20Z" /></svg><span>' + message + '</span>';
        }

        // ----------------------------------------------------
        // 2. Unified Search and Date Filters (Paso 7)
        // ----------------------------------------------------
        var searchInput = document.getElementById('search_input');
        var dateInput = document.getElementById('filter_date');
        var clearDateBtn = document.getElementById('clear_date');

        function applyFilters() {
            var query = searchInput ? searchInput.value.toLowerCase().trim() : '';
            var filterDate = dateInput ? dateInput.value : '';

            // Filter Clientes
            var clientRows = document.querySelectorAll('.clients-table tbody tr');
            clientRows.forEach(function (row) {
                var id = row.cells[0] ? row.cells[0].textContent.toLowerCase() : '';
                var name = row.cells[1] ? row.cells[1].textContent.toLowerCase() : '';
                var phone = row.cells[2] ? row.cells[2].textContent.toLowerCase() : '';
                var dirAndLoc = row.cells[3] ? row.cells[3].textContent.toLowerCase() : '';
                var status = row.cells[4] ? row.cells[4].textContent.toLowerCase() : '';
                var rowDate = row.getAttribute('data-fecha-creacion') || '';

                var textMatches = id.indexOf(query) > -1 ||
                    name.indexOf(query) > -1 ||
                    phone.indexOf(query) > -1 ||
                    dirAndLoc.indexOf(query) > -1 ||
                    status.indexOf(query) > -1;

                var dateMatches = filterDate === '' || rowDate === filterDate;

                if (textMatches && dateMatches) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });

            // Filter Pedidos
            var orderRows = document.querySelectorAll('.orders-table tbody tr');
            orderRows.forEach(function (row) {
                var orderId = row.cells[0] ? row.cells[0].textContent.toLowerCase() : '';
                var client = row.cells[1] ? row.cells[1].textContent.toLowerCase() : '';
                var phone = row.cells[2] ? row.cells[2].textContent.toLowerCase() : '';
                var prod = row.cells[3] ? row.cells[3].textContent.toLowerCase() : '';
                var price = row.cells[4] ? row.cells[4].textContent.toLowerCase() : '';
                var payment = row.cells[5] ? row.cells[5].textContent.toLowerCase() : '';
                var rowDate = row.getAttribute('data-fecha-creacion') || '';

                var textMatches = orderId.indexOf(query) > -1 ||
                    client.indexOf(query) > -1 ||
                    phone.indexOf(query) > -1 ||
                    prod.indexOf(query) > -1 ||
                    price.indexOf(query) > -1 ||
                    payment.indexOf(query) > -1;

                var dateMatches = filterDate === '' || rowDate === filterDate;

                if (textMatches && dateMatches) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        if (searchInput) searchInput.addEventListener('keyup', applyFilters);
        if (dateInput) dateInput.addEventListener('change', applyFilters);
        if (clearDateBtn) {
            clearDateBtn.addEventListener('click', function () {
                if (dateInput) {
                    dateInput.value = '';
                    applyFilters();
                }
            });
        }

        // ----------------------------------------------------
        // 3. Audio Alarm System (Paso 8 y 15)
        // ----------------------------------------------------
        var APP_BASE_URL = '<?php echo rtrim(str_replace(DIRECTORY_SEPARATOR, "/", dirname($_SERVER["SCRIPT_NAME"])), "/") . "/"; ?>';
        var alarmAudio = new Audio(APP_BASE_URL + 'alarma/alarma.mp3');
        alarmAudio.loop = true;
        var alarmOverlay = document.getElementById('alarmOverlay');
        var alarmList = document.getElementById('alarmList');
        var silenceBtn = document.getElementById('silenceAlarmBtn');

        // New DOM elements
        var bellBtn = document.getElementById('btn_alarm_bell');
        var alarmBadge = document.getElementById('alarm_badge');
        var closeAlarmBtn = document.getElementById('closeAlarmModal');

        // Track ID of clients that have been manually silenced during the session
        var silencedClientIds = {};
        var dismissedClientIds = {};
        var isAlarmSounding = false;

        // Unlock audio context on first user click/interaction to bypass autoplay policies
        var audioUnlocked = false;
        function unlockAudio() {
            if (audioUnlocked) return;

            var playPromise = alarmAudio.play();
            if (playPromise !== undefined) {
                playPromise.then(function () {
                    alarmAudio.pause();
                    alarmAudio.currentTime = 0;
                    audioUnlocked = true;
                    console.log("Audio channel unlocked!");
                    checkAlarms(); // Trigger immediate check to start the sound if needed
                }).catch(function (e) {
                    console.log("Audio unlock failed, will try again: ", e);
                });
            } else {
                alarmAudio.pause();
                audioUnlocked = true;
                checkAlarms();
            }

            document.removeEventListener('click', unlockAudio);
            document.removeEventListener('keydown', unlockAudio);
        }
        document.addEventListener('click', unlockAudio);
        document.addEventListener('keydown', unlockAudio);

        window.triggerAlarmAction = function (action, idUnico, minutes) {
            // Stop sound immediately for this item (optimistic feedback)
            silencedClientIds[idUnico] = true;
            checkAlarms();

            var url = APP_BASE_URL + 'api/alarms/action?action=' + action + '&id_unico=' + encodeURIComponent(idUnico) + '&minutes=' + (minutes || 0);

            fetch(url)
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (data.status === 'success') {
                        // Update table row attributes dynamically
                        var row = document.querySelector('tr[data-id-unico="' + idUnico + '"]');
                        if (row) {
                            if (action === 'snooze') {
                                var snoozeMinutes = parseInt(minutes, 10);
                                var futureTimeSeconds = Math.floor(Date.now() / 1000) + (snoozeMinutes * 60);
                                row.setAttribute('data-posponer-hasta-timestamp', futureTimeSeconds);
                            } else if (action === 'delete') {
                                row.setAttribute('data-lapso-tiempo', '');
                            }
                        }
                        // Remove from silenced list as the new state is saved in the DOM
                        delete silencedClientIds[idUnico];
                        delete dismissedClientIds[idUnico];
                    } else {
                        alert('Error: ' + (data.message || 'No se pudo realizar la acción.'));
                        delete silencedClientIds[idUnico]; // restore state
                        delete dismissedClientIds[idUnico];
                    }
                    checkAlarms(); // Full recalculation
                })
                .catch(function (err) {
                    console.error(err);
                    alert('Error de conexión al servidor.');
                    delete silencedClientIds[idUnico];
                    delete dismissedClientIds[idUnico];
                    checkAlarms();
                });
        };

        function parseLapsoTiempo(str) {
            if (!str) return 0;
            str = str.toLowerCase().trim();
            if (str === '1/2 hora') return 0.5;

            var match = str.match(/^(\d+)h\s*1\/2\s*hora$/);
            if (match) {
                return parseInt(match[1]) + 0.5;
            }

            // Fallbacks
            match = str.match(/^(\d+)\s*horas?$/);
            if (match) {
                return parseInt(match[1]);
            }
            return 0;
        }

        function checkAlarms() {
            var rows = document.querySelectorAll('.clients-table tbody tr');
            var nowSeconds = Math.floor(Date.now() / 1000);
            var triggeredAlarms = [];
            var soundShouldPlay = false;

            rows.forEach(function (row) {
                var idUnico = row.getAttribute('data-id-unico');
                var nombre = row.getAttribute('data-nombre');
                var status = row.getAttribute('data-status');
                var lapsoStr = row.getAttribute('data-lapso-tiempo');

                if (status !== 'Pendiente' || !lapsoStr) return;

                var hours = parseLapsoTiempo(lapsoStr);
                if (hours === 0) return;

                var creationTimestamp = parseInt(row.getAttribute('data-fecha-creacion-timestamp'), 10);
                var posponerHastaTimestamp = parseInt(row.getAttribute('data-posponer-hasta-timestamp'), 10);

                if (isNaN(creationTimestamp)) {
                    var creationStr = row.getAttribute('data-fecha-creacion-full');
                    if (!creationStr) return;
                    creationTimestamp = Math.floor(new Date(creationStr.replace(' ', 'T')).getTime() / 1000);
                }
                if (isNaN(posponerHastaTimestamp)) {
                    posponerHastaTimestamp = 0;
                }

                var dueTimestamp = creationTimestamp + (hours * 3600);

                if (nowSeconds >= dueTimestamp && (posponerHastaTimestamp === 0 || nowSeconds >= posponerHastaTimestamp)) {
                    triggeredAlarms.push({ id: idUnico, name: nombre, limit: lapsoStr });

                    // If it hasn't been silenced, trigger sound
                    if (!silencedClientIds[idUnico]) {
                        soundShouldPlay = true;
                    }
                }
            });

            // Update Alarm bell badge and ring animation
            if (triggeredAlarms.length > 0) {
                if (alarmBadge) {
                    alarmBadge.textContent = triggeredAlarms.length;
                    alarmBadge.style.display = 'inline-flex';
                }
                if (soundShouldPlay && bellBtn) {
                    bellBtn.style.animation = 'ring 2s ease infinite';
                    bellBtn.style.borderColor = '#ef4444';
                    bellBtn.style.background = 'rgba(239, 68, 68, 0.15)';
                    bellBtn.style.color = '#ef4444';
                } else if (bellBtn) {
                    bellBtn.style.animation = 'none';
                    bellBtn.style.borderColor = 'rgba(255,255,255,0.15)';
                    bellBtn.style.background = 'rgba(255,255,255,0.05)';
                    bellBtn.style.color = '#fff';
                }
            } else {
                if (alarmBadge) {
                    alarmBadge.style.display = 'none';
                    alarmBadge.textContent = '0';
                }
                if (bellBtn) {
                    bellBtn.style.animation = 'none';
                    bellBtn.style.borderColor = 'rgba(255,255,255,0.15)';
                    bellBtn.style.background = 'rgba(255,255,255,0.05)';
                    bellBtn.style.color = '#fff';
                }
            }

            // Update Alarm overlay display
            if (triggeredAlarms.length > 0) {
                alarmList.innerHTML = '';
                triggeredAlarms.forEach(function (item) {
                    var isSilenced = silencedClientIds[item.id];
                    var badgeText = isSilenced ? ' <span style="font-size:0.75rem; color:#888; margin-left:0.5rem;">(Silenciado)</span>' : '';

                    var actionButtonsHtml = '';
                    if (!isSilenced) {
                        actionButtonsHtml = '<div style="margin-top: 0.5rem; display: flex; gap: 0.5rem; flex-wrap: wrap;">' +
                            '<button onclick="triggerAlarmAction(\'snooze\', \'' + item.id + '\', 5)" class="btn btn-secondary" style="font-size: 0.75rem; padding: 0.25rem 0.5rem; background: rgba(255,255,255,0.08); border-radius: 4px; border: 1px solid rgba(255,255,255,0.15); color: #fff; cursor: pointer;">⏰ Posponer 5 minutos</button>' +
                            '<button onclick="triggerAlarmAction(\'snooze\', \'' + item.id + '\', 10)" class="btn btn-secondary" style="font-size: 0.75rem; padding: 0.25rem 0.5rem; background: rgba(255,255,255,0.08); border-radius: 4px; border: 1px solid rgba(255,255,255,0.15); color: #fff; cursor: pointer;">⏰ Posponer 10 minutos</button>' +
                            '<button onclick="triggerAlarmAction(\'snooze\', \'' + item.id + '\', 20)" class="btn btn-secondary" style="font-size: 0.75rem; padding: 0.25rem 0.5rem; background: rgba(255,255,255,0.08); border-radius: 4px; border: 1px solid rgba(255,255,255,0.15); color: #fff; cursor: pointer;">⏰ Posponer 20 minutos</button>' +
                            '<button onclick="triggerAlarmAction(\'delete\', \'' + item.id + '\')" class="btn btn-danger" style="font-size: 0.75rem; padding: 0.25rem 0.5rem; background: #ef4444; border-radius: 4px; border: none; color: #fff; cursor: pointer;">❌ Eliminar Alarma</button>' +
                            '</div>';
                    }

                    alarmList.innerHTML += '<div style="padding: 0.65rem 0; border-bottom: 1px solid rgba(255,255,255,0.05);">' +
                        '🔔 <strong>' + item.id + '</strong> - ' + item.name + ' (' + item.limit + ')' + badgeText +
                        actionButtonsHtml +
                        '</div>';
                });

                // Check if there is any triggered alarm that has not been dismissed
                var hasUndismissed = false;
                triggeredAlarms.forEach(function (item) {
                    if (!dismissedClientIds[item.id]) {
                        hasUndismissed = true;
                    }
                });

                if (hasUndismissed) {
                    alarmOverlay.style.display = 'flex';
                } else {
                    alarmOverlay.style.display = 'none';
                }
            } else {
                alarmOverlay.style.display = 'none';
            }

            // Sound playing control
            if (soundShouldPlay) {
                if (alarmAudio.paused) {
                    alarmAudio.play().then(function () {
                        isAlarmSounding = true;
                    }).catch(function (e) {
                        console.log("Autoplay restrictions prevented alarm sound. Waiting for user interaction.");
                        isAlarmSounding = false;
                    });
                }
            } else {
                if (!alarmAudio.paused) {
                    alarmAudio.pause();
                }
                isAlarmSounding = false;
            }
        }

        function dismissAllActiveAlarms() {
            var rows = document.querySelectorAll('.clients-table tbody tr');
            var nowSeconds = Math.floor(Date.now() / 1000);

            rows.forEach(function (row) {
                var idUnico = row.getAttribute('data-id-unico');
                var status = row.getAttribute('data-status');
                var lapsoStr = row.getAttribute('data-lapso-tiempo');

                if (status !== 'Pendiente' || !lapsoStr) return;

                var hours = parseLapsoTiempo(lapsoStr);
                if (hours === 0) return;

                var creationTimestamp = parseInt(row.getAttribute('data-fecha-creacion-timestamp'), 10);
                var posponerHastaTimestamp = parseInt(row.getAttribute('data-posponer-hasta-timestamp'), 10);

                if (isNaN(creationTimestamp)) {
                    var creationStr = row.getAttribute('data-fecha-creacion-full');
                    if (!creationStr) return;
                    creationTimestamp = Math.floor(new Date(creationStr.replace(' ', 'T')).getTime() / 1000);
                }
                if (isNaN(posponerHastaTimestamp)) {
                    posponerHastaTimestamp = 0;
                }

                var dueTimestamp = creationTimestamp + (hours * 3600);

                if (nowSeconds >= dueTimestamp && (posponerHastaTimestamp === 0 || nowSeconds >= posponerHastaTimestamp)) {
                    silencedClientIds[idUnico] = true;
                    dismissedClientIds[idUnico] = true;
                }
            });

            // Pause sound immediately
            alarmAudio.pause();
            isAlarmSounding = false;

            // Hide overlay
            alarmOverlay.style.display = 'none';

            // Recheck immediately to update
            checkAlarms();
        }

        if (silenceBtn) {
            silenceBtn.addEventListener('click', function () {
                // Mark all currently triggered alarms as silenced
                var rows = document.querySelectorAll('.clients-table tbody tr');
                var nowSeconds = Math.floor(Date.now() / 1000);

                rows.forEach(function (row) {
                    var idUnico = row.getAttribute('data-id-unico');
                    var status = row.getAttribute('data-status');
                    var lapsoStr = row.getAttribute('data-lapso-tiempo');

                    if (status !== 'Pendiente' || !lapsoStr) return;

                    var hours = parseLapsoTiempo(lapsoStr);
                    if (hours === 0) return;

                    var creationTimestamp = parseInt(row.getAttribute('data-fecha-creacion-timestamp'), 10);
                    var posponerHastaTimestamp = parseInt(row.getAttribute('data-posponer-hasta-timestamp'), 10);

                    if (isNaN(creationTimestamp)) {
                        var creationStr = row.getAttribute('data-fecha-creacion-full');
                        if (!creationStr) return;
                        creationTimestamp = Math.floor(new Date(creationStr.replace(' ', 'T')).getTime() / 1000);
                    }
                    if (isNaN(posponerHastaTimestamp)) {
                        posponerHastaTimestamp = 0;
                    }

                    var dueTimestamp = creationTimestamp + (hours * 3600);

                    if (nowSeconds >= dueTimestamp && (posponerHastaTimestamp === 0 || nowSeconds >= posponerHastaTimestamp)) {
                        silencedClientIds[idUnico] = true;
                    }
                });

                // Pause sound immediately
                alarmAudio.pause();
                isAlarmSounding = false;

                // Recheck immediately to update view list labels
                checkAlarms();
            });
        }

        // Dismiss alarm overlay when clicking 'X'
        if (closeAlarmBtn) {
            closeAlarmBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                dismissAllActiveAlarms();
            });
        }

        // Toggle alarm overlay when clicking the bell icon
        if (bellBtn) {
            bellBtn.addEventListener('click', function (e) {
                e.stopPropagation();

                // Clear dismissed state so all triggered alarms are visible
                dismissedClientIds = {};
                checkAlarms();

                alarmOverlay.style.display = 'flex';
            });
        }

        // Dismiss alarm overlay and silence when clicking outside the card
        document.addEventListener('click', function (event) {
            if (alarmOverlay && (alarmOverlay.style.display === 'flex' || alarmOverlay.style.display === 'block')) {
                var card = alarmOverlay.querySelector('.glass-card');
                if (card && !card.contains(event.target) && !bellBtn.contains(event.target)) {
                    dismissAllActiveAlarms();
                }
            }
        });

        // Run alarm evaluation every 20 seconds
        setInterval(checkAlarms, 20000);
        // Run once on load after 2 seconds
        setTimeout(checkAlarms, 2000);
    })();
</script>