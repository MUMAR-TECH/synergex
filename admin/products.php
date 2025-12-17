<?php
// ============================================================================
// FILE: admin/products.php - Product Management
// ============================================================================
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$db = Database::getInstance();
$success = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
            case 'edit':
                $name = sanitizeInput($_POST['name']);
                $description = sanitizeInput($_POST['description']);
                $price = floatval($_POST['price']);
                $unit = sanitizeInput($_POST['unit']);
                $features = sanitizeInput($_POST['features']);
                $isActive = isset($_POST['is_active']) ? 1 : 0;
                
                $data = [
                    'name' => $name,
                    'description' => $description,
                    'price' => $price,
                    'unit' => $unit,
                    'features' => $features,
                    'is_active' => $isActive
                ];
                
                // Handle image upload
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $upload = uploadImage($_FILES['image'], 'product');
                    if ($upload['success']) {
                        $data['image'] = $upload['filename'];
                    }
                }
                
                if ($_POST['action'] === 'add') {
                    $db->insert('products', $data);
                    $success = 'Product added successfully';
                } else {
                    $id = intval($_POST['id']);
                    $db->update('products', $data, 'id = ?', [$id]);
                    $success = 'Product updated successfully';
                }
                break;
                
            case 'delete':
                $id = intval($_POST['id']);
                $db->delete('products', 'id = ?', [$id]);
                $success = 'Product deleted successfully';
                break;
        }
    }
}

$products = getProducts(false);

include 'includes/admin_header.php';
?>

<div class="page-header">
    <h1>Product Management</h1>
    <button class="btn btn-primary" onclick="showAddModal()">+ Add New Product</button>
</div>

<?php if ($success): ?>
<div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert alert-error"><?php echo $error; ?></div>
<?php endif; ?>

<div class="table-responsive">
    <table class="data-table">
        <thead>
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Price</th>
                <th>Unit</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
            <tr>
                <td>
                    <?php if ($product['image']): ?>
                    <img src="<?php echo UPLOAD_URL . $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px;">
                    <?php else: ?>
                    <div style="width: 60px; height: 60px; background: #f0f0f0; border-radius: 5px; display: flex; align-items: center; justify-content: center;">No Image</div>
                    <?php endif; ?>
                </td>
                <td><strong><?php echo htmlspecialchars($product['name']); ?></strong></td>
                <td>K<?php echo number_format($product['price'], 2); ?></td>
                <td><?php echo htmlspecialchars($product['unit']); ?></td>
                <td>
                    <span class="status-badge status-<?php echo $product['is_active'] ? 'active' : 'inactive'; ?>">
                        <?php echo $product['is_active'] ? 'Active' : 'Inactive'; ?>
                    </span>
                </td>
                <td>
                    <button class="btn-icon" onclick='editProduct(<?php echo json_encode($product); ?>)' title="Edit">✏️</button>
                    <button class="btn-icon" onclick="deleteProduct(<?php echo $product['id']; ?>)" title="Delete">🗑️</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Add/Edit Product Modal -->
<div id="productModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2 id="modalTitle">Add New Product</h2>
        
        <form id="productForm" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" id="formAction" value="add">
            <input type="hidden" name="id" id="productId">
            
            <div class="form-group">
                <label for="name">Product Name *</label>
                <input type="text" id="name" name="name" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="3"></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="price">Price (ZMW) *</label>
                    <input type="number" id="price" name="price" step="0.01" required>
                </div>
                
                <div class="form-group">
                    <label for="unit">Unit</label>
                    <input type="text" id="unit" name="unit" value="per unit">
                </div>
            </div>
            
            <div class="form-group">
                <label for="features">Features (separated by |)</label>
                <input type="text" id="features" name="features" placeholder="Durable|Water-resistant|Eco-friendly">
            </div>
            
            <div class="form-group">
                <label for="image">Product Image</label>
                <input type="file" id="image" name="image" accept="image/*">
                <div id="imagePreview"></div>
            </div>
            
            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_active" id="is_active" checked>
                    Active (visible on website)
                </label>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Product</button>
            </div>
        </form>
    </div>
</div>

<style>
.modal {
    display: none;
    position: fixed;
    z-index: 10000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: white;
    margin: 3% auto;
    padding: 2rem;
    border-radius: 10px;
    width: 90%;
    max-width: 600px;
    max-height: 90vh;
    overflow-y: auto;
}

.close {
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    line-height: 20px;
}

.close:hover {
    color: #FF6600;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

#imagePreview {
    margin-top: 1rem;
}

#imagePreview img {
    max-width: 200px;
    border-radius: 5px;
}
</style>

<script>
function showAddModal() {
    document.getElementById('modalTitle').textContent = 'Add New Product';
    document.getElementById('formAction').value = 'add';
    document.getElementById('productForm').reset();
    document.getElementById('imagePreview').innerHTML = '';
    document.getElementById('productModal').style.display = 'block';
}

function editProduct(product) {
    document.getElementById('modalTitle').textContent = 'Edit Product';
    document.getElementById('formAction').value = 'edit';
    document.getElementById('productId').value = product.id;
    document.getElementById('name').value = product.name;
    document.getElementById('description').value = product.description || '';
    document.getElementById('price').value = product.price;
    document.getElementById('unit').value = product.unit;
    document.getElementById('features').value = product.features || '';
    document.getElementById('is_active').checked = product.is_active == 1;
    
    if (product.image) {
        document.getElementById('imagePreview').innerHTML = 
            `<img src="<?php echo UPLOAD_URL; ?>${product.image}" alt="Current image">`;
    }
    
    document.getElementById('productModal').style.display = 'block';
}

function deleteProduct(id) {
    if (confirm('Are you sure you want to delete this product?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="${id}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function closeModal() {
    document.getElementById('productModal').style.display = 'none';
}

// Image preview
document.getElementById('image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagePreview').innerHTML = 
                `<img src="${e.target.result}" alt="Preview" style="max-width: 200px; border-radius: 5px;">`;
        };
        reader.readAsDataURL(file);
    }
});

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('productModal');
    if (event.target == modal) {
        closeModal();
    }
}
</script>

<?php include 'includes/admin_footer.php'; ?>