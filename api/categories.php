<?php
require_once __DIR__ . '/../config.php';

setCORS();
 $method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $slug = isset($_GET['slug']) ? sanitize($_GET['slug']) : null;
        $endpoint = '/categories?order=id.asc';
        
        if ($slug) {
            $endpoint .= '&slug=eq.' . $slug;
        }
        
        $result = supabaseRequest('GET', $endpoint);
        
        if ($slug && empty($result)) {
            errorResponse('Category not found', 404);
        }
        
        jsonResponse($slug ? $result[0] ?? $result : $result);
        break;

    case 'POST':
        $input = getJSONInput();
        $result = supabaseRequest('POST', '/categories', $input);
        jsonResponse($result, 201);
        break;

    case 'PUT':
        $slug = isset($_GET['slug']) ? sanitize($_GET['slug']) : null;
        if (!$slug) errorResponse('Category slug required');
        $input = getJSONInput();
        $result = supabaseRequest('PATCH', '/categories?slug=eq.' . $slug, $input);
        jsonResponse($result);
        break;

    case 'DELETE':
        $slug = isset($_GET['slug']) ? sanitize($_GET['slug']) : null;
        if (!$slug) errorResponse('Category slug required');
        supabaseRequest('DELETE', '/categories?slug=eq.' . $slug);
        jsonResponse(['message' => 'Category deleted']);
        break;

    default:
        errorResponse('Method not allowed', 405);
}