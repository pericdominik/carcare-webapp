document.addEventListener("DOMContentLoaded", () => {
    loadDashboardStats();
});

async function loadDashboardStats() {
    const messageBox = document.getElementById("dashboard-message");
    const upcomingList = document.getElementById("upcoming-reminders-list");

    try {
        const response = await fetch("/carcare/api/dashboard/get_stats.php");
        const result = await response.json();

        if (!response.ok || !result.success) {
            showDashboardMessage("error", result.message);
            upcomingList.innerHTML = `
                <p class="empty-state">Nije moguće dohvatiti podatke za dashboard.</p>
            `;
            return;
        }

        renderStats(result.stats);
        renderUpcomingReminders(result.upcomingReminders);

    } catch (error) {
        showDashboardMessage(
            "error",
            "Nije moguće dohvatiti dashboard podatke. Pokušaj ponovno."
        );

        upcomingList.innerHTML = `
            <p class="empty-state">Nije moguće dohvatiti podsjetnike.</p>
        `;
    }
}

function renderStats(stats) {
    document.getElementById("stat-vehicles").textContent = stats.totalVehicles;
    document.getElementById("stat-services").textContent = stats.totalServices;
    document.getElementById("stat-cost").textContent = formatCurrency(stats.totalServiceCost);
    document.getElementById("stat-reminders").textContent = stats.activeReminders;
}

function renderUpcomingReminders(reminders) {
    const upcomingList = document.getElementById("upcoming-reminders-list");

    if (reminders.length === 0) {
        upcomingList.innerHTML = `
            <p class="empty-state">
                Nema aktivnih podsjetnika.
            </p>
        `;
        return;
    }

    upcomingList.innerHTML = "";

    reminders.forEach((reminder) => {
        const card = document.createElement("article");
        card.className = "reminder-card";

        card.innerHTML = `
            <h3>${escapeHtml(reminder.title)}</h3>

            <div class="reminder-meta">
                <span><strong>Vozilo:</strong> ${escapeHtml(reminder.brand)} ${escapeHtml(reminder.model)}</span>
                <span><strong>Datum:</strong> ${formatDate(reminder.reminder_date)}</span>
                <span class="status-badge status-active">Aktivan</span>
            </div>

            <p class="reminder-description">
                ${reminder.description ? escapeHtml(reminder.description) : "Nema dodatne napomene."}
            </p>
        `;

        upcomingList.appendChild(card);
    });
}

function showDashboardMessage(type, message) {
    const messageBox = document.getElementById("dashboard-message");
    messageBox.className = `form-message ${type}`;
    messageBox.textContent = message;
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

function escapeHtml(value) {
    return String(value)
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;");
}