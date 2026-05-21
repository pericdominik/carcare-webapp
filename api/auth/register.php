<?php

header("Content-Type: application/json; charset=UTF-8");

require_once "../../includes/db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Metoda nije dopuštena."
    ]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);

$name = trim($input["name"] ?? "");
$email = trim($input["email"] ?? "");
$password = $input["password"] ?? "";
$confirmPassword = $input["confirmPassword"] ?? "";

if ($name === "" || $email === "" || $password === "" || $confirmPassword === "") {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Sva polja su obvezna."
    ]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "E-mail adresa nije ispravna."
    ]);
    exit;
}

if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Lozinka mora imati najmanje 6 znakova."
    ]);
    exit;
}

if ($password !== $confirmPassword) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Lozinke se ne podudaraju."
    ]);
    exit;
}

try {
    $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
    $checkStmt->execute([
        "email" => $email
    ]);

    $existingUser = $checkStmt->fetch();

    if ($existingUser) {
        http_response_code(409);
        echo json_encode([
            "success" => false,
            "message" => "Korisnik s ovom e-mail adresom već postoji."
        ]);
        exit;
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $insertStmt = $pdo->prepare("
        INSERT INTO users (name, email, password_hash)
        VALUES (:name, :email, :password_hash)
    ");

    $insertStmt->execute([
        "name" => $name,
        "email" => $email,
        "password_hash" => $passwordHash
    ]);

    http_response_code(201);
    echo json_encode([
        "success" => true,
        "message" => "Registracija je uspješna. Sada se možeš prijaviti."
    ]);
    exit;

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Došlo je do pogreške pri registraciji."
    ]);
    exit;
}