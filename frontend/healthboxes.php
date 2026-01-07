<?php
require_once 'header.php';
require_once __DIR__ . '/../backend/db_connect.php';

// Show single health box if ID is provided
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT h.*, u.name as nutritionist_name, u.contact_number as nutritionist_contact FROM health_boxes h JOIN users u ON h.nutritionist_id = u.id WHERE h.id = ?");
    $stmt->execute([$_GET['id']]);
    $box = $stmt->fetch();
    
    if ($box): ?>
        <div class="container mx-auto p-4">
            <div class="glass p-8 max-w-4xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <img src="<?php echo htmlspecialchars($box['image_url']); ?>" 
                             alt="<?php echo htmlspecialchars($box['name']); ?>" 
                             class="w-full h-96 object-cover rounded-lg">
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold mb-4"><?php echo htmlspecialchars($box['name']); ?></h1>
                        <p class="mb-4"><?php echo translate('by'); ?> 
                            <a href="farmer_profile.php?id=<?php echo $box['nutritionist_id']; ?>" 
                               class="text-green-500"><?php echo htmlspecialchars($box['nutritionist_name']); ?></a>
                        </p>
                        <p class="mb-2"><strong><?php echo translate('contact_number'); ?>:</strong> <?php echo htmlspecialchars($box['nutritionist_contact']); ?></p>
                        <p class="text-2xl mb-4">₹<?php echo number_format($box['price'], 2); ?></p>
                        <p class="mb-4"><?php echo htmlspecialchars($box['description']); ?></p>
                        <div class="flex space-x-4 items-center">
                            <input type="number" min="1" value="1" id="qty-<?php echo $box['id']; ?>" 
                                   class="glass p-2 w-20">
                            <button onclick="addToCart(<?php echo $box['id']; ?>, 'health_box')" 
                                    class="glass p-4 flex-grow"><?php echo translate('add_to_cart'); ?></button>
                            <button onclick="addToFavorites(<?php echo $box['id']; ?>, 'health_box')" 
                                    class="glass p-4"><?php echo translate('add_to_favorites'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php 
    else:
        echo '<div class="container mx-auto p-4"><div class="glass p-8 text-center">Health box not found</div></div>';
    endif;
    require_once 'footer.php';
    return;
}

// Rest of the existing health boxes listing code...
$stmt = $pdo->query("SELECT h.*, u.name as nutritionist_name FROM health_boxes h JOIN users u ON h.nutritionist_id = u.id");
$health_boxes = $stmt->fetchAll();
?>

<div class="container mx-auto p-4">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <?php foreach ($health_boxes as $box): ?>
            <div class="glass p-4 flex flex-col justify-between h-full border border-gray-200 rounded-lg shadow-sm">
                <a href="healthboxes.php?id=<?php echo $box['id']; ?>" class="block mb-2 focus:outline-none" style="cursor:pointer">
                    <img src="<?php echo $box['image_url']; ?>" alt="<?php echo $box['name']; ?>" class="w-full h-48 object-cover rounded mb-2">
                    <h3 class="text-xl font-semibold mb-1"><?php echo $box['name']; ?></h3>
                </a>
                <a href="farmer_profile.php?id=<?php echo $box['nutritionist_id']; ?>" class="text-green-500 mb-1" style="cursor:pointer"><?php echo $box['nutritionist_name']; ?></a>
                <p class="mb-2"><?php echo translate('price'); ?>: ₹<?php echo $box['price']; ?></p>
                <div class="flex items-center space-x-2 mb-2">
                    <input type="number" min="1" value="1" id="qty-<?php echo $box['id']; ?>" class="glass p-2 w-20">
                    <button onclick="addToCart(<?php echo $box['id']; ?>, 'health_box')" class="glass p-2 cursor-pointer" type="button"><?php echo translate('add_to_cart'); ?></button>
                    <button onclick="addToFavorites(<?php echo $box['id']; ?>, 'health_box')" class="glass p-2 cursor-pointer" type="button"><?php echo translate('add_to_favorites'); ?></button>
                </div>
                <a href="healthboxes.php?id=<?php echo $box['id']; ?>" class="glass px-4 py-2 rounded-lg text-center mt-auto cursor-pointer block" style="cursor:pointer"><?php echo translate('view_details'); ?></a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once 'footer.php';
