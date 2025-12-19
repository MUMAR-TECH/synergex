<?php
// ============================================================================
// FILE: admin/includes/admin_utilities.php - Standardized Admin Functions
// ============================================================================

/**
 * Standardized edit handler for admin forms
 */
function handleAdminFormSubmission($tableName, $requiredFields = [], $optionalFields = [], $imageField = null, $customValidation = null) {
    global $db;
    $result = ['success' => false, 'message' => '', 'action' => ''];
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['action'])) {
        return $result;
    }
    
    // CSRF validation
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $result['message'] = 'Invalid security token. Please try again.';
        return $result;
    }
    
    $action = $_POST['action'];
    $result['action'] = $action;
    
    try {
        switch ($action) {
            case 'add':
            case 'edit':
                // Build data array
                $data = [];
                error_log("Processing form for $tableName. Required fields: " . print_r($requiredFields, true));
                error_log("POST data: " . print_r($_POST, true));
                
                // Process required fields
                foreach ($requiredFields as $field) {
                    if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
                        $result['message'] = "Field '$field' is required.";
                        error_log("Required field '$field' is missing or empty");
                        return $result;
                    }
                    $data[$field] = sanitizeInput($_POST[$field]);
                    error_log("Added required field '$field': " . $data[$field]);
                }
                
                // Process optional fields
                foreach ($optionalFields as $field) {
                    if (isset($_POST[$field])) {
                        if (in_array($field, ['price', 'year', 'display_order'])) {
                            $data[$field] = is_numeric($_POST[$field]) ? $_POST[$field] : 0;
                        } elseif (in_array($field, ['is_active', 'is_featured'])) {
                            $data[$field] = isset($_POST[$field]) ? 1 : 0;
                        } else {
                            $data[$field] = sanitizeInput($_POST[$field]);
                        }
                        error_log("Added optional field '$field': " . $data[$field]);
                    } else {
                        error_log("Optional field '$field' not provided in POST");
                    }
                }
                
                // Handle image upload
                $imageUploaded = false;
                if ($imageField && isset($_FILES[$imageField]) && $_FILES[$imageField]['error'] === UPLOAD_ERR_OK) {
                    $upload = uploadImage($_FILES[$imageField], $tableName);
                    if ($upload['success']) {
                        $data[$imageField] = $upload['filename'];
                        $imageUploaded = true;
                    } else {
                        $result['message'] = $upload['message'];
                        return $result;
                    }
                } elseif ($imageField && isset($_FILES[$imageField]) && $_FILES[$imageField]['error'] !== UPLOAD_ERR_NO_FILE) {
                    $uploadErrors = [
                        UPLOAD_ERR_INI_SIZE => 'File is too large (exceeds upload_max_filesize)',
                        UPLOAD_ERR_FORM_SIZE => 'File is too large (exceeds MAX_FILE_SIZE)',
                        UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
                        UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
                        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
                        UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
                    ];
                    $result['message'] = $uploadErrors[$_FILES[$imageField]['error']] ?? 'Unknown upload error';
                    return $result;
                }
                
                // Custom validation
                if ($customValidation && is_callable($customValidation)) {
                    $validationResult = $customValidation($data);
                    if ($validationResult !== true) {
                        $result['message'] = $validationResult;
                        return $result;
                    }
                }
                
                // Perform database operation
                if ($action === 'add') {
                    if ($imageField && !$imageUploaded) {
                        $result['message'] = "Please upload an image.";
                        return $result;
                    }
                    
                    error_log("Attempting to insert into $tableName: " . print_r($data, true));
                    $id = $db->insert($tableName, $data);
                    
                    if ($id) {
                        $result['success'] = true;
                        $result['message'] = ucfirst($tableName) . ' item added successfully';
                        $result['id'] = $id;
                        error_log("Insert successful. New ID: $id");
                    } else {
                        $result['message'] = 'Failed to save record to database.';
                        error_log("Insert failed for $tableName");
                    }
                } else {
                    // Edit
                    $id = intval($_POST['id']);
                    if ($id <= 0) {
                        $result['message'] = 'Invalid ID provided.';
                        return $result;
                    }
                    
                    // Handle image replacement for edit
                    if ($imageField && $imageUploaded) {
                        // Delete old image
                        $oldRecord = $db->fetchOne("SELECT $imageField FROM $tableName WHERE id = ?", [$id]);
                        if ($oldRecord && !empty($oldRecord[$imageField]) && file_exists(UPLOAD_PATH . $oldRecord[$imageField])) {
                            unlink(UPLOAD_PATH . $oldRecord[$imageField]);
                        }
                    } elseif ($imageField && !$imageUploaded) {
                        // Don't update image field if no new image uploaded
                        unset($data[$imageField]);
                    }
                    
                    // Use named placeholder to avoid mixing positional/named params
                    $updateResult = $db->update($tableName, $data, 'id = :id', ['id' => $id]);
                    error_log("Update query executed. Table: $tableName, ID: $id, Data: " . print_r($data, true));
                    error_log("Update result: " . ($updateResult ? 'TRUE' : 'FALSE'));
                    if ($updateResult) {
                        $result['success'] = true;
                        $result['message'] = ucfirst($tableName) . ' item updated successfully';
                        $result['id'] = $id;
                    } else {
                        $result['message'] = 'Failed to update record.';
                        error_log("Database update failed for $tableName ID $id");
                    }
                }
                break;
                
            case 'delete':
                $id = intval($_POST['id']);
                error_log("Delete operation: Table: $tableName, ID: $id");
                if ($id <= 0) {
                    $result['message'] = 'Invalid ID provided.';
                    error_log("Delete failed: Invalid ID ($id)");
                    return $result;
                }
                
                // Delete associated image if exists
                if ($imageField) {
                    $record = $db->fetchOne("SELECT $imageField FROM $tableName WHERE id = ?", [$id]);
                    if ($record && !empty($record[$imageField]) && file_exists(UPLOAD_PATH . $record[$imageField])) {
                        $deleted = unlink(UPLOAD_PATH . $record[$imageField]);
                        error_log("Image file deletion: " . ($deleted ? 'SUCCESS' : 'FAILED') . " - " . $record[$imageField]);
                    }
                }
                
                // Use named placeholder for consistency
                $deleteResult = $db->delete($tableName, 'id = :id', ['id' => $id]);
                error_log("Delete result: " . ($deleteResult ? 'TRUE' : 'FALSE'));
                if ($deleteResult) {
                    $result['success'] = true;
                    $result['message'] = ucfirst($tableName) . ' item deleted successfully';
                } else {
                    $result['message'] = 'Failed to delete record.';
                    error_log("Database delete failed for $tableName ID $id");
                }
                break;
                
            default:
                $result['message'] = 'Invalid action specified.';
        }
        
    } catch (Exception $e) {
        error_log("Admin form error: " . $e->getMessage());
        $result['message'] = 'An error occurred while processing your request.';
    }
    
    return $result;
}

