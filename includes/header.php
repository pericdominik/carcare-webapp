<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION["user_id"]);
$userName = $_SESSION["user_name"] ?? "";
?>

<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarCare</title>
    <link rel="stylesheet" href="/carcare/assets/css/style.css">
</head>
<body>

<header class="site-header">
    <div class="container header-content">
        <a href="/carcare/index.php" class="logo">CarCare</a>

        <nav class="main-nav" aria-label="Glavna navigacija">
            <ul>
                <li><a href="/carcare/index.php">Početna</a></li>

                <?php if ($isLoggedIn): ?>
                    <li><a href="/carcare/dashboard.php">Nadzorna ploča</a></li>
                    <li><a href="/carcare/vehicles.php">Moja vozila</a></li>
                    <li><a href="/carcare/services.php">Servisi</a></li>
                    <li><a href="/carcare/reminders.php">Podsjetnici</a></li>
                    <li><a href="/carcare/sources.php">Izvori</a></li>
                    <li><a href="/carcare/logout.php">Odjava</a></li>
                <?php else: ?>
                    <li><a href="/carcare/register.php">Registracija</a></li>
                    <li><a href="/carcare/login.php">Prijava</a></li>
                    <li><a href="/carcare/sources.php">Izvori</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>