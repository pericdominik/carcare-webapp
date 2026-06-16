<?php require_once "includes/header.php"; ?>

<main>
    <div class="container">
        <section class="page-header">
            <h1>Izvori i literatura</h1>
            <p>
                Na ovoj stranici navedeni su izvori, dokumentacija i alati korišteni
                tijekom izrade web aplikacije CarCare.
            </p>
        </section>

        <section class="list-card">
            <h2>Korištene tehnologije</h2>

            <div class="source-list">
                <article class="source-item">
                    <h3>PHP</h3>
                    <p>
                        PHP je korišten za izradu poslužiteljskog dijela aplikacije,
                        obradu zahtjeva, rad sa sesijama i komunikaciju s bazom podataka.
                    </p>
                    <a href="https://www.php.net/docs.php" target="_blank" rel="noopener">
                        PHP dokumentacija
                    </a>
                </article>

                <article class="source-item">
                    <h3>MySQL</h3>
                    <p>
                        MySQL baza podataka korištena je za spremanje korisnika, vozila,
                        servisnih zapisa i podsjetnika.
                    </p>
                    <a href="https://dev.mysql.com/doc/" target="_blank" rel="noopener">
                        MySQL dokumentacija
                    </a>
                </article>

                <article class="source-item">
                    <h3>MDN Web Docs</h3>
                    <p>
                        MDN dokumentacija korištena je kao pomoć za HTML, CSS,
                        JavaScript, Fetch API i rad s DOM elementima.
                    </p>
                    <a href="https://developer.mozilla.org/" target="_blank" rel="noopener">
                        MDN Web Docs
                    </a>
                </article>

                <article class="source-item">
                    <h3>XAMPP</h3>
                    <p>
                        XAMPP je korišten kao lokalno razvojno okruženje za pokretanje
                        Apache poslužitelja i MySQL baze podataka.
                    </p>
                    <a href="https://www.apachefriends.org/" target="_blank" rel="noopener">
                        XAMPP
                    </a>
                </article>

                <article class="source-item">
                    <h3>phpMyAdmin</h3>
                    <p>
                        phpMyAdmin je korišten za izradu, pregled i administraciju
                        baze podataka tijekom razvoja aplikacije.
                    </p>
                    <a href="https://www.phpmyadmin.net/docs/" target="_blank" rel="noopener">
                        phpMyAdmin dokumentacija
                    </a>
                </article>
            </div>
        </section>
        <section class="list-card sources-section">
                <h2>Sigurnosni koncepti</h2>

                <div class="source-list">
                    <article class="source-item">
                        <h3>OWASP Cross Site Scripting Prevention Cheat Sheet</h3>
                        <p>
                            Ovaj izvor korišten je za razumijevanje XSS napada i važnosti
                            sigurnog prikaza korisničkih podataka. U aplikaciji se zato kod
                            dinamičkog prikaza podataka koristi funkcija escapeHtml(), kako bi
                            se smanjio rizik od umetanja zlonamjernog HTML ili JavaScript koda.
                        </p>
                        <a href="https://cheatsheetseries.owasp.org/cheatsheets/Cross_Site_Scripting_Prevention_Cheat_Sheet.html" target="_blank" rel="noopener">
                            OWASP XSS Prevention Cheat Sheet
                        </a>
                    </article>

                    <article class="source-item">
                        <h3>MDN Web Docs — Cross-site scripting (XSS)</h3>
                        <p>
                            MDN objašnjava Cross-site scripting kao sigurnosni napad u kojem
                            napadač pokušava izvršiti zlonamjerni kod unutar web stranice.
                            Izvor je povezan s dijelovima aplikacije gdje se korisnički unosi
                            prikazuju u DOM-u pomoću JavaScripta.
                        </p>
                        <a href="https://developer.mozilla.org/en-US/docs/Web/Security/Attacks/XSS" target="_blank" rel="noopener">
                            MDN Cross-site scripting
                        </a>
                    </article>

                    <article class="source-item">
                        <h3>OWASP SQL Injection Prevention Cheat Sheet</h3>
                        <p>
                            Ovaj izvor korišten je za razumijevanje SQL injection napada i
                            važnosti korištenja pripremljenih SQL upita. U aplikaciji se za
                            rad s bazom koriste PDO prepare() i execute(), čime se korisnički
                            unos ne spaja izravno u SQL naredbe.
                        </p>
                        <a href="https://cheatsheetseries.owasp.org/cheatsheets/SQL_Injection_Prevention_Cheat_Sheet.html" target="_blank" rel="noopener">
                            OWASP SQL Injection Prevention
                        </a>
                    </article>

                    <article class="source-item">
                        <h3>PHP Manual — PDO Prepared Statements</h3>
                        <p>
                            PHP dokumentacija korištena je za rad s pripremljenim upitima.
                            Prepared statements korišteni su u API datotekama za registraciju,
                            prijavu, dodavanje vozila, servisa i podsjetnika.
                        </p>
                        <a href="https://www.php.net/manual/en/pdo.prepared-statements.php" target="_blank" rel="noopener">
                            PHP PDO Prepared Statements
                        </a>
                    </article>

                    <article class="source-item">
                        <h3>PHP Manual — password_hash()</h3>
                        <p>
                            Ovaj izvor korišten je za sigurnije spremanje lozinki. Lozinke se
                            u aplikaciji ne spremaju kao običan tekst, nego se pri registraciji
                            sprema njihov hash, a pri prijavi se provjeravaju funkcijom
                            password_verify().
                        </p>
                        <a href="https://www.php.net/manual/en/function.password-hash.php" target="_blank" rel="noopener">
                            PHP password_hash()
                        </a>
                    </article>
                </div>
            </section>
    </div>
</main>

<?php require_once "includes/footer.php"; ?>