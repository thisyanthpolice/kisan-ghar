<?php
require_once 'header.php';
require_once 'db_connect.php';

// Strict role check
if (!isLoggedIn() || getUserRole() !== 'admin') {
    header('Location: login.php');
    exit();
}

$section = $_GET['section'] ?? 'products';

// Handle POST requests for updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($section === 'products' && isset($_POST['edit_id'])) {
        $stmt = $pdo->prepare("UPDATE products SET category = ?, name = ?, image_url = ?, quantity_available = ?, price = ? WHERE id = ?");
        $stmt->execute([$_POST['category'], $_POST['name'], $_POST['image_url'], $_POST['quantity'], $_POST['price'], $_POST['edit_id']]);
    } elseif ($section === 'health_boxes' && isset($_POST['edit_id'])) {
        $stmt = $pdo->prepare("UPDATE health_boxes SET name = ?, description = ?, image_url = ?, price = ? WHERE id = ?");
        $stmt->execute([$_POST['name'], $_POST['description'], $_POST['image_url'], $_POST['price'], $_POST['edit_id']]);
    }
    header('Location: admin_dashboard.php?section=' . $section);
    exit();
}

// Get data based on section
if ($section === 'products') {
    $products = $pdo->query("SELECT p.*, u.name as farmer_name FROM products p JOIN users u ON p.farmer_id = u.id")->fetchAll();
} elseif ($section === 'health_boxes') {
    $health_boxes = $pdo->query("SELECT h.*, u.name as nutritionist_name FROM health_boxes h JOIN users u ON h.nutritionist_id = u.id")->fetchAll();
} elseif ($section === 'users') {
    $farmers = $pdo->query("SELECT * FROM users WHERE role = 'farmer'")->fetchAll();
    $nutritionists = $pdo->query("SELECT * FROM users WHERE role = 'nutritionist'")->fetchAll();
    $customers = $pdo->query("SELECT * FROM users WHERE role = 'customer'")->fetchAll();
}
?>

