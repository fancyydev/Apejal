<?php
session_start();

// Verifica que el usuario ha iniciado sesión y tiene el tipo correcto
if (!isset($_SESSION['id']) || !isset($_SESSION['tipo']) || $_SESSION['tipo'] != 4) {
    // Redirige al usuario a la página de inicio de sesión si no tiene permiso
    header('Location: ../../index.html');
    exit();
}

// Aquí podrías incluir lógica específica para el tipo de usuario 1

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Junta Local</title>
    <link rel="stylesheet" href="../../Styles/menus.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>

    <div class="barra-lateral">
        <div class="nombre-pagina">
            <img id="img_apeajal" src="../../Assets/Img/Imagenes/Logo.jpeg" width="50VW" height="50VH" class="d-inline-block align-top" alt="">
            <span>JuntaLocal</span>
        </div>

        <button class="boton" data-content="solicitudes">
            <ion-icon name="archive-outline"></ion-icon>
            <span>Solicitudes</span>
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
                <h1 id="title" >Productores</h1>
                <input id="inputSearch" type="text">
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

    $('button[data-content="productores"]').click(function() {
        $('#title').text('Productores'); 
        cargarProductores(); 
    });

    $('button[data-content="solicitudes"]').click(function() {
        $('#title').text('Solicitudes'); 
        cargarSolicitudes(); 
    });

    $('button[data-content="huertas"]').click(function() {
        $('#title').text('Huertas');
        cargarHuertas(); 
    });

    $('button[data-content="tecnicos"]').click(function() {
        $('#title').text('Técnicos'); 
        cargarTecnicos(); 
    });

    $('button[data-content="municipios"]').click(function() {
        $('#title').text('Municipios');
        cargarMunicipios(); 
    });

    function cargarProductores() {
        // Cambiar los encabezados de la tabla
        $('.table-body thead').html(`
                <tr>
                    <th>Id</th>
                    <th>Accion</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Telefono</th>
                    <th>RFC</th>
                    <th>Curp</th>
                    <th>Estatus</th>
                </tr>
            `);

        $.ajax({
            url: '../../Backend/JuntasLocales/obtenerProductores.php',
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
                                <button id="btnEdit" onclick="editRow(this)">
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
        $.ajax({
            url: '../../Backend/JuntasLocales/obtenerSolicitudes.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                const tableBody = $('.table-body tbody');
                tableBody.empty();

                $.each(data, function(index, solicitud) {
                    const row = `
                        <tr>
                            <td>${solicitud.id_solicitud}</td>
                            <td>${solicitud.nombre_solicitud}</td>
                            <td>${solicitud.fecha}</td>
                            <td>${solicitud.estatus}</td>
                        </tr>
                    `;
                    tableBody.append(row);
                });
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
            <th>Nombre Junta Local</th>
            <th>Nombre Huerta</th>
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
        url: '../../Backend/JuntasLocales/obtenerHuertas.php', // La URL del archivo PHP
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
                                <button id="btnEdit" onclick="editRow(this)">
                                    <ion-icon name="pencil-outline"></ion-icon>
                                </button>
                                <button id="btnDelete" onclick="deleteRow(this)">
                                    <ion-icon name="trash-outline"></ion-icon>
                                </button>
                            </td>
                            <td>${huerta.nombre_productor}</td>
                            <td>${huerta.nombre_junta_local}</td>
                            <td>${huerta.nombre_huerta}</td>
                            <td>${huerta.localidad}</td>
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
                        <td colspan="23">No se encontraron huertas o ocurrió un error</td>
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
        url: '../../Backend/JuntasLocales/obtenerTecnicos.php', // La URL del archivo PHP
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
                                <button id="btnEdit" onclick="editRow(this)">
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
        url: '../../Backend/JuntasLocales/obtenerMunicipios.php', // La URL correcta para obtener los municipios
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

    </script>
    <!-- Ionicons-->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <!-- Script of menu -->
    <script src="../../Components/JuntasLocales/menuDesplegable.js"></script>
</body>
</html>