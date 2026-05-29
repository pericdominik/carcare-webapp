<?php

header("Content-Type: application/json; charset=UTF-8");

session_start();

require_once "../../includes/db.php";

if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "Moraš biti prijavljen za brisanje servisnog zapisa."
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

$serviceId = (int)($input["serviceId"] ?? 0);

if ($serviceId <= 0) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Neispravan ID servisnog zapisa."
    ]);
    exit;
}

try {
    $stmt = $pdo->prepare("
        DELETE sr
        FROM service_records sr
        INNER JOIN vehicles v ON sr.vehicle_id = v.id
        WHERE sr.id = :service_id
        AND v.user_id = :user_id
    ");

    $stmt->execute([
        "service_id" => $serviceId,
        "user_id" => $_SESSION["user_id"]
    ]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "Servisni zapis nije pronađen ili nemaš dozvolu za brisanje."
        ]);
        exit;
    }

    echo json_encode([
        "success" => true,
        "message" => "Servisni zapis je uspješno obrisan."
    ]);
    exit;

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Došlo je do pogreške pri brisanju servisnog zapisa."
    ]);
    exit;
}