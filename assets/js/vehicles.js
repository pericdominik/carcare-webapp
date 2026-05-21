document.addEventListener("DOMContentLoaded", () => {
    const vehicleForm = document.getElementById("vehicle-form");

    loadVehicles();

    if (vehicleForm) {
        vehicleForm.addEventListener("submit", (event) => {
            event.preventDefault();

            const brand = document.getElementById("brand").value.trim();
            const model = document.getElementById("model").value.trim();
            const year = document.getElementById("year").value.trim();
            const licensePlate = document.getElementById("license_plate").value.trim();
            const mileage = document.getElementById("mileage").value.trim();

            clearVehicleErrors();

            let isValid = true;

            if (brand === "") {
                showInputError("brand", "brand-error", "Unesi marku vozila.");
                isValid = false;
            }

            if (model === "") {
                showInputError("model", "model-error", "Unesi model vozila.");
                isValid = false;
            }

            const currentYear = new Date().getFullYear();
            const yearNumber = Number(year);

            if (year === "") {
                showInputError("year", "year-error", "Unesi godinu proizvodnje.");
                isValid = false;
            } else if (yearNumber < 1950 || yearNumber > currentYear + 1) {
                showInputError(
                    "year",
                    "year-error",
                    `Godina mora biti između 1950 i ${currentYear + 1}.`
                );
                isValid = false;
            }

            const mileageNumber = Number(mileage);

            if (mileage === "") {
                showInputError("mileage", "mileage-error", "Unesi trenutnu kilometražu.");
                isValid = false;
            } else if (mileageNumber < 0) {
                showInputError("mileage", "mileage-error", "Kilometraža ne može biti negativna.");
                isValid = false;
            }

            if (isValid) {
                addVehicle({
                    brand,
                    model,
                    year: yearNumber,
                    licensePlate,
                    mileage: mileageNumber
                });
            }
        });
    }
});

function clearVehicleErrors() {
    const errorElements = document.querySelectorAll("#vehicle-form .error-message");
    const inputElements = document.querySelectorAll("#vehicle-form input");

    errorElements.forEach((element) => {
        element.textContent = "";
    });

    inputElements.forEach((input) => {
        input.classList.remove("input-error");
    });

    const message = document.getElementById("vehicle-message");
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


async function addVehicle(vehicleData) {
    try {
        const response = await fetch("/carcare/api/vehicles/add_vehicle.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(vehicleData)
        });

        const result = await response.json();

        if (!response.ok || !result.success) {
            showFormMessage("vehicle-message", "error", result.message);
            return;
        }

        showFormMessage("vehicle-message", "success", result.message);

        document.getElementById("vehicle-form").reset();

        loadVehicles();

    } catch (error) {
        showFormMessage(
            "vehicle-message",
            "error",
            "Nije moguće spremiti vozilo. Pokušaj ponovno."
        );
    }
}


async function loadVehicles() {
    const vehiclesList = document.getElementById("vehicles-list");
    const vehicleCount = document.getElementById("vehicle-count");

    vehiclesList.innerHTML = `<p class="empty-state">Učitavanje vozila...</p>`;

    try {
        const response = await fetch("/carcare/api/vehicles/get_vehicles.php");
        const result = await response.json();

        if (!response.ok || !result.success) {
            vehiclesList.innerHTML = `<p class="empty-state">${result.message}</p>`;
            vehicleCount.textContent = "0 vozila";
            return;
        }

        renderVehicles(result.vehicles);

    } catch (error) {
        vehiclesList.innerHTML = `
            <p class="empty-state">
                Nije moguće dohvatiti vozila. Pokušaj ponovno.
            </p>
        `;
        vehicleCount.textContent = "0 vozila";
    }
}

function renderVehicles(vehicles) {
    const vehiclesList = document.getElementById("vehicles-list");
    const vehicleCount = document.getElementById("vehicle-count");

    vehicleCount.textContent = getVehicleCountText(vehicles.length);

    if (vehicles.length === 0) {
        vehiclesList.innerHTML = `
            <p class="empty-state">
                Još nemaš dodanih vozila. Dodaj prvo vozilo pomoću forme.
            </p>
        `;
        return;
    }

    vehiclesList.innerHTML = "";

    vehicles.forEach((vehicle) => {
        const card = document.createElement("article");
        card.className = "vehicle-card";

        card.innerHTML = `
            <h3>${escapeHtml(vehicle.brand)} ${escapeHtml(vehicle.model)}</h3>

            <div class="vehicle-meta">
                <span><strong>Godina:</strong> ${vehicle.year}</span>
                <span><strong>Registracija:</strong> ${escapeHtml(vehicle.license_plate || "Nije unesena")}</span>
                <span><strong>Kilometraža:</strong> ${Number(vehicle.mileage).toLocaleString("hr-HR")} km</span>
            </div>

            <div class="card-actions">
                <a class="btn btn-secondary btn-small" href="/carcare/services.php?vehicle_id=${vehicle.id}">
                    Servisi
                </a>
                <button
                    class="btn btn-danger btn-small"
                    type="button"
                    data-delete-vehicle="${vehicle.id}"
                >
                    Obriši
                </button>
            </div>
        `;

        vehiclesList.appendChild(card);

        const deleteButton = card.querySelector("[data-delete-vehicle]");

        deleteButton.addEventListener("click", () => {
            const confirmed = confirm(
                `Jesi li siguran da želiš obrisati vozilo ${vehicle.brand} ${vehicle.model}?`
            );

            if (confirmed) {
                deleteVehicle(vehicle.id);
            }
        });
    });
}

function getVehicleCountText(count) {
    if (count === 1) {
        return "1 vozilo";
    }

    if (count >= 2 && count <= 4) {
        return `${count} vozila`;
    }

    return `${count} vozila`;
}

function escapeHtml(value) {
    return String(value)
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;");
}


async function deleteVehicle(vehicleId) {
    try {
        const response = await fetch("/carcare/api/vehicles/delete_vehicle.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                vehicleId
            })
        });

        const result = await response.json();

        if (!response.ok || !result.success) {
            showFormMessage("vehicle-message", "error", result.message);
            return;
        }

        showFormMessage("vehicle-message", "success", result.message);

        loadVehicles();

    } catch (error) {
        showFormMessage(
            "vehicle-message",
            "error",
            "Nije moguće obrisati vozilo. Pokušaj ponovno."
        );
    }
}