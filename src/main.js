import './style.css'

async function getMakes() {
    let year = document.getElementById('vehicle_year').value;

    const response = await fetch('https://vpic.nhtsa.dot.gov/api//vehicles/GetMakesForVehicleType/moto?format=json')

    const data = await response.json();

    let makes = data.Results;

    makes.forEach(make => {
        let option = document.createElement('option');
        option.value = make.MakeName;
        option.text = make.MakeName;
        document.getElementById('vehicle_make').appendChild(option);
    })
}

async function getModels() {
    let modelSelect = document.getElementById('vehicle_model');

    while (modelSelect.firstChild) {
        modelSelect.removeChild(modelSelect.firstChild);
    }

    let make = document.getElementById('vehicle_make').value;
    let year = document.getElementById('vehicle_year').value;

    const response = await fetch(`https://vpic.nhtsa.dot.gov/api/vehicles/GetModelsForMakeYear/make/${make}/modelyear/${year}/vehicletype/moto?format=json`);

    const data = await response.json();

    let models = data.Results;

    models.forEach(model => {
        let option = document.createElement('option');
        option.value = model.Model_Name;
        option.text = model.Model_Name;
        document.getElementById('vehicle_model').appendChild(option);
    })
}

document.getElementById('vehicle_year').addEventListener('change', getMakes);

document.getElementById('vehicle_make').addEventListener('change', getModels);