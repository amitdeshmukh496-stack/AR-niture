<?php
require_once __DIR__ . '/../config.php';

setCORS();
 $method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $id       = isset($_GET['id']) ? (int) $_GET['id'] : null;
        $category = isset($_GET['category']) ? sanitize($_GET['category']) : null;
        $search   = isset($_GET['search']) ? sanitize($_GET['search']) : null;

        // Build the Supabase endpoint
        // Try with join first
        try {
            $endpoint = '/products?select=*,categories(id,name,slug,icon)&order=created_at.desc';

            if ($id) {
                $endpoint .= '&id=eq.' . $id;
            }
            if ($search) {
                $endpoint .= '&or=(name.ilike.*' . urlencode($search) . '*,description.ilike.*' . urlencode($search) . '*)';
            }

            $products = supabaseRequest('GET', $endpoint);

        } catch (Exception $e) {
            // Fallback: fetch without join, then attach categories manually
            $endpoint = '/products?select=*&order=created_at.desc';

            if ($id) {
                $endpoint .= '&id=eq.' . $id;
            }
            if ($search) {
                $endpoint .= '&or=(name.ilike.*' . urlencode($search) . '*,description.ilike.*' . urlencode($search) . '*)';
            }

            $products = supabaseRequest('GET', $endpoint);

            // Fetch all categories and map them
            $categories = supabaseRequest('GET', '/categories?select=id,name,slug,icon');
            $catMap = [];
            foreach ($categories as $c) {
                $catMap[$c['id']] = $c;
            }

            // Attach category data to each product
            foreach ($products as &$p) {
                $catId = $p['category_id'] ?? null;
                if ($catId && isset($catMap[$catId])) {
                    $p['categories'] = $catMap[$catId];
                } else {
                    $p['categories'] = null;
                }
            }
        }

        // Filter by category slug (do this in PHP since PostgREST 
        // cross-table filters can be tricky)
        if ($category && !empty($products)) {
            $products = array_filter($products, function($p) use ($category) {
                $catSlug = $p['categories']['slug'] ?? null;
                return $catSlug === $category;
            });
            $products = array_values($products); // Re-index array
        }

        if ($id && empty($products)) {
            errorResponse('Product not found', 404);
        }

        jsonResponse($id ? ($products[0] ?? $products) : $products);
        break;

    case 'POST':
        $input = getJSONInput();
        $result = supabaseRequest('POST', '/products', $input);
        jsonResponse($result, 201);
        break;

    case 'PUT':
        $id = isset($_GET['id']) ? (int) $_GET['id'] : null;
        if (!$id) errorResponse('Product ID required');
        $input = getJSONInput();
        $result = supabaseRequest('PATCH', '/products?id=eq.' . $id, $input);
        jsonResponse($result);
        break;

    case 'DELETE':
        $id = isset($_GET['id']) ? (int) $_GET['id'] : null;
        if (!$id) errorResponse('Product ID required');
        supabaseRequest('DELETE', '/products?id=eq.' . $id);
        jsonResponse(['message' => 'Product deleted', 'id' => $id]);
        break;

    default:
        errorResponse('Method not allowed', 405);
}