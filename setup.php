<?php
// Run this once to verify your Supabase REST API connection works
// Visit: http://localhost/setup.php

require_once __DIR__ . '/config.php';

header('Content-Type: text/html; charset=utf-8');

echo '<h2>AR-nature — Supabase API Connection Test</h2>';
echo '<p>Using REST API (No PDO driver needed)</p><hr>';

try {
    // Test 1: Fetch Categories
    $categories = supabaseRequest('GET', '/categories?select=id,name');
    $catCount = count($categories);
    echo "<p>✅ <strong>Connected to Supabase!</strong></p>";
    echo "<p>✅ Categories table: <strong>{$catCount}</strong> rows found</p>";

    // Test 2: Fetch Products
    $products = supabaseRequest('GET', '/products?select=id,name,price,categories(name)');
    $prodCount = count($products);
    echo "<p>✅ Products table: <strong>{$prodCount}</strong> rows found</p>";

    // Test 3: Check Orders table exists
    $orders = supabaseRequest('GET', '/orders?select=id&limit=1');
    echo "<p>✅ Orders table: <strong>Accessible</strong></p>";

    // Print a sample of the products fetched from the database
    if (!empty($products)) {
        echo '<h3>Sample Products from Database:</h3><pre style="background:#f4f4f4; padding:15px; border-radius:8px; overflow-x:auto;">';
        print_r($products);
        echo '</pre>';
    }

    echo '<p style="color:green;font-weight:bold; font-size:1.2rem;">🎉 All checks passed! Your backend is ready.</p>';
    echo '<p style="color:#888;">(Delete setup.php for security before going live)</p>';

} catch (Exception $e) {
    echo '<p style="color:red;font-weight:bold;">❌ Connection Failed: ' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<h4>Troubleshooting:</h4>';
    echo '<ol>';
    echo '<li>Make sure you pasted your <strong>SUPABASE_URL</strong> and <strong>SUPABASE_SERVICE_KEY</strong> into <code>config.php</code>.</li>';
    echo '<li>Ensure you ran the <strong>SQL setup code</strong> in the Supabase SQL Editor (Step 1 from previous message).</li>';
    echo '<li>Check that your Supabase project is not paused (free tiers pause after inactivity).</li>';
    echo '</ol>';
}
?>