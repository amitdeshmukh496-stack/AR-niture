<?php
session_start();
require_once 'config.php';

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: admin.php');
    exit;
}

 $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: admin.php');
        exit;
    } else {
        $error = 'Invalid username or password!';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AR-nature | Admin Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg-main: #ffffff;
            --text-main: #111827;
            --text-muted: #6b7280;
            --accent: #FF6B35; /* Your brand orange */
            --border-color: #e5e7eb;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: var(--bg-main); color: var(--text-main); height: 100vh; display: flex; align-items: center; justify-content: center; border-top: 5px solid var(--accent); }

        .login-container { width: 100%; max-width: 380px; padding: 20px; }
        
        .logo { text-align: center; margin-bottom: 40px; }
        .logo h1 { font-size: 2rem; font-weight: 700; letter-spacing: -0.5px; }
        .logo h1 span { color: var(--accent); }
        .logo p { color: var(--text-muted); font-size: 0.95rem; margin-top: 5px; }

        .form-group { margin-bottom: 20px; position: relative; }
        .form-group label { display: block; margin-bottom: 8px; font-size: 0.85rem; font-weight: 600; color: var(--text-main); }
        
        .input-icon { position: absolute; top: 38px; left: 15px; color: var(--text-muted); font-size: 0.9rem; }
        
        .light-input {
            width: 100%;
            background: #f9fafb;
            border: 1px solid var(--border-color);
            color: var(--text-main);
            border-radius: 10px;
            padding: 14px 15px 14px 42px;
            font-size: 0.95rem;
            outline: none;
            transition: 0.2s;
        }
        .light-input:focus {
            background: #ffffff;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
        }

        .btn-accent {
            width: 100%;
            background: var(--accent);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 14px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
            margin-top: 10px;
        }
        .btn-accent:hover { 
            background: #e85a2a; 
            transform: translateY(-1px); 
            box-shadow: 0 4px 12px rgba(255, 107, 53, 0.25); 
        }

        .error-msg { 
            color: #ef4444; 
            background: rgba(239, 68, 68, 0.05); 
            border: 1px solid rgba(239, 68, 68, 0.2);
            padding: 12px; 
            border-radius: 10px; 
            margin-bottom: 25px; 
            text-align: center; 
            font-size: 0.9rem;
            font-weight: 500;
        }

        .back-link { 
            display: block; 
            text-align: center; 
            margin-top: 30px; 
            color: var(--text-muted); 
            text-decoration: none; 
            font-size: 0.9rem; 
            font-weight: 500;
        }
        .back-link:hover { color: var(--accent); }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <h1>AR-<span>niture</span></h1>
            <p>Admin Dashboard Login</p>
        </div>
        
        <?php if ($error): ?>
            <div class="error-msg">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label>Username</label>
                <i class="fas fa-user input-icon"></i>
                <input type="text" name="username" class="light-input" placeholder="Enter username" required autofocus>
            </div>
            <div class="form-group">
                <label>Password</label>
                <i class="fas fa-lock input-icon"></i>
                <input type="password" name="password" class="light-input" placeholder="Enter password" required>
            </div>
            <button type="submit" class="btn-accent">Sign In</button>
        </form>
        
        <a href="index.html" class="back-link"><i class="fas fa-arrow-left me-1"></i> Back to Store</a>
    </div>
</body>
</html>