<?php
require_once 'header.php';
require_once 'db_connect.php';

// Strict role check
if (!isLoggedIn() || getUserRole() !== 'nutritionist') {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$section = $_GET['section'] ?? 'health_boxes';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $section === 'health_boxes') {
    $edit_id = $_POST['edit_id'] ?? null;
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $image_url = trim($_POST['image_url'] ?? '');
    $price = trim($_POST['price'] ?? '');
    if (!$name || !$description || !$image_url || !$price) {
        $error = 'All fields are required.';
    } else {
        if ($edit_id) {
            $stmt = $pdo->prepare("UPDATE health_boxes SET name = ?, description = ?, image_url = ?, price = ? WHERE id = ? AND nutritionist_id = ?");
            $stmt->execute([$name, $description, $image_url, $price, $edit_id, $user_id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO health_boxes (nutritionist_id, name, description, image_url, price) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $name, $description, $image_url, $price]);
        }
        header('Location: nutritionist_dashboard.php');
        exit();
    }
}

$health_boxes = $pdo->query("SELECT * FROM health_boxes WHERE nutritionist_id = $user_id")->fetchAll();
$orders = $pdo->query("SELECT o.*, oi.quantity, oi.price, h.name as health_box_name FROM orders o JOIN order_items oi ON o.id = oi.order_id JOIN health_boxes h ON oi.health_box_id = h.id WHERE oi.nutritionist_id = $user_id")->fetchAll(PDO::FETCH_GROUP);
?>

<div class="container mx-auto p-4 flex">
    <aside class="glass p-4 w-64">
        <h3 class="text-xl font-bold mb-4"><?php echo translate('nutritionist_dashboard'); ?></h3>
        <ul>
            <li><a href="?section=health_boxes" class="glass p-2 block"><?php echo translate('health_boxes'); ?></a></li>
            <li><a href="?section=orders" class="glass p-2 block"><?php echo translate('orders'); ?></a></li>
            <li><a href="?section=profile" class="glass p-2 block"><?php echo translate('profile'); ?></a></li>
        </ul>
    </aside>
    <main class="flex-grow glass p-4 ml-4">
        <?php if ($section === 'health_boxes'): ?>
            <h2 class="text-2xl font-bold mb-4"><?php echo translate('health_boxes'); ?></h2>
            <button onclick="showHealthBoxForm()" class="glass p-2 mb-4 bg-green-500 text-white rounded shadow hover:scale-105 transition-transform">+ <?php echo translate('add_health_box'); ?></button>
            <form method="POST" class="glass p-4 mb-4<?php if (!empty($error) || $_SERVER['REQUEST_METHOD'] === 'POST') echo ''; else echo ' hidden'; ?>" id="healthBoxForm">
                <?php if (!empty($error)): ?>
                    <div class="text-red-500 mb-2"><?php echo $error; ?></div>
                <?php endif; ?>
                <input type="hidden" name="edit_id" id="edit_id" value="<?php echo htmlspecialchars($_POST['edit_id'] ?? ''); ?>">
                <input type="text" name="name" id="name" placeholder="<?php echo translate('name'); ?>" class="glass p-2 w-full mb-4" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
                <textarea name="description" id="description" placeholder="<?php echo translate('description'); ?>" class="glass p-2 w-full mb-4" required><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                <input type="text" name="image_url" id="image_url" placeholder="<?php echo translate('image_url'); ?>" class="glass p-2 w-full mb-4" value="<?php echo htmlspecialchars($_POST['image_url'] ?? ''); ?>" required>
                <input type="number" name="price" id="price" placeholder="<?php echo translate('price'); ?>" class="glass p-2 w-full mb-4" value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>" required>
                <div class="flex gap-2">
                    <button type="submit" id="submitBtn" class="glass p-2 flex-grow bg-green-500 text-white"><?php echo translate('add_health_box'); ?></button>
                    <button type="button" onclick="hideHealthBoxForm()" class="glass p-2 text-red-500">Cancel</button>
                </div>
            </form>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php $i = 0; foreach ($health_boxes as $box): $bgClass = ($i++ % 2 == 0) ? 'grid-blue' : 'grid-green'; ?>
                    <div class="glass p-4 flex flex-col justify-between h-full border border-gray-200 rounded-lg shadow-sm <?php echo $bgClass; ?>">
                        <div>
                            <h3 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($box['name']); ?></h3>
                            <p class="text-sm mb-2"><?php echo htmlspecialchars($box['description']); ?></p>
                            <p><?php echo translate('price'); ?>: ₹<?php echo number_format($box['price'], 2); ?></p>
                        </div>
                        <div class="flex gap-2 mt-2">
                            <button onclick="editHealthBox(<?php echo htmlspecialchars(json_encode($box)); ?>)" class="glass p-2 text-blue-500 flex-1">✏️ <?php echo translate('edit'); ?></button>
                            <button onclick="deleteHealthBox(<?php echo $box['id']; ?>)" class="glass p-2 text-red-500 flex-1">🗑️ <?php echo translate('delete'); ?></button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php elseif ($section === 'orders'): ?>
            <h2 class="text-2xl font-bold mb-4"><?php echo translate('orders'); ?></h2>
            <?php foreach ($orders as $order_id => $items): ?>
                <div class="glass p-4 mb-4">
                    <h3><?php echo translate('order'); ?> #<?php echo $order_id; ?></h3>
                    <?php foreach ($items as $item): ?>
                        <p><?php echo $item['health_box_name']; ?> x <?php echo $item['quantity']; ?> - ₹<?php echo $item['price']; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        <?php elseif ($section === 'profile'): ?>
            <h2 class="text-2xl font-bold mb-4"><?php echo translate('profile'); ?></h2>
            <?php include 'profile.php'; ?>
        <?php endif; ?>
    </main>
