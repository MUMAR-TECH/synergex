<?php
// ============================================================================
// FILE: admin/setup.php - Initial Admin Account Setup
// This page allows you to create the first admin account
// After creating an account, you can login at index.php
// ============================================================================
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';

$success = '';
$error = '';

// Check if admin already exists
try {
    $db = Database::getInstance();
    
    // Create admin_users table if it doesn't exist
    $db->getConnection()->exec("
        CREATE TABLE IF NOT EXISTS admin_users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            name VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_email (email)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    // Check if any admin exists
    $existingAdmins = $db->fetchAll("SELECT COUNT(*) as count FROM admin_users");
    $adminCount = $existingAdmins[0]['count'] ?? 0;
    
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Validation
        if (empty($email) || empty($name) || empty($password)) {
            $error = 'All fields are required';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Invalid email address';
        } elseif (strlen($password) < 8) {
            $error = 'Password must be at least 8 characters long';
        } elseif ($password !== $confirmPassword) {
            $error = 'Passwords do not match';
        } else {
            // Check if email already exists
            $existing = $db->fetchOne("SELECT id FROM admin_users WHERE email = ?", [$email]);
            
            if ($existing) {
                $error = 'An admin account with this email already exists';
            } else {
                // Create admin account
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                
                $db->insert('admin_users', [
                    'email' => $email,
                    'password' => $hashedPassword,
                    'name' => $name
                ]);
                
                $success = 'Admin account created successfully! You can now login.';
            }
        }
    }
} catch (Exception $e) {
    $error = 'Database Error: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Admin Account - Synergex Solutions</title>
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
            padding: 2rem;
        }
        
        .setup-container {
            background: white;
            padding: 3rem;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 450px;
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
            font-size: 0.95rem;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        
        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border-left: 4px solid #17a2b8;
            margin-bottom: 1.5rem;
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
        
        .btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        
        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #eee;
        }
        
        .login-link a {
            color: #1A3E7F;
            text-decoration: none;
            font-weight: 500;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
        
        .password-requirements {
            font-size: 0.85rem;
            color: #666;
            margin-top: 0.25rem;
        }
    </style>
</head>
<body>
    <div class="setup-container">
        <div class="logo">
            <h1>Synergex Solutions</h1>
            <p class="subtitle">Admin Account Setup</p>
        </div>
        
        <?php if ($success): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($success); ?>
            <br><br>
            <a href="index.php" style="color: #155724; font-weight: 600; text-decoration: underline;">Click here to login →</a>
        </div>
        <?php else: ?>
        
        <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <div class="alert alert-info">
            <strong>Create your admin account</strong><br>
            Fill in the form below to create the first admin account for the system.
        </div>
        
        <form method="POST" id="setupForm">
            <div class="form-group">
                <label for="name">Full Name *</label>
                <input type="text" id="name" name="name" 
                       placeholder="Enter your full name" 
                       value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" 
                       required autofocus>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" id="email" name="email" 
                       placeholder="admin@synergex.com" 
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" 
                       required>
            </div>
            
            <div class="form-group">
                <label for="password">Password *</label>
                <input type="password" id="password" name="password" 
                       placeholder="Enter password (min 8 characters)" 
                       required minlength="8">
                <div class="password-requirements">
                    Password must be at least 8 characters long
                </div>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password *</label>
                <input type="password" id="confirm_password" name="confirm_password" 
                       placeholder="Confirm your password" 
                       required minlength="8">
            </div>
            
            <button type="submit" class="btn">Create Admin Account</button>
        </form>
        
        <?php endif; ?>
        
        <div class="login-link">
            <a href="index.php">Already have an account? Login here</a>
        </div>
    </div>
    
    <script>
        // Password confirmation validation
        document.getElementById('setupForm')?.addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
                return false;
            }
            
            if (password.length < 8) {
                e.preventDefault();
                alert('Password must be at least 8 characters long!');
                return false;
            }
        });
        
        // Real-time password match indicator
        const passwordInput = document.getElementById('password');
        const confirmInput = document.getElementById('confirm_password');
        
        function checkPasswordMatch() {
            if (confirmInput.value && passwordInput.value) {
                if (passwordInput.value === confirmInput.value) {
                    confirmInput.style.borderColor = '#28a745';
                } else {
                    confirmInput.style.borderColor = '#dc3545';
                }
            } else {
                confirmInput.style.borderColor = '#ddd';
            }
        }
        
        passwordInput?.addEventListener('input', checkPasswordMatch);
        confirmInput?.addEventListener('input', checkPasswordMatch);
    </script>
</body>
</html>

