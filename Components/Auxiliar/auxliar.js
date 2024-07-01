class auxliar {
    constructor() {}

    validateAll(json) {
        var resultado = { estado: true, texto: "<h4>Por favor de correguir los siguientes errores</h4><br/>" };
        json.forEach(dato => {
            switch (dato.typeOf) {
                case "int":
                    if (dato.valor <= 0) {
                        resultado.texto = resultado.texto + dato.mensaje;
                        resultado.estado = false;
                    }
                    break;
                case "string":
                    if (dato.valor.length === 0) {
                        resultado.texto = resultado.texto + dato.mensaje;
                        resultado.estado = false;
                    }
                    break;
                case "file":
                    if (dato.valor === 0) {
                        resultado.texto = resultado.texto + dato.mensaje;
                        resultado.estado = false;
                    }
                    break;
                case "select":
                    if (dato.valor === '-20') {
                        resultado.texto = resultado.texto + dato.mensaje;
                        resultado.estado = false;
                    }
                    break;
                case "table":
                    if (dato.valor <= 0) {
                        resultado.texto = resultado.texto + dato.mensaje;
                        resultado.estado = false;
                    }
                    break;
                case "date":
                    if (dato.valor.length === 0) {
                        resultado.texto = resultado.texto + dato.mensaje;
                        resultado.estado = false;
                    }
                    break;
                case "double":
                    if (isNaN(dato.valor) || parseFloat(dato.valor) <= 0) {
                        resultado.texto = resultado.texto + dato.mensaje;
                        resultado.estado = false;
                    }
                    break;
            }
        });

        return resultado;
    }

    alert(mensaje, botton = true, eliminar = true) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                html: mensaje,
                showConfirmButton: botton,
                allowOutsideClick: eliminar,
            });
        } else {
            alert(mensaje); // Fallback si Swal no está definido
        }
    }

    cerrar() {
        if (typeof Swal !== 'undefined') {
            Swal.close();
        }
    }

    getParameterByName(name) {
        name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
        var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
            results = regex.exec(location.search);
        return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
    }
}
