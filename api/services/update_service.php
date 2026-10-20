<?php

header("Content-Type: application/json; charset=UTF-8");

session_start();

require_once "../../includes/db.php";

if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "Moraš biti prijavljen za uređivanje servisa."
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
$serviceType = trim($input["serviceType"] ?? "");
$serviceDate = trim($input["serviceDate"] ?? "");
$mileageAtService = (int)($input["mileageAtService"] ?? 0);
$cost = (float)($input["cost"] ?? 0);
$description = trim($input["description"] ?? "");

if (
    $serviceId <= 0 ||
    $serviceType === "" ||
    $serviceDate === "" ||
    $mileageAtService < 0 ||
    $cost < 0
) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Vrsta servisa, datum, kilometraža i cijena su obvezni."
    ]);
    exit;
}

$dateParts = explode("-", $serviceDate);

if (
    count($dateParts) !== 3 ||
    !checkdate(
        (int)$dateParts[1],
        (int)$dateParts[2],
        (int)$dateParts[0]
    )
) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Datum servisa nije ispravan."
    ]);
    exit;
}

try {
    $pdo->beginTransaction();

    $serviceStmt = $pdo->prepare("
        SELECT sr.vehicle_id
        FROM service_records sr
        INNER JOIN vehicles v ON sr.vehicle_id = v.id
        WHERE sr.id = :service_id
        AND v.user_id = :user_id
    ");

    $serviceStmt->execute([
        "service_id" => $serviceId,
        "user_id" => $_SESSION["user_id"]
    ]);

    $service = $serviceStmt->fetch();

    if (!$service) {
        $pdo->rollBack();

        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "Servis nije pronađen ili nemaš dozvolu za uređivanje."
        ]);
        exit;
    }

    $vehicleId = (int)$service["vehicle_id"];

    $vehicleStmt = $pdo->prepare("
        SELECT id, mileage
        FROM vehicles
        WHERE id = :vehicle_id
        AND user_id = :user_id
        FOR UPDATE
    ");

    $vehicleStmt->execute([
        "vehicle_id" => $vehicleId,
        "user_id" => $_SESSION["user_id"]
    ]);

    $vehicle = $vehicleStmt->fetch();

    if (!$vehicle) {
        $pdo->rollBack();

        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "Vozilo nije pronađeno."
        ]);
        exit;
    }

    $checkStmt = $pdo->prepare("
        SELECT id
        FROM service_records
        WHERE id = :service_id
        AND vehicle_id = :vehicle_id
        FOR UPDATE
    ");

    $checkStmt->execute([
        "service_id" => $serviceId,
        "vehicle_id" => $vehicleId
    ]);

    if (!$checkStmt->fetch()) {
        $pdo->rollBack();

        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "Servis više nije dostupan."
        ]);
        exit;
    }

    $historyStmt = $pdo->prepare("
        SELECT service_date, mileage_at_service
        FROM service_records
        WHERE vehicle_id = :vehicle_id
        AND id <> :service_id
        FOR UPDATE
    ");

    $historyStmt->execute([
        "vehicle_id" => $vehicleId,
        "service_id" => $serviceId
    ]);

    $earlierMileage = null;
    $laterMileage = null;

    foreach ($historyStmt->fetchAll() as $record) {
        $recordMileage = (int)$record["mileage_at_service"];

        if ($record["service_date"] < $serviceDate) {
            $earlierMileage = $earlierMileage === null
                ? $recordMileage
                : max($earlierMileage, $recordMileage);
        } elseif ($record["service_date"] > $serviceDate) {
            $laterMileage = $laterMileage === null
                ? $recordMileage
                : min($laterMileage, $recordMileage);
        }
    }

    if (
        $earlierMileage !== null &&
        $mileageAtService < $earlierMileage
    ) {
        $pdo->rollBack();

        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Kilometraža ne može biti manja od " .
                number_format($earlierMileage, 0, ",", ".") .
                " km jer postoji raniji servis s tom kilometražom."
        ]);
        exit;
    }

    if (
        $laterMileage !== null &&
        $mileageAtService > $laterMileage
    ) {
        $pdo->rollBack();

        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Kilometraža ne može biti veća od " .
                number_format($laterMileage, 0, ",", ".") .
                " km jer postoji kasniji servis s tom kilometražom."
        ]);
        exit;
    }

    $stmt = $pdo->prepare("
        UPDATE service_records
        SET
            service_type = :service_type,
            service_date = :service_date,
            mileage_at_service = :mileage_at_service,
            cost = :cost,
            description = :description
        WHERE id = :service_id
        AND vehicle_id = :vehicle_id
    ");

    $stmt->execute([
        "service_type" => $serviceType,
        "service_date" => $serviceDate,
        "mileage_at_service" => $mileageAtService,
        "cost" => $cost,
        "description" => $description,
        "service_id" => $serviceId,
        "vehicle_id" => $vehicleId
    ]);

    if ($mileageAtService > (int)$vehicle["mileage"]) {
        $updateMileageStmt = $pdo->prepare("
            UPDATE vehicles
            SET mileage = :mileage
            WHERE id = :vehicle_id
            AND user_id = :user_id
        ");

        $updateMileageStmt->execute([
            "mileage" => $mileageAtService,
            "vehicle_id" => $vehicleId,
            "user_id" => $_SESSION["user_id"]
        ]);
    }

    $pdo->commit();

    echo json_encode([
        "success" => true,
        "message" => "Servisni zapis je uspješno ažuriran."
    ]);
    exit;

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Došlo je do pogreške pri uređivanju servisa."
    ]);
    exit;
}