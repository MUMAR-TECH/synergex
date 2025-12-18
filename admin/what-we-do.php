<?php
require_once __DIR__ . '/../includes/admin_header.php';
require_once __DIR__ . '/../includes/db.php';

$db = Database::getInstance();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = $_POST['id'] ?? null;
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $icon = $_POST['icon'] ?? '';
    $features = $_POST['features'] ?? '';

    if ($action === 'add') {
        $db->execute("INSERT INTO what_we_do (title, description, icon, features) VALUES (?, ?, ?, ?)", [$title, $description, $icon, $features]);
    } elseif ($action === 'edit' && $id) {
        $db->execute("UPDATE what_we_do SET title = ?, description = ?, icon = ?, features = ? WHERE id = ?", [$title, $description, $icon, $features, $id]);
    } elseif ($action === 'delete' && $id) {
        $db->execute("DELETE FROM what_we_do WHERE id = ?", [$id]);
    }

    header('Location: what-we-do.php');
    exit;
}

// Fetch all entries
$services = $db->fetchAll("SELECT * FROM what_we_do ORDER BY id ASC");
?>

<div class="admin-container">
    <h1>Manage What We Do</h1>

    <button onclick="document.getElementById('addEditModal').style.display = 'block';">Add New</button>

    <table>
        <thead>
            <tr>
                <th>Icon</th>
                <th>Title</th>
                <th>Description</th>
                <th>Features</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($services as $service): ?>
            <tr>
                <td><?php echo htmlspecialchars($service['icon']); ?></td>
                <td><?php echo htmlspecialchars($service['title']); ?></td>
                <td><?php echo htmlspecialchars($service['description']); ?></td>
                <td><?php echo htmlspecialchars($service['features']); ?></td>
                <td>
                    <button onclick="editService(<?php echo htmlspecialchars(json_encode($service)); ?>)">Edit</button>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?php echo $service['id']; ?>">
                        <button type="submit">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div id="addEditModal" style="display:none;">
    <form method="POST">
        <input type="hidden" name="action" value="add">
        <input type="hidden" name="id" id="serviceId">

        <label for="icon">Icon</label>
        <input type="text" name="icon" id="serviceIcon" required>

        <label for="title">Title</label>
        <input type="text" name="title" id="serviceTitle" required>

        <label for="description">Description</label>
        <textarea name="description" id="serviceDescription" required></textarea>

        <label for="features">Features (separated by |)</label>
        <textarea name="features" id="serviceFeatures" required></textarea>

        <button type="submit">Save</button>
        <button type="button" onclick="document.getElementById('addEditModal').style.display = 'none';">Cancel</button>
    </form>
</div>

<script>
function editService(service) {
    document.getElementById('serviceId').value = service.id;
    document.getElementById('serviceIcon').value = service.icon;
    document.getElementById('serviceTitle').value = service.title;
    document.getElementById('serviceDescription').value = service.description;
    document.getElementById('serviceFeatures').value = service.features;
    document.querySelector('[name=action]').value = 'edit';
    document.getElementById('addEditModal').style.display = 'block';
}
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>