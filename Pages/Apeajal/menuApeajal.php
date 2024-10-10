<?php
session_start();

// Verifica que el usuario ha iniciado sesión y tiene el tipo correcto
if (!isset($_SESSION['id']) || !isset($_SESSION['tipo']) || $_SESSION['tipo'] != 5) {
    // Redirige al usuario a la página de inicio de sesión si no tiene permiso
    header('Location: ../../index.html');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Apeajal</title>
    <link rel="stylesheet" href="../../Styles/menus.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>

    <div class="barra-lateral">
        <div class="nombre-pagina">
            <img id="img_apeajal" src="../../Assets/Img/Imagenes/Logo.jpeg" width="50VW" height="50VH" class="d-inline-block align-top" alt="">
            <span>Apeajal</span>
        </div>

        <button class="boton" data-content="solicitudes">
            <ion-icon name="archive-outline"></ion-icon>
            <span>Solicitudes</span>
        </button>

        <button class="boton" data-content="administradores">
            <ion-icon name="archive-outline"></ion-icon>
            <span>Admins
            </span>
        </button>

        <button class="boton" data-content="juntalocal">
            <ion-icon name="archive-outline"></ion-icon>
            <span>Junta Local</span>
        </button>


        <button class="boton" data-content="huertas">
            <ion-icon name="flower-outline"></ion-icon>
            <span>Huertas</span>
        </button>

        <button class="boton" data-content="tecnicos">
            <ion-icon name="build-outline"></ion-icon>
            <span>Tecnicos</span>
        </button>

        <button class="boton" data-content="productores">
            <ion-icon name="person-circle-outline"></ion-icon>
            <span>Productores</span>
        </button>

        <button class="boton" data-content="laboratorio">
            <ion-icon name="location-outline"></ion-icon>
            <span>Laboratorio</span>
        </button>

        <button class="boton" data-content="municipios">
            <ion-icon name="location-outline"></ion-icon>
            <span>Municipios</span>
        </button>

        <div class="usuario">
            <ion-icon id="key" name="key-outline"></ion-icon>
            <div class="info-usuario">
                <div class="nombre-email">
                    <span class="nombre">David Fregoso Leon</span>
                    <span class="email">davidfregosoleon12@gmail.com</span>
                </div>
                <ion-icon id="settings" name="ellipsis-vertical-outline"></ion-icon>
            </div>
        </div>
    </div>

    <div class="contenido-principal">
        <div class="table">
            <section class="table-header">
                <h1 id="title" >Solicitudes</h1>
                <input id="inputSearch" type="text">
                <button id="btnSearch"> Buscar </button>
                <button id="btnAdd"> Agregar </button>
            </section>
            
            <section class="table-body">
                <table>
                    <thead>
                        <!-- Aquí se cargarán los datos dinámicamente -->
                    </thead>
                    <tbody>
                        <!-- Aquí se cargarán los datos dinámicamente -->
                    </tbody>
                </table>
            </section>
        </div>
    </div>
    
    <script>
    $(document).ready(function() {
        let currentContext = '';

        $('button[data-content="solicitudes"]').click(function() {
            $('#title').text('Solicitudes'); 
            currentContext = 'solicitudes';
            cargarSolicitudes(); 
        });

        $('button[data-content="administradores"]').click(function() {
            $('#title').text('Administradores'); 
            currentContext = 'administradores';
            cargarAdministradores(); 
        });

        // Detectar contexto actual (Productores, Técnicos o Huertas)
        $('button[data-content="juntalocal"]').click(function() {
            $('#title').text('Junta Local'); 
            currentContext = 'juntalocal';
            cargarJuntaLocal(); 
        });

        $('button[data-content="productores"]').click(function() {
            $('#title').text('Productores'); 
            currentContext = 'productores';
            cargarProductores(); 
        });

        $('button[data-content="huertas"]').click(function() {
            $('#title').text('Huertas');
            currentContext = 'huertas';
            cargarHuertas(); 
        });

        $('button[data-content="tecnicos"]').click(function() {
            $('#title').text('Técnicos'); 
            currentContext = 'tecnicos';
            cargarTecnicos(); 
        });
        
        $('button[data-content="municipios"]').click(function() {
            $('#title').text('Municipios');
            currentContext = 'municipios';
            cargarMunicipios(); 
        });

        $('button[data-content="laboratorio"]').click(function() {
            $('#title').text('Laboratorio'); 
            currentContext = 'laboratorio';
            cargarLaboratorio(); 
        });

        // Evento de búsqueda (al hacer clic en el botón o presionar Enter)
        $('#btnSearch').click(function() {
            filtrarDatos(currentContext);
        });

        $('#inputSearch').on('keypress', function(e) {
            if (e.which === 13) { // Tecla Enter
                filtrarDatos(currentContext);
            }
        });

        $('#btnAdd').on('click', function() {
            if (currentContext === 'administradores') {
                window.location.href = '../Apeajal/agregarAdmin.html';
            } else if (currentContext === 'juntalocal') {
                window.location.href = '../Apeajal/agregarJL.html';
            } else if (currentContext === 'huertas') {
                window.location.href = '../Apeajal/agregarHuerta.html';
            } else if (currentContext === 'tecnicos') {
                window.location.href = '../Apeajal/agregarTecnico.html';
            } else if (currentContext === 'productores') {
                window.location.href = '../Apeajal/agregarProductor.html';
            } else if (currentContext === 'laboratorio') {
                window.location.href = '../Apeajal/agregarLaboratorio.html';
            } else if (currentContext === 'municipios') {
                window.location.href = '../Apeajal/edit.html';
            }
        });
    

        // Función para filtrar los datos según el contexto
    function filtrarDatos(context) {
        const searchValue = $('#inputSearch').val().toLowerCase();

        $('.table-body tbody tr').each(function() {
            const row = $(this);
            let showRow = false;

            if (context === 'productores') {
                const nombre = row.find('td:eq(2)').text().toLowerCase(); // Nombre Usuario
                const correo = row.find('td:eq(3)').text().toLowerCase(); // Correo
                if (nombre.includes(searchValue) || correo.includes(searchValue)) {
                    showRow = true;
                }
            } else if (context === 'tecnicos') {
                const nombre = row.find('td:eq(2)').text().toLowerCase(); // Nombre Usuario
                const correo = row.find('td:eq(4)').text().toLowerCase(); // Correo
                const junta = row.find('td:eq(3)').text().toLowerCase();  // Nombre Junta Local
                if (nombre.includes(searchValue) || correo.includes(searchValue) || junta.includes(searchValue)) {
                    showRow = true;
                }
            } else if (context === 'huertas') {
                const productor = row.find('td:eq(2)').text().toLowerCase(); // Nombre Productor
                const junta = row.find('td:eq(3)').text().toLowerCase();    // Nombre Junta Local
                const huerta = row.find('td:eq(4)').text().toLowerCase();   // Nombre Huerta
                const localidad = row.find('td:eq(5)').text().toLowerCase(); // Localidad
                if (productor.includes(searchValue) || junta.includes(searchValue) || huerta.includes(searchValue) || localidad.includes(searchValue)) {
                    showRow = true;
                }
            } else if (context === 'laboratorio') {
                const nombre = row.find('td:eq(2)').text().toLowerCase(); // Nombre Usuario
                const correo = row.find('td:eq(4)').text().toLowerCase(); // Correo
                const junta = row.find('td:eq(3)').text().toLowerCase();  // Nombre Junta Local
                if (nombre.includes(searchValue) || correo.includes(searchValue) || junta.includes(searchValue)) {
                    showRow = true;
                }
            } else if (context === 'municipios') {
                const nombre = row.find('td:eq(2)').text().toLowerCase(); // Nombre Productor
                if (nombre.includes(searchValue)) {
                    showRow = true;
                }
            }

            // Mostrar o esconder la fila según el resultado del filtro
            if (showRow) {
                row.show();
            } else {
                row.hide();
            }
        });
    }

    function cargarJuntaLocal() {
    // Cambiar los encabezados de la tabla
    $('.table-body thead').html(`
        <tr>
            <th>Id Junta Local</th>
            <th>Accion</th>
            <th>Nombre</th>
            <th>Administrador</th>
            <th>Correo</th>
            <th>Teléfono</th>
            <th>Domicilio</th>
            <th>Carga municipios</th>
            <th>Estatus</th>
            <th>Ruta</th>
        </tr>
    `);

    // Hacer la llamada AJAX para obtener los técnicos
    $.ajax({
        url: '../../Backend/Apeajal/obtenerJuntaLocal.php', // La URL del archivo PHP
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            const tableBody = $('.table-body tbody');
            tableBody.empty(); // Limpiar el contenido anterior de la tabla

            // Verificar si se recibieron técnicos
            if (data.length > 0 && !data.error) {
                // Recorrer los datos recibidos y agregarlos a la tabla
                $.each(data, function(index, juntalocal) {
                    const row = `
                        <tr>
                            <td>${juntalocal.idjuntalocal}</td>
                            <td>
                                <button id="btnEdit" onclick="editRow(${juntalocal.idjuntalocal}, 'juntaLocal')">
                                    <ion-icon name="pencil-outline"></ion-icon>
                                </button>
                                <button id="btnDelete" onclick="deleteRow(this)">
                                    <ion-icon name="trash-outline"></ion-icon>
                                </button>
                            </td>
                            <td>${juntalocal.nombre}</td>
                            <td>${juntalocal.nombre_admin}</td>
                            <td>${juntalocal.correo}</td>
                            <td>${juntalocal.teléfono }</td>
                            <td>${juntalocal.domicilio }</td>
                            <td>${juntalocal.carga_municipios}</td> 
                            <td>${juntalocal.estatus}</td>
                            <td>${juntalocal.ruta_img}</td>
                        </tr>
                    `;
                    tableBody.append(row); // Agregar la fila a la tabla
                });
            } else {
                // Mostrar un mensaje si no hay técnicos disponibles o hay un error
                const row = `
                    <tr>
                        <td colspan="5">No se encontraron técnicos o ocurrió un error</td>
                    </tr>
                `;
                tableBody.append(row);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al obtener los técnicos:', status, error);
            console.error('Respuesta del servidor:', xhr.responseText);
        }
    });
}

function cargarAdministradores() {
        // Cambiar los encabezados de la tabla
        $('.table-body thead').html(`
                <tr>
                    <th>Id</th>
                    <th>Accion</th>
                    <th>Nombre Usuario</th>
                    <th>Tipo</th>
                    <th>Correo</th>
                    <th>Telefono</th>
                </tr>
            `);

        $.ajax({
            url: '../../Backend/Apeajal/obtenerAdministradores.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                const tableBody = $('.table-body tbody');
                tableBody.empty();

                $.each(data, function(index, administrador) {
                    const row = `
                        <tr>
                            <td>${administrador.id_usuario}</td>
                            <td>
                                <button id="btnEdit" onclick="editRow(${administrador.id_usuario}, 'administrador')">
                                    <ion-icon name="pencil-outline"></ion-icon>
                                </button>
                                <button id="btnDelete" onclick="deleteRow(this)">
                                    <ion-icon name="trash-outline"></ion-icon>
                                </button>
                            </td>
                            <td>${administrador.nombre_usuario}</td>
                            <td>${administrador.nombre_tipo}</td>
                            <td>${administrador.correo}</td>
                            <td>${administrador.teléfono}</td>
                        </tr>
                    `;
                    tableBody.append(row);
                });
            },
            error: function(xhr, status, error) {
                console.error('Error al obtener los productores:', status, error);
                console.error('Respuesta del servidor:', xhr.responseText);
            }
        });
    }


    function cargarProductores() {
        // Cambiar los encabezados de la tabla
        $('.table-body thead').html(`
                <tr>
                    <th>Id</th>
                    <th>Accion</th>
                    <th>Nombre Usuario</th>
                    <th>Correo</th>
                    <th>Telefono</th>
                    <th>RFC</th>
                    <th>Curp</th>
                    <th>Estatus</th>
                </tr>
            `);

        $.ajax({
            url: '../../Backend/Apeajal/obtenerProductores.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                const tableBody = $('.table-body tbody');
                tableBody.empty();

                $.each(data, function(index, productor) {
                    const row = `
                        <tr>
                            <td>${productor.id_productor}</td>
                            <td>
                                <button id="btnEdit" onclick="editRow(${productor.id_productor}, 'productor')">
                                    <ion-icon name="pencil-outline"></ion-icon>
                                </button>
                                <button id="btnDelete" onclick="deleteRow(this)">
                                    <ion-icon name="trash-outline"></ion-icon>
                                </button>
                            </td>
                            <td>${productor.nombre}</td>
                            <td>${productor.correo}</td>
                            <td>${productor.teléfono}</td>
                            <td>${productor.rfc}</td>
                            <td>${productor.curp}</td>
                            <td>${productor.estatus}</td>
                        </tr>
                    `;
                    tableBody.append(row);
                });
            },
            error: function(xhr, status, error) {
                console.error('Error al obtener los productores:', status, error);
                console.error('Respuesta del servidor:', xhr.responseText);
            }
        });
    }

   
    function cargarSolicitudes() {
    $('.table-body thead').html(`
        <tr>
            <th>Id</th>
            <th>Accion</th>
            <th>Status</th>
            <th>Productor</th>
            <th>Huerta</th>
            <th>Junta Local</th>
            <th>Fecha</th>
            <th>Tecnico</th>
        </tr>
    `);

    $.ajax({
        url: '../../Backend/Apeajal/obtenerSolicitudes.php',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            const tableBody = $('.table-body tbody');
            console.log(data);

            tableBody.empty();

            if (data.solicitudes.length > 0) {
                $.each(data.solicitudes, function(index, solicitud) {
                    console.log(solicitud);

                    const row = `
                        <tr>
                            <td>${solicitud.id_solicitud}</td>
                            <td>
                                <button id="btnAssign" class="btnAssign" onclick="editRow(${solicitud.id_solicitud}, 'solicitud')">
                                    <ion-icon name="checkmark-outline"></ion-icon> Asignar
                                </button>
                            </td>
                            <td>${solicitud.status}</td>
                            <td>${solicitud.nombre_productor}</td>
                            <td>${solicitud.nombre_huerta}</td>
                            <td>${solicitud.nombre_junta}</td>
                            <td>${solicitud.fecha_programada}</td>
                            <td>${solicitud.nombre_tecnico}</td>
                        </tr>
                    `;
                    
                    tableBody.append(row);
                });
            } else {
                // Si no hay solicitudes, mostrar un mensaje
                const messageRow = `
                    <tr>
                        <td colspan="6" style="text-align: center;">${data.message}</td>
                    </tr>
                `;
                tableBody.append(messageRow);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al obtener las solicitudes:', status, error);
            console.error('Respuesta del servidor:', xhr.responseText);
        }
    });
}


    function cargarHuertas() {
    // Cambiar los encabezados de la tabla
    $('.table-body thead').html(`
        <tr>
            <th>Id Huerta</th>
            <th>Accion</th>
            <th>Nombre Productor</th>
            <th>Nombre Huerta</th>
            <th>Nombre Junta</th>
            <th>Localidad</th>
            <th>Centroide</th>
            <th>Hectáreas</th>
            <th>Pronóstico de Cosecha</th>
            <th>Longitud</th>
            <th>Altitud</th>
            <th>Altura Nivel del Mar</th>
            <th>Variedad</th>
            <th>Nombre Empresa</th>
            <th>Encargado Empresa</th>
            <th>Supervisor Huerta</th>
            <th>Año Plantación</th>
            <th>Árboles por Hectárea</th>
            <th>Total Árboles</th>
            <th>Etapa Fenológica</th>
            <th>Fechas SV 01</th>
            <th>Fechas SV 02</th>
            <th>Ruta KML</th>
            <th>Fecha de Registro</th>
        </tr>
    `);

    // Hacer la llamada AJAX para obtener las huertas
    $.ajax({
        url: '../../Backend/Apeajal/obtenerHuertas.php', // La URL del archivo PHP
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            const tableBody = $('.table-body tbody');
            tableBody.empty(); // Limpiar el contenido anterior de la tabla

            // Verificar si se recibieron huertas
            if (data.length > 0 && !data.error) {
                // Recorrer los datos recibidos y agregarlos a la tabla
                $.each(data, function(index, huerta) {
                    const row = `
                        <tr>
                            <td>${huerta.id_hue}</td>
                            <td>
                                <button id="btnEdit" onclick="editRow('${huerta.id_hue}','huerta')">
                                    <ion-icon name="pencil-outline"></ion-icon>
                                </button>
                                <button id="btnDelete" onclick="deleteRow(this)">
                                    <ion-icon name="trash-outline"></ion-icon>
                                </button>
                            </td>
                            <td>${huerta.nombre_productor}</td>
                            <td>${huerta.nombre_huerta}</td>
                            <td>${huerta.nombre_junta}</td>
                            <td>${huerta.nombre_municipio}</td>
                            <td>${huerta.centroide}</td>
                            <td>${huerta.hectareas}</td>
                            <td>${huerta.pronostico_de_cosecha}</td>
                            <td>${huerta.longitud}</td>
                            <td>${huerta.altitud}</td>
                            <td>${huerta.altura_nivel_del_mar}</td>
                            <td>${huerta.variedad}</td>
                            <td>${huerta.nomempresa}</td>
                            <td>${huerta.encargadoempresa}</td>
                            <td>${huerta.supervisorhuerta}</td>
                            <td>${huerta.añoplantacion}</td>
                            <td>${huerta.arbolesporhectareas}</td>
                            <td>${huerta.totalarboles}</td>
                            <td>${huerta.etapafenologica}</td>
                            <td>${huerta.fechasv_01}</td>
                            <td>${huerta.fechasv_02}</td>
                            <td>${huerta.rutaKML}</td>
                            <td>${huerta.fechaRegistro}</td>
                        </tr>
                    `;
                    tableBody.append(row); // Agregar la fila a la tabla
                });
            } else {
                // Mostrar un mensaje si no hay huertas disponibles o hay un error
                const row = `
                    <tr>
                        <td colspan="23">No se encontraron huertas</td>
                    </tr>
                `;
                tableBody.append(row);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al obtener las huertas:', status, error);
            console.error('Respuesta del servidor:', xhr.responseText);
        }
    });
}


    function cargarTecnicos() {
    // Cambiar los encabezados de la tabla
    $('.table-body thead').html(`
        <tr>
            <th>Id Técnico</th>
            <th>Accion</th>
            <th>Nombre Usuario</th>
            <th>Nombre Junta Local</th>
            <th>Correo</th>
            <th>Teléfono</th>
            <th>Carga municipios</th>
            <th>Estatus</th>
        </tr>
    `);

    // Hacer la llamada AJAX para obtener los técnicos
    $.ajax({
        url: '../../Backend/Apeajal/obtenerTecnicos.php', // La URL del archivo PHP
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            const tableBody = $('.table-body tbody');
            tableBody.empty(); // Limpiar el contenido anterior de la tabla

            // Verificar si se recibieron técnicos
            if (data.length > 0 && !data.error) {
                // Recorrer los datos recibidos y agregarlos a la tabla
                $.each(data, function(index, tecnico) {
                    const row = `
                        <tr>
                            <td>${tecnico.id_tecnico}</td>
                            <td>
                                <button id="btnEdit" onclick="editRow(${tecnico.id_tecnico}, 'tecnico')">
                                    <ion-icon name="pencil-outline"></ion-icon>
                                </button>
                                <button id="btnDelete" onclick="deleteRow(this)">
                                    <ion-icon name="trash-outline"></ion-icon>
                                </button>
                            </td>
                            <td>${tecnico.nombre_usuario}</td>
                            <td>${tecnico.nombre_junta}</td>
                            <td>${tecnico.correo}</td>
                            <td>${tecnico.teléfono }</td>
                            <td>${tecnico.nombres_municipios}</td> 
                            <td>${tecnico.estatus}</td>
                        </tr>
                    `;
                    tableBody.append(row); // Agregar la fila a la tabla
                });
            } else {
                // Mostrar un mensaje si no hay técnicos disponibles o hay un error
                const row = `
                    <tr>
                        <td colspan="5">No se encontraron técnicos o ocurrió un error</td>
                    </tr>
                `;
                tableBody.append(row);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al obtener los técnicos:', status, error);
            console.error('Respuesta del servidor:', xhr.responseText);
        }
    });
}

function cargarLaboratorio() {
   // Cambiar los encabezados de la tabla
   $('.table-body thead').html(`
        <tr>
            <th>Id Personal Lab</th>
            <th>Accion</th>
            <th>Nombre Usuario</th>
            <th>Nombre Junta Local</th>
            <th>Correo</th>
            <th>Teléfono</th>
            <th>Estatus</th>
        </tr>
    `);

    // Hacer la llamada AJAX para obtener los técnicos
    $.ajax({
        url: '../../Backend/Apeajal/obtenerLaboratorio.php', // La URL del archivo PHP
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            const tableBody = $('.table-body tbody');
            tableBody.empty(); // Limpiar el contenido anterior de la tabla

            // Verificar si se recibieron técnicos
            if (data.length > 0 && !data.error) {
                // Recorrer los datos recibidos y agregarlos a la tabla
                $.each(data, function(index, laboratorio) {
                    const row = `
                        <tr>
                            <td>${laboratorio.id_laboratorio}</td>
                            <td>
                                <button id="btnEdit" onclick="editRow(${laboratorio.id_laboratorio}, 'laboratorio')">
                                    <ion-icon name="pencil-outline"></ion-icon>
                                </button>
                                <button id="btnDelete" onclick="deleteRow(this)">
                                    <ion-icon name="trash-outline"></ion-icon>
                                </button>
                            </td>
                            <td>${laboratorio.nombre_usuario}</td>
                            <td>${laboratorio.nombre_junta}</td>
                            <td>${laboratorio.correo}</td>
                            <td>${laboratorio.teléfono }</td>
                            <td>${laboratorio.estatus}</td>
                        </tr>
                    `;
                    tableBody.append(row); // Agregar la fila a la tabla
                });
            } else {
                // Mostrar un mensaje si no hay técnicos disponibles o hay un error
                const row = `
                    <tr>
                        <td colspan="5">No se encontraron personal de laboratorio disponibles o ocurrió un error</td>
                    </tr>
                `;
                tableBody.append(row);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al obtener el personal de laboratorio:', status, error);
            console.error('Respuesta del servidor:', xhr.responseText);
        }
    });
}


    function cargarMunicipios() {
    // Cambiar los encabezados de la tabla
    $('.table-body thead').html(`
        <tr>
            <th>Id Municipio</th>
            <th>Accion</th>
            <th>Nombre Municipio</th>
        </tr>
    `);

    // Hacer la llamada AJAX para obtener los municipios
    $.ajax({
        url: '../../Backend/Apeajal/obtenerMunicipios.php', // La URL correcta para obtener los municipios
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            const tableBody = $('.table-body tbody');
            tableBody.empty(); // Limpiar el contenido anterior de la tabla

            // Verificar si se recibieron municipios
            if (data.length > 0 && data[0].id_municipio !== '') {
                // Recorrer los datos recibidos y agregarlos a la tabla
                $.each(data, function(index, municipio) {
                    const row = `
                        <tr>
                            <td>${municipio.id_municipio}</td>
                            <td>
                                <button id="btnEdit" onclick="editRow(this)">
                                    <ion-icon name="pencil-outline"></ion-icon>
                                </button>
                                <button id="btnDelete" onclick="deleteRow(this)">
                                    <ion-icon name="trash-outline"></ion-icon>
                                </button>
                            </td>
                            <td>${municipio.nombre}</td>
                        </tr>
                    `;
                    tableBody.append(row); // Agregar la fila a la tabla
                });
            } else {
                // Mostrar un mensaje si no hay municipios disponibles
                const row = `
                    <tr>
                        <td colspan="2">No hay municipios disponibles</td>
                    </tr>
                `;
                tableBody.append(row);
                }
            },
        error: function(xhr, status, error) {
            console.error('Error al obtener los municipios:', status, error);
            console.error('Respuesta del servidor:', xhr.responseText);
            }
        });
    }
});

