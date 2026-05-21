<?php
require_once "includes/auth_check.php";
require_once "includes/header.php";
?>

<main>
    <div class="container">
        <section class="hero">
            <div class="hero-content">
                <h1>Dobrodošao, <?php echo htmlspecialchars($_SESSION["user_name"]); ?>!</h1>
                <p>
                    Prijava je uspješna. U sljedećim koracima ovdje ćemo napraviti
                    pravu nadzornu ploču sa statistikama vozila, servisa i podsjetnika.
                </p>
            </div>
        </section>
    </div>
</main>

<?php require_once "includes/footer.php"; ?>