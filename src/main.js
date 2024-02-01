import './style.css'

async function getMakes() {
    let year = document.getElementById('vehicle_year').value;

    await fetch('https://vpic.nhtsa.dot.gov/api//vehicles/GetMakesForVehicleType/moto?format=json')
    .then(response => {
        return response.json();
    })
    .catch(error => {
        console.log(error);
    })
}

function getModels() {
    let year = document.getElementById('vehicle_year').value;
    let make = document.getElementById('vehicle_make').value;
}

document.getElementById('vehicle_year').addEventListener('change', );

document.getElementById('vehicle_make').addEventListener('change', getModels);