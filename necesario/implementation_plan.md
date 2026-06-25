# Plan de Implementación - Instrucciones de Ins2.md

Este documento describe la estrategia técnica para implementar los 15 pasos indicados en el archivo [Ins2.md](file:///d:/htdocs%20Solo%20Full1/Mis%20proyectos%20Xampp8030/pendientes.createsoftw.com/necesario/Ins2.md) en la aplicación de Call Center.

## User Review Required

> [!IMPORTANT]
> - **Acceso por Enlace (Paso 1 y 13):** Para garantizar que el enlace de inicio de sesión funcione tanto para `jvczxc2021@gmail.com` como para `frank@gmail.com` (o cualquier otro correo provisto en el parámetro `email`), la aplicación creará automáticamente al usuario en la base de datos con la contraseña dada si no existe previamente, y luego iniciará la sesión.
> - **Estado de Llamada Simplificado (Paso 10):** Se eliminarán 9 estados de llamada del formulario y del modelo. Los únicos estados válidos que quedarán activos serán **Pendiente** y **Exito pedido pendiente**.
> - **Campos Obligatorios (Paso 11):** Todos los campos del formulario de registro de clientes (incluyendo observación, lapsos, dirección, ubicación geográfica y archivo adjunto) y del formulario de pedido serán obligatorios.

## Open Questions

*Ninguna. Todos los requerimientos están especificados.*

## Proposed Changes

---

### Base de Datos y Migraciones

#### [NEW] [migration.php](file:///d:/htdocs%20Solo%20Full1/Mis%20proyectos%20Xampp8030/pendientes.createsoftw.com/src/Infrastructure/Database/migration.php)
Crear un script de migración en PHP para estructurar y poblar la base de datos:
- Crear las tablas `biartet_venezuela_estados`, `biartet_venezuela_municipios` y `biartet_venezuela_ciudades`.
- Descargar y parsear la información de `venezuela.json` para poblar las tablas geográficas, mapeando los estados, municipios y sus parroquias/ciudades correspondientes.
- Crear la tabla `biartet_pedido` con los campos: `id` (Orden), `cliente`, `telefono`, `producto`, `precio`, `direccion`, `pago`, `fecha` (Fecha de entrega), `nota` y `fecha_creacion`.
- Alterar la tabla `biartet_clientes` agregando las columnas `estado_id`, `municipio_id`, `ciudad_id` y `archivo_adjunto` si no existen.
- Asegurar la existencia del usuario `frank@gmail.com` con contraseña `584126317284`.

---

### Modelo y Lógica de Dominio

#### [MODIFY] [Client.php](file:///d:/htdocs%20Solo%20Full1/Mis%20proyectos%20Xampp8030/pendientes.createsoftw.com/src/Domain/Model/Client.php)
- Añadir las nuevas propiedades: `$estado_id`, `$municipio_id`, `$ciudad_id` y `$archivo_adjunto`.
- Modificar el método `validate()` para que todos los campos sean obligatorios (incluyendo los geográficos, lapsos, observación y archivo adjunto).
- Actualizar la lista de estados válidos permitiendo únicamente `Pendiente` y `Exito pedido pendiente`.

---

### Acceso a Datos (Persistencia)

#### [MODIFY] [MysqlClientRepository.php](file:///d:/htdocs%20Solo%20Full1/Mis%20proyectos%20Xampp8030/pendientes.createsoftw.com/src/Infrastructure/Persistence/MysqlClientRepository.php)
- Modificar las consultas de `INSERT` y `UPDATE` para incluir las columnas `estado_id`, `municipio_id`, `ciudad_id` y `archivo_adjunto`.
- Modificar el método `mapRowToClient()` para poblar los nuevos atributos del modelo `Client`.
- Implementar métodos auxiliares para consultar estados, municipios por estado y ciudades por municipio directamente de la base de datos.

---

### Ruteador y Controladores

#### [MODIFY] [index.php](file:///d:/htdocs%20Solo%20Full1/Mis%20proyectos%20Xampp8030/pendientes.createsoftw.com/index.php)
- Incorporar el procesamiento de auto-login vía parámetros GET en la URL (`email`, `pass`, `token=identificador_unico`) al inicio de la carga. Si los datos son válidos, crear al usuario si no existe, iniciar sesión, enviar correo y redirigir a `./` para limpiar la URL.
- Agregar las siguientes rutas al enrutador:
  - `GET /api/locations` -> `ClientController@getLocations` (para cascada AJAX de estados/municipios/ciudades).
  - `POST /orders/save` -> `ClientController@saveOrder` (para guardar pedidos).
  - `POST /session/keep-alive` -> `AuthController@keepAlive` (para extender la sesión).

#### [MODIFY] [AuthController.php](file:///d:/htdocs%20Solo%20Full1/Mis%20proyectos%20Xampp8030/pendientes.createsoftw.com/src/Infrastructure/Controller/AuthController.php)
- Agregar validación de inactividad de 10 minutos en las peticiones web en base a `$_SESSION['last_activity']`.
- Enviar correos de notificación mediante la función `mail()` de PHP en los eventos de inicio y cierre de sesión.
- Implementar el método `keepAlive()` para actualizar `$_SESSION['last_activity']`.

#### [MODIFY] [ClientController.php](file:///d:/htdocs%20Solo%20Full1/Mis%20proyectos%20Xampp8030/pendientes.createsoftw.com/src/Infrastructure/Controller/ClientController.php)
- En `save()`, procesar la subida del archivo adjunto a la carpeta `uploads/`. Si no se sube un nuevo archivo en edición, mantener el actual. Validar obligatoriedad de todos los campos.
- Implementar `saveOrder()` para validar y guardar un nuevo pedido en `biartet_pedido`.
- Implementar `getLocations()` para devolver la lista de ubicaciones geográficas en formato JSON para el frontend.
- Modificar `index()` para consultar tanto los clientes como los pedidos para mostrarlos en el panel.

---

### Vistas y Interfaz de Usuario (Frontend)

#### [MODIFY] [style.css](file:///d:/htdocs%20Solo%20Full1/Mis%20proyectos%20Xampp8030/pendientes.createsoftw.com/css/style.css)
- Añadir estilos para el modal de "Exito pedido pendiente" y la tabla de pedidos.
- Incorporar estilos para el botón de revelar/ocultar contraseña.
- Adaptar las clases del plugin **Tom Select** para que encaje visualmente con la estética de Glassmorphic oscuro.
- Diseñar la notificación emergente de la alarma sonora y el aviso de inactividad.

#### [MODIFY] [layout.php](file:///d:/htdocs%20Solo%20Full1/Mis%20proyectos%20Xampp8030/pendientes.createsoftw.com/src/Infrastructure/Views/layout.php)
- Incluir en el `<head>` la hoja de estilos de **Tom Select** y su script JS desde CDN.
- Implementar el script JS de control de inactividad de 10 minutos:
  - A los 9 minutos de inactividad, muestra un modal con un contador de 60 segundos y un botón "Extender Sesión".
  - Si se presiona "Extender Sesión", realiza un POST AJAX a `/session/keep-alive` y reinicia el temporizador.
  - De lo contrario, redirige automáticamente a `/logout?inactive=1` para cerrar sesión con mensaje de despedida.

#### [MODIFY] [login.php](file:///d:/htdocs%20Solo%20Full1/Mis%20proyectos%20Xampp8030/pendientes.createsoftw.com/src/Infrastructure/Views/login.php)
- Agregar un botón con icono de ojo en el campo de contraseña para alternar su visibilidad (`password` <-> `text`).
- Mostrar un mensaje de despedida si se redirige por inactividad.

#### [MODIFY] [client_form.php](file:///d:/htdocs%20Solo%20Full1/Mis%20proyectos%20Xampp8030/pendientes.createsoftw.com/src/Infrastructure/Views/client_form.php)
- Configurar la etiqueta `<form>` con `enctype="multipart/form-data"` para permitir la subida de archivos.
- Agregar los selects de Estado, Municipio y Ciudad arriba del campo de Dirección.
- Inicializar **Tom Select** en los tres campos geográficos para hacerlos auto-completables y modernos.
- Implementar la lógica JS para actualizar en cascada los selectores: al cambiar Estado, cargar Municipios; al cambiar Municipio, cargar Ciudades.
- Agregar el campo de tipo `file` para el archivo adjunto (con link al archivo actual si ya existe).
- Ajustar las opciones del select `Llamar en (Horas)` según las opciones requeridas de lapsos con incrementos de media hora.
- Marcar todos los campos como requeridos.

#### [MODIFY] [dashboard.php](file:///d:/htdocs%20Solo%20Full1/Mis%20proyectos%20Xampp8030/pendientes.createsoftw.com/src/Infrastructure/Views/dashboard.php)
- Agregar el botón "Exito pedido pendiente" en la cabecera.
- Crear el modal/formulario emergente para registrar un pedido en `biartet_pedido`.
- Añadir una segunda tabla en la parte inferior para listar los "Pedidos Registrados".
- En la parte superior derecha de la sección de tablas, añadir el filtro de Fecha de Creación.
- Implementar el filtro unificado en JS para buscar en tiempo real en ambas tablas simultáneamente (por término de búsqueda y por fecha de creación).
- Añadir el sistema de alarma sonora: un script que evalúa cada 30 segundos si el tiempo transcurrido desde el registro de algún cliente pendiente excede las horas programadas en su campo `Llamar en (Horas)`. Si se cumple, reproduce el archivo `alarma/alarma.mp3` de forma continua y muestra una alerta visual con opción de silenciar.

## Verification Plan

### Manual Verification
1. **Acceso por Enlace:** Abrir la URL `http://localhost/pendientes/?email=frank@gmail.com&pass=584126317284&token=identificador_unico` en el navegador y verificar que inicia sesión automáticamente y redirige al panel.
2. **Cierre de Sesión por Inactividad:** Cambiar temporalmente el tiempo de inactividad a 1 minuto y verificar que a los 50 segundos se muestra el diálogo y si no se pulsa, se cierra la sesión mostrando el mensaje.
3. **Revelar Contraseña:** Ir a la pantalla de login, escribir una contraseña y pulsar el ojo para verificar que se muestra y se oculta correctamente.
4. **Combos Geográficos de Venezuela:** Ir a "Nuevo Registro", verificar que el selector de Estados muestra las opciones venezolanas, que al escoger "Miranda" se cargan sus municipios (ej. Guaicaipuro) y que al escoger un municipio se cargan sus ciudades correspondientes.
5. **Formulario y Archivo Adjunto:** Intentar guardar un cliente sin rellenar algún campo y verificar que el navegador lo impide. Guardar un registro con imagen adjunta y comprobar que se almacena en la carpeta `uploads/`.
6. **Filtro de Fecha:** Cambiar el filtro de fecha en la cabecera y comprobar que filtra las filas de clientes y de pedidos por su fecha de creación.
7. **Formulario de Pedidos:** Pulsar el botón "Exito pedido pendiente", llenar el formulario, presionar "Registrar Cliente" y verificar que se inserta en la base de datos y aparece en la tabla de pedidos.
8. **Alarma Sonora:** Registrar un cliente y poner "1/2 hora" de lapso. Alterar la fecha de creación en la base de datos para que sea de hace 35 minutos. Verificar que la alarma suena y se muestra el banner de advertencia.
