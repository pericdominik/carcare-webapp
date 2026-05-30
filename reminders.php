<?php
require_once "includes/auth_check.php";
require_once "includes/header.php";
?>

<main>
    <div class="container">
        <section class="page-header">
            <h1>Podsjetnici</h1>
            <p>
                Dodaj važne podsjetnike za vozila, poput registracije, tehničkog pregleda,
                zamjene ulja ili drugih budućih obveza.
            </p>
        </section>

        <section class="content-grid">
            <article class="form-card">
                <h2>Dodaj podsjetnik</h2>

                <div id="reminder-message" class="form-message" aria-live="polite"></div>

                <form id="reminder-form" class="app-form" novalidate>
                    <div class="form-group">
                        <label for="reminder_vehicle_id">Vozilo</label>
                        <select id="reminder_vehicle_id" name="reminder_vehicle_id">
                            <option value="">Učitavanje vozila...</option>
                        </select>
                        <small class="error-message" id="reminder-vehicle-error"></small>
                    </div>

                    <div class="form-group">
                        <label for="title">Naslov podsjetnika</label>
                        <input
                            type="text"
                            id="title"
                            name="title"
                            placeholder="npr. Registracija vozila"
                        >
                        <small class="error-message" id="title-error"></small>
                    </div>

                    <div class="form-group">
                        <label for="reminder_date">Datum podsjetnika</label>
                        <input type="date" id="reminder_date" name="reminder_date">
                        <small class="error-message" id="reminder-date-error"></small>
                    </div>

                    <div class="form-group">
                        <label for="reminder_description">Opis / napomena</label>
                        <textarea
                            id="reminder_description"
                            name="reminder_description"
                            rows="4"
                            placeholder="npr. Provjeriti dokumente i dogovoriti termin za tehnički pregled."
                        ></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary auth-btn">
                        Spremi podsjetnik
                    </button>
                </form>
            </article>

            <article class="list-card">
                <div class="section-title-row">
                    <h2>Moji podsjetnici</h2>
                    <span id="reminders-count" class="badge">0 podsjetnika</span>
                </div>

                <div class="service-toolbar">
                    <div class="form-group">
                        <label for="reminder_vehicle_filter">Prikaži podsjetnike za vozilo</label>
                        <select id="reminder_vehicle_filter">
                            <option value="">Učitavanje vozila...</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="reminder_status_filter">Status</label>
                        <select id="reminder_status_filter">
                            <option value="">Svi podsjetnici</option>
                            <option value="active">Aktivni</option>
                            <option value="completed">Riješeni</option>
                        </select>
                    </div>
                </div>

                <div id="reminders-list" class="services-list">
                    <p class="empty-state">Odaberi vozilo za prikaz podsjetnika.</p>
                </div>
            </article>
        </section>
    </div>
</main>

<script src="/carcare/assets/js/reminders.js?v=2"></script>

<?php require_once "includes/footer.php"; ?>