<?php
require_once 'header.php';
require_once 'db_connect.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Get favorite products
$stmt = $pdo->prepare("
    SELECT p.*, 'product' as type, u.name as farmer_name 
    FROM favorites f 
    JOIN products p ON f.product_id = p.id 
    JOIN users u ON p.farmer_id = u.id
    WHERE f.user_id = ? AND f.product_id IS NOT NULL
");
$stmt->execute([$user_id]);
$favorite_products = $stmt->fetchAll();

// Get favorite health boxes
$stmt = $pdo->prepare("
    SELECT h.*, 'health_box' as type, u.name as nutritionist_name 
    FROM favorites f 
    JOIN health_boxes h ON f.health_box_id = h.id 
    JOIN users u ON h.nutritionist_id = u.id
    WHERE f.user_id = ? AND f.health_box_id IS NOT NULL
");
$stmt->execute([$user_id]);
$favorite_health_boxes = $stmt->fetchAll();
?>

<div class="container mx-auto p-4">
    <h2 class="text-2xl font-bold mb-6"><?php echo translate('favorites'); ?></h2>

    <?php if (empty($favorite_products) && empty($favorite_health_boxes)): ?>
        <div class="glass p-8 text-center">
            <p class="text-xl"><?php echo translate('no_favorites'); ?></p>
            <div class="mt-4 space-x-4">
                <a href="products.php" class="glass px-4 py-2 inline-block hover:scale-105 transition-transform">
                    <?php echo translate('explore_products'); ?>
                </a>
                <a href="healthboxes.php" class="glass px-4 py-2 inline-block hover:scale-105 transition-transform">
                    <?php echo translate('explore_health_boxes'); ?>
                </a>
            </div>
        </div>
    <?php else: ?>
        <?php if (!empty($favorite_products)): ?>
            <h3 class="text-xl font-semibold mb-4"><?php echo translate('favorite_products'); ?></h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <?php foreach ($favorite_products as $product): ?>
                    <div class="glass p-4 flex flex-col justify-between h-full border border-gray-200 rounded-lg shadow-sm" id="product-<?php echo $product['id']; ?>">
                        <div class="relative">
                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                 class="w-full h-48 object-cover rounded-lg mb-4">
                            <button onclick="removeFromFavorites(<?php echo $product['id']; ?>, 'product')" 
                                    class="absolute top-2 right-2 glass p-2 rounded-full hover:scale-110 transition-transform"
                                    title="<?php echo translate('remove_from_favorites'); ?>">
                                ❌
                            </button>
                        </div>
                        <h3 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="text-sm mb-2"><?php echo translate('by'); ?> <?php echo htmlspecialchars($product['farmer_name']); ?></p>
                        <p class="mb-4">₹<?php echo number_format($product['price'], 2); ?></p>
                        <div class="flex space-x-2">
                            <input type="number" min="1" value="1" id="qty-<?php echo $product['id']; ?>" class="glass p-2 w-20">
                            <button onclick="addToCart(<?php echo $product['id']; ?>, 'product')" class="glass p-2 flex-grow cursor-pointer">
                                <?php echo translate('add_to_cart'); ?>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($favorite_health_boxes)): ?>
            <h3 class="text-xl font-semibold mb-4"><?php echo translate('favorite_health_boxes'); ?></h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <?php foreach ($favorite_health_boxes as $box): ?>
                    <div class="glass p-4 flex flex-col justify-between h-full border border-gray-200 rounded-lg shadow-sm" id="health-box-<?php echo $box['id']; ?>">
                        <div class="relative">
                            <img src="<?php echo htmlspecialchars($box['image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($box['name']); ?>" 
                                 class="w-full h-48 object-cover rounded-lg mb-4">
                            <button onclick="removeFromFavorites(<?php echo $box['id']; ?>, 'health_box')" 
                                    class="absolute top-2 right-2 glass p-2 rounded-full hover:scale-110 transition-transform"
                                    title="<?php echo translate('remove_from_favorites'); ?>">
                                ❌
                            </button>
                        </div>
                        <h3 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($box['name']); ?></h3>
                        <p class="text-sm mb-2"><?php echo translate('by'); ?> <?php echo htmlspecialchars($box['nutritionist_name']); ?></p>
                        <p class="mb-4">₹<?php echo number_format($box['price'], 2); ?></p>
                        <div class="flex space-x-2">
                            <input type="number" min="1" value="1" id="qty-<?php echo $box['id']; ?>" class="glass p-2 w-20">
                            <button onclick="addToCart(<?php echo $box['id']; ?>, 'health_box')" class="glass p-2 flex-grow cursor-pointer">
                                <?php echo translate('add_to_cart'); ?>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
async function removeFromFavorites(id, type) {
    try {
        const response = await fetch('remove_from_favorites.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id, type })
        });
        
        if (response.ok) {
            // Remove the item from the UI
            const element = document.getElementById(`${type}-${id}`);
            element.classList.add('scale-0', 'opacity-0');
            setTimeout(() => {
                element.remove();
                // Check if there are no more items
                const products = document.querySelectorAll('[id^="product-"]');
                const boxes = document.querySelectorAll('[id^="health-box-"]');
                if (products.length === 0 && boxes.length === 0) {
                    location.reload(); // Reload to show empty state
                }
            }, 300);
        }
    } catch (error) {
        console.error('Error removing from favorites:', error);
        alert('Failed to remove from favorites. Please try again.');
    }
}
</script>

<?php require_once 'footer.php';
require_once 'sidebar.php';