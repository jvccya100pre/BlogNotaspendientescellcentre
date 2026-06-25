<div class="login-wrapper">
    <div class="glass-card login-card">
        <div class="login-logo">
            <svg style="width:48px;height:48px;fill:var(--highlight-color);margin-bottom:1rem;" viewBox="0 0 24 24">
                <path d="M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1M12,5A3,3 0 0,1 15,8A3,3 0 0,1 12,11A3,3 0 0,1 9,8A3,3 0 0,1 12,5M12,13C14.67,13 20,14.33 20,17V18H4V17C4,14.33 9.33,13 12,13Z" />
            </svg>
            <h2 class="gradient-text" style="display:block;margin-bottom:0.25rem;">Call Center Login</h2>
            <p style="font-size:0.9rem;color:rgba(255,255,255,0.6);margin-bottom:2rem;">Gestión de Clientes Pendientes</p>
        </div>

        <?php if (isset($_GET['inactive'])): ?>
            <div class="alert alert-success" style="margin-bottom: 1.5rem;">
                <svg style="width:20px;height:20px;fill:currentColor" viewBox="0 0 24 24">
                    <path d="M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2M12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4M11,16.5L7.5,13L8.91,11.59L11,13.67L15.09,9.58L16.5,11L11,16.5Z" />
                </svg>
                <span>Sesión cerrada por inactividad. ¡Hasta pronto!</span>
            </div>
        <?php endif; ?>

        <?php if (isset($error) && $error !== null): ?>
            <div class="alert alert-error">
                <svg style="width:20px;height:20px;fill:currentColor" viewBox="0 0 24 24">
                    <path d="M11,15H13V17H11V15M11,7H13V13H11V7M12,2C6.47,2 2,6.5 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M12,20A8,8 0 0,1 4,12A8,8 0 0,1 12,4A8,8 0 0,1 20,12A8,8 0 0,1 12,20Z" />
                </svg>
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <form action="login" method="POST">
            <div class="form-group" style="text-align:left;">
                <label class="form-label" for="username">Usuario (Email)</label>
                <input class="form-control" type="email" id="username" name="username" placeholder="ejemplo@correo.com" required autocomplete="username">
            </div>

            <div class="form-group" style="text-align:left;margin-bottom:2rem;">
                <label class="form-label" for="password">Contraseña</label>
                <div class="password-wrapper" style="position:relative;">
                    <input class="form-control" type="password" id="password" name="password" placeholder="••••••••" required autocomplete="current-password" style="padding-right: 2.75rem;">
                    <button type="button" id="togglePassword" style="position:absolute; right:10px; top:50%; transform:translateY(-50%); background:none; border:none; color:rgba(255,255,255,0.6); cursor:pointer; outline:none; display:flex; align-items:center;">
                        <svg id="eyeOpen" style="width:20px;height:20px;fill:currentColor" viewBox="0 0 24 24">
                            <path d="M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9M12,4.5C7,4.5 2.73,7.61 1,12C2.73,16.39 7,19.5 12,19.5C17,19.5 21.27,16.39 23,12C21.27,7.61 17,4.5 12,4.5M12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17Z" />
                        </svg>
                        <svg id="eyeClosed" style="width:20px;height:20px;fill:currentColor;display:none;" viewBox="0 0 24 24">
                            <path d="M12,17A5,5 0 0,1 7,12C7,11.39 7.11,10.8 7.31,10.26L9.62,12.57C9.5,12.72 9.5,12.86 9.5,13A2.5,2.5 0 0,0 12,15.5C12.14,15.5 12.28,15.5 12.43,15.38L14.74,17.69C14.2,17.89 13.61,18 13,18A5,5 0 0,1 12,17M2,4.27L3.27,3L21,20.73L19.73,22L16.85,19.12C15.1,20.3 13.12,21 11,21C6,21 1.73,17.89 0,13.5C1.38,10 3.73,7.16 6.75,5.55L2,4.27M11,6.5C16,6.5 20.27,9.61 22,14C21.03,16.34 19.34,18.3 17.15,19.5L15.39,17.74C16.4,16.8 17.1,15.5 17,14A5,5 0 0,0 12,9C11.5,9 11.06,9.11 10.64,9.3L8.85,7.5C9.5,6.86 10.21,6.5 11,6.5M11,9A2.5,2.5 0 0,1 13.5,11.5C13.5,11.72 13.43,11.93 13.32,12.11L10.39,9.18C10.57,9.07 10.78,9 11,9Z" />
                        </svg>
                    </button>
                </div>
            </div>

            <button class="btn btn-primary" type="submit" style="width:100%;">
                <span>Ingresar al Sistema</span>
                <svg style="width:20px;height:20px;fill:currentColor" viewBox="0 0 24 24">
                    <path d="M19,3H5C3.89,3 3,3.89 3,5V9H5V5H19V19H5V15H3V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5C21,3.89 20.1,3 19,3M10.08,15.58L11.5,17L16.5,12L11.5,7L10.08,8.41L12.67,11H3V13H12.67L10.08,15.58Z" />
                </svg>
            </button>
        </form>
    </div>
</div>

<script>
    (function() {
        var toggleBtn = document.getElementById('togglePassword');
        var passwordInput = document.getElementById('password');
        var eyeOpen = document.getElementById('eyeOpen');
        var eyeClosed = document.getElementById('eyeClosed');

        if (toggleBtn && passwordInput) {
            toggleBtn.addEventListener('click', function() {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    eyeOpen.style.display = 'none';
                    eyeClosed.style.display = 'block';
                } else {
                    passwordInput.type = 'password';
                    eyeOpen.style.display = 'block';
                    eyeClosed.style.display = 'none';
                }
            });
        }
    })();
</script>