function editRow(id, tipo) {
    let url = '';
    if(tipo == 'juntaLocal') {
        url = '../../Backend/Apeajal/envioDatosJL.php?idjuntalocal=' + id;
    } else if (tipo === 'productor') {
        url = '../../Backend/JuntasLocales/envioDatosProductor.php?id_productor=' + id;
    } else if (tipo === 'tecnico') {
        url = '../../Backend/JuntasLocales/envioDatosTecnico.php?id_tecnico=' + id; 
    } else if(tipo == 'laboratorio') {
        url = '../../Backend/JuntasLocales/envioDatosPLaboratorio.php?id_laboratorio=' + id;
    } else if(tipo == 'solicitud') {
        url = '../../Backend/JuntasLocales/envioDatosSolicitud.php?id_solicitud=' + id;     
    } else if(tipo == 'administrador') {
        url = '../../Backend/Apeajal/envioDatosAdministrador.php?id_usuario=' + id;     
    }else if(tipo == 'huerta') {
        url = '../../Backend/JuntasLocales/envioDatosHuerta.php?id_hue=' + id;
    } else {
        console.error('Tipo desconocido:', tipo);
        return;
    }

    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            // Guarda los datos en sessionStorage
            sessionStorage.setItem('id_tipo', tipo);
            if(tipo == 'juntaLocal') {
                sessionStorage.setItem('idjuntalocal', id);
                sessionStorage.setItem('nombre', data.nombre);
                sessionStorage.setItem('nomAdmin', data.nombre_admin);
                sessionStorage.setItem('idAdmin', data.id_usuario);
                sessionStorage.setItem('correo', data.correo);
                sessionStorage.setItem('telefono', data.teléfono);
                sessionStorage.setItem('domicilio', data.domicilio);
                sessionStorage.setItem('carga_municipios', data.carga_municipios);
                sessionStorage.setItem('estatus', data.estatus);
                sessionStorage.setItem('ruta_img', data.ruta_img);
                sessionStorage.setItem('municipios', data.municipios);
                window.location.href = '../Apeajal/editarJL.html';
            } else if (tipo === 'productor') {
                sessionStorage.setItem('id_productor', id);
                sessionStorage.setItem('id_usuario', data.id_usuario);
                sessionStorage.setItem('nombre', data.nombre);
                sessionStorage.setItem('rfc', data.rfc);
                sessionStorage.setItem('curp', data.curp);
                sessionStorage.setItem('correo', data.correo);
                sessionStorage.setItem('telefono', data.teléfono);
                sessionStorage.setItem('contraseña', data.contraseña);
                sessionStorage.setItem('status', data.estatus);
                window.location.href = '../Apeajal/editarProductor.html';
            } else if (tipo === 'tecnico') {
                sessionStorage.setItem('id_tecnico', id);
                sessionStorage.setItem('id_usuario', data.id_usuario);
                sessionStorage.setItem('nombre', data.nombre);
                sessionStorage.setItem('correo', data.correo);
                sessionStorage.setItem('telefono', data.teléfono);
                sessionStorage.setItem('contraseña', data.contraseña);
                sessionStorage.setItem('status', data.estatus);
                sessionStorage.setItem('jl_id', data.idjuntalocal); 
                sessionStorage.setItem('jl', data.nombre_junta);
                sessionStorage.setItem('carga_municipios', data.carga_municipios);
                sessionStorage.setItem('municipios', data.municipios);
                window.location.href = '../Apeajal/editarTecnico.html';
            } else if (tipo == 'laboratorio') {
                sessionStorage.setItem('id_laboratorio', id);
                sessionStorage.setItem('id_usuario', data.id_usuario);
                sessionStorage.setItem('nombre', data.nombre);
                sessionStorage.setItem('correo', data.correo);
                sessionStorage.setItem('telefono', data.teléfono);
                sessionStorage.setItem('contraseña', data.contraseña);
                sessionStorage.setItem('status', data.estatus);
                sessionStorage.setItem('jl_id', data.idjuntalocal); 
                sessionStorage.setItem('jl', data.nombre_junta);
                window.location.href = '../Apeajal/editarLaboratorio.html';
            } else if (tipo == 'administrador') {
                sessionStorage.setItem('id_usuario', id);
                sessionStorage.setItem('nombre', data.nombre);
                sessionStorage.setItem('id_tipo', data.id_tipo);
                sessionStorage.setItem('nombre_tipo', data.nombre_tipo);
                sessionStorage.setItem('correo', data.correo);
                sessionStorage.setItem('telefono', data.teléfono);
                sessionStorage.setItem('contraseña', data.teléfono);
                window.location.href = '../Apeajal/editarAdmin.html';

            } else if (tipo == 'solicitud') {
                sessionStorage.setItem('id_solicitud', id);
                sessionStorage.setItem('status', data.status);
                sessionStorage.setItem('nombre_productor', data.nombre_productor);
                sessionStorage.setItem('nombre_huerta', data.nombre_huerta);
                sessionStorage.setItem('fecha_programada', data.fecha_programada);
                sessionStorage.setItem('nombre_tecnico', data.nombre_tecnico);
                sessionStorage.setItem('id_tecnico', data.id_tecnico);
                window.location.href = '../Apeajal/asignarSolicitudes.php';
            } else if (tipo == 'huerta') {
                sessionStorage.setItem('id_hue', id);
                sessionStorage.setItem('nombre_huerta', data.nombre);
                sessionStorage.setItem('localidad', data.localidad);
                sessionStorage.setItem('centroide', data.centroide);
                sessionStorage.setItem('hectareas', data.hectareas);
                sessionStorage.setItem('pronostico_de_cosecha', data.pronostico_de_cosecha);
                sessionStorage.setItem('longitud', data.longitud);
                sessionStorage.setItem('altitud', data.altitud);
                sessionStorage.setItem('altura_nivel_del_mar', data.altura_nivel_del_mar);
                sessionStorage.setItem('variedad', data.variedad); 
                sessionStorage.setItem('nomempresa', data.nomempresa); 
                sessionStorage.setItem('encargadoempresa', data.encargadoempresa); 
                sessionStorage.setItem('supervisorhuerta', data.supervisorhuerta); 
                sessionStorage.setItem('anoplantacion', data.anoplantacion); 
                sessionStorage.setItem('arbolesporhectareas', data.arbolesporhectareas);
                sessionStorage.setItem('totalarboles', data.totalarboles); 
                sessionStorage.setItem('etapafenologica', data.etapafenologica);
                sessionStorage.setItem('fechasv_01', data.fechasv_01); 
                sessionStorage.setItem('fechasv_02', data.fechasv_02);
                sessionStorage.setItem('rutaKML', data.rutaKML); 
                sessionStorage.setItem('fechaRegistro', data.fechaRegistro);
                window.location.href = '../Apeajal/editarHuerta.html';
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al obtener los datos:', status, error);
            console.error('Respuesta del servidor:', xhr.responseText);
        }
    });
}

    </script>
    <!-- Ionicons-->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <!-- Script of menu -->
    <script src="../../Components/Apeajal/menuDesplegable.js"></script>
</body>
</html>
