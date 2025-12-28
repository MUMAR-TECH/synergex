<?php
// ============================================================================
// FILE: admin/index.php - Admin Login
// ============================================================================
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';

// Prevent redirect loops - check if we just came from this page
$justRedirected = isset($_GET['retry']);

// If already logged in, redirect to dashboard (but not if we just got here)
if (SessionManager::has('admin_id') && !$justRedirected) {
    // Double-check the admin_id is valid
    $adminId = SessionManager::get('admin_id');
    if ($adminId && $adminId > 0) {
        header('Location: dashboard.php');
        exit;
    } else {
        // Invalid session, clear it
        SessionManager::destroy();
    }
}

$error = '';
$timeout = isset($_GET['timeout']) ? 'Your session has expired. Please login again.' : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password';
    } else {
        try {
            $db = Database::getInstance();
            $admin = $db->fetchOne("SELECT * FROM admin_users WHERE email = ?", [$email]);
            
            if ($admin && isset($admin['id']) && $admin['id'] > 0 && password_verify($password, $admin['password'])) {
                // Clear any existing session first
                SessionManager::destroy();
                
                // Start fresh session
                SessionManager::init();
                SessionManager::regenerate();
                
                // Set session variables with explicit values
                SessionManager::set('admin_id', (int)$admin['id']);
                SessionManager::set('admin_name', $admin['name']);
                SessionManager::set('admin_email', $admin['email']);
                SessionManager::set('login_time', time());
                SessionManager::set('logged_in', true);
                
                // Verify session was set correctly
                if (SessionManager::get('admin_id') > 0) {
                    // Set remember me cookie if checked
                    if ($remember) {
                        $token = bin2hex(random_bytes(32));
                        CookieManager::set('remember_token', $token, 30 * 24 * 60 * 60);
                    }
                    
                    // Redirect to dashboard
                    header('Location: dashboard.php');
                    exit;
                } else {
                    $error = 'Session initialization failed. Please try again.';
                }
            } else {
                $error = 'Invalid email or password';
                // Add delay to prevent brute force
                sleep(1);
            }
        } catch (Exception $e) {
            $error = 'Login error: ' . (APP_DEBUG ? $e->getMessage() : 'Please try again');
            if (APP_DEBUG) {
                error_log('Login error: ' . $e->getMessage());
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Synergex Solutions</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Montserrat:wght@600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1A3E7F 0%, #2a5298 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-container {
            background: white;
            padding: 3rem;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        h1 {
            font-family: 'Montserrat', sans-serif;
            color: #1A3E7F;
            text-align: center;
            margin-bottom: 0.5rem;
            font-size: 1.8rem;
        }
        
        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 2rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 500;
        }
        
        input {
            width: 100%;
            padding: 0.875rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        input:focus {
            outline: none;
            border-color: #1A3E7F;
        }
        
        .btn {
            width: 100%;
            padding: 0.875rem;
            background: #FF6600;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: background 0.3s;
        }
        
        .btn:hover {
            background: #e55a00;
        }
        
        .error {
            background: #ffebee;
            color: #c62828;
            padding: 0.875rem;
            border-radius: 5px;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        
        .back-link {
            text-align: center;
            margin-top: 1.5rem;
        }
        
        .back-link a {
            color: #1A3E7F;
            text-decoration: none;
        }
        
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <h1>Synergex Solutions</h1>
            <p class="subtitle">Admin Panel</p>
        </div>
        
        <?php if ($error): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required autofocus>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn">Login</button>
        </form>
        
        <div class="back-link">
            <a href="setup.php">Create Admin Account</a> | 
            <a href="<?php echo SITE_URL; ?>">← Back to Website</a>
        </div>
    </div>
</body>
</html>
