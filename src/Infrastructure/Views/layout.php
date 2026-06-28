<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'Call Center'; ?> - Sistema de Clientes Pendientes</title>
    <!-- Base href to handle subdirectory mapping if needed -->
    <base href="<?php echo rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/'; ?>">
    <link rel="stylesheet" href="css/style.css">

    <!-- Tom Select CDN (Paso 4) -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
</head>

<body>

    <!-- Main Navigation Bar -->
    <nav class="navbar">
        <a href="./" class="navbar-brand">
            <svg style="width:24px;height:24px;fill:currentColor" viewBox="0 0 24 24">
                <path
                    d="M6.62,10.79C8.06,13.62 10.38,15.94 13.21,17.38L15.41,15.18C15.69,14.9 16.08,14.82 16.43,14.93C17.55,15.3 18.75,15.5 20,15.5A1,1 0 0,1 21,16.5V20A1,1 0 0,1 20,21A17,17 0 0,1 3,4A1,1 0 0,1 4,3H7.5A1,1 0 0,1 8.5,4C8.5,5.25 8.7,6.45 9.07,7.57C9.18,7.92 9.1,8.31 8.82,8.59L6.62,10.79Z" />
            </svg>
            <span>CallCenter</span>
        </a>

        <?php if (isset($_SESSION['user'])): ?>
            <!--Agregar 'style' de letra para los 3 'a href' y quitar el subrayado que indica link separando 10px entra palabras y colocar color amarillo a las letras -->
            <div class="navbar-nav">

                <a href="./" style='color:yellow; font-weight:bold; margin-right:10px;'>INICIO</a>
                <a href="./?view=pendientes" style='color:yellow; font-weight:bold; margin-right:10px;'>PENDIENTES</a>
                <a href="./?view=exitosas" style='color:yellow; font-weight:bold; margin-right:10px;'>EXITOSAS</a>
            </div>

            <div class="navbar-user">
                <span class="user-tag"><?php echo htmlspecialchars($_SESSION['user']['username']); ?></span>
                <a href="logout" class="btn btn-secondary btn-danger action-btn"
                    style="padding: 0.35rem 0.8rem; font-size: 0.85rem;">Salir</a>
            </div>
        <?php endif; ?>
    </nav>

    <!-- Main Content Container -->
    <div class="container">
        <?php echo $content; ?>
    </div>

    <!-- Inactivity Timeout Warning Modal (Paso 2) -->
    <?php if (isset($_SESSION['user'])): ?>
        <div id="inactivityModal" class="modal-overlay"
            style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.85); z-index:9999; justify-content:center; align-items:center; backdrop-filter:blur(10px);">
            <div class="glass-card"
                style="max-width:400px; text-align:center; padding:2.5rem; border:1px solid var(--highlight-color); border-radius:16px;">
                <svg style="width:64px;height:64px;fill:var(--highlight-color);margin-bottom:1rem;display:inline-block;"
                    viewBox="0 0 24 24">
                    <path d="M12,2L1,21H23L12,2M12,6L19.8,18H4.2L12,6M11,10V14H13V10H11M11,16V18H13V16H11Z" />
                </svg>
                <h3 style="margin-bottom:0.75rem; color:var(--highlight-color); font-size:1.5rem;">¿Sigues ahí?</h3>
                <p style="margin-bottom:1.5rem; font-size:0.95rem; color:rgba(255,255,255,0.8); line-height:1.5;">Su sesión
                    está a punto de cerrarse por inactividad. Se cerrará automáticamente en <strong id="inactivityTimer"
                        style="color:var(--highlight-color); font-size:1.1rem;">60</strong> segundos.</p>
                <button id="extendSessionBtn" class="btn btn-highlight" style="width:100%;">Extender Inicio de
                    Sesión</button>
            </div>
        </div>

        <script>
            (function () {
                var inactivityTime = 0;
                var warningTime = 540; // 9 minutes (in seconds)
                var logoutTime = 600;  // 10 minutes (in seconds)
                var countdownInterval;
                var checkInterval;
                var lastPingTime = Date.now();

                // Reset activity counter
                function resetInactivity() {
                    inactivityTime = 0;
                }

                // Activity listeners
                window.onload = resetInactivity;
                window.onmousemove = resetInactivity;
                window.onmousedown = resetInactivity;
                window.onclick = resetInactivity;
                window.onscroll = resetInactivity;
                window.onkeypress = resetInactivity;

                // Check status every second
                checkInterval = setInterval(function () {
                    inactivityTime++;

                    // Silent heartbeat to keep server alive if there is local activity
                    if (inactivityTime === 0 && (Date.now() - lastPingTime > 240000)) { // 4 minutes
                        lastPingTime = Date.now();
                        var xhr = new XMLHttpRequest();
                        xhr.open('POST', 'session/keep-alive', true);
                        xhr.send();
                    }

                    if (inactivityTime >= warningTime) {
                        clearInterval(checkInterval); // Pause normal check
                        var modal = document.getElementById('inactivityModal');
                        if (modal) {
                            modal.style.display = 'flex';
                            startCountdown();
                        }
                    }
                }, 1000);

                function startCountdown() {
                    var remaining = logoutTime - warningTime;
                    var timerSpan = document.getElementById('inactivityTimer');
                    if (timerSpan) {
                        timerSpan.textContent = remaining;
                    }

                    countdownInterval = setInterval(function () {
                        remaining--;
                        if (timerSpan) {
                            timerSpan.textContent = remaining;
                        }

                        if (remaining <= 0) {
                            clearInterval(countdownInterval);
                            window.location.href = 'logout?inactive=1';
                        }
                    }, 1000);
                }

                var extendBtn = document.getElementById('extendSessionBtn');
                if (extendBtn) {
                    extendBtn.addEventListener('click', function () {
                        var xhr = new XMLHttpRequest();
                        xhr.open('POST', 'session/keep-alive', true);
                        xhr.onload = function () {
                            if (xhr.status === 200) {
                                document.getElementById('inactivityModal').style.display = 'none';
                                clearInterval(countdownInterval);
                                inactivityTime = 0;
                                lastPingTime = Date.now();

                                // Resume normal checks
                                checkInterval = setInterval(function () {
                                    inactivityTime++;

                                    if (inactivityTime === 0 && (Date.now() - lastPingTime > 240000)) {
                                        lastPingTime = Date.now();
                                        var pXhr = new XMLHttpRequest();
                                        pXhr.open('POST', 'session/keep-alive', true);
                                        pXhr.send();
                                    }

                                    if (inactivityTime >= warningTime) {
                                        clearInterval(checkInterval);
                                        var pModal = document.getElementById('inactivityModal');
                                        if (pModal) {
                                            pModal.style.display = 'flex';
                                            startCountdown();
                                        }
                                    }
                                }, 1000);
                            } else {
                                window.location.href = 'logout';
                            }
                        };
                        xhr.send();
                    });
                }
            })();
        </script>
    <?php endif; ?>

    <!-- Scroll Up Button in Bottom Right Corner -->
    <button id="btnScrollTop" class="btn-scroll-top" title="Subir al inicio">
        <svg viewBox="0 0 24 24">
            <path d="M7.41,15.41L12,10.83L16.59,15.41L18,14L12,8L6,14L7.41,15.41Z" />
        </svg>
    </button>

    <!-- Scroll to Top Script -->
    <script>
        (function () {
            var btn = document.getElementById('btnScrollTop');

            // Show/hide button on scroll
            window.addEventListener('scroll', function () {
                if (window.pageYOffset > 300) {
                    btn.classList.add('show');
                } else {
                    btn.classList.remove('show');
                }
            });

            // Scroll to top on click
            btn.addEventListener('click', function () {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        })();
    </script>

    <!-- Dropdown Interactive Script -->
    <script>
        (function () {
            var dropdowns = document.querySelectorAll('.nav-item.dropdown');
            dropdowns.forEach(function (dropdown) {
                var toggle = dropdown.querySelector('.dropdown-toggle');
                if (toggle) {
                    toggle.addEventListener('click', function (e) {
                        e.preventDefault();
                        e.stopPropagation();

                        // Close other dropdowns if any
                        dropdowns.forEach(function (other) {
                            if (other !== dropdown) {
                                other.classList.remove('show');
                            }
                        });

                        dropdown.classList.toggle('show');
                    });
                }
            });

            // Close dropdowns when clicking outside
            document.addEventListener('click', function (e) {
                dropdowns.forEach(function (dropdown) {
                    if (!dropdown.contains(e.target)) {
                        dropdown.classList.remove('show');
                    }
                });
            });
        })();
    </script>
</body>

</html>