/**
 * Generate standardized modal HTML for admin forms
 */
function generateAdminModal($modalId, $title, $fields, $hasImage = false) {
    ob_start();
    ?>
    <div id="<?php echo $modalId; ?>" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('<?php echo $modalId; ?>')">&times;</span>
            <h2 id="<?php echo $modalId; ?>Title"><?php echo $title; ?></h2>
            
            <form id="<?php echo $modalId; ?>Form" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" id="<?php echo $modalId; ?>Action" value="add">
                <input type="hidden" name="id" id="<?php echo $modalId; ?>Id">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                
                <?php foreach ($fields as $field): ?>
                <div class="form-group">
                    <label for="<?php echo $field['id']; ?>"><?php echo $field['label']; ?> <?php echo isset($field['required']) && $field['required'] ? '*' : ''; ?></label>
                    
                    <?php if ($field['type'] === 'textarea'): ?>
                        <textarea id="<?php echo $field['id']; ?>" name="<?php echo $field['name']; ?>" 
                                  <?php echo isset($field['required']) && $field['required'] ? 'required' : ''; ?>
                                  rows="<?php echo $field['rows'] ?? 3; ?>"></textarea>
                    
                    <?php elseif ($field['type'] === 'select'): ?>
                        <select id="<?php echo $field['id']; ?>" name="<?php echo $field['name']; ?>"
                                <?php echo isset($field['required']) && $field['required'] ? 'required' : ''; ?>>
                            <?php foreach ($field['options'] as $value => $label): ?>
                            <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                            <?php endforeach; ?>
                        </select>
                    
                    <?php elseif ($field['type'] === 'checkbox'): ?>
                        <label class="checkbox-label">
                            <input type="checkbox" id="<?php echo $field['id']; ?>" name="<?php echo $field['name']; ?>" value="1">
                            <?php echo $field['checkbox_label'] ?? $field['label']; ?>
                        </label>
                    
                    <?php else: ?>
                        <input type="<?php echo $field['type']; ?>" 
                               id="<?php echo $field['id']; ?>" 
                               name="<?php echo $field['name']; ?>"
                               <?php echo isset($field['required']) && $field['required'] ? 'required' : ''; ?>
                               <?php echo isset($field['step']) ? 'step="' . $field['step'] . '"' : ''; ?>
                               <?php echo isset($field['min']) ? 'min="' . $field['min'] . '"' : ''; ?>
                               <?php echo isset($field['max']) ? 'max="' . $field['max'] . '"' : ''; ?>>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
                
                <?php if ($hasImage): ?>
                <div class="form-group">
                    <label for="image">Image <span id="imageRequired">*</span></label>
                    <input type="file" id="image" name="image" accept="image/*">
                    <small class="form-help">Supported formats: JPG, JPEG, PNG, GIF, WEBP. Max size: 5MB</small>
                    <div id="imagePreview"></div>
                </div>
                <?php endif; ?>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('<?php echo $modalId; ?>')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
    function showAddModal() {
        document.getElementById('<?php echo $modalId; ?>Title').textContent = 'Add <?php echo $title; ?>';
        document.getElementById('<?php echo $modalId; ?>Action').value = 'add';
        document.getElementById('<?php echo $modalId; ?>Form').reset();
        <?php if ($hasImage): ?>
        document.getElementById('imagePreview').innerHTML = '';
        document.getElementById('image').required = true;
        document.getElementById('imageRequired').style.display = '';
        <?php endif; ?>
        document.getElementById('<?php echo $modalId; ?>').style.display = 'block';
    }
    
    function editItem(item) {
        document.getElementById('<?php echo $modalId; ?>Title').textContent = 'Edit <?php echo $title; ?>';
        document.getElementById('<?php echo $modalId; ?>Action').value = 'edit';
        document.getElementById('<?php echo $modalId; ?>Id').value = item.id;
        
        <?php foreach ($fields as $field): ?>
        <?php if ($field['type'] === 'checkbox'): ?>
        document.getElementById('<?php echo $field['id']; ?>').checked = item.<?php echo $field['name']; ?> == 1;
        <?php else: ?>
        document.getElementById('<?php echo $field['id']; ?>').value = item.<?php echo $field['name']; ?> || '';
        <?php endif; ?>
        <?php endforeach; ?>
        
        <?php if ($hasImage): ?>
        document.getElementById('image').required = false;
        document.getElementById('imageRequired').style.display = 'none';
        
        if (item.image) {
            document.getElementById('imagePreview').innerHTML = 
                `<div style="margin-top: 1rem;">
                    <strong>Current Image:</strong><br>
                    <img src="<?php echo UPLOAD_URL; ?>${item.image}" alt="Current image" style="max-width: 200px; max-height: 200px; border-radius: 5px; margin-top: 0.5rem;">
                    <br><small>Leave image field empty to keep current image, or select a new image to replace it.</small>
                 </div>`;
        }
        <?php endif; ?>
        
        document.getElementById('<?php echo $modalId; ?>').style.display = 'block';
    }
    
    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }
    
    function deleteItem(id) {
        if (confirm('Are you sure you want to delete this item?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="${id}">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }
    
    window.onclick = function(event) {
        const modal = document.getElementById('<?php echo $modalId; ?>');
        if (event.target == modal) {
            closeModal('<?php echo $modalId; ?>');
        }
    }
    </script>
    <?php
    return ob_get_clean();
}
?>