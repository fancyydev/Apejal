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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SISTEMA APEAJAL</title>
    <link href="../../Styles/navbar.css" rel="stylesheet">
    <link href="../../Styles/movimientos.css" rel="stylesheet">
    <!--LINKS PARA BOOSTRAP y iconos-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    
    <!--Links para jquery-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!--Links para dataTable-->
    <link href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.css" rel="stylesheet" type="text/css">
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.js" type="text/javascript" charset="utf8"></script>

    <!--Links para moment-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>    

    <!-- Links para alert-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10.10.1/dist/sweetalert2.min.css">
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>   
        
    <!--Links para funciones auxliar-->
    <script src="../../Components/Auxiliar/auxliar.js" ></script>
    <script src="../../Components/Login/login.js"></script>


</head>
<body onload="onload()">
    <div>
        <nav class="navbar logo">
            <a class="navbar-brand">
                <img src="../../Assets/Img/Imagenes/Logo.jpeg" width="50VW" height="50VH" class="d-inline-block align-top" alt="">
            </a>
            <ul class="nav justify-content-end">
                <li class="nav-item">
                    <a id="logout">
                        <img class="img-responsive" src="../../Assets/Img/Imagenes/salida.png" width="50VW" height="50VH" alt="" />
                    </a>                
                </li>
            </ul>
        </nav>
        
        <nav class="navbar navbar-expand-lg">
            <div class="linea"></div>
        </nav>
        <div class="container">
            <div class="row">
                <div class="col-lg-2 ">
                </div>
                <div class="col-lg-8 card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                        </div>
                        <div class="col-md-6">
                            <h3 class="text-center">NUEVA JUNTA LOCAL</h3>
                        </div>
                        <div class="col-md-3">
                            <label for="staticEmail" class="form-label">Fecha</label>
                            <input class="form-control" type="date" name="Fecha" id="Fecha" disabled/>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 ">
                </div>
            </div>
        </div>
    </div>
        <div class="container">
            <div class="row">
                <div class="col-lg-2 ">
                </div>
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">Junta Local</div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="staticEmail" class="form-label">Nombre</label>
                                        <input class="form-control" type="text" name="nombre" id="nombre" />
                                    </div>
                                    <div class="col-md-6">
                                        <label for="staticEmail" class="form-label">Administrador junta local</label>
                                        <select class="form-control" name="admin" id="admin" >
                                            <option value="">Seleccione administrador</option>
                                        </select>
                                    </div>  
                                </div>
                                <br>
                                <div class="row g-3">
                                    <div class="col-md-5">
                                        <label for="staticEmail" class="form-label">Domicilio</label>
                                        <input class="form-control" type="text" name="domicilio" id="domicilio" />
                                    </div>
                                    <div class="col-md-5">
                                        <label for="staticEmail" class="form-label">Telefono</label>
                                        <input class="form-control" type="text" name="telefono" id="telefono" />
                                    </div>
                                </div>
                                <br>
                                <div class="row g-3">
                                    <div class="col-md-5">
                                        <label for="staticEmail" class="form-label">Correo</label>
                                        <input class="form-control" type="text" name="correo" id="correo" />
                                    </div>                                                   
                                    <div class="col-md-5">
                                        <label for="municipio" class="form-label">Municipio</label>
                                        <select class="form-control" name="municipio" id="municipio">
                                            <option value="">Seleccione un municipio</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2">
                    </div>
                </div>
            </div>
        </div>

        <br>
        <div class="container">
            <div class="row ">
                <div class="col-lg-3 ">
                </div>
                <div class="col-lg-3">
                    <button type="button" id="registrar" class="btn insert btn-xs btn-block text-center">Guardar junta local</button>
                </div>
                <div class="col-lg-3">
                    <button type="button" id="Cancelar" class="btn cancel btn-xs btn-block text-center">Cancelar</button>
                </div>
                <div class="col-lg-3">
                </div>
            </div>
        </div>
<br>
<script>
        //const session = new login();

        
        const aux = new auxliar();
        $(document).ready(function(){
            //session.confirmarlogin("No-Clientes");

            $.ajax({
                url: '../../Backend/JuntasLocales/obtenerAdminJL.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    //Itera sobre el array de respuesta y agrega lo que encuentra en la base de datos
                    $.each(response, function(index, user) {
                        $('#admin').append($('<option>', {
                            value: user.id_usuario,
                            text: user.nombre
                        }));
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error al obtener los administradores:', status, error);
                }
            });

            $.ajax({
                url: '../../Backend/JuntasLocales/obtenerMunicipios.php',
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    data.forEach(function(municipio) {
                        $('#municipio').append(`<option value="${municipio.id_municipio}">${municipio.nombre}</option>`);
                    });
                },
                error: function() {
                    aux.alert('<h5>Error</h5><br/><p>No se pudieron cargar los municipios</p>', true, true);
                }
            });

            $('#registrar').click(function() {
                
            var resultado = aux.validateAll(
                    [
                        {"valor":document.getElementById("nombre").value,"typeOf":"string","mensaje":"<p>Insertar nombre</p><br>"},
                        {"valor":document.getElementById("domicilio").value,"typeOf":"string","mensaje":"<p>Insertar domicilio</p><br>"},
                        {"valor":document.getElementById("telefono").value,"typeOf":"string","mensaje":"<p>Insertar teléfono</p><br>"},
                        {"valor":document.getElementById("correo").value,"typeOf":"string","mensaje":"<p>Insertar correo</p><br>"},
                        {"valor":document.getElementById("municipio").value,"typeOf":"int","mensaje":"<p>Seleccionar municipio</p><br>"},
                     ]
                );
            console.log(resultado)
            if(resultado.estado){
                const formData = new FormData();
                formData.append("nombre", document.getElementById("nombre").value);
                formData.append("domicilio", document.getElementById("domicilio").value);
                formData.append("telefono", document.getElementById("telefono").value);
                formData.append("correo", document.getElementById("correo").value);
                formData.append("municipio", document.getElementById("municipio").value);

                $.ajax({
                    url: '../../Backend/JuntasLocales/registrarJuntaLocal.php',
                    method: 'POST',
                    data: JSON.stringify({
                        "nombre":document.getElementById("nombre").value,
                        "domicilio":document.getElementById("domicilio").value,
                        "telefono":document.getElementById("telefono").value,
                        "correo":document.getElementById("correo").value,
                        "municipio":document.getElementById("municipio").value,
                    }),
                    contentType: 'application/json',
                    beforeSend: function() {
                        aux.alert('<h5>Espere</h5><br/><p>Guardando datos</p>', false, false);
                    },
                    success: function(respuesta){
                        console.log(respuesta)
                        aux.alert("datos guardados", true, true);
                        //window.location.href = "../SistemaCliente/login.html";
                    },
                    error: function() {
                        aux.cerrar();
                        aux.alert('<h5>Error</h5><br/><p>No se pudo guardar los datos</p>', true, true);
                    }
                });
            } else {
                aux.alert(resultado.texto, true, true);
            }
            });

            $('#Cancelar').click(function() {
                //window.location.href = "../SistemaCliente/login.html";
            });
        });

        function onload(){
            var now = new Date();
            var dateString = moment(now).format('YYYY-MM-DD');
            document.getElementById("Fecha").value = dateString;
        }

</script>
</body>
</html>