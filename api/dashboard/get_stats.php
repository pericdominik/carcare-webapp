<?php

header("Content-Type: application/json; charset=UTF-8");

session_start();

require_once "../../includes/db.php";

if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "Moraš biti prijavljen za pregled statistike."
    ]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Metoda nije dopuštena."
    ]);
    exit;
}

try {
    $userId = $_SESSION["user_id"];

    $vehiclesStmt = $pdo->prepare("
        SELECT COUNT(*) AS total_vehicles
        FROM vehicles
        WHERE user_id = :user_id
    ");

    $vehiclesStmt->execute([
        "user_id" => $userId
    ]);

    $vehiclesCount = $vehiclesStmt->fetch();

    $servicesStmt = $pdo->prepare("
        SELECT 
            COUNT(sr.id) AS total_services,
            COALESCE(SUM(sr.cost), 0) AS total_service_cost
        FROM service_records sr
        INNER JOIN vehicles v ON sr.vehicle_id = v.id
        WHERE v.user_id = :user_id
    ");

    $servicesStmt->execute([
        "user_id" => $userId
    ]);

    $servicesStats = $servicesStmt->fetch();

    $remindersStmt = $pdo->prepare("
        SELECT COUNT(r.id) AS active_reminders
        FROM reminders r
        INNER JOIN vehicles v ON r.vehicle_id = v.id
        WHERE v.user_id = :user_id
        AND r.status = 'active'
    ");

    $remindersStmt->execute([
        "user_id" => $userId
    ]);

    $remindersStats = $remindersStmt->fetch();

    $upcomingStmt = $pdo->prepare("
        SELECT 
            r.id,
            r.title,
            r.reminder_date,
            r.description,
            v.brand,
            v.model
        FROM reminders r
        INNER JOIN vehicles v ON r.vehicle_id = v.id
        WHERE v.user_id = :user_id
        AND r.status = 'active'
        ORDER BY r.reminder_date ASC
        LIMIT 5
    ");

    $upcomingStmt->execute([
        "user_id" => $userId
    ]);

    $upcomingReminders = $upcomingStmt->fetchAll();

    echo json_encode([
        "success" => true,
        "stats" => [
            "totalVehicles" => (int)$vehiclesCount["total_vehicles"],
            "totalServices" => (int)$servicesStats["total_services"],
            "totalServiceCost" => (float)$servicesStats["total_service_cost"],
            "activeReminders" => (int)$remindersStats["active_reminders"]
        ],
        "upcomingReminders" => $upcomingReminders
    ]);
    exit;

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Došlo je do pogreške pri dohvaćanju statistike."
    ]);
    exit;
}