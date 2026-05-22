<?php

header("Content-Type: application/json; charset=UTF-8");

session_start();

require_once "../../includes/db.php";

if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "Moraš biti prijavljen za dodavanje servisa."
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
$serviceType = trim($input["serviceType"] ?? "");
$serviceDate = trim($input["serviceDate"] ?? "");
$mileageAtService = (int)($input["mileageAtService"] ?? 0);
$cost = (float)($input["cost"] ?? 0);
$description = trim($input["description"] ?? "");

if ($vehicleId <= 0 || $serviceType === "" || $serviceDate === "" || $mileageAtService < 0 || $cost < 0) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Vozilo, vrsta servisa, datum, kilometraža i cijena su obvezni."
    ]);
    exit;
}

$dateParts = explode("-", $serviceDate);

if (
    count($dateParts) !== 3 ||
    !checkdate((int)$dateParts[1], (int)$dateParts[2], (int)$dateParts[0])
) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Datum servisa nije ispravan."
    ]);
    exit;
}

try {
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
        INSERT INTO service_records
            (vehicle_id, service_type, service_date, mileage_at_service, cost, description)
        VALUES
            (:vehicle_id, :service_type, :service_date, :mileage_at_service, :cost, :description)
    ");

    $stmt->execute([
        "vehicle_id" => $vehicleId,
        "service_type" => $serviceType,
        "service_date" => $serviceDate,
        "mileage_at_service" => $mileageAtService,
        "cost" => $cost,
        "description" => $description
    ]);

    echo json_encode([
        "success" => true,
        "message" => "Servisni zapis je uspješno dodan."
    ]);
    exit;

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Došlo je do pogreške pri spremanju servisa."
    ]);
    exit;
}