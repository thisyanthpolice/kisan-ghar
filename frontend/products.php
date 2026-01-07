<?php
require_once 'header.php';
require_once __DIR__ . '/../backend/db_connect.php';

// Show single product if ID is provided
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT p.*, u.name as farmer_name, u.contact_number as farmer_contact FROM products p JOIN users u ON p.farmer_id = u.id WHERE p.id = ?");
    $stmt->execute([$_GET['id']]);
    $product = $stmt->fetch();
    
    if ($product): ?>
        <div class="container mx-auto p-4">
            <div class="glass p-8 max-w-4xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>" 
                             class="w-full h-96 object-cover rounded-lg">
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold mb-4"><?php echo htmlspecialchars($product['name']); ?></h1>
                        <p class="mb-4"><?php echo translate('by'); ?> 
                            <a href="farmer_profile.php?id=<?php echo $product['farmer_id']; ?>" 
                               class="text-green-500"><?php echo htmlspecialchars($product['farmer_name']); ?></a>
                        </p>
                        <p class="mb-2"><strong><?php echo translate('contact_number'); ?>:</strong> <?php echo htmlspecialchars($product['farmer_contact']); ?></p>
                        <p class="text-2xl mb-4">₹<?php echo number_format($product['price'], 2); ?></p>
                        <p class="mb-4"><?php echo translate('category'); ?>: <?php echo translate($product['category']); ?></p>
                        <div class="flex space-x-4 items-center">
                            <input type="number" min="1" value="1" id="qty-<?php echo $product['id']; ?>" 
                                   class="glass p-2 w-20">
                            <button onclick="addToCart(<?php echo $product['id']; ?>, 'product')" 
                                    class="glass p-4 flex-grow"><?php echo translate('add_to_cart'); ?></button>
                            <button onclick="addToFavorites(<?php echo $product['id']; ?>, 'product')" 
                                    class="glass p-4"><?php echo translate('add_to_favorites'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php 
    else:
        echo '<div class="container mx-auto p-4"><div class="glass p-8 text-center">Product not found</div></div>';
    endif;
    require_once 'footer.php';
    return;
}

// Rest of the existing products listing code...
$category = $_GET['category'] ?? '';
$query = "SELECT p.*, u.name as farmer_name FROM products p JOIN users u ON p.farmer_id = u.id";
if ($category) {
    $query .= " WHERE p.category = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$category]);
} else {
    $stmt = $pdo->query($query);
}
$products = $stmt->fetchAll();
?>

<div class="container mx-auto p-4">
    <div class="flex justify-end mb-4">
        <select onchange="window.location.href=this.value" class="glass p-2">
            <option value="products.php" <?php echo empty($category) ? 'selected' : ''; ?>><?php echo translate('all_categories'); ?></option>
            <option value="?category=vegetables" <?php echo $category === 'vegetables' ? 'selected' : ''; ?>><?php echo translate('vegetables'); ?></option>
            <option value="?category=fruits" <?php echo $category === 'fruits' ? 'selected' : ''; ?>><?php echo translate('fruits'); ?></option>
            <option value="?category=nuts" <?php echo $category === 'nuts' ? 'selected' : ''; ?>><?php echo translate('nuts'); ?></option>
            <option value="?category=dairy" <?php echo $category === 'dairy' ? 'selected' : ''; ?>><?php echo translate('dairy'); ?></option>
        </select>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <?php foreach ($products as $product): ?>
            <div class="glass p-4 flex flex-col justify-between h-full border border-gray-200 rounded-lg shadow-sm">
                <a href="products.php?id=<?php echo $product['id']; ?>" class="block mb-2 focus:outline-none" style="cursor:pointer">
                    <img src="<?php echo $product['image_url']; ?>" alt="<?php echo $product['name']; ?>" class="w-full h-48 object-cover rounded mb-2">
                    <h3 class="text-xl font-semibold mb-1"><?php echo $product['name']; ?></h3>
                </a>
                <a href="farmer_profile.php?id=<?php echo $product['farmer_id']; ?>" class="text-green-500 mb-1" style="cursor:pointer"><?php echo $product['farmer_name']; ?></a>
                <p class="mb-2"><?php echo translate('price'); ?>: ₹<?php echo $product['price']; ?></p>
                <div class="flex items-center space-x-2 mb-2">
                    <input type="number" min="1" value="1" id="qty-<?php echo $product['id']; ?>" class="glass p-2 w-20">
                    <button onclick="addToCart(<?php echo $product['id']; ?>, 'product')" class="glass p-2 cursor-pointer" type="button"><?php echo translate('add_to_cart'); ?></button>
                    <button onclick="addToFavorites(<?php echo $product['id']; ?>, 'product')" class="glass p-2 cursor-pointer" type="button"><?php echo translate('add_to_favorites'); ?></button>
                </div>
                <a href="products.php?id=<?php echo $product['id']; ?>" class="glass px-4 py-2 rounded-lg text-center mt-auto cursor-pointer block" style="cursor:pointer"><?php echo translate('view_details'); ?></a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once 'footer.php';
