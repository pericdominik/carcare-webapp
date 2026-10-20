<?php

header("Content-Type: application/json; charset=UTF-8");

session_start();

require_once "../../includes/db.php";

if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "Moraš biti prijavljen za uređivanje vozila."
    ]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    header("Allow: POST");

    echo json_encode([
        "success" => false,
        "message" => "Metoda nije dopuštena."
    ]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);

if (!is_array($input)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Neispravni podaci zahtjeva."
    ]);
    exit;
}

$brand = is_string($input["brand"] ?? null)
    ? trim($input["brand"])
    : "";

$model = is_string($input["model"] ?? null)
    ? trim($input["model"])
    : "";

$licensePlate = is_string($input["licensePlate"] ?? null)
    ? trim($input["licensePlate"])
    : "";

$currentYear = (int)date("Y");

$vehicleId = filter_var(
    $input["vehicleId"] ?? null,
    FILTER_VALIDATE_INT,
    ["options" => ["min_range" => 1]]
);

$year = filter_var(
    $input["year"] ?? null,
    FILTER_VALIDATE_INT,
    ["options" => [
        "min_range" => 1950,
        "max_range" => $currentYear + 1
    ]]
);

$mileage = filter_var(
    $input["mileage"] ?? null,
    FILTER_VALIDATE_INT,
    ["options" => [
        "min_range" => 0,
        "max_range" => 2147483647
    ]]
);

if (
    $vehicleId === false ||
    $brand === "" ||
    $model === "" ||
    $year === false ||
    $mileage === false
) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Provjeri podatke. Godina i kilometraža moraju biti cijeli brojevi u dopuštenom rasponu."
    ]);
    exit;
}

try {
    $userId = $_SESSION["user_id"];

    $checkStmt = $pdo->prepare("
        SELECT id
        FROM vehicles
        WHERE id = :vehicle_id
        AND user_id = :user_id
    ");

    $checkStmt->execute([
        "vehicle_id" => $vehicleId,
        "user_id" => $userId
    ]);

    if (!$checkStmt->fetch()) {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "Vozilo nije pronađeno ili nemaš dozvolu za uređivanje."
        ]);
        exit;
    }

    $stmt = $pdo->prepare("
        UPDATE vehicles
        SET
            brand = :brand,
            model = :model,
            year = :year,
            license_plate = :license_plate,
            mileage = :mileage
        WHERE id = :vehicle_id
        AND user_id = :user_id
    ");

    $stmt->execute([
        "brand" => $brand,
        "model" => $model,
        "year" => $year,
        "license_plate" => $licensePlate,
        "mileage" => $mileage,
        "vehicle_id" => $vehicleId,
        "user_id" => $userId
    ]);

    echo json_encode([
        "success" => true,
        "message" => "Podaci vozila su uspješno spremljeni."
    ]);
    exit;

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Došlo je do pogreške pri uređivanju vozila."
    ]);
    exit;
}