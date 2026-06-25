 # Cosas que no debo olvidar
 Uso de colores ROOT
	:root {
		--corporate-color: #460809; /* Color Corporativo (Rojo Oscuro) */
		--corporate-colore: #B11F19; /* Color Corporativo (Rojo Oscuro) */
		--highlight-color: #FFDF20; /* Color de Resalte (Amarillo/Dorado) */
		--text-color-light: #FFFFFF;
		--border-color: #FFFFFF;
	}
	
	
	Generado con links Amigables y MVC

	Panel Usuario con Credenciales: Usuario ("jvczxc2021@gmail.com", Password "Losteques.2026")

    Mysql credenciales => array(
        'host' => 'localhost',
        'name' => 'createso_datosVPS',
        'user' => 'createso_vpsdatos',
        'pass' => 'Tresado37#',
        'charset' => 'utf8mb4',
    );

	php version 5.2.3

	Boton subir scroll en la ezquina inferior derecha 
# Sistame de llamada de Colcenter a clientes pendientes
	Aplicar La arquitectura hexagonal

    Generar una web con inicio de session sin recuperar contraseña ni registrarse solo iniciar session y cerrar session.

    registrar en campos imput 
        1. numero de telefono
        2. nombre
        3. dirección
        4. estado de llamada: 
            Pendiente
            Llamar de nuevo
            Exito pedido pendiente
            No contesta
            No hay respuesta
            Teléfono descompuesto
            Teléfono ocupado
            Cambio de número
            Número no existe
            Fuera de servicio
            no interesado
        5. observacion: texto para anotar algo importante  
        6. llamar dentro de lapso de tiempo: 
            1 hora
            2 horas
            3 horas
            4 horas
            5 horas
            6 horas
            7 horas
            8 horas
            9 horas
            10 horas
            11 horas
            12 horas
            13 horas
            14 horas
            15 horas
            16 horas
            17 horas
            18 horas
            19 horas
            20 horas
            21 horas
            22 horas
            23 horas
            24 horas
        7. lLamar en lapso de dias maximo 20 dias.
            1 dia
            2 dias
            3 dias
            4 dias
            5 dias
            6 dias
            7 dias
            8 dias
            9 dias
            10 dias
            11 dias
            12 dias
            13 dias
            14 dias
            15 dias
            16 dias
            17 dias
            18 dias
            19 dias
            20 dias
        8. Boton guardar: guardar 

todo debe almacenarse en la base de datos MYSQL y debe tener su respectivo CRUD con sus respectivos validaciones. en esta aplicacion la base de datos ya esta creada solo se debe crear las tablas y los campos necesarios. de los campos antes mencionados. de la tabla con su prefijo "biartet_".

cada registro debe contener un campo fecha_creacion y fecha_actualizacion, ademas de un id autogenerado.

los campos antes mencionados son opcionales solo se debe crear los campos necesarios para que la aplicacion funcione.

la aplicacion debe ser responsiva y debe funcionar en dispositivos moviles. la aplicacion debe tener un diseño moderno y atractivo. 

la aplicacion debe tener un diseño moderno y atractivo. la aplicacion debe tener un diseño moderno y atractivo. la aplicacion debe tener un diseño moderno y atractivo.

cada registro debe tener un campo identificador_unico el cual servira para identificar cada registro de forma unica. este campo debe ser autogenerado.

cada registro debe tener un campo estado el cual permitira cambiar el estado de registro de activo a inactivo.  este campo debe ser autogenerado.

cada registro debe tener un campo fecha_creacion y fecha_actualizacion, ademas de un id autogenerado.

se podra imprimir en formato ".txt" el archivo reporte en texto plano selecionando el dia mediante calendario. el archivo reporte debe tener el formato "Día Mes año - Hora.txt" y debe contener la siguiente informacion:

Nombre

numero de telefono

dirección

estado de llamada: 
    Pendiente
    Llamar de nuevo
    Exito pedido pendiente
    No contesta
    No hay respuesta
    Teléfono descompuesto
    Teléfono ocupado
    Cambio de número
    Número no existe
    Fuera de servicio
    no interesado

observacion: texto para anotar algo importante  

fecha_creacion

fecha_actualizacion

Debe crearse un archivo ".sql" para crear las tablas necesarias. con los nombres de tablas con el prefijo "biartet_".