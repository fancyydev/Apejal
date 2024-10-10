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
    <!-- CSS de Bootstrap -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"> -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script> -->
  </head>
<body>
    <!-- Contenido de tu página -->
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

        <button class="boton" data-content="laboratorio">
            <ion-icon name="location-outline"></ion-icon>
            <span>Laboratorio</span>
        </button>

        <button class="boton" data-content="municipios">
            <ion-icon name="location-outline"></ion-icon>
            <span>Municipios</span>
        </button>
        <form action="../../Backend/JuntasLocales/generadorReporteExcel.php" method="POST">
            <button class="boton" type="submit">Descargar Reporte General (Excel)</button>
        </form>
        <form action="../../Backend/JuntasLocales/generadorReportePdf.php" method="POST">
            <button class="boton" type="submit">Descargar Reporte General (PDF)</button>
        </form>


        <div class="usuario">
            <ion-icon id="key" name="key-outline"></ion-icon>
            <div class="info-usuario">
                <div class="nombre-email">
                    <span class="nombre" id="nombre"></span>
                    <span class="email" id="email"></span>
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
                <button id="btnSearch"> Buscar </button>
                <button id="btnAdd"> Agregar </button>
                <button id="btnReport"> Reporte </button>
                
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

        // Detectar contexto actual (Productores, Técnicos o Huertas)
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

        $('button[data-content="laboratorio"]').click(function() {
            $('#title').text('Laboratorio'); 
            currentContext = 'laboratorio';
            cargarLaboratorio(); 
        });
        
        $('button[data-content="municipios"]').click(function() {
            $('#title').text('Municipios');
            currentContext = 'municipios';
            cargarMunicipios(); 
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
            if (currentContext === 'solicitudes') {
                window.location.href = '../JuntasLocales/agregarJL.html';
            } else if (currentContext === 'huertas') {
                window.location.href = '../JuntasLocales/agregarHuerta.html';
            } else if (currentContext === 'tecnicos') {
                console.log("Si entro al if pero no mando a agregar tecnicos");
                window.location.href = '../JuntasLocales/agregarTecnico.html';
            } else if (currentContext === 'productores') {
                window.location.href = '../JuntasLocales/agregarProductor.html';
            } else if (currentContext === 'laboratorio') {
                window.location.href = '../JuntasLocales/agregarLaboratorio.html';
            } else if (currentContext === 'municipios') {
                window.location.href = '../JuntasLocales/edit.html';
            }
        });
        $('#btnReport').on('click', function() {
            const crearModalReporte = (pdfRoute, excelRoute) => {
    const dropdownHtml = `
        <div id="reportModal" class="modal">
            <div class="modal-content">
                <h2>Selecciona el tipo de reporte:</h2>
                <label for="tipoReporte">Tipo de reporte:</label>
                <select id="tipoReporte">
                    <option value="">-- Selecciona --</option>
                    <option value="pdf">PDF</option>
                    <option value="excel">Excel</option>
                </select>
                <div class="button-container">
                    <button id="generarReporteBtn">Generar Reporte</button>
                    <button id="cancelarBtn">Cancelar</button>
                </div>
            </div>
        </div>
    `;
    $('body').append(dropdownHtml);

    $('#generarReporteBtn').on('click', function() {
        const tipo = $('#tipoReporte').val();
        if (tipo === 'pdf') {
            window.location.href = pdfRoute;
        } else if (tipo === 'excel') {
            window.location.href = excelRoute;
        } else {
            alert('Por favor, selecciona un tipo de reporte.');
        }
        $('#reportModal').remove();
    });

    $('#cancelarBtn').on('click', function() {
        $('#reportModal').remove();
    });
};

    if (currentContext === 'solicitudes') {
        crearModalReporte(
            '../../Backend/JuntasLocales/generadorReporteSolicitudPdf.php',
            '../../Backend/JuntasLocales/generadorReporteSolicitudExcel.php'
        );
    } else if (currentContext === 'huertas') {
        crearModalReporte(
            '../../Backend/JuntasLocales/generadorReporteHuertaPdf.php',
            '../../Backend/JuntasLocales/generadorReporteHuertaExcel.php'
        );
    } else if (currentContext === 'tecnicos') {
        crearModalReporte(
            '../../Backend/JuntasLocales/generadorReporteTecnicoPdf.php',
            '../../Backend/JuntasLocales/generadorReporteTecnicoExcel.php'
        );
    } else if (currentContext === 'productores') {
        crearModalReporte(
            '../../Backend/JuntasLocales/generadorReporteProductorPdf.php',
            '../../Backend/JuntasLocales/generadorReporteProductorExcel.php'
        );
    } else if (currentContext === 'laboratorio') {
        crearModalReporte(
            '../../Backend/JuntasLocales/generadorReporteLaboratorioPdf.php',
            '../../Backend/JuntasLocales/generadorReporteLaboratorioExcel.php'
        );
    } else if (currentContext === 'municipios') {
        crearModalReporte(
            '../../Backend/JuntasLocales/generadorReporteMunicipiosPdf.php',
            '../../Backend/JuntasLocales/generadorReporteMunicipiosExcel.php'
        );
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
            } else if (context === 'laboratorio') {
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

    function cargarProductores() {
        // Cambiar los encabezados de la tabla
        $('.table-body thead').html(`
                <tr>
                    <th>Id</th>
                    <th>Accion</th>
                    <th>Nombre Usuario</th>
                    <th>Junta Local</th>
                    <th>Correo</th>
                    <th>Telefono</th>
                    <th>RFC</th>
                    <th>Curp</th>
                    <th>Estatus</th>
                </tr>}
                
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
                                <button id="btnEdit" onclick="editRow(${productor.id_productor}, 'productor')">
                                    <ion-icon name="pencil-outline"></ion-icon>
                                </button>
                                <button id="btnDelete" onclick="deleteRow(this)">
                                    <ion-icon name="trash-outline"></ion-icon>
                                </button>
                            </td>
                            <td>${productor.nombre}</td>
                            <td>${productor.nombre_junta}</td>
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

    // function cargarSolicitudes() {
    //     $('.table-body thead').html(`
    //         <tr>
    //             <th>Id</th>
    //             <th>Accion</th>
    //             <th>Status</th>
    //             <th>Productor</th>
    //             <th>Huerta</th>
    //             <th>Fecha</th>
    //             <th>Tecnico</th>
    //         </tr>
    //     `);

    //     $.ajax({
    //         url: '../../Backend/JuntasLocales/obtenerSolicitudes.php',
    //         type: 'GET',
    //         dataType: 'json',
    //         success: function(data) {
    //             const tableBody = $('.table-body tbody');
    //             tableBody.empty();

    //             $.each(data, function(index, solicitud) {
    //                 const row = `
    //                     <tr>
    //                         <td>${solicitud.id_solicitud}</td>
    //                         <td>
    //                             <button id="btnEdit" onclick="">
    //                                 <ion-icon name="pencil-outline"></ion-icon>
    //                             </button>
    //                             <button id="btnDelete" onclick="deleteRow(this)">
    //                                 <ion-icon name="trash-outline"></ion-icon>
    //                             </button>
    //                         </td>
    //                         <td>${solicitud.status}</td>
    //                         <td>${solicitud.nombre_productor}</td>
    //                         <td>${solicitud.nombre_huerta}</td>
    //                         <td>${solicitud.fecha_programada}</td>
    //                         <td>${solicitud.nombre_tecnico}</td>
    //                     </tr>
    //                 `;
    //                 tableBody.append(row);
    //             });
    //         },
    //         error: function(xhr, status, error) {
    //             console.error('Error al obtener las solicitudes:', status, error);
    //             console.error('Respuesta del servidor:', xhr.responseText);
    //         }
    //     });
    // }

    function cargarSolicitudes() {
    $('.table-body thead').html(`
        <tr>
            <th>Id</th>
            <th>Accion</th>
            <th>Status</th>
            <th>Productor</th>
            <th>Huerta</th>
            <th>Fecha</th>
            <th>Tecnico</th>
        </tr>
    `);

    $.ajax({
        url: '../../Backend/JuntasLocales/obtenerSolicitudes.php',
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
            <th>Junta Local</th>
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
                    sessionStorage.setItem('nombre_productor', huerta.nombre_productor); // Guardar el último productor
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
                            <td>${huerta.nombre_junta_local}</td>
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
                            <td>${huerta.anoplantacion}</td>
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
        url: '../../Backend/JuntasLocales/obtenerLaboratorio.php', // La URL del archivo PHP
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
            console.error('Error al obtener el personal del laboratorio: ', status, error);
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

function editRow(id, tipo) {
    
    let url = '';
    if (tipo === 'productor') {
        url = '../../Backend/JuntasLocales/envioDatosProductor.php?id_productor=' + id;
    } else if (tipo === 'tecnico') {
        url = '../../Backend/JuntasLocales/envioDatosTecnico.php?id_tecnico=' + id; 
    } else if(tipo == 'laboratorio') {
        url = '../../Backend/JuntasLocales/envioDatosPLaboratorio.php?id_laboratorio=' + id;
    } else if(tipo == 'solicitud') {
        url = '../../Backend/JuntasLocales/envioDatosSolicitud.php?id_solicitud=' + id;     
    } else if(tipo == 'huerta') {
        url = '../../Backend/JuntasLocales/envioDatosHuerta.php?id_hue=' + id;
        console.log('Entrando a huerta con ID:', id);
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
            if (tipo === 'productor') {
                sessionStorage.setItem('id_productor', id);
                sessionStorage.setItem('id_usuario', data.id_usuario);
                sessionStorage.setItem('nombre', data.nombre);
                sessionStorage.setItem('rfc', data.rfc);
                sessionStorage.setItem('curp', data.curp);
                sessionStorage.setItem('correo', data.correo);
                sessionStorage.setItem('telefono', data.teléfono);
                sessionStorage.setItem('contraseña', data.contraseña);
                sessionStorage.setItem('status', data.estatus);
            
                // Redirigir a editarProductor.html
                window.location.href = '../JuntasLocales/editarProductor.html';

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
                // Redirigir a editarTecnico.html
                window.location.href = '../JuntasLocales/editarTecnico.html';
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
                window.location.href = '../JuntasLocales/editarLaboratorio.html';
            } else if (tipo == 'solicitud') {
                sessionStorage.setItem('id_solicitud', id);
                sessionStorage.setItem('status', data.status);
                sessionStorage.setItem('nombre_productor', data.nombre_productor);
                sessionStorage.setItem('nombre_huerta', data.nombre_huerta);
                sessionStorage.setItem('fecha_programada', data.fecha_programada);
                sessionStorage.setItem('nombre_tecnico', data.nombre_tecnico);
                sessionStorage.setItem('id_tecnico', data.id_tecnico);
                window.location.href = '../JuntasLocales/asignarSolicitudes.php';
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
                window.location.href = '../JuntasLocales/editarHuerta.html';
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
