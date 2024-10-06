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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitar Analisis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10.10.1/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../../Components/Auxiliar/auxliar.js"></script>
    <link href="../../Styles/navbar.css" rel="stylesheet">
    <link href="../../Styles/movimientos.css" rel="stylesheet">
    <link href="../../Styles/checkboxes.css" rel="stylesheet">
</head>
<body>
    
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
    </div>

    <div class="container">
        <h3 class="text-center">Solicitar análisis a una huerta</h3>
        <div class="card">
            <div class="card-header">Detalles de la solicitud</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-5 mx-auto text-center"> <!-- Agregado mx-auto y text-center -->
                        <label for="huerta" class="form-label">Huerta</label>
                        <select class="form-control" name="huerta" id="huerta">
                            <option value="">Seleccione Huerta</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <br>
    <div class="row text-center">
        <div class="col-lg-3"></div>
        <div class="col-lg-3">
            <button type="button" id="regristar" class="btn btn-primary btn-block">Solicitar</button>
        </div>
        <div class="col-lg-3">
            <button type="button" id="Cancelar" class="btn btn-secondary btn-block">Cancelar</button>
        </div>
        <div class="col-lg-3"></div>
    </div>
    <br>

    <script>
        const aux = new auxliar();

        $(document).ready(function() {
            // Cargar huertas
            $.ajax({
                url: '../../Backend/Productores/obtenerOpcionesHuertas.php',
                type: 'GET',
                dataType: 'json',
                    success: function(response) {
                    $.each(response, function(index, huerta) {
                        $('#huerta').append($('<option>', {
                            value: huerta.id_hue,
                            text: huerta.nombre
                        }));
                    });
                },
                error: function(xhr, statusLab, error) {
                    console.error('Error al obtener juntas locales:', statusLab, error);
                }
            });

            // Evento para registrar productor
            $('#regristar').click(function() {
                resultado = aux.validateAll([
                    {"valor": document.getElementById("huerta").value, "typeOf": "string", "mensaje": "<p>Insertar huerta</p><br>"}
                ]);

                if (resultado.estado) {
                    $.ajax({
                        url: '../../Backend/Productores/registrarSolicitud.php',
                        type: 'POST',
                        data: {
                            huerta: document.getElementById("huerta").value,
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
                                    window.location.href = "../Productores/menuProductor.php";
                                }
                            });
                        },
                        error: function(xhr, statusLab, error) {
                            let errorMessage = "Ocurrió un error al guardar la solicitud.";
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message; // Mensaje de error desde el backend
                            } else if (xhr.statusLab === 0) {
                                errorMessage = "No se pudo conectar al servidor. Verifica tu conexión a Internet.";
                            } else {
                                errorMessage += " Código de error: " + xhr.statusLab + " - " + error;
                            }
                            aux.alert("<h5>Error</h5><br/><p>" + errorMessage + "</p>", true, true);
                        }
                    });
                } else {
                    aux.alert(resultado.texto, true, true);
                }
            });

            $('#Cancelar').click(function() {
                window.location.href = "../Productores/menuProductor.php";
            });
        });

    </script>
</body>
</html>
