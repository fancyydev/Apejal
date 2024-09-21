document.addEventListener('DOMContentLoaded', function() {
    
    const aPlantacion = [];
    for (let i = 2000; i <= 2050; i += 1) {
        aPlantacion.push(i);
    }

    const ciudades = [
        "Acatic", "Acatlán de Juárez", "Ahualulco de Mercado", "Amacueca", "Amatitán", "Ameca",
        "Arandas", "Atemajac de Brizuela", "Atengo", "Atenguillo", "Atotonilco el Alto", "Atoyac",
        "Autlán de Navarro", "Ayotlán", "Ayutla", "Bolaños", "Cabo Corrientes", "Casimiro Castillo",
        "Cañadas de Obregón", "Chapala", "Chimaltitán", "Chiquilistlán", "Cihuatlán", "Cocula",
        "Colotlán", "Concepción de Buenos Aires", "Cuautitlán de García Barragán", "Cuautla", 
        "Cuquío", "Degollado", "Ejutla", "El Arenal", "El Grullo", "El Limón", "El Salto", 
        "Encarnación de Díaz", "Etzatlán", "Gómez Farías", "Guachinango", "Guadalajara", "Hostotipaquillo",
        "Huejúcar", "Huejuquilla el Alto", "Ixtlahuacán de los Membrillos", "Ixtlahuacán del Río",
        "Jamay", "Jesús María", "Jilotlán de los Dolores", "Jocotepec", "Juanacatlán", "Juchitlán",
        "La Barca", "La Huerta", "La Manzanilla de la Paz", "Lagos de Moreno", "Magdalena", "Mascota",
        "Mazamitla", "Mexticacán", "Mezquitic", "Mixtlán", "Ocotlán", "Ojuelos de Jalisco", "Pihuamo",
        "Poncitlán", "Puerto Vallarta", "Quitupan", "San Cristóbal de la Barranca", "San Diego de Alejandría",
        "San Gabriel", "San Ignacio Cerro Gordo", "San Juan de los Lagos", "San Juanito de Escobedo", 
        "San Julián", "San Marcos", "San Martín de Bolaños", "San Martín Hidalgo", "San Miguel el Alto",
        "San Pedro Tlaquepaque", "San Sebastián del Oeste", "Santa María de los Ángeles", 
        "Santa María del Oro", "Sayula", "Tala", "Talpa de Allende", "Tamazula de Gordiano", "Tapalpa",
        "Tecalitlán", "Techaluta de Montenegro", "Tecolotlán", "Tenamaxtlán", "Tepatitlán de Morelos",
        "Teocaltiche", "Teocuitatlán de Corona", "Tequila", "Teuchitlán", "Tizapán el Alto",
        "Tlajomulco de Zúñiga", "Tonalá", "Tonaya", "Tonila", "Totatiche", "Tototlán", 
        "Tuxcacuesco", "Tuxcueca", "Tuxpan", "Unión de San Antonio", "Unión de Tula", 
        "Valle de Guadalupe", "Valle de Juárez", "Villa Corona", "Villa Guerrero", "Villa Hidalgo",
        "Villa Purificación", "Yahualica de González Gallo", "Zacoalco de Torres", "Zapopan", 
        "Zapotiltic", "Zapotitlán de Vadillo", "Zapotlán del Rey", "Zapotlán el Grande", "Zapotlanejo"
    ];



    const ciudadSelect = document.getElementById('ciudad');
    const aPlantacionSelect = document.getElementById('aPlantacion');

    //Llena el select de ciudades
    ciudades.forEach(city => {
        const option = document.createElement('option');
        option.value = city;
        option.textContent = city;
        ciudadSelect.appendChild(option);
    });

    aPlantacion.forEach(anio => {
        const option = document.createElement('option');
        option.value = anio;
        option.textContent = anio;
        aPlantacionSelect.appendChild(option);
    });

});

