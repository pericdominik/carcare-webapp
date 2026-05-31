<?php

header("Content-Type: application/json; charset=UTF-8");

session_start();

require_once "../../includes/db.php";

if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "Moraš biti prijavljen za brisanje podsjetnika."
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

$reminderId = (int)($input["reminderId"] ?? 0);

if ($reminderId <= 0) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Neispravan ID podsjetnika."
    ]);
    exit;
}

try {
    $stmt = $pdo->prepare("
        DELETE r
        FROM reminders r
        INNER JOIN vehicles v ON r.vehicle_id = v.id
        WHERE r.id = :reminder_id
        AND v.user_id = :user_id
    ");

    $stmt->execute([
        "reminder_id" => $reminderId,
        "user_id" => $_SESSION["user_id"]
    ]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "Podsjetnik nije pronađen ili nemaš dozvolu za brisanje."
        ]);
        exit;
    }

    echo json_encode([
        "success" => true,
        "message" => "Podsjetnik je uspješno obrisan."
    ]);
    exit;

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Došlo je do pogreške pri brisanju podsjetnika."
    ]);
    exit;
} 