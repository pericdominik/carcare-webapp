<?php
require_once "includes/auth_check.php";
require_once "includes/header.php";
?>

<main>
    <div class="container">
        <section class="page-header">
            <h1>Dobrodošao, <?php echo htmlspecialchars($_SESSION["user_name"]); ?>!</h1>
            <p>
                Ovdje možeš vidjeti kratki pregled svojih vozila, servisnih troškova
                i aktivnih podsjetnika.
            </p>
        </section>

        <section id="dashboard-message" class="form-message" aria-live="polite"></section>

        <section class="stats-grid">
            <article class="stat-card">
                <span class="stat-label">Moja vozila</span>
                <strong id="stat-vehicles">0</strong>
                <p>Ukupan broj dodanih vozila.</p>
            </article>

            <article class="stat-card">
                <span class="stat-label">Servisni zapisi</span>
                <strong id="stat-services">0</strong>
                <p>Ukupan broj spremljenih servisa.</p>
            </article>

            <article class="stat-card">
                <span class="stat-label">Trošak servisa</span>
                <strong id="stat-cost">0,00 €</strong>
                <p>Ukupan zabilježeni trošak održavanja.</p>
            </article>

            <article class="stat-card">
                <span class="stat-label">Aktivni podsjetnici</span>
                <strong id="stat-reminders">0</strong>
                <p>Podsjetnici koji još nisu označeni kao riješeni.</p>
            </article>
        </section>

        <section class="dashboard-grid">
            <article class="list-card">
                <div class="section-title-row">
                    <h2>Najbliži podsjetnici</h2>
                    <a href="/carcare/reminders.php" class="btn btn-secondary btn-small">
                        Svi podsjetnici
                    </a>
                </div>

                <div id="upcoming-reminders-list" class="services-list">
                    <p class="empty-state">Učitavanje podsjetnika...</p>
                </div>
            </article>

            <article class="list-card">
                <h2>Brze akcije</h2>

                <div class="quick-actions">
                    <a href="/carcare/vehicles.php" class="quick-action-card">
                        <strong>Dodaj vozilo</strong>
                        <span>Unesi novo vozilo u aplikaciju.</span>
                    </a>

                    <a href="/carcare/services.php" class="quick-action-card">
                        <strong>Dodaj servis</strong>
                        <span>Zabilježi servisni zahvat i trošak.</span>
                    </a>

                    <a href="/carcare/reminders.php" class="quick-action-card">
                        <strong>Dodaj podsjetnik</strong>
                        <span>Postavi buduću obvezu za vozilo.</span>
                    </a>
                </div>
            </article>
        </section>
    </div>
</main>

<script src="/carcare/assets/js/dashboard.js?v=2"></script>

<?php require_once "includes/footer.php"; ?>