<?php

header("Content-Type: application/json; charset=UTF-8");

session_start();

require_once "../../includes/db.php";

if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "Moraš biti prijavljen za dodavanje podsjetnika."
    ]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Metoda nije dopuštena."
    ]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);

$vehicleId = (int)($input["vehicleId"] ?? 0);
$title = trim($input["title"] ?? "");
$reminderDate = trim($input["reminderDate"] ?? "");
$description = trim($input["description"] ?? "");

if ($vehicleId <= 0 || $title === "" || $reminderDate === "") {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Vozilo, naslov i datum podsjetnika su obvezni."
    ]);
    exit;
}

// Provjera formata datuma YYYY-MM-DD
$dateParts = explode("-", $reminderDate);

if (
    count($dateParts) !== 3 ||
    !checkdate((int)$dateParts[1], (int)$dateParts[2], (int)$dateParts[0])
) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Datum podsjetnika nije ispravan."
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
        INSERT INTO reminders (vehicle_id, title, reminder_date, description, status)
        VALUES (:vehicle_id, :title, :reminder_date, :description, 'active')
    ");

    $stmt->execute([
        "vehicle_id" => $vehicleId,
        "title" => $title,
        "reminder_date" => $reminderDate,
        "description" => $description
    ]);

    echo json_encode([
        "success" => true,
        "message" => "Podsjetnik je uspješno dodan."
    ]);
    exit;

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Došlo je do pogreške pri spremanju podsjetnika."
    ]);
    exit;
}