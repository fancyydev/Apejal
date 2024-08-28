const img_apeajal = document.getElementById("img_apeajal")
const barra_lateral = document.querySelector(".barra-lateral")
//Selecciona el primer span que encuentra
//const span = document.querySelector("span")
const spans = document.querySelectorAll("span")

img_apeajal.addEventListener("click",()=>{
    barra_lateral.classList.toggle("mini-barra-lateral");
    spans.forEach((span)=> {
        span.classList.toggle("oculto")
    });
});
