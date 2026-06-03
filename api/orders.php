<?php
require_once __DIR__ . '/../config.php';

setCORS();
 $method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $id = isset($_GET['id']) ? (int) $_GET['id'] : null;
        
        if ($id) {
            $order = supabaseRequest('GET', '/orders?id=eq.' . $id);
            if (empty($order)) errorResponse('Order not found', 404);
            
            $items = supabaseRequest('GET', '/order_items?order_id=eq.' . $id);
            $order[0]['items'] = $items;
            jsonResponse($order[0]);
        }
        
        // Fetch ALL orders
        $orders = supabaseRequest('GET', '/orders?order=created_at.desc');
        
        if (!empty($orders)) {
            // Extract all order IDs into a comma-separated string: 1,2,3
            $orderIds = array_column($orders, 'id');
            $idString = implode(',', $orderIds);
            
            // Fetch ALL items for ALL orders in ONE single request
            $allItems = supabaseRequest('GET', '/order_items?order_id=in.(' . $idString . ')&order=id.asc');
            
            // Group items by their order_id
            $itemsByOrder = [];
            foreach ($allItems as $item) {
                $itemsByOrder[$item['order_id']][] = $item;
            }
            
            // Attach the grouped items back to their respective orders
            foreach ($orders as &$order) {
                $order['items'] = $itemsByOrder[$order['id']] ?? [];
            }
        }
        
        jsonResponse($orders);
        break;

    case 'POST':
        $input = getJSONInput();
        
        $address = $input['delivery_address'] ?? null;
        $items   = $input['items'] ?? [];
        
        if (!$address || empty($items)) {
            errorResponse('delivery_address and items[] are required');
        }

        // Calculate total
        $total = 0;
        foreach ($items as $item) {
            $total += (int)($item['price'] ?? 0) * (int)($item['quantity'] ?? 1);
        }

        // 1. Insert Order
        $orderData = [
            'total_amount'     => $total,
            'delivery_address' => sanitize($address),
            'customer_name'    => isset($input['customer_name']) ? sanitize($input['customer_name']) : null,
            'customer_email'   => isset($input['customer_email']) ? sanitize($input['customer_email']) : null,
            'customer_phone'   => isset($input['customer_phone']) ? sanitize($input['customer_phone']) : null,
        ];

        $createdOrder = supabaseRequest('POST', '/orders', $orderData);
        
        if (empty($createdOrder)) {
            errorResponse('Failed to create order', 500);
        }

        $orderId = $createdOrder[0]['id'];

        // 2. Insert Order Items
        $orderItems = [];
        foreach ($items as $item) {
            $itemData = [
                'order_id'     => $orderId,
                'product_id'   => isset($item['product_id']) ? (int)$item['product_id'] : null,
                'product_name' => sanitize($item['product_name'] ?? ''),
                'price'        => (int)($item['price'] ?? 0),
                'quantity'     => (int)($item['quantity'] ?? 1),
                'image_url'    => $item['image_url'] ?? null,
            ];
            $createdItem = supabaseRequest('POST', '/order_items', $itemData);
            $orderItems[] = $createdItem[0];
        }

        $createdOrder[0]['items'] = $orderItems;
        jsonResponse($createdOrder[0], 201);
        break;

    case 'PATCH':
        $id = isset($_GET['id']) ? (int) $_GET['id'] : null;
        if (!$id) errorResponse('Order ID required');
        
        $input = getJSONInput();
        $updateData = [];
        if (isset($input['order_status'])) $updateData['order_status'] = sanitize($input['order_status']);
        if (isset($input['payment_status'])) $updateData['payment_status'] = sanitize($input['payment_status']);
        
        if (empty($updateData)) errorResponse('No fields to update');
        
        $result = supabaseRequest('PATCH', '/orders?id=eq.' . $id, $updateData);
        if (empty($result)) errorResponse('Order not found', 404);
        
        jsonResponse($result[0]);
        break;

    default:
        errorResponse('Method not allowed', 405);
}