document.addEventListener('DOMContentLoaded', function() {
    
    const aPlantacion = [];
    for (let i = 2000; i <= 2050; i += 1) {
        aPlantacion.push(i);
    }

    const aPlantacionSelect = document.getElementById('aPlantacion');

    aPlantacion.forEach(anio => {
        const option = document.createElement('option');
        option.value = anio;
        option.textContent = anio;
        aPlantacionSelect.appendChild(option);
    });

});

