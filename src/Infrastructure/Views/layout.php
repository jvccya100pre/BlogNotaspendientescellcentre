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

    <style>
        /* Self-contained Navbar and Dropdown Styles (ins6.md) */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            background: rgba(18, 2, 2, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            color: #ffffff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .navbar-brand span {
            color: var(--highlight-color, #ffdf20);
        }

        .navbar-nav {
            display: flex;
            list-style: none;
            align-items: center;
            gap: 10px;
            margin: 0;
            padding: 0;
        }

        .nav-item {
            position: relative;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            padding: 0.4rem 0.8rem;
            border-radius: 6px;
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
        }

        .nav-link:hover,
        .nav-link:focus {
            color: var(--highlight-color, #ffdf20);
            background: rgba(255, 255, 255, 0.05);
        }

        /* Dropdown Menu styling */
        .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            min-width: 200px;
            background: rgba(30, 8, 9, 0.98);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            border-radius: 8px;
            padding: 0.5rem 0;
            margin-top: 0.5rem;
            list-style: none;
            z-index: 150;
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: opacity 0.25s ease, transform 0.25s ease, visibility 0.25s;
        }

        .dropdown.show .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-icon {
            display: inline-block;
            transition: transform 0.25s ease;
        }

        .dropdown.show .dropdown-icon {
            transform: rotate(180deg);
        }

        .dropdown-item {
            display: block;
            padding: 0.6rem 1.2rem;
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            transition: all 0.2s ease;
        }

        .dropdown-item:hover {
            background: rgba(255, 255, 255, 0.05);
            color: var(--highlight-color, #ffdf20);
            padding-left: 1.5rem;
        }

        .navbar-user {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-tag {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.7);
            background: rgba(255, 255, 255, 0.08);
            padding: 0.35rem 0.8rem;
            border-radius: 20px;
        }

        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                gap: 1rem;
                padding: 1rem 1.5rem;
                text-align: center;
            }

            .navbar-nav {
                width: 100%;
                justify-content: center;
                gap: 0.5rem;
                flex-direction: column;
            }

            .nav-item {
                width: 100%;
            }

            .nav-link {
                width: 100%;
                justify-content: center;
                padding: 0.6rem;
            }

            .dropdown-menu {
                position: static;
                display: none;
                opacity: 1;
                visibility: visible;
                transform: none;
                box-shadow: none;
                background: rgba(0, 0, 0, 0.2);
                width: 100%;
                margin-top: 0.25rem;
                border-radius: 6px;
            }

            .dropdown.show .dropdown-menu {
                display: block;
            }

            .dropdown-item {
                text-align: center;
                padding: 0.6rem;
            }

            .dropdown-item:hover {
                padding-left: 1.2rem;
            }

            .navbar-user {
                width: 100%;
                justify-content: center;
                flex-direction: column;
                gap: 0.5rem;
            }
        }
    </style>
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

        <?php if (isset($_SESSION['user'])): 
            $db = DatabaseConnection::getInstance();
            $usernameEscaped = mysqli_real_escape_string($db, $_SESSION['user']['username']);
            $isAdminRes = mysqli_query($db, "SELECT `is_admin` FROM `biartet_users` WHERE `username` = '$usernameEscaped' LIMIT 1");
            $isAdminRow = $isAdminRes ? mysqli_fetch_assoc($isAdminRes) : null;
            $isAdmin = ($isAdminRow && (int)$isAdminRow['is_admin'] === 1);
        ?>
            <!--Agregar 'style' de letra para los 3 'a href' y quitar el subrayado que indica link separando 10px entra palabras y colocar color amarillo a las letras -->
            <div class="navbar-nav">

                <a href="./" style='color:yellow; font-weight:bold; text-decoration:none;'>INICIO</a>
                <a href="./?view=pendientes" style='color:yellow; font-weight:bold; text-decoration:none;'>PENDIENTES</a>
                <a href="./?view=exitosas" style='color:yellow; font-weight:bold; text-decoration:none;'>EXITOSAS</a>
                <a href="campaigns" style='color:yellow; font-weight:bold; text-decoration:none;'>CAMPAÑAS</a>

                <!-- Dropdown interactivo de Herramientas -->
                <div class="nav-item dropdown" style="position: relative; display: inline-block;">
                    <a href="#" class="dropdown-toggle" style='color:yellow; font-weight:bold; text-decoration:none; display: flex; align-items: center; gap: 4px;'>
                        📢 HERRAMIENTAS <span class="dropdown-icon" style="font-size:0.6rem; vertical-align:middle; display:inline-block; transition: transform 0.25s ease;">▼</span>
                    </a>
                    <div class="dropdown-menu">
                        <a href="memorandums" class="dropdown-item">📢 Memorándums</a>
                        <a href="products" class="dropdown-item">📦 Catálogo de Productos</a>
                        <?php if ($isAdmin): ?>
                            <a href="Admin/" class="dropdown-item">👥 Panel Admin (Usuarios)</a>
                            <a href="daily-prizes" class="dropdown-item">💰 Control de Premios/Día</a>
                            <a href="logs" class="dropdown-item">📋 Logs del Sistema</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="navbar-user" style="display: flex; align-items: center; gap: 15px;">
                <span class="user-tag"><?php echo htmlspecialchars($_SESSION['user']['username']); ?></span>
                <a href="logout" class="btn btn-secondary btn-danger action-btn"
                    style="padding: 0.35rem 0.8rem; font-size: 0.85rem; text-decoration: none;">Salir</a>
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