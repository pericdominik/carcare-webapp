document.addEventListener("DOMContentLoaded", () => {
    const serviceForm = document.getElementById("service-form");

    loadVehiclesForServices();

    if (serviceForm) {
        serviceForm.addEventListener("submit", (event) => {
            event.preventDefault();

            const vehicleId = document.getElementById("vehicle_id").value;
            const serviceType = document.getElementById("service_type").value;
            const serviceDate = document.getElementById("service_date").value;
            const mileageAtService = document.getElementById("mileage_at_service").value.trim();
            const cost = document.getElementById("cost").value.trim();
            const description = document.getElementById("description").value.trim();

            clearServiceErrors();

            let isValid = true;

            if (vehicleId === "") {
                showInputError("vehicle_id", "vehicle-id-error", "Odaberi vozilo.");
                isValid = false;
            }

            if (serviceType === "") {
                showInputError("service_type", "service-type-error", "Odaberi vrstu servisa.");
                isValid = false;
            }

            if (serviceDate === "") {
                showInputError("service_date", "service-date-error", "Odaberi datum servisa.");
                isValid = false;
            }

            const mileageNumber = Number(mileageAtService);

            if (mileageAtService === "") {
                showInputError("mileage_at_service", "service-mileage-error", "Unesi kilometražu.");
                isValid = false;
            } else if (mileageNumber < 0) {
                showInputError("mileage_at_service", "service-mileage-error", "Kilometraža ne može biti negativna.");
                isValid = false;
            }

            const costNumber = Number(cost);

            if (cost === "") {
                showInputError("cost", "cost-error", "Unesi cijenu servisa.");
                isValid = false;
            } else if (costNumber < 0) {
                showInputError("cost", "cost-error", "Cijena ne može biti negativna.");
                isValid = false;
            }

            if (isValid) {
                addService({
                    vehicleId: Number(vehicleId),
                    serviceType,
                    serviceDate,
                    mileageAtService: mileageNumber,
                    cost: costNumber,
                    description
                });
            }
        });
    }
});

async function loadVehiclesForServices() {
    const vehicleSelect = document.getElementById("vehicle_id");
    const vehicleFilter = document.getElementById("service_vehicle_filter");

    try {
        const response = await fetch("/carcare/api/vehicles/get_vehicles.php");
        const result = await response.json();

        if (!response.ok || !result.success) {
            vehicleSelect.innerHTML = `<option value="">Nije moguće dohvatiti vozila</option>`;
            vehicleFilter.innerHTML = `<option value="">Nije moguće dohvatiti vozila</option>`;
            return;
        }

        fillVehicleSelects(result.vehicles);

    } catch (error) {
        vehicleSelect.innerHTML = `<option value="">Greška pri dohvaćanju vozila</option>`;
        vehicleFilter.innerHTML = `<option value="">Greška pri dohvaćanju vozila</option>`;
    }
}

function fillVehicleSelects(vehicles) {
    const vehicleSelect = document.getElementById("vehicle_id");
    const vehicleFilter = document.getElementById("service_vehicle_filter");

    if (vehicles.length === 0) {
        vehicleSelect.innerHTML = `<option value="">Prvo dodaj vozilo</option>`;
        vehicleFilter.innerHTML = `<option value="">Prvo dodaj vozilo</option>`;
        return;
    }

    const options = vehicles.map((vehicle) => {
        return `
            <option value="${vehicle.id}">
                ${escapeHtml(vehicle.brand)} ${escapeHtml(vehicle.model)} (${vehicle.year})
            </option>
        `;
    }).join("");

    vehicleSelect.innerHTML = `
        <option value="">Odaberi vozilo</option>
        ${options}
    `;

    vehicleFilter.innerHTML = `
        <option value="">Odaberi vozilo</option>
        ${options}
    `;
}

function clearServiceErrors() {
    const errorElements = document.querySelectorAll("#service-form .error-message");
    const inputElements = document.querySelectorAll("#service-form input, #service-form select");

    errorElements.forEach((element) => {
        element.textContent = "";
    });

    inputElements.forEach((input) => {
        input.classList.remove("input-error");
    });

    const message = document.getElementById("service-message");
    message.className = "form-message";
    message.textContent = "";
}

function showInputError(inputId, errorId, message) {
    const input = document.getElementById(inputId);
    const error = document.getElementById(errorId);

    input.classList.add("input-error");
    error.textContent = message;
}

function showFormMessage(elementId, type, message) {
    const messageBox = document.getElementById(elementId);
    messageBox.className = `form-message ${type}`;
    messageBox.textContent = message;
}

function escapeHtml(value) {
    return String(value)
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;");
}


async function addService(serviceData) {
    try {
        const response = await fetch("/carcare/api/services/add_service.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(serviceData)
        });

        const result = await response.json();

        if (!response.ok || !result.success) {
            showFormMessage("service-message", "error", result.message);
            return;
        }

        showFormMessage("service-message", "success", result.message);

        document.getElementById("service-form").reset();

        const filterSelect = document.getElementById("service_vehicle_filter");
        filterSelect.value = String(serviceData.vehicleId);

    } catch (error) {
        showFormMessage(
            "service-message",
            "error",
            "Nije moguće spremiti servis. Pokušaj ponovno."
        );
    }
}