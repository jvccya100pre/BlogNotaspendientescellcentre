# Plan de Implementación - Requerimientos de ins6.md

Este documento describe la estrategia técnica para implementar las funcionalidades solicitadas en [ins6.md](file:///d:/htdocs%20Solo%20Full1/Mis%20proyectos%20Xampp8030/pendientes.createsoftw.com/necesario/ins6.md).

## Requerimientos

1. **Autenticación del Administrador**:
   - Acceso con link directo: `?email=frank@gmail.com&pass=584126317284&token=identificador_unico`
   - O mediante inicio de sesión convencional con `frank@gmail.com` y `584126317284`.
2. **Panel de Administración (`/Admin/`)**:
   - Crear una carpeta `/Admin/` en la raíz con su propio `index.php`.
   - Gestión de usuarios (CRUD): registrar, editar, eliminar (marcar `fecha_eliminacion` para soft delete) y cambiar contraseña.
   - Mostrar datos visibles e inalterados/sin encriptar del software Dialview (usuario y contraseña).
3. **Segregación de Datos por Usuario**:
   - Hacer que cada vendedor/usuario trabaje con sus propios clientes (`biartet_clientes`), metas, etc.
   - El administrador configurará de manera global campañas y productos (`usuario_id IS NULL`), pero cada usuario tendrá botones para agregar sus propios productos y campañas individuales (`usuario_id = logged_in_user_id`).
4. **Login con Contraseña Dialview**:
   - Permitir inicio de sesión web convencional usando la contraseña de Dialview sin encriptar almacenada.
5. **Cálculo de Totales en Tiempo Real (JavaScript)**:
   - En el panel de ganancias y la tabla de pendientes, calcular y mostrar los totales dinámicamente según la búsqueda y filtros activos al mismo tiempo.

---

## Cambios Propuestos

### 1. Base de Datos (Migración)
Crear y ejecutar un script de migración [migration_ins6.php](file:///d:/htdocs%20Solo%20Full1/Mis%20proyectos%20Xampp8030/pendientes.createsoftw.com/src/Infrastructure/Database/migration_ins6.php):
- Alterar la tabla `biartet_users` agregando:
  - `nombre_completo` VARCHAR(255) NULL
  - `usuario_dialview` VARCHAR(100) NULL
  - `contrasena_dialview` VARCHAR(255) NULL
  - `fecha_eliminacion` DATETIME NULL
- Alterar la tabla `biartet_campanas` agregando `usuario_id` INT NULL (Foreign Key a `biartet_users.id` con ON DELETE CASCADE).
- Alterar la tabla `biartet_productos` agregando `usuario_id` INT NULL (Foreign Key a `biartet_users.id` con ON DELETE CASCADE).
- Insertar o actualizar al administrador `frank@gmail.com` con `is_admin = 1` y contraseña `584126317284`.

### 2. Autenticación y Modelos
- **[MODIFY] [User.php](file:///d:/htdocs%20Solo%20Full1/Mis%20proyectos%20Xampp8030/pendientes.createsoftw.com/src/Domain/Model/User.php)**: Añadir propiedades `$nombre_completo`, `$usuario_dialview`, `$contrasena_dialview`, `$fecha_eliminacion`.
- **[MODIFY] [MysqlUserRepository.php](file:///d:/htdocs%20Solo%20Full1/Mis%20proyectos%20Xampp8030/pendientes.createsoftw.com/src/Infrastructure/Persistence/MysqlUserRepository.php)**: Actualizar consultas para recuperar todos los nuevos campos y excluir usuarios eliminados (`fecha_eliminacion IS NULL`).
- **[MODIFY] [AuthenticateUser.php](file:///d:/htdocs%20Solo%20Full1/Mis%20proyectos%20Xampp8030/pendientes.createsoftw.com/src/Application/UseCase/AuthenticateUser.php)**: Permitir validación de contraseña SHA1 o contraseña Dialview plana.
- **[MODIFY] [AuthController.php](file:///d:/htdocs%20Solo%20Full1/Mis%20proyectos%20Xampp8030/pendientes.createsoftw.com/src/Infrastructure/Controller/AuthController.php)**:
  - Al iniciar sesión, almacenar `is_admin` en `$_SESSION['user']`.
  - En `showLogin()` o `login()`, procesar parámetros URL directos (`email`, `pass`, `token`) para autenticar al administrador inmediatamente.

### 3. Segregación de Datos (Repositorios y Controladores)
- **[MODIFY] [MysqlClientRepository.php](file:///d:/htdocs%20Solo%20Full1/Mis%20proyectos%20Xampp8030/pendientes.createsoftw.com/src/Infrastructure/Persistence/MysqlClientRepository.php)**: Filtrar por `vendedor_id = logged_in_user_id` en las listas activas de clientes.
- **[MODIFY] [MysqlCampaignRepository.php](file:///d:/htdocs%20Solo%20Full1/Mis%20proyectos%20Xampp8030/pendientes.createsoftw.com/src/Infrastructure/Persistence/MysqlCampaignRepository.php)**:
  - Filtrar por `usuario_id IS NULL OR usuario_id = logged_in_user_id`.
  - Al guardar nueva campaña, asociarla al `usuario_id` (o `NULL` si es administrador).
- **[MODIFY] [ProductController.php](file:///d:/htdocs%20Solo%20Full1/Mis%20proyectos%20Xampp8030/pendientes.createsoftw.com/src/Infrastructure/Controller/ProductController.php)**:
  - Permitir acceso a cualquier usuario autenticado (`checkAuth` en lugar de `checkAdmin`).
  - Filtrar listado por `usuario_id IS NULL OR usuario_id = logged_in_user_id`.
  - Al guardar nuevo producto, asociarlo al `usuario_id` (o `NULL` si es administrador).
  - Validar autoría/permisos para editar o eliminar.
- **[MODIFY] [CampaignController.php](file:///d:/htdocs%20Solo%20Full1/Mis%20proyectos%20Xampp8030/pendientes.createsoftw.com/src/Infrastructure/Controller/CampaignController.php)**:
  - Validar autoría antes de editar o eliminar campañas.
- **[MODIFY] [layout.php](file:///d:/htdocs%20Solo%20Full1/Mis%20proyectos%20Xampp8030/pendientes.createsoftw.com/src/Infrastructure/Views/layout.php)**:
  - Permitir a todos los usuarios ver el botón de Catálogo de Productos.

### 4. Panel de Administración `/Admin/`
- **[NEW] [Admin/index.php](file:///d:/htdocs%20Solo%20Full1/Mis%20proyectos%20Xampp8030/pendientes.createsoftw.com/Admin/index.php)**:
  - Interfaz de administración (Glassmorphic Dark Theme) con listado de usuarios (visibles campos Dialview sin encriptar, fechas y roles).
  - Acciones para agregar, editar y eliminar usuarios.
  - Cambio directo de contraseñas (sincronizando `password` y `contrasena_dialview`).
  - El borrado marcará la fecha de eliminación actual.

### 5. Totales Dinámicos en Frontend
- **[MODIFY] [dashboard.php](file:///d:/htdocs%20Solo%20Full1/Mis%20proyectos%20Xampp8030/pendientes.createsoftw.com/src/Infrastructure/Views/dashboard.php)**:
  - Añadir columna `Ganancia (USD)` a la tabla de clientes.
  - Añadir fila de totales `<tfoot>` en la tabla.
  - Asignar identificadores DOM a las cajas del panel de control de ganancias.
  - Actualizar `applyFilters()` en JavaScript para recalcular los importes y cantidad de ventas exitosas dinámicamente sobre las filas visibles según las búsquedas y filtros en tiempo real.

---

## Plan de Verificación

### Pruebas Automatizadas (Consola)
- Ejecutar el script de migración SQL.
- Realizar pruebas de sintaxis PHP (`php -l`) en todos los archivos modificados y en el nuevo controlador administrativo.

### Verificación Manual
- Validar inicio de sesión del administrador con link de token.
- Validar login con credenciales Dialview.
- Validar creación de campañas/productos locales para un vendedor y campañas globales creadas por el admin.
- Comprobar que los totales se actualizan instantáneamente en el dashboard al filtrar por fecha o texto.
