<?php require_once "includes/header.php"; ?>

<main>
    <div class="container">
        <section class="auth-section">
            <div class="auth-card">
                <h1>Registracija</h1>
                <p class="auth-intro">
                    Kreiraj korisnički račun i počni voditi evidenciju o svojim vozilima,
                    servisima i podsjetnicima.
                </p>

                <div id="register-message" class="form-message" aria-live="polite"></div>

                <form id="register-form" class="auth-form" novalidate>
                    <div class="form-group">
                        <label for="name">Ime i prezime</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            placeholder="npr. Marko Marić"
                            autocomplete="name"
                        >
                        <small class="error-message" id="name-error"></small>
                    </div>

                    <div class="form-group">
                        <label for="email">E-mail adresa</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="npr. marko@gmail.com"
                            autocomplete="email"
                        >
                        <small class="error-message" id="email-error"></small>
                    </div>

                    <div class="form-group">
                        <label for="password">Lozinka</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Najmanje 6 znakova"
                            autocomplete="new-password"
                        >
                        <small class="error-message" id="password-error"></small>
                    </div>

                    <div class="form-group">
                        <label for="confirm-password">Potvrdi lozinku</label>
                        <input
                            type="password"
                            id="confirm-password"
                            name="confirm-password"
                            placeholder="Ponovno upiši lozinku"
                            autocomplete="new-password"
                        >
                        <small class="error-message" id="confirm-password-error"></small>
                    </div>

                    <button type="submit" class="btn btn-primary auth-btn">
                        Registriraj se
                    </button>
                </form>

                <p class="auth-switch">
                    Već imaš račun?
                    <a href="/carcare/login.php">Prijavi se</a>
                </p>
            </div>
        </section>
    </div>
</main>

<script src="/carcare/assets/js/auth.js"></script>

<?php require_once "includes/footer.php"; ?>