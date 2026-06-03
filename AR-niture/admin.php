<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AR-nature | Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f6f9; display: flex; min-height: 100vh; }
        .sidebar { width: 260px; background: #2C2C2C; color: white; padding: 20px 0; position: fixed; height: 100vh; overflow-y: auto; z-index: 1000; display: flex; flex-direction: column; }
        .sidebar-brand { text-align: center; padding: 10px 20px 30px; border-bottom: 1px solid rgba(255,255,255,0.1); font-size: 1.4rem; font-weight: 700; }
        .sidebar-brand span { color: #FF6B35; }
        .sidebar-menu { margin-top: 20px; flex-grow: 1; }
        .menu-item { display: flex; align-items: center; padding: 12px 25px; color: rgba(255,255,255,0.7); text-decoration: none; transition: 0.2s; cursor: pointer; }
        .menu-item:hover, .menu-item.active { background: rgba(255,255,255,0.1); color: white; border-left: 4px solid #FF6B35; }
        .menu-item i { margin-right: 15px; font-size: 1.1rem; width: 20px; text-align: center; }
        .sidebar-footer { border-top: 1px solid rgba(255,255,255,0.1); padding-top: 10px; }
        .logout-item { color: #ef4444 !important; }
        .logout-item:hover { background: rgba(239, 68, 68, 0.1) !important; border-left-color: #ef4444 !important; color: #ef4444 !important; }
        
        .main-content { margin-left: 260px; flex: 1; padding: 30px; width: calc(100% - 260px); }
        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .top-bar h2 { font-weight: 700; color: #2C2C2C; margin: 0; }
        .stat-card { background: white; border-radius: 15px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 20px; transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-icon { width: 60px; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: white; }
        .bg-revenue { background: linear-gradient(135deg, #FF6B35, #ff9a76); }
        .bg-orders { background: linear-gradient(135deg, #4facfe, #00f2fe); }
        .bg-pending { background: linear-gradient(135deg, #f093fb, #f5576c); }
        .bg-delivered { background: linear-gradient(135deg, #43e97b, #38f9d7); }
        .stat-info h3 { font-size: 1.8rem; font-weight: 700; margin: 0; }
        .stat-info p { margin: 0; color: #888; font-size: 0.9rem; }
        .data-card { background: white; border-radius: 15px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .data-card h5 { font-weight: 700; margin-bottom: 20px; }
        .badge-pending { background: #fff3cd; color: #856404; }
        .badge-delivered { background: #d4edda; color: #155724; }
        .badge-completed { background: #d1ecf1; color: #0c5460; }
        .item-img { width: 50px; height: 50px; object-fit: cover; border-radius: 8px; }
        @media (max-width: 991px) { .sidebar { margin-left: -260px; } .sidebar.active { margin-left: 0; } .main-content { margin-left: 0; width: 100%; } }
    </style>
</head>
<body>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">AR-<span>nature</span></div>
        <div class="sidebar-menu">
            <a href="index.html" class="menu-item"><i class="fas fa-store"></i> Store Front</a>
            <div class="menu-item active"><i class="fas fa-tachometer-alt"></i> Dashboard</div>
            <a href="order.html" class="menu-item"><i class="fas fa-box"></i> Orders</a>
            <a href="product.html" class="menu-item"><i class="fas fa-couch"></i> Products</a>
        </div>

        <div class="sidebar-footer">
            <a href="logout.php" class="menu-item logout-item"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <div class="main-content">
        <div class="top-bar">
            <h2>Dashboard</h2>
            <button class="btn btn-outline-dark d-lg-none" onclick="document.getElementById('sidebar').classList.toggle('active')"><i class="fas fa-bars"></i></button>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-xl-3 col-md-6"><div class="stat-card"><div class="stat-icon bg-revenue"><i class="fas fa-rupee-sign"></i></div><div class="stat-info"><h3 id="totalRevenue">₹0</h3><p>Total Revenue</p></div></div></div>
            <div class="col-xl-3 col-md-6"><div class="stat-card"><div class="stat-icon bg-orders"><i class="fas fa-shopping-bag"></i></div><div class="stat-info"><h3 id="totalOrders">0</h3><p>Total Orders</p></div></div></div>
            <div class="col-xl-3 col-md-6"><div class="stat-card"><div class="stat-icon bg-pending"><i class="fas fa-clock"></i></div><div class="stat-info"><h3 id="pendingOrders">0</h3><p>Pending Orders</p></div></div></div>
            <div class="col-xl-3 col-md-6"><div class="stat-card"><div class="stat-icon bg-delivered"><i class="fas fa-check-circle"></i></div><div class="stat-info"><h3 id="deliveredOrders">0</h3><p>Delivered</p></div></div></div>
        </div>

        <div class="data-card" id="ordersSection">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="m-0"><i class="fas fa-list-ul me-2"></i>Recent Orders</h5>
                <button class="btn btn-sm btn-outline-dark" onclick="fetchOrders()"><i class="fas fa-sync-alt me-1"></i> Refresh</button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead><tr><th>Order ID</th><th>Customer</th><th>Contact</th><th>Items</th><th>Total</th><th>Status</th><th>Date</th><th>Action</th></tr></thead>
                    <tbody id="ordersTableBody"><tr><td colspan="8" class="text-center py-4 text-muted">Loading orders...</td></tr></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="orderDetailModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0"><h5 class="modal-title fw-bold">Order Details</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-md-6"><h6 class="fw-bold text-muted mb-2">CUSTOMER INFO</h6><p class="mb-1"><strong>Name:</strong> <span id="detailName">-</span></p><p class="mb-1"><strong>Email:</strong> <span id="detailEmail">-</span></p><p class="mb-1"><strong>Phone:</strong> <span id="detailPhone">-</span></p><p class="mb-0"><strong>Address:</strong> <span id="detailAddress">-</span></p></div>
                        <div class="col-md-6 text-md-end mt-3 mt-md-0"><h6 class="fw-bold text-muted mb-2">ORDER INFO</h6><p class="mb-1"><strong>Order ID:</strong> <span id="detailOrderId" class="text-danger fw-bold">#-</span></p><p class="mb-1"><strong>Date:</strong> <span id="detailDate">-</span></p><p class="mb-0"><strong>Status:</strong> <span id="detailStatus" class="badge">-</span></p></div>
                    </div>
                    <hr>
                    <h6 class="fw-bold text-muted mb-3">ORDER ITEMS</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light"><tr><th>Product</th><th>Price</th><th>Qty</th><th class="text-end">Total</th></tr></thead>
                            <tbody id="orderItemsTableBody"></tbody>
                            <tfoot><tr><td colspan="3" class="text-end fw-bold">Grand Total</td><td class="text-end fw-bold text-danger fs-5" id="detailGrandTotal">₹0</td></tr></tfoot>
                        </table>
                    </div>
                </div>
                <div class="modal-footer border-0"><button class="btn btn-outline-dark" data-bs-dismiss="modal">Close</button></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const API_BASE = './api';
        let allOrders = [];

        document.addEventListener('DOMContentLoaded', fetchOrders);

        async function fetchOrders() {
            const tbody = document.getElementById('ordersTableBody');
            tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-muted">Loading...</td></tr>';

            try {
                const res = await fetch(`${API_BASE}/orders.php`);
                const rawText = await res.text();
                
                let data;
                try {
                    data = JSON.parse(rawText);
                } catch (e) {
                    console.error("PHP/HTML Error Response:", rawText);
                    throw new Error("API returned HTML instead of JSON. Press F12 → Console to see the PHP error.");
                }

                if (!res.ok) throw new Error(data.message || 'Failed to fetch');
                
                allOrders = data;
                updateStats();
                renderTable();

            } catch (err) {
                console.error(err);
                tbody.innerHTML = `<tr><td colspan="8" class="text-center py-4 text-danger">Error: ${err.message}</td></tr>`;
            }
        }

        function updateStats() {
            const totalRev = allOrders.reduce((sum, o) => sum + parseInt(o.total_amount), 0);
            const totalOrd = allOrders.length;
            const pendingOrd = allOrders.filter(o => o.order_status === 'pending').length;
            const deliveredOrd = allOrders.filter(o => o.order_status === 'delivered').length;

            document.getElementById('totalRevenue').textContent = '₹' + totalRev.toLocaleString();
            document.getElementById('totalOrders').textContent = totalOrd;
            document.getElementById('pendingOrders').textContent = pendingOrd;
            document.getElementById('deliveredOrders').textContent = deliveredOrd;
        }

        function renderTable() {
            const tbody = document.getElementById('ordersTableBody');
            if (allOrders.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-muted">No orders yet</td></tr>';
                return;
            }
            tbody.innerHTML = allOrders.map(order => {
                const statusClass = order.order_status === 'delivered' ? 'badge-delivered' : 'badge-pending';
                const date = new Date(order.created_at).toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' });
                return `
                    <tr>
                        <td class="fw-bold text-danger">#${order.id}</td>
                        <td>${order.customer_name || 'N/A'}</td>
                        <td><small>${order.customer_email || '-'}</small><br><small>${order.customer_phone || ''}</small></td>
                        <td>${order.items ? order.items.length : 0} items</td>
                        <td class="fw-bold">₹${parseInt(order.total_amount).toLocaleString()}</td>
                        <td><span class="badge ${statusClass}">${order.order_status}</span></td>
                        <td>${date}</td>
                        <td><button class="btn btn-sm btn-outline-dark" onclick="viewOrder(${order.id})"><i class="fas fa-eye"></i></button></td>
                    </tr>
                `;
            }).join('');
        }

        function viewOrder(orderId) {
            const order = allOrders.find(o => o.id === orderId);
            if (!order) return;
            document.getElementById('detailName').textContent = order.customer_name || 'N/A';
            document.getElementById('detailEmail').textContent = order.customer_email || 'N/A';
            document.getElementById('detailPhone').textContent = order.customer_phone || 'N/A';
            document.getElementById('detailAddress').textContent = order.delivery_address || 'N/A';
            document.getElementById('detailOrderId').textContent = '#' + order.id;
            document.getElementById('detailDate').textContent = new Date(order.created_at).toLocaleString('en-IN');
            const statusEl = document.getElementById('detailStatus');
            statusEl.textContent = order.order_status;
            statusEl.className = 'badge ' + (order.order_status === 'delivered' ? 'badge-delivered' : 'badge-pending');
            const itemsTbody = document.getElementById('orderItemsTableBody');
            if (order.items && order.items.length > 0) {
                itemsTbody.innerHTML = order.items.map(item => `<tr><td><div class="d-flex align-items-center gap-2"><img src="${item.image_url}" class="item-img"><span class="fw-bold">${item.product_name}</span></div></td><td>₹${parseInt(item.price).toLocaleString()}</td><td>${item.quantity}</td><td class="text-end fw-bold">₹${(parseInt(item.price) * item.quantity).toLocaleString()}</td></tr>`).join('');
            } else { itemsTbody.innerHTML = '<tr><td colspan="4" class="text-center">No items</td></tr>'; }
            document.getElementById('detailGrandTotal').textContent = '₹' + parseInt(order.total_amount).toLocaleString();
            new bootstrap.Modal(document.getElementById('orderDetailModal')).show();
        }
    </script>
</body>
</html>