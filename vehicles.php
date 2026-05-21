<?php
require_once "includes/auth_check.php";
require_once "includes/header.php";
?>

<main>
    <div class="container">
        <section class="page-header">
            <h1>Moja vozila</h1>
            <p>
                Dodaj svoja vozila i vodi osnovne podatke poput marke, modela,
                godine proizvodnje, registracije i trenutne kilometraže.
            </p>
        </section>

        <section class="content-grid">
            <article class="form-card">
                <h2>Dodaj novo vozilo</h2>

                <div id="vehicle-message" class="form-message" aria-live="polite"></div>

                <form id="vehicle-form" class="app-form" novalidate>
                    <div class="form-group">
                        <label for="brand">Marka vozila</label>
                        <input
                            type="text"
                            id="brand"
                            name="brand"
                            placeholder="npr. Volkswagen"
                        >
                        <small class="error-message" id="brand-error"></small>
                    </div>

                    <div class="form-group">
                        <label for="model">Model vozila</label>
                        <input
                            type="text"
                            id="model"
                            name="model"
                            placeholder="npr. Golf 7"
                        >
                        <small class="error-message" id="model-error"></small>
                    </div>

                    <div class="form-group">
                        <label for="year">Godina proizvodnje</label>
                        <input
                            type="number"
                            id="year"
                            name="year"
                            placeholder="npr. 2016"
                        >
                        <small class="error-message" id="year-error"></small>
                    </div>

                    <div class="form-group">
                        <label for="license_plate">Registracijska oznaka</label>
                        <input
                            type="text"
                            id="license_plate"
                            name="license_plate"
                            placeholder="npr. OS-123-AB"
                        >
                        <small class="error-message" id="license-plate-error"></small>
                    </div>

                    <div class="form-group">
                        <label for="mileage">Trenutna kilometraža</label>
                        <input
                            type="number"
                            id="mileage"
                            name="mileage"
                            placeholder="npr. 175000"
                        >
                        <small class="error-message" id="mileage-error"></small>
                    </div>

                    <button type="submit" class="btn btn-primary auth-btn">
                        Spremi vozilo
                    </button>
                </form>
            </article>

            <article class="list-card">
                <div class="section-title-row">
                    <h2>Popis vozila</h2>
                    <span id="vehicle-count" class="badge">0 vozila</span>
                </div>

                <div id="vehicles-list" class="vehicles-list">
                    <p class="empty-state">Učitavanje vozila...</p>
                </div>
            </article>
        </section>
    </div>
</main>

<script src="/carcare/assets/js/vehicles.js"></script>

<?php require_once "includes/footer.php"; ?>