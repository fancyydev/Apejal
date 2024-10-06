<?php
session_start();

// Verifica que el usuario ha iniciado sesión y tiene el tipo correcto
if (!isset($_SESSION['id']) || !isset($_SESSION['tipo']) || $_SESSION['tipo'] != 1) {
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
    <title>Menu Productores</title>
    <link rel="stylesheet" href="../../Styles/menus.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>

    <div class="barra-lateral">
        <div class="nombre-pagina">
            <img id="img_apeajal" src="../../Assets/Img/Imagenes/Logo.jpeg" width="50VW" height="50VH" class="d-inline-block align-top" alt="">
            <span>Productores</span>
        </div>

        <button class="boton" data-content="solicitudes">
            <ion-icon name="archive-outline"></ion-icon>
            <span>Solicitudes</span>
        </button>

        <button class="boton" data-content="huertas">
            <ion-icon name="flower-outline"></ion-icon>
            <span>Huertas</span>
        </button>

        <!-- <button class="boton" data-content="reportes">
            <ion-icon name="build-outline"></ion-icon>
            <span>Reportes</span>
        </button> -->

        <div class="usuario">
            <ion-icon id="key" name="key-outline"></ion-icon>
            <div class="info-usuario">
                <div class="nombre-email">
                    <span class="nombre" id="nombre">David Fregoso Leon</span>
                    <span class="email" id="email">davidfregosoleon12@gmail.com</span>
                </div>
                <ion-icon id="settings" name="ellipsis-vertical-outline"></ion-icon>
            </div>
        </div>
        <div>
            <button id="btnLogout" class="logout-button" onclick="window.location.href='../../Backend/Login/logout.php'">
            <ion-icon name="log-out-outline"></ion-icon>
                Cerrar Sesión
            </button>
        </div>
    </div>


    <div class="contenido-principal">
        <div class="table">
            <section class="table-header">
                <h1 id="title" >Solicitudes</h1>
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

    $('button[data-content="solicitudes"]').click(function() {
        $('#title').text('Solicitudes'); 
        currentContext = 'solicitudes'; 
        cargarSolicitudes();
    });

    $('button[data-content="huertas"]').click(function() {
        $('#title').text('Huertas');
        currentContext = 'huertas';
        cargarHuertas(); 
    });

    // $('button[data-content="reportes"]').click(function() {
    //     $('#title').text('Reportes'); 
    //     currentContext = 'huertas';
    //     cargarReportes(); 
    // });

    $('#btnSearch').click(function() {
        filtrarDatos(currentContext);
    });

    $('#inputSearch').on('keypress', function(e) {
        if (e.which === 13) { // Tecla Enter
            filtrarDatos(currentContext);
        }
    });

    $('#btnAdd').on('click', function() {
        if (currentContext === 'solicitudes') {
             window.location.href = '../Productores/agregarSolicitud.php';
        } 
    });

         // Función para filtrar los datos según el contexto
    function filtrarDatos(context) {
        const searchValue = $('#inputSearch').val().toLowerCase();

        $('.table-body tbody tr').each(function() {
            const row = $(this);
            let showRow = false;

            if (context === 'solicitudes') {
                const nombre = row.find('td:eq(2)').text().toLowerCase(); // Nombre Usuario
                const correo = row.find('td:eq(3)').text().toLowerCase(); // Correo
                const junta = row.find('td:eq(4)').text().toLowerCase();  // Nombre Junta Local
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
            } 

            // Mostrar o esconder la fila según el resultado del filtro
            if (showRow) {
                row.show();
            } else {
                row.hide();
            }
        });
    }
    
    function cargarSolicitudes() {
    $('.table-body thead').html(`
        <tr>
            <th>Id</th>
            <th>Accion</th>
            <th>Status</th>
            <th>Huerta</th>
            <th>Fecha</th>
            <th>Tecnico</th>
        </tr>
    `);

    $.ajax({
        url: '../../Backend/Productores/obtenerSolicitudes.php',
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
                                <button id="btnEdit" onclick="">
                                    <ion-icon name="pencil-outline"></ion-icon>
                                </button>
                                <button id="btnDelete" onclick="deleteRow(this)">
                                    <ion-icon name="trash-outline"></ion-icon>
                                </button>
                            </td>
                            <td>${solicitud.status}</td>
                            <td>${solicitud.nombre_huerta}</td>
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
        url: '../../Backend/Productores/obtenerHuertas.php', // La URL del archivo PHP
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
                        <td colspan="23">No se encontraron huertas para este productor</td>
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


    function cargarReportes() {
    
}

});

</script>
<!-- Ionicons-->
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
<!-- Script of menu -->
<script src="../../Components/JuntasLocales/menuDesplegable.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
    // Realiza una solicitud para obtener los datos del usuario
    fetch('../../Backend/Login/cargadatos.php')
        .then(response => response.json())
        .then(data => {
            console.log(data); // Verifica los datos aquí
            if (!data.error && data.usuario) {
                document.getElementById('nombre').textContent = data.usuario.nombre;
                document.getElementById('email').textContent = data.usuario.correo;
            } else {
                console.error('Error al obtener los datos del usuario:', data.mensaje);
            }
        })
        .catch(error => console.error('Error:', error));
    });
</script>


</body>
</html>
