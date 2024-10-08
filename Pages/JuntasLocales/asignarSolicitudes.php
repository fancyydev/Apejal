<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Productores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10.10.1/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../../Components/Auxiliar/auxliar.js"></script>
    <link href="../../Styles/navbar.css" rel="stylesheet">
    <link href="../../Styles/movimientos.css" rel="stylesheet">
    <link href="../../Styles/checkboxes.css" rel="stylesheet">
    <!-- Link para jQuery UI CSS -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <!--Links para jquery-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- Links para jQuery UI -->
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    
</head>
<body>
    
    <div>
        <nav class="navbar logo">
            <a class="navbar-brand">
                <img src="../../Assets/Img/Imagenes/Logo.jpeg" width="50VW" height="50VH" class="d-inline-block align-top" alt="">
            </a>
            <ul class="nav justify-content-end">
            </ul>
        </nav>
        <nav class="navbar navbar-expand-lg">
            <div class="linea"></div>
        </nav>
    </div>

    <div class="container">
        <h3 class="text-center">Asignar Solicitud</h3>
        <div class="card">
            <div class="card-header">Detalles de la solicitud</div>
            <div class="card-body">
                <div class="row g-3">

                    <div class="col-md-5">
                        <label for="productor" class="form-label">Productor</label>
                        <input class="form-control" type="text" name="productor" id="productor" maxlength="50" readonly />
                    </div>
                    <div class="col-md-5">
                        <label for="huerta" class="form-label">Huerta</label>
                        <input class="form-control" type="text" name="huerta" id="huerta" maxlength="50" readonly />
                    </div>
                    <div class="col-md-5">
                        <label for="status" class="form-label">Estatus</label>
                        <select class="form-control" name="status" id="status">
                            <option value="">Seleccione el estatus</option>
                            <option value="pendiente">Pendiente</option>
                            <option value="activa">Activa</option>
                            <option value="cancelada">Cancelada</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label for="tecnico" class="form-label">Tecnico</label>
                        <select class="form-control" name="tecnico" id="tecnico">
                            <option value="">Seleccione Tecnico</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="fechaProg" class="form-label">Fecha programada</label>
                        <input class="form-control" type="text" name="fechaProg" id="fechaProg" />
                    </div>
                </div>
            </div>
        </div>

        <br>
        <div class="row">
            <div class="col-lg-3"></div>
            <div class="col-lg-3">
                <button type="button" id="regristar" class="btn btn-primary btn-block">Guardar productor</button>
            </div>
            <div class="col-lg-3">
                <button type="button" id="Cancelar" class="btn btn-secondary btn-block">Cancelar</button>
            </div>
            <div class="col-lg-3"></div>
        </div>
        <br>
    </div>

    <script>
        const aux = new auxliar();

        $(document).ready(function() {

            // Recupera los datos del productor desde sessionStorage
            const id_solicitud = sessionStorage.getItem('id_solicitud');
            const id_tecnico = sessionStorage.getItem('id_tecnico');
            const nombre_productor = sessionStorage.getItem('nombre_productor');
            const nombre_huerta = sessionStorage.getItem('nombre_huerta');
            const status = sessionStorage.getItem('status');
            const nombre_tecnico = sessionStorage.getItem('nombre_tecnico');
            const fecha_programada = sessionStorage.getItem('fecha_programada');

            // Rellena los campos del formulario
            $('#productor').val(nombre_productor);
            $('#huerta').val(nombre_huerta);
            $('#status').val(status);

            // Si la fecha es null o no existe, dejamos el campo vacío, si no, asignamos la fecha
            if (fecha_programada != "null") {
                $('#fechaProg').val(fecha_programada);
            } else {
                $('#fechaProg').val(''); // Deja el campo vacío si no hay fecha
            }

            // Cargar Juntas Locales
            $.ajax({
                url: '../../Backend/JuntasLocales/obtenerTecnicosOpciones.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    $.each(response, function(index, tecnico) {
                        $('#tecnico').append($('<option>', {
                            value: tecnico.id_tecnico,
                            text: tecnico.nombre
                        }));
                    });
                    if (id_tecnico) {
                        $('#tecnico').val(id_tecnico).change();  // Esto fuerza a que se seleccione la junta local y cargue los municipios
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error al obtener tecnicos: ', status, error);
                }
            });

            // Inicializar el calendario datepicker en el campo de fecha
            $("#fechaProg").datepicker({
                dateFormat: 'yy-mm-dd',   // Formato de la fecha (Año-Mes-Día)
                changeMonth: true,        // Permitir cambiar el mes
                changeYear: true          // Permitir cambiar el año
            });

            $('#regristar').click(function() {
                
                // Validación de los campos
                var resultado = aux.validateAll([
                    {"valor": document.getElementById("status").value, "typeOf": "string", "mensaje": "<p>Insertar estatus</p><br>"},
                    {"valor": document.getElementById("tecnico").value, "typeOf": "string", "mensaje": "<p>Insertar tecnico</p><br>"},
                    {"valor": document.getElementById("fechaProg").value, "typeOf": "string", "mensaje": "<p>Insertar la fecha programada</p><br>"},
                ]);
       
                var tecnicoValue = document.getElementById("tecnico").value; 
                
                if (resultado.estado) {
                    $.ajax({
                        url: '../../Backend/JuntasLocales/actualizarSolicitud.php',
                        type: 'POST',
                        data: {

                            id_solicitud: id_solicitud,
                            status: document.getElementById("status").value,
                            fecha_programada: document.getElementById("fechaProg").value,
                            tecnico: tecnicoValue
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Listo',
                                html: '<p>Guardado correctamente</p>',
                                icon: 'success',
                                showConfirmButton: true,
                                allowOutsideClick: false
                            }).then((result) => {
                                // Redirigir a la página deseada cuando el usuario hace clic en "Aceptar"
                                if (result.isConfirmed) {
                                    window.location.href = "../JuntasLocales/menuJuntaLocal.php";
                                }
                            });
                        },
                        error: function(xhr, status, error) {
                            let errorMessage = "Ocurrió un error al guardar la solicitud.";
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message; // Mensaje de error desde el backend
                            } else if (xhr.status === 0) {
                                errorMessage = "No se pudo conectar al servidor. Verifica tu conexión a Internet.";
                            } else {
                                errorMessage += " Código de error: " + xhr.status + " - " + error;
                            }
                            aux.alert("<h5>Error</h5><br/><p>" + errorMessage + "</p>", true, true);
                        }
                    });
                } else {
                    aux.alert(resultado.texto, true, true);
                }
            });


            $('#Cancelar').click(function() {
                window.location.href = "../JuntasLocales/menuJuntaLocal.php";
            });
        });

    </script>
</body>
</html>