<?php

header("Content-Type: application/json; charset=UTF-8");

session_start();

require_once "../../includes/db.php";

if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "Moraš biti prijavljen za pregled podsjetnika."
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

$vehicleId = (int)($_GET["vehicle_id"] ?? 0);

if ($vehicleId <= 0) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Nije odabrano ispravno vozilo."
    ]);
    exit;
}

try {
    // Provjera pripada li vozilo prijavljenom korisniku
    $vehicleStmt = $pdo->prepare("
        SELECT id
        FROM vehicles
        WHERE id = :vehicle_id
        AND user_id = :user_id
        LIMIT 1
    ");

    $vehicleStmt->execute([
        "vehicle_id" => $vehicleId,
        "user_id" => $_SESSION["user_id"]
    ]);

    $vehicle = $vehicleStmt->fetch();

    if (!$vehicle) {
        http_response_code(403);
        echo json_encode([
            "success" => false,
            "message" => "Odabrano vozilo nije pronađeno."
        ]);
        exit;
    }

    $stmt = $pdo->prepare("
        SELECT id, vehicle_id, title, reminder_date, description, status, created_at
        FROM reminders
        WHERE vehicle_id = :vehicle_id
        ORDER BY 
            CASE 
                WHEN status = 'active' THEN 0
                ELSE 1
            END,
            reminder_date ASC,
            created_at DESC
    ");

    $stmt->execute([
        "vehicle_id" => $vehicleId
    ]);

    $reminders = $stmt->fetchAll();

    echo json_encode([
        "success" => true,
        "reminders" => $reminders
    ]);
    exit;

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Došlo je do pogreške pri dohvaćanju podsjetnika."
    ]);
    exit;
}