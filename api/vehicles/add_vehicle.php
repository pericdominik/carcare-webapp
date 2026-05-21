<?php

header("Content-Type: application/json; charset=UTF-8");

session_start();

require_once "../../includes/db.php";

if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "Moraš biti prijavljen za dodavanje vozila."
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

$brand = trim($input["brand"] ?? "");
$model = trim($input["model"] ?? "");
$year = (int)($input["year"] ?? 0);
$licensePlate = trim($input["licensePlate"] ?? "");
$mileage = (int)($input["mileage"] ?? 0);

$currentYear = (int)date("Y");

if ($brand === "" || $model === "" || $year === 0 || $mileage < 0) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Marka, model, godina i kilometraža su obvezni."
    ]);
    exit;
}

if ($year < 1950 || $year > $currentYear + 1) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Godina proizvodnje nije u dopuštenom rasponu."
    ]);
    exit;
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO vehicles (user_id, brand, model, year, license_plate, mileage)
        VALUES (:user_id, :brand, :model, :year, :license_plate, :mileage)
    ");

    $stmt->execute([
        "user_id" => $_SESSION["user_id"],
        "brand" => $brand,
        "model" => $model,
        "year" => $year,
        "license_plate" => $licensePlate,
        "mileage" => $mileage
    ]);

    echo json_encode([
        "success" => true,
        "message" => "Vozilo je uspješno dodano."
    ]);
    exit;

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Došlo je do pogreške pri spremanju vozila."
    ]);
    exit;
}