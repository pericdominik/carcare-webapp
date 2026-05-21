<?php

header("Content-Type: application/json; charset=UTF-8");

session_start();

require_once "../../includes/db.php";

if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "Moraš biti prijavljen za pregled vozila."
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
    $stmt = $pdo->prepare("
        SELECT id, brand, model, year, license_plate, mileage, created_at
        FROM vehicles
        WHERE user_id = :user_id
        ORDER BY created_at DESC
    ");

    $stmt->execute([
        "user_id" => $_SESSION["user_id"]
    ]);

    $vehicles = $stmt->fetchAll();

    echo json_encode([
        "success" => true,
        "vehicles" => $vehicles
    ]);
    exit;

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Došlo je do pogreške pri dohvaćanju vozila."
    ]);
    exit;
}