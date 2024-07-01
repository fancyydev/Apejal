
class login {
  
    constructor () {
    }
    
    confirmarlogin(information){
        this.verificarSesion();
        switch(information){
            case "Cliente":
                if(window.sessionStorage.getItem('dato') == undefined )
                    window.location.href = "/SistemaCliente/login.html"
            break;
            case "No-Clientes":
                if(window.sessionStorage.getItem('dato') != undefined)
                    window.location.href = "/SistemaCliente/index.html"
            break;
            case "Empleado":
                if(window.sessionStorage.getItem('username') == undefined)
                    window.location.href = "/index.html"    
            break;
            case "No-Empleado":
                if(window.sessionStorage.getItem('username') != undefined)
                    window.location.href = "/menu.html"    
            break;

            default:
            break;
        }
    }

    alertLogout(users){
        Swal.fire({
        title: 'Usted desea cerrar sesión?',
        showDenyButton: true,
        confirmButtonText: 'Yes',
        denyButtonText: 'No',
        customClass: {
            actions: 'my-actions',
            cancelButton: 'order-1 right-gap',
            confirmButton: 'order-2',
            denyButton: 'order-3',
        }
        }).then((result) => {
            if (result.isConfirmed) {

                window.sessionStorage.clear();

                if (users=="Cliente")
                    window.location.href = "/SistemaCliente/login.html"
                else
                    window.location.href = "/index.html"

            }
        })
    }

    position(){
        return window.sessionStorage.getItem("position");
    }

    cerrar(){
        Swal.close();
    }

    verificarSesion() {
        $.ajax({ 
            url: "/src/php/auxiliar/login.php", 
            method: "POST",
            data: {"Metodo":'ComprobacionSession',},
        }).done(function(respuesta){
            var session=Boolean(respuesta)
            if(!session){
                localStorage.clear();
            }
        })
}
    


    

}
