<?php require_once "includes/header.php"; ?>

<main>
    <div class="container">
        <section class="hero">
            <div class="hero-content">
                <h1>Prati održavanje svog vozila na jednom mjestu.</h1>
                <p>
                    CarCare je web aplikacija za jednostavno vođenje servisne povijesti,
                    praćenje troškova održavanja i organiziranje važnih podsjetnika za tvoje vozilo.
                </p>

                <div class="button-group">
                    <a href="/carcare/register.php" class="btn btn-primary">Kreiraj račun</a>
                    <a href="/carcare/login.php" class="btn btn-secondary">Prijavi se</a>
                </div>
            </div>
        </section>

        <section class="features" aria-label="Glavne mogućnosti aplikacije">
            <article class="feature-card">
                <h2>Moja vozila</h2>
                <p>Dodaj svoja vozila i vodi pregled osnovnih podataka poput marke, modela i kilometraže.</p>
            </article>

            <article class="feature-card">
                <h2>Servisna povijest</h2>
                <p>Bilježi servise, popravke i troškove kako bi uvijek znao što je na automobilu odrađeno.</p>
            </article>

            <article class="feature-card">
                <h2>Pametni podsjetnici</h2>
                <p>Zapiši buduće obveze poput registracije, tehničkog pregleda ili zamjene ulja.</p>
            </article>
        </section>
    </div>
</main>

<?php require_once "includes/footer.php"; ?>