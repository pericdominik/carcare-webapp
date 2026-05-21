<?php

header("Content-Type: application/json; charset=UTF-8");

session_start();

require_once "../../includes/db.php";

if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "Moraš biti prijavljen za brisanje vozila."
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

if ($vehicleId <= 0) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Neispravan ID vozila."
    ]);
    exit;
}

try {
    $stmt = $pdo->prepare("
        DELETE FROM vehicles
        WHERE id = :vehicle_id
        AND user_id = :user_id
    ");

    $stmt->execute([
        "vehicle_id" => $vehicleId,
        "user_id" => $_SESSION["user_id"]
    ]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "Vozilo nije pronađeno ili nemaš dozvolu za brisanje."
        ]);
        exit;
    }

    echo json_encode([
        "success" => true,
        "message" => "Vozilo je uspješno obrisano."
    ]);
    exit;

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Došlo je do pogreške pri brisanju vozila."
    ]);
    exit;
}