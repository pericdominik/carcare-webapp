document.addEventListener("DOMContentLoaded", () => {
    const serviceForm = document.getElementById("service-form");
    const serviceVehicleFilter = document.getElementById("service_vehicle_filter");

    loadVehiclesForServices();

    serviceVehicleFilter.addEventListener("change", () => {
    const vehicleId = serviceVehicleFilter.value;

    if (vehicleId === "") {
        resetServicesView();
        return;
    }

        loadServices(vehicleId);
    });


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

        loadServices(serviceData.vehicleId);

    } catch (error) {
        showFormMessage(
            "service-message",
            "error",
            "Nije moguće spremiti servis. Pokušaj ponovno."
        );
    }
}


async function loadServices(vehicleId) {
    const servicesList = document.getElementById("services-list");
    const servicesCount = document.getElementById("services-count");
    const totalCost = document.getElementById("total-cost");

    servicesList.innerHTML = `<p class="empty-state">Učitavanje servisnih zapisa...</p>`;
    servicesCount.textContent = "0 zapisa";
    totalCost.textContent = "0,00 €";

    try {
        const response = await fetch(`/carcare/api/services/get_services.php?vehicle_id=${vehicleId}`);
        const result = await response.json();

        if (!response.ok || !result.success) {
            servicesList.innerHTML = `<p class="empty-state">${result.message}</p>`;
            return;
        }

        renderServices(result.services, result.totalCost);

    } catch (error) {
        servicesList.innerHTML = `
            <p class="empty-state">
                Nije moguće dohvatiti servisne zapise. Pokušaj ponovno.
            </p>
        `;
    }
}


function renderServices(services, totalCostValue) {
    const servicesList = document.getElementById("services-list");
    const servicesCount = document.getElementById("services-count");
    const totalCost = document.getElementById("total-cost");

    servicesCount.textContent = getServicesCountText(services.length);
    totalCost.textContent = formatCurrency(totalCostValue);

    if (services.length === 0) {
        servicesList.innerHTML = `
            <p class="empty-state">
                Za odabrano vozilo još nema servisnih zapisa.
            </p>
        `;
        return;
    }

    servicesList.innerHTML = "";

    services.forEach((service) => {
        const card = document.createElement("article");
        card.className = "service-card";

        card.innerHTML = `
            <h3>${escapeHtml(service.service_type)}</h3>

            <div class="service-meta">
                <span><strong>Datum:</strong> ${formatDate(service.service_date)}</span>
                <span><strong>Kilometraža:</strong> ${Number(service.mileage_at_service).toLocaleString("hr-HR")} km</span>
                <span><strong>Cijena:</strong> ${formatCurrency(service.cost)}</span>
            </div>

            <p class="service-description">
                ${service.description ? escapeHtml(service.description) : "Nema dodatne napomene."}
            </p>

            <div class="card-actions">
                <button class="btn btn-danger btn-small" type="button">
                    Obriši
                </button>
            </div>
        `;

        servicesList.appendChild(card);
    });
}

function resetServicesView() {
    document.getElementById("services-list").innerHTML = `
        <p class="empty-state">Odaberi vozilo za prikaz servisne povijesti.</p>
    `;
    document.getElementById("services-count").textContent = "0 zapisa";
    document.getElementById("total-cost").textContent = "0,00 €";
}

function getServicesCountText(count) {
    if (count === 1) {
        return "1 zapis";
    }

    return `${count} zapisa`;
}

function formatCurrency(value) {
    return Number(value).toLocaleString("hr-HR", {
        style: "currency",
        currency: "EUR"
    });
}

function formatDate(dateString) {
    const date = new Date(dateString);

    return date.toLocaleDateString("hr-HR", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric"
    });
}