<?php
// ============================================================================
// FILE: admin/settings.php - Site Settings Management
// ============================================================================
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$db = Database::getInstance();
$success = '';
$error = '';

// Handle settings update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_site_info':
                updateSetting('site_name', sanitizeInput($_POST['site_name']));
                updateSetting('tagline', sanitizeInput($_POST['tagline']));
                updateSetting('email', sanitizeInput($_POST['email']));
                updateSetting('phone', sanitizeInput($_POST['phone']));
                updateSetting('whatsapp', sanitizeInput($_POST['whatsapp']));
                updateSetting('address', sanitizeInput($_POST['address']));
                updateSetting('mission', sanitizeInput($_POST['mission']));
                updateSetting('vision', sanitizeInput($_POST['vision']));
                $success = 'Site information updated successfully';
                break;
                
            case 'update_impact':
                $db->update('impact_stats', [
                    'plastic_recycled' => intval($_POST['plastic_recycled']),
                    'eco_pavers_produced' => intval($_POST['eco_pavers_produced']),
                    'institutions_served' => intval($_POST['institutions_served']),
                    'youths_engaged' => intval($_POST['youths_engaged'])
                ], 'id = 1');
                $success = 'Impact statistics updated successfully';
                break;
                
            case 'change_password':
                $currentPassword = $_POST['current_password'];
                $newPassword = $_POST['new_password'];
                $confirmPassword = $_POST['confirm_password'];
                
                $admin = $db->fetchOne("SELECT password FROM admin_users WHERE id = ?", [$_SESSION['admin_id']]);
                
                if (!password_verify($currentPassword, $admin['password'])) {
                    $error = 'Current password is incorrect';
                } elseif ($newPassword !== $confirmPassword) {
                    $error = 'New passwords do not match';
                } elseif (strlen($newPassword) < 8) {
                    $error = 'Password must be at least 8 characters';
                } else {
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                    $db->update('admin_users', ['password' => $hashedPassword], 'id = ?', [$_SESSION['admin_id']]);
                    $success = 'Password changed successfully';
                }
                break;
        }
    }
}

// Fetch current settings
$siteName = getSetting('site_name', 'Synergex Solutions');
$tagline = getSetting('tagline', 'Turning Waste Into Sustainable Value');
$email = getSetting('email', 'synergexsolutions25@gmail.com');
$phone = getSetting('phone', '0770377471');
$whatsapp = getSetting('whatsapp', '260770377471');
$address = getSetting('address', 'Kitwe, Copperbelt Province, Zambia');
$mission = getSetting('mission', 'Creating sustainable value from waste');
$vision = getSetting('vision', 'A cleaner, greener Zambia');

$impactStats = getImpactStats();

include 'includes/admin_header.php';
?>

<div class="page-header">
    <h1>Settings</h1>
</div>

<?php if ($success): ?>
<div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert alert-error"><?php echo $error; ?></div>
<?php endif; ?>

<div style="display: grid; gap: 2rem;">
    <!-- Site Information -->
    <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <h2>Site Information</h2>
        <form method="POST">
            <input type="hidden" name="action" value="update_site_info">
            
            <div class="form-row">
                <div class="form-group">
                    <label for="site_name">Site Name</label>
                    <input type="text" id="site_name" name="site_name" value="<?php echo htmlspecialchars($siteName); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="tagline">Tagline</label>
                    <input type="text" id="tagline" name="tagline" value="<?php echo htmlspecialchars($tagline); ?>" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="whatsapp">WhatsApp Number</label>
                    <input type="tel" id="whatsapp" name="whatsapp" value="<?php echo htmlspecialchars($whatsapp); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($address); ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="mission">Mission Statement</label>
                <textarea id="mission" name="mission" rows="3" required><?php echo htmlspecialchars($mission); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="vision">Vision Statement</label>
                <textarea id="vision" name="vision" rows="3" required><?php echo htmlspecialchars($vision); ?></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Save Site Information</button>
        </form>
    </div>
    
    <!-- Impact Statistics -->
    <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <h2>Impact Statistics</h2>
        <form method="POST">
            <input type="hidden" name="action" value="update_impact">
            
            <div class="form-row">
                <div class="form-group">
                    <label for="plastic_recycled">Plastic Recycled (kg)</label>
                    <input type="number" id="plastic_recycled" name="plastic_recycled" value="<?php echo $impactStats['plastic_recycled']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="eco_pavers_produced">Eco-Pavers Produced</label>
                    <input type="number" id="eco_pavers_produced" name="eco_pavers_produced" value="<?php echo $impactStats['eco_pavers_produced']; ?>" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="institutions_served">Institutions Served</label>
                    <input type="number" id="institutions_served" name="institutions_served" value="<?php echo $impactStats['institutions_served']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="youths_engaged">Youths Engaged</label>
                    <input type="number" id="youths_engaged" name="youths_engaged" value="<?php echo $impactStats['youths_engaged']; ?>" required>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">Update Statistics</button>
        </form>
    </div>
    
    <!-- Change Password -->
    <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <h2>Change Password</h2>
        <form method="POST">
            <input type="hidden" name="action" value="change_password">
            
            <div class="form-group">
                <label for="current_password">Current Password</label>
                <input type="password" id="current_password" name="current_password" required>
            </div>
            
            <div class="form-group">
                <label for="new_password">New Password (min 8 characters)</label>
                <input type="password" id="new_password" name="new_password" minlength="8" required>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" minlength="8" required>
            </div>
            
            <button type="submit" class="btn btn-primary">Change Password</button>
        </form>
    </div>
    
    <!-- System Information -->
    <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <h2>System Information</h2>
        <div class="detail-row">
            <strong>PHP Version:</strong> <?php echo phpversion(); ?>
        </div>
        <div class="detail-row">
            <strong>Server Software:</strong> <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?>
        </div>
        <div class="detail-row">
            <strong>Database:</strong> MySQL/MariaDB
        </div>
        <div class="detail-row">
            <strong>Upload Max Size:</strong> <?php echo ini_get('upload_max_filesize'); ?>
        </div>
        <div class="detail-row">
            <strong>Site URL:</strong> <?php echo SITE_URL; ?>
        </div>
        <div class="detail-row">
            <strong>Version:</strong> 1.0.0
        </div>
    </div>
</div>

<style>
.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.detail-row {
    padding: 0.75rem 0;
    border-bottom: 1px solid #eee;
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-row strong {
    color: #1A3E7F;
    display: inline-block;
    width: 200px;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include 'includes/admin_footer.php'; ?>
