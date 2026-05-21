<?php require_once "includes/header.php"; ?>

<main>
    <div class="container">
        <section class="auth-section">
            <div class="auth-card">
                <h1>Prijava</h1>
                <p class="auth-intro">
                    Prijavi se u svoj CarCare račun i nastavi upravljati vozilima,
                    servisima i podsjetnicima.
                </p>

                <div id="login-message" class="form-message" aria-live="polite"></div>

                <form id="login-form" class="auth-form" novalidate>
                    <div class="form-group">
                        <label for="login-email">E-mail adresa</label>
                        <input
                            type="email"
                            id="login-email"
                            name="email"
                            placeholder="npr. marko@gmail.com"
                            autocomplete="email"
                        >
                        <small class="error-message" id="login-email-error"></small>
                    </div>

                    <div class="form-group">
                        <label for="login-password">Lozinka</label>
                        <input
                            type="password"
                            id="login-password"
                            name="password"
                            placeholder="Unesi svoju lozinku"
                            autocomplete="current-password"
                        >
                        <small class="error-message" id="login-password-error"></small>
                    </div>

                    <button type="submit" class="btn btn-primary auth-btn">
                        Prijavi se
                    </button>
                </form>

                <p class="auth-switch">
                    Nemaš račun?
                    <a href="/carcare/register.php">Registriraj se</a>
                </p>
            </div>
        </section>
    </div>
</main>

<script src="/carcare/assets/js/auth.js"></script>

<?php require_once "includes/footer.php"; ?>