</div>

<script>
function showHealthBoxForm(editing = false) {
    document.getElementById('healthBoxForm').classList.remove('hidden');
    if (!editing) {
        document.getElementById('edit_id').value = '';
        document.getElementById('name').value = '';
        document.getElementById('description').value = '';
        document.getElementById('image_url').value = '';
        document.getElementById('price').value = '';
        document.getElementById('submitBtn').textContent = 'Add Health Box';
    }
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
function hideHealthBoxForm() {
    document.getElementById('healthBoxForm').classList.add('hidden');
}
// AJAX add/edit health box
const healthBoxForm = document.getElementById('healthBoxForm');
if (healthBoxForm) {
    healthBoxForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const errorDiv = document.getElementById('healthBoxFormError');
        if (errorDiv) { errorDiv.classList.add('hidden'); errorDiv.textContent = ''; }
        const editId = document.getElementById('edit_id').value;
        const data = {
            id: editId,
            name: document.getElementById('name').value,
            description: document.getElementById('description').value,
            image_url: document.getElementById('image_url').value,
            price: document.getElementById('price').value
        };
        try {
            const url = editId ? 'edit_health_box_ajax.php' : 'add_health_box_ajax.php';
            const res = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await res.json();
            if (result.success) {
                location.reload(); // reload to show new/edited health box
            } else {
                if (errorDiv) { errorDiv.textContent = result.error || 'Error saving health box'; errorDiv.classList.remove('hidden'); }
            }
        } catch (err) {
            if (errorDiv) { errorDiv.textContent = 'Error saving health box'; errorDiv.classList.remove('hidden'); }
        }
    });
}
function editHealthBox(box) {
    document.getElementById('edit_id').value = box.id;
    document.getElementById('name').value = box.name;
    document.getElementById('description').value = box.description;
    document.getElementById('image_url').value = box.image_url;
    document.getElementById('price').value = box.price;
    document.getElementById('submitBtn').textContent = 'Update Health Box';
    showHealthBoxForm(true);
}
function deleteHealthBox(id) {
    if (confirm('Are you sure you want to delete this health box?')) {
        fetch('delete_health_box.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        })
        .then(res => res.json())
        .then(result => {
            if (result.success) {
                // Remove the health box card from the DOM
                const card = document.querySelector('[onclick*="deleteHealthBox(' + id + '"]')?.closest('div.glass');
                if (card) card.remove();
            } else {
                alert(result.error || 'Failed to delete health box');
            }
        });
    }
}
</script>

<?php require_once 'footer.php'; ?>