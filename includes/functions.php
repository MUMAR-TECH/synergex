<?php
require_once __DIR__ . '/db.php';

function getSetting($key, $default = '') {
    $db = Database::getInstance();
    $result = $db->fetchOne("SELECT setting_value FROM site_settings WHERE setting_key = ?", [$key]);
    return $result ? $result['setting_value'] : $default;
}

function updateSetting($key, $value) {
    $db = Database::getInstance();
    $existing = $db->fetchOne("SELECT id FROM site_settings WHERE setting_key = ?", [$key]);
    
    if ($existing) {
        return $db->update('site_settings', ['setting_value' => $value], 'setting_key = ?', [$key]);
    } else {
        return $db->insert('site_settings', ['setting_key' => $key, 'setting_value' => $value]);
    }
}

function getImpactStats() {
    $db = Database::getInstance();
    return $db->fetchOne("SELECT * FROM impact_stats ORDER BY id DESC LIMIT 1");
}

function getProducts($activeOnly = true) {
    $db = Database::getInstance();
    $sql = "SELECT * FROM products";
    if ($activeOnly) {
        $sql .= " WHERE is_active = 1";
    }
    $sql .= " ORDER BY id ASC";
    return $db->fetchAll($sql);
}

function getProduct($id) {
    $db = Database::getInstance();
    return $db->fetchOne("SELECT * FROM products WHERE id = ?", [$id]);
}

function getGallery($category = null, $limit = null) {
    $db = Database::getInstance();
    $sql = "SELECT * FROM gallery";
    $params = [];
    
    if ($category) {
        $sql .= " WHERE category = ?";
        $params[] = $category;
    }
    
    $sql .= " ORDER BY display_order ASC, created_at DESC";
    
    if ($limit) {
        $sql .= " LIMIT " . intval($limit);
    }
    
    return $db->fetchAll($sql, $params);
}

function getAchievements($limit = null) {
    $db = Database::getInstance();
    $sql = "SELECT * FROM achievements ORDER BY year DESC, display_order ASC";
    
    if ($limit) {
        $sql .= " LIMIT " . intval($limit);
    }
    
    return $db->fetchAll($sql);
}

function getPartners($activeOnly = true) {
    $db = Database::getInstance();
    $sql = "SELECT * FROM partners";
    if ($activeOnly) {
        $sql .= " WHERE is_active = 1";
    }
    $sql .= " ORDER BY display_order ASC";
    return $db->fetchAll($sql);
}

function getHeroSlides($activeOnly = true) {
    $db = Database::getInstance();
    $sql = "SELECT * FROM hero_slider";
    if ($activeOnly) {
        $sql .= " WHERE is_active = 1";
    }
    $sql .= " ORDER BY display_order ASC, created_at DESC";
    return $db->fetchAll($sql);
}

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function uploadImage($file, $prefix = 'img') {
    $targetDir = UPLOAD_PATH;
    
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    $imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $newFileName = $prefix . '_' . time() . '_' . uniqid() . '.' . $imageFileType;
    $targetFile = $targetDir . $newFileName;
    
    $check = getimagesize($file["tmp_name"]);
    if ($check === false) {
        return ['success' => false, 'message' => 'File is not an image.'];
    }
    
    if ($file["size"] > 5000000) {
        return ['success' => false, 'message' => 'File is too large. Max 5MB.'];
    }
    
    if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
        return ['success' => false, 'message' => 'Only JPG, JPEG, PNG, GIF & WEBP files allowed.'];
    }
    
    if (move_uploaded_file($file["tmp_name"], $targetFile)) {
        return ['success' => true, 'filename' => $newFileName];
    } else {
        return ['success' => false, 'message' => 'Error uploading file.'];
    }
}

function formatNumber($number) {
    if ($number >= 1000000) {
        return number_format($number / 1000000, 1) . 'M+';
    } elseif ($number >= 1000) {
        return number_format($number / 1000, 1) . 'K+';
    }
    return number_format($number);
}

function isLoggedIn() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . ADMIN_URL . '/index.php');
        exit;
    }
}

function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>