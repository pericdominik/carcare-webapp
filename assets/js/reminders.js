let currentReminders = [];

document.addEventListener("DOMContentLoaded", () => {
    const reminderForm = document.getElementById("reminder-form");
    const reminderVehicleFilter = document.getElementById("reminder_vehicle_filter");
    const reminderStatusFilter = document.getElementById("reminder_status_filter");

    loadVehiclesForReminders();

    reminderVehicleFilter.addEventListener("change", () => {
        const vehicleId = reminderVehicleFilter.value;

        if (vehicleId === "") {
            resetRemindersView();
            return;
        }

        loadReminders(vehicleId);
    });

    reminderStatusFilter.addEventListener("change", () => {
        applyReminderStatusFilter();
    });


    if (reminderForm) {
        reminderForm.addEventListener("submit", (event) => {
            event.preventDefault();

            const vehicleId = document.getElementById("reminder_vehicle_id").value;
            const title = document.getElementById("title").value.trim();
            const reminderDate = document.getElementById("reminder_date").value;
            const description = document.getElementById("reminder_description").value.trim();

            clearReminderErrors();

            let isValid = true;

            if (vehicleId === "") {
                showInputError(
                    "reminder_vehicle_id",
                    "reminder-vehicle-error",
                    "Odaberi vozilo."
                );
                isValid = false;
            }

            if (title === "") {
                showInputError(
                    "title",
                    "title-error",
                    "Unesi naslov podsjetnika."
                );
                isValid = false;
            }

            if (reminderDate === "") {
                showInputError(
                    "reminder_date",
                    "reminder-date-error",
                    "Odaberi datum podsjetnika."
                );
                isValid = false;
            }

            if (isValid) {
                addReminder({
                    vehicleId: Number(vehicleId),
                    title,
                    reminderDate,
                    description
                });
            }
        });
    }
});

async function loadVehiclesForReminders() {
    const reminderVehicleSelect = document.getElementById("reminder_vehicle_id");
    const reminderVehicleFilter = document.getElementById("reminder_vehicle_filter");

    try {
        const response = await fetch("/carcare/api/vehicles/get_vehicles.php");
        const result = await response.json();

        if (!response.ok || !result.success) {
            reminderVehicleSelect.innerHTML = `<option value="">Nije moguće dohvatiti vozila</option>`;
            reminderVehicleFilter.innerHTML = `<option value="">Nije moguće dohvatiti vozila</option>`;
            return;
        }

        fillReminderVehicleSelects(result.vehicles);

    } catch (error) {
        reminderVehicleSelect.innerHTML = `<option value="">Greška pri dohvaćanju vozila</option>`;
        reminderVehicleFilter.innerHTML = `<option value="">Greška pri dohvaćanju vozila</option>`;
    }
}

function fillReminderVehicleSelects(vehicles) {
    const reminderVehicleSelect = document.getElementById("reminder_vehicle_id");
    const reminderVehicleFilter = document.getElementById("reminder_vehicle_filter");

    if (vehicles.length === 0) {
        reminderVehicleSelect.innerHTML = `<option value="">Prvo dodaj vozilo</option>`;
        reminderVehicleFilter.innerHTML = `<option value="">Prvo dodaj vozilo</option>`;
        return;
    }

    const options = vehicles.map((vehicle) => {
        return `
            <option value="${vehicle.id}">
                ${escapeHtml(vehicle.brand)} ${escapeHtml(vehicle.model)} (${vehicle.year})
            </option>
        `;
    }).join("");

    reminderVehicleSelect.innerHTML = `
        <option value="">Odaberi vozilo</option>
        ${options}
    `;

    reminderVehicleFilter.innerHTML = `
        <option value="">Odaberi vozilo</option>
        ${options}
    `;
}

