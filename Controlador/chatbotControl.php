<?php
header('Content-Type: application/json');

require_once '../Modelo/chatbotMdl.php';

$message = $_POST['message'] ?? '';

$chatService = new ChatService();
$response = $chatService->processMessage($message);

echo json_encode([
    "response" => $response
]);

exit;
