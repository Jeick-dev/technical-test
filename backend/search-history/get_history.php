<?php

session_start();

    header("Content-type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Allow-Headers: Content-Type");

    require_once("../database-connection/database.php");

    if ($_SERVER["REQUEST_METHOD"] != "GET") {
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
        exit();
    }

    try {
        $database = new Database();
        $conn = $database->getConnection();
        $currentSessionId = session_id();

        $stmt = $conn->prepare("SELECT id, search_term, searched_at FROM search_history WHERE session_id = :session_id ORDER BY searched_at DESC");
        $stmt->bindParam(":session_id", $currentSessionId, PDO::PARAM_STR);
        $stmt->execute();

        $history = $stmt->fetchAll(PDO::FETCH_ASSOC);

        http_response_code(200);
        echo json_encode([
            "success" => true,
            "history" => $history
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => "Failed to retrieve search history: " . $e->getMessage()]);
    }