function clearReminderErrors() {
    const errorElements = document.querySelectorAll("#reminder-form .error-message");
    const inputElements = document.querySelectorAll(
        "#reminder-form input, #reminder-form select"
    );

    errorElements.forEach((element) => {
        element.textContent = "";
    });

    inputElements.forEach((input) => {
        input.classList.remove("input-error");
    });

    const message = document.getElementById("reminder-message");
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


async function addReminder(reminderData) {
    try {
        const response = await fetch("/carcare/api/reminders/add_reminder.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(reminderData)
        });

        const result = await response.json();

        if (!response.ok || !result.success) {
            showFormMessage("reminder-message", "error", result.message);
            return;
        }

        showFormMessage("reminder-message", "success", result.message);

        document.getElementById("reminder-form").reset();

        const reminderVehicleFilter = document.getElementById("reminder_vehicle_filter");
        reminderVehicleFilter.value = String(reminderData.vehicleId);

        document.getElementById("reminder_status_filter").value = "active";

        loadReminders(reminderData.vehicleId);

    } catch (error) {
        showFormMessage(
            "reminder-message",
            "error",
            "Nije moguće spremiti podsjetnik. Pokušaj ponovno."
        );
    }
}


async function loadReminders(vehicleId) {
    const remindersList = document.getElementById("reminders-list");
    const remindersCount = document.getElementById("reminders-count");

    remindersList.innerHTML = `<p class="empty-state">Učitavanje podsjetnika...</p>`;
    remindersCount.textContent = "0 podsjetnika";

    try {
        const response = await fetch(`/carcare/api/reminders/get_reminders.php?vehicle_id=${vehicleId}`);
        const result = await response.json();

        if (!response.ok || !result.success) {
            remindersList.innerHTML = `<p class="empty-state">${result.message}</p>`;
            return;
        }

        currentReminders = result.reminders;
        applyReminderStatusFilter();

    } catch (error) {
        remindersList.innerHTML = `
            <p class="empty-state">
                Nije moguće dohvatiti podsjetnike. Pokušaj ponovno.
            </p>
        `;
    }
}

function applyReminderStatusFilter() {
    const statusFilter = document.getElementById("reminder_status_filter");
    const selectedStatus = statusFilter.value;

    let filteredReminders = currentReminders;

    if (selectedStatus !== "") {
        filteredReminders = currentReminders.filter((reminder) => {
            return reminder.status === selectedStatus;
        });
    }

    renderReminders(filteredReminders);
}

function renderReminders(reminders) {
    const remindersList = document.getElementById("reminders-list");
    const remindersCount = document.getElementById("reminders-count");

    remindersCount.textContent = getRemindersCountText(reminders.length);

    if (reminders.length === 0) {
        remindersList.innerHTML = `
            <p class="empty-state">
                Nema podsjetnika za odabrani filter.
            </p>
        `;
        return;
    }

    remindersList.innerHTML = "";

    reminders.forEach((reminder) => {
        const card = document.createElement("article");
        card.className = "reminder-card";

        const statusText = reminder.status === "active" ? "Aktivan" : "Riješen";
        const statusClass = reminder.status === "active" ? "status-active" : "status-completed";

        card.innerHTML = `
            <h3>${escapeHtml(reminder.title)}</h3>

            <div class="reminder-meta">
                <span><strong>Datum:</strong> ${formatDate(reminder.reminder_date)}</span>
                <span class="status-badge ${statusClass}">${statusText}</span>
            </div>

            <p class="reminder-description">
                ${reminder.description ? escapeHtml(reminder.description) : "Nema dodatne napomene."}
            </p>

            <div class="card-actions">
                ${
                    reminder.status === "active"
                        ? `<button class="btn btn-secondary btn-small" type="button">Označi kao riješeno</button>`
                        : ""
                }

                <button class="btn btn-danger btn-small" type="button">
                    Obriši
                </button>
            </div>
        `;

        remindersList.appendChild(card);
    });
}

function resetRemindersView() {
    currentReminders = [];

    document.getElementById("reminders-list").innerHTML = `
        <p class="empty-state">Odaberi vozilo za prikaz podsjetnika.</p>
    `;

    document.getElementById("reminders-count").textContent = "0 podsjetnika";
    document.getElementById("reminder_status_filter").value = "";
}

function getRemindersCountText(count) {
    if (count === 1) {
        return "1 podsjetnik";
    }

    return `${count} podsjetnika`;
}

function formatDate(dateString) {
    const date = new Date(dateString);

    return date.toLocaleDateString("hr-HR", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric"
    });
}