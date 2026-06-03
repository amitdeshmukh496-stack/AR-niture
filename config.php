<?php


define('SUPABASE_URL', 'https://tzdxqpxvifhvssfxxjzo.supabase.co'); 
define('SUPABASE_SERVICE_KEY', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InR6ZHhxcHh2aWZodnNzZnh4anpvIiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTc4MDQ2MDIwNywiZXhwIjoyMDk2MDM2MjA3fQ.j-8dNlLud6hXMgtwUux57d6zTGvEn4rshNdPOmMXjw8');           // <-- PASTE YOUR SERVICE ROLE KEY


function supabaseRequest(string $method, string $endpoint, ?array $data = null) {
    $url = SUPABASE_URL . '/rest/v1' . $endpoint;

    $headers = [
        'apikey: ' . SUPABASE_SERVICE_KEY,
        'Authorization: Bearer ' . SUPABASE_SERVICE_KEY,
        'Content-Type: application/json',
        'Prefer: return=representation',
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

    if ($data !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error    = curl_error($ch);
    curl_close($ch);

    if ($error) {
        errorResponse('cURL Error: ' . $error, 500);
    }

    $decoded = json_decode($response, true);

    if (isset($decoded['error']) || isset($decoded['code']) || isset($decoded['message'])) {
        $msg = $decoded['message'] ?? $decoded['msg'] ?? json_encode($decoded);
        $code = $decoded['status'] ?? $httpCode;
        errorResponse('Supabase: ' . $msg, $code >= 400 ? $code : 400);
    }

    if ($httpCode >= 400 && $decoded === null) {
        errorResponse('HTTP Error ' . $httpCode . ': ' . substr($response, 0, 200), $httpCode);
    }

    return $decoded;
}


function jsonResponse(array $data, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    exit;
}

function errorResponse(string $message, int $code = 400): void {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => true, 'message' => $message]);
    exit;
}


function setCORS(): void {
    $allowed = ['http://localhost:3000', 'http://localhost:8000', 'http://127.0.0.1:5500', 'null'];
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    if (in_array($origin, $allowed) || $origin === '') {
        header("Access-Control-Allow-Origin: " . ($origin ?: '*'));
    }
    header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, apikey');
    header('Access-Control-Max-Age: 86400');

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}

function sanitize(string $input): string {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function getJSONInput(): array {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'admin123'); // Change this to a strong password!