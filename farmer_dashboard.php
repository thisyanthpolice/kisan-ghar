<?php
require_once 'header.php';
require_once 'db_connect.php';

// Strict role check
if (!isLoggedIn() || getUserRole() !== 'farmer') {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$section = $_GET['section'] ?? 'products';
$error = '';

// Remove PHP POST add logic, keep only for edit/delete if needed
$error = '';

$products = $pdo->query("SELECT * FROM products WHERE farmer_id = $user_id")->fetchAll();
$orders = $pdo->query("SELECT o.*, oi.quantity, oi.price, p.name as product_name FROM orders o JOIN order_items oi ON o.id = oi.order_id JOIN products p ON oi.product_id = p.id WHERE oi.farmer_id = $user_id")->fetchAll(PDO::FETCH_GROUP);
?>

<div class="container mx-auto p-4 flex">
    <aside class="glass p-4 w-64">
        <h3 class="text-xl font-bold mb-4"><?php echo translate('farmer_dashboard'); ?></h3>
        <ul>
            <li><a href="?section=products" class="glass p-2 block"><?php echo translate('products'); ?></a></li>
            <li><a href="?section=orders" class="glass p-2 block"><?php echo translate('orders'); ?></a></li>
            <li><a href="?section=profile" class="glass p-2 block"><?php echo translate('profile'); ?></a></li>
        </ul>
    </aside>
    <main class="flex-grow glass p-4 ml-4">
        <?php if ($section === 'products'): ?>
            <h2 class="text-2xl font-bold mb-4"><?php echo translate('products'); ?></h2>
            <button onclick="showProductForm()" class="glass p-2 mb-4 bg-green-500 text-white rounded shadow hover:scale-105 transition-transform">+ <?php echo translate('add_product'); ?></button>
            <form method="POST" class="glass p-4 mb-4 hidden" id="productForm">
                <div id="productFormError" class="text-red-500 mb-2 hidden"></div>
                <input type="hidden" name="edit_id" id="edit_id">
                <select name="category" id="category" class="glass p-2 w-full mb-4" required>
                    <option value="">Select Category</option>
                    <option value="vegetables"><?php echo translate('vegetables'); ?></option>
                    <option value="fruits"><?php echo translate('fruits'); ?></option>
                    <option value="nuts"><?php echo translate('nuts'); ?></option>
                    <option value="dairy"><?php echo translate('dairy'); ?></option>
                </select>
                <input type="text" name="name" id="name" placeholder="<?php echo translate('name'); ?>" class="glass p-2 w-full mb-4" required>
                <input type="text" name="image_url" id="image_url" placeholder="<?php echo translate('image_url'); ?>" class="glass p-2 w-full mb-4" required>
                <input type="number" name="quantity" id="quantity" placeholder="<?php echo translate('quantity'); ?>" class="glass p-2 w-full mb-4" required>
                <input type="number" name="price" id="price" placeholder="<?php echo translate('price'); ?>" class="glass p-2 w-full mb-4" required>
                <div class="flex gap-2">
                    <button type="submit" id="submitBtn" class="glass p-2 flex-grow bg-green-500 text-white"><?php echo translate('add_product'); ?></button>
                    <button type="button" onclick="hideProductForm()" class="glass p-2 text-red-500">Cancel</button>
                </div>
            </form>
            <div id="productsGrid" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php $i = 0; foreach ($products as $product): $bgClass = ($i++ % 2 == 0) ? 'grid-blue' : 'grid-green'; ?>
                    <div class="glass p-4 flex flex-col justify-between h-full border border-gray-200 rounded-lg shadow-sm <?php echo $bgClass; ?>">
                        <div>
                            <h3 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="mb-1"><?php echo translate('category'); ?>: <?php echo htmlspecialchars($product['category']); ?></p>
                            <p class="mb-1"><?php echo translate('price'); ?>: ₹<?php echo number_format($product['price'], 2); ?></p>
                            <p class="mb-2"><?php echo translate('quantity'); ?>: <?php echo $product['quantity_available']; ?></p>
                        </div>
                        <div class="flex gap-2 mt-2">
                            <button onclick="editProduct(<?php echo htmlspecialchars(json_encode($product)); ?>)" class="glass p-2 text-blue-500 flex-1">✏️ <?php echo translate('edit'); ?></button>
                            <button onclick="deleteProduct(<?php echo $product['id']; ?>)" class="glass p-2 text-red-500 flex-1">🗑️ <?php echo translate('delete'); ?></button>
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
                        <p><?php echo $item['product_name']; ?> x <?php echo $item['quantity']; ?> - ₹<?php echo $item['price']; ?></p>
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
function showProductForm(editing = false) {
    document.getElementById('productForm').classList.remove('hidden');
    if (!editing) {
        document.getElementById('edit_id').value = '';
        document.getElementById('category').value = '';
        document.getElementById('name').value = '';
        document.getElementById('image_url').value = '';
        document.getElementById('quantity').value = '';
        document.getElementById('price').value = '';
        document.getElementById('submitBtn').textContent = 'Add Product';
    }
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
function hideProductForm() {
    document.getElementById('productForm').classList.add('hidden');
}
// AJAX add product
const productForm = document.getElementById('productForm');
if (productForm) {
    productForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const errorDiv = document.getElementById('productFormError');
        errorDiv.classList.add('hidden');
        errorDiv.textContent = '';
        const data = {
            category: document.getElementById('category').value,
            name: document.getElementById('name').value,
            image_url: document.getElementById('image_url').value,
            quantity: document.getElementById('quantity').value,
            price: document.getElementById('price').value
        };
        try {
            const res = await fetch('add_product_ajax.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await res.json();
            if (result.success) {
                location.reload(); // reload to show new product
            } else {
                errorDiv.textContent = result.error || 'Error adding product';
                errorDiv.classList.remove('hidden');
            }
        } catch (err) {
            errorDiv.textContent = 'Error adding product';
            errorDiv.classList.remove('hidden');
        }
    });
}
function deleteProduct(id) {
    if (confirm('Are you sure you want to delete this product?')) {
        fetch('delete_product.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        })
        .then(res => res.json())
        .then(result => {
            if (result.success) {
                // Remove the product card from the DOM
                const card = document.querySelector('[onclick*="deleteProduct(' + id + '"]')?.closest('div.glass');
                if (card) card.remove();
            } else {
                alert(result.error || 'Failed to delete product');
            }
        });
    }
}
function editProduct(product) {
    document.getElementById('edit_id').value = product.id;
    document.getElementById('category').value = product.category;
    document.getElementById('name').value = product.name;
    document.getElementById('image_url').value = product.image_url;
    document.getElementById('quantity').value = product.quantity_available;
    document.getElementById('price').value = product.price;
    document.getElementById('submitBtn').textContent = 'Update Product';
    showProductForm(true);
}
</script>

<?php require_once 'footer.php'; ?>