<div class="container mx-auto p-4 flex">
    <aside class="glass p-4 w-64">
        <h3 class="text-xl font-bold mb-4"><?php echo translate('admin_dashboard'); ?></h3>
        <ul>
            <li><a href="?section=products" class="glass p-2 block mb-2"><?php echo translate('products'); ?></a></li>
            <li><a href="?section=health_boxes" class="glass p-2 block mb-2"><?php echo translate('health_boxes'); ?></a></li>
            <li><a href="?section=users" class="glass p-2 block"><?php echo translate('users'); ?></a></li>
        </ul>
    </aside>
    <main class="flex-grow glass p-4 ml-4">
        <?php if ($section === 'products'): ?>
            <h2 class="text-2xl font-bold mb-4"><?php echo translate('products'); ?></h2>
            <form method="POST" id="productForm" class="glass p-4 mb-4 hidden">
                <input type="hidden" name="edit_id" id="product_edit_id">
                <select name="category" id="product_category" class="glass p-2 w-full mb-4" required>
                    <option value="vegetables"><?php echo translate('vegetables'); ?></option>
                    <option value="fruits"><?php echo translate('fruits'); ?></option>
                    <option value="nuts"><?php echo translate('nuts'); ?></option>
                    <option value="dairy"><?php echo translate('dairy'); ?></option>
                </select>
                <input type="text" name="name" id="product_name" placeholder="<?php echo translate('name'); ?>" class="glass p-2 w-full mb-4" required>
                <input type="text" name="image_url" id="product_image_url" placeholder="<?php echo translate('image_url'); ?>" class="glass p-2 w-full mb-4" required>
                <input type="number" name="quantity" id="product_quantity" placeholder="<?php echo translate('quantity'); ?>" class="glass p-2 w-full mb-4" required>
                <input type="number" name="price" id="product_price" placeholder="<?php echo translate('price'); ?>" class="glass p-2 w-full mb-4" required>
                <div class="flex gap-2">
                    <button type="submit" class="glass p-2 flex-grow"><?php echo translate('update_product'); ?></button>
                    <button type="button" onclick="cancelEdit('productForm')" class="glass p-2 text-red-500"><?php echo translate('cancel'); ?></button>
                </div>
            </form>
            <div class="grid grid-cols-1 gap-4">
                <?php foreach ($products as $product): ?>
                    <div class="glass p-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-xl font-semibold"><?php echo htmlspecialchars($product['name']); ?></h3>
                                <p class="text-sm"><?php echo translate('by'); ?> <?php echo htmlspecialchars($product['farmer_name']); ?></p>
                                <p><?php echo translate('price'); ?>: ₹<?php echo number_format($product['price'], 2); ?></p>
                                <p><?php echo translate('category'); ?>: <?php echo translate($product['category']); ?></p>
                                <p><?php echo translate('quantity'); ?>: <?php echo $product['quantity_available']; ?></p>
                            </div>
                            <div class="flex gap-2">
                                <button onclick="editProduct(<?php echo htmlspecialchars(json_encode($product)); ?>)" class="glass p-2 text-blue-500">
                                    <?php echo translate('edit'); ?>
                                </button>
                                <button onclick="deleteProduct(<?php echo $product['id']; ?>)" class="glass p-2 text-red-500">
                                    <?php echo translate('delete'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php elseif ($section === 'health_boxes'): ?>
            <h2 class="text-2xl font-bold mb-4"><?php echo translate('health_boxes'); ?></h2>
            <form method="POST" id="healthBoxForm" class="glass p-4 mb-4 hidden">
                <input type="hidden" name="edit_id" id="box_edit_id">
                <input type="text" name="name" id="box_name" placeholder="<?php echo translate('name'); ?>" class="glass p-2 w-full mb-4" required>
                <textarea name="description" id="box_description" placeholder="<?php echo translate('description'); ?>" class="glass p-2 w-full mb-4" required></textarea>
                <input type="text" name="image_url" id="box_image_url" placeholder="<?php echo translate('image_url'); ?>" class="glass p-2 w-full mb-4" required>
                <input type="number" name="price" id="box_price" placeholder="<?php echo translate('price'); ?>" class="glass p-2 w-full mb-4" required>
                <div class="flex gap-2">
                    <button type="submit" class="glass p-2 flex-grow"><?php echo translate('update_health_box'); ?></button>
                    <button type="button" onclick="cancelEdit('healthBoxForm')" class="glass p-2 text-red-500"><?php echo translate('cancel'); ?></button>
                </div>
            </form>
            <div class="grid grid-cols-1 gap-4">
                <?php foreach ($health_boxes as $box): ?>
                    <div class="glass p-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-xl font-semibold"><?php echo htmlspecialchars($box['name']); ?></h3>
                                <p class="text-sm"><?php echo translate('by'); ?> <?php echo htmlspecialchars($box['nutritionist_name']); ?></p>
                                <p><?php echo translate('price'); ?>: ₹<?php echo number_format($box['price'], 2); ?></p>
                                <p class="text-sm"><?php echo htmlspecialchars($box['description']); ?></p>
                            </div>
                            <div class="flex gap-2">
                                <button onclick="editHealthBox(<?php echo htmlspecialchars(json_encode($box)); ?>)" class="glass p-2 text-blue-500">
                                    <?php echo translate('edit'); ?>
                                </button>
                                <button onclick="deleteHealthBox(<?php echo $box['id']; ?>)" class="glass p-2 text-red-500">
                                    <?php echo translate('delete'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php elseif ($section === 'users'): ?>
            <h2 class="text-2xl font-bold mb-6"><?php echo translate('users'); ?></h2>
            
            <div class="mb-8">
                <h3 class="text-xl font-semibold mb-4"><?php echo translate('farmers'); ?></h3>
                <div class="grid grid-cols-1 gap-4">
                    <?php foreach ($farmers as $user): ?>
                        <div class="glass p-4">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="font-semibold"><?php echo htmlspecialchars($user['name']); ?></h4>
                                    <p class="text-sm"><?php echo htmlspecialchars($user['email']); ?></p>
                                    <p class="text-sm"><?php echo htmlspecialchars($user['location']); ?></p>
                                </div>
                                <button onclick="deleteUser(<?php echo $user['id']; ?>)" class="glass p-2 text-red-500">
                                    <?php echo translate('delete'); ?>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="mb-8">
                <h3 class="text-xl font-semibold mb-4"><?php echo translate('nutritionists'); ?></h3>
                <div class="grid grid-cols-1 gap-4">
                    <?php foreach ($nutritionists as $user): ?>
                        <div class="glass p-4">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="font-semibold"><?php echo htmlspecialchars($user['name']); ?></h4>
                                    <p class="text-sm"><?php echo htmlspecialchars($user['email']); ?></p>
                                    <p class="text-sm"><?php echo htmlspecialchars($user['location']); ?></p>
                                </div>
                                <button onclick="deleteUser(<?php echo $user['id']; ?>)" class="glass p-2 text-red-500">
                                    <?php echo translate('delete'); ?>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div>
                <h3 class="text-xl font-semibold mb-4"><?php echo translate('customers'); ?></h3>
                <div class="grid grid-cols-1 gap-4">
                    <?php foreach ($customers as $user): ?>
                        <div class="glass p-4">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="font-semibold"><?php echo htmlspecialchars($user['name']); ?></h4>
                                    <p class="text-sm"><?php echo htmlspecialchars($user['email']); ?></p>
                                    <p class="text-sm"><?php echo htmlspecialchars($user['location']); ?></p>
                                </div>
                                <button onclick="deleteUser(<?php echo $user['id']; ?>)" class="glass p-2 text-red-500">
                                    <?php echo translate('delete'); ?>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </main>
</div>

<script>
function editProduct(product) {
    document.getElementById('productForm').classList.remove('hidden');
    document.getElementById('product_edit_id').value = product.id;
    document.getElementById('product_category').value = product.category;
    document.getElementById('product_name').value = product.name;
    document.getElementById('product_image_url').value = product.image_url;
    document.getElementById('product_quantity').value = product.quantity_available;
    document.getElementById('product_price').value = product.price;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function editHealthBox(box) {
    document.getElementById('healthBoxForm').classList.remove('hidden');
    document.getElementById('box_edit_id').value = box.id;
    document.getElementById('box_name').value = box.name;
    document.getElementById('box_description').value = box.description;
    document.getElementById('box_image_url').value = box.image_url;
    document.getElementById('box_price').value = box.price;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function cancelEdit(formId) {
    document.getElementById(formId).classList.add('hidden');
    document.getElementById(formId).reset();
}

function deleteProduct(id) {
    if (confirm('<?php echo translate('confirm_delete'); ?>')) {
        fetch('delete_product.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        }).then(() => location.reload());
    }
}

function deleteHealthBox(id) {
    if (confirm('<?php echo translate('confirm_delete'); ?>')) {
        fetch('delete_health_box.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        }).then(() => location.reload());
    }
}

function deleteUser(id) {
    if (confirm('<?php echo translate('confirm_delete'); ?>')) {
        fetch('delete_user.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        }).then(() => location.reload());
    }
}
</script>

<?php require_once 'footer.php'; ?>