<?php

session_start();

header("Content-type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once("../database-connection/database.php");

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
    exit();
}

$input = json_decode(file_get_contents("php://input"), true);
$searchTerm = isset($input["search_term"]) ? trim($input["search_term"]) : null;

function validateSearchTerm($term)
{
    if (strlen($term) > 500) {
        return false;
    }
    return true;
}

if (empty($searchTerm)) {
    http_response_code(400);
    echo json_encode(["error" => "Search term is required"]);
    exit();
}

if (!validateSearchTerm($searchTerm)) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid search term"]);
    exit();
}


try {
    $database = new Database();
    $conn = $database->getConnection();
    $currentSessionId = session_id();

    $stmt = $conn->prepare("INSERT INTO search_history (search_term, session_id) VALUES (:search_term, :session_id)");
    $stmt->bindParam(":search_term", $searchTerm, PDO::PARAM_STR);
    $stmt->bindParam(":session_id", $currentSessionId, PDO::PARAM_STR);
    $stmt->execute();

    http_response_code(201);
    echo json_encode([
        "success" => true,
        "message" => "Search history saved successfully",
        "id" => $conn->lastInsertId()
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to save search history: " . $e->getMessage()]);
}

