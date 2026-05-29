<?php
require_once "includes/auth_check.php";
require_once "includes/header.php";
?>

<main>
    <div class="container">
        <section class="page-header">
            <h1>Servisna povijest</h1>
            <p>
                Odaberi vozilo i zabilježi servisne zahvate, troškove, datum servisa
                i kilometražu na kojoj je servis napravljen.
            </p>
        </section>

        <section class="content-grid">
            <article class="form-card">
                <h2>Dodaj servisni zapis</h2>

                <div id="service-message" class="form-message" aria-live="polite"></div>

                <form id="service-form" class="app-form" novalidate>
                    <div class="form-group">
                        <label for="vehicle_id">Vozilo</label>
                        <select id="vehicle_id" name="vehicle_id">
                            <option value="">Učitavanje vozila...</option>
                        </select>
                        <small class="error-message" id="vehicle-id-error"></small>
                    </div>

                    <div class="form-group">
                        <label for="service_type">Vrsta servisa</label>
                        <select id="service_type" name="service_type">
                            <option value="">Odaberi vrstu servisa</option>
                            <option value="Redovni servis">Mali servis</option>
                            <option value="Zamjena ulja">Zamjena ulja</option>
                            <option value="Kočnice">Kočnice</option>
                            <option value="Gume">Gume</option>
                            <option value="Kvačilo">Kvačilo</option>
                            <option value="Amortizeri">Amortizeri</option>
                            <option value="Registracija">Registracija</option>
                            <option value="Ostalo">Ostalo</option>
                        </select>
                        <small class="error-message" id="service-type-error"></small>
                    </div>

                    <div class="form-group">
                        <label for="service_date">Datum servisa</label>
                        <input type="date" id="service_date" name="service_date">
                        <small class="error-message" id="service-date-error"></small>
                    </div>

                    <div class="form-group">
                        <label for="mileage_at_service">Kilometraža na servisu</label>
                        <input
                            type="number"
                            id="mileage_at_service"
                            name="mileage_at_service"
                            placeholder="npr. 180000"
                        >
                        <small class="error-message" id="service-mileage-error"></small>
                    </div>

                    <div class="form-group">
                        <label for="cost">Cijena servisa (€)</label>
                        <input
                            type="number"
                            id="cost"
                            name="cost"
                            step="0.01"
                            placeholder="npr. 250.00"
                        >
                        <small class="error-message" id="cost-error"></small>
                    </div>

                    <div class="form-group">
                        <label for="description">Opis / napomena</label>
                        <textarea
                            id="description"
                            name="description"
                            rows="4"
                            placeholder="npr. Zamijenjeno ulje, filter ulja i filter zraka."
                        ></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary auth-btn">
                        Spremi servis
                    </button>
                </form>
            </article>

            <article class="list-card">
                <div class="section-title-row">
                    <h2>Zapisi servisa</h2>
                    <span id="services-count" class="badge">0 zapisa</span>
                </div>

                <div class="service-toolbar">
                    <div class="form-group">
                        <label for="service_vehicle_filter">Prikaži servise za vozilo</label>
                        <select id="service_vehicle_filter">
                            <option value="">Učitavanje vozila...</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="service_type_filter">Filtriraj po vrsti</label>
                        <select id="service_type_filter">
                            <option value="">Sve vrste</option>
                            <option value="Redovni servis">Redovni servis</option>
                            <option value="Zamjena ulja">Zamjena ulja</option>
                            <option value="Kočnice">Kočnice</option>
                            <option value="Gume">Gume</option>
                            <option value="Kvačilo">Kvačilo</option>
                            <option value="Amortizeri">Amortizeri</option>
                            <option value="Registracija">Registracija</option>
                            <option value="Ostalo">Ostalo</option>
                        </select>
                    </div>

                    <div class="total-cost-box">
                        <span>Ukupan trošak</span>
                        <strong id="total-cost">0,00 €</strong>
                    </div>
                </div>

                <div id="services-list" class="services-list">
                    <p class="empty-state">Odaberi vozilo za prikaz servisne povijesti.</p>
                </div>
            </article>
        </section>
    </div>
</main>

<script src="/carcare/assets/js/services.js?v=3"></script>

<?php require_once "includes/footer.php"; ?>