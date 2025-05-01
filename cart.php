<?php
require_once 'header.php';
require_once 'db_connect.php';

if (!isLoggedIn()) {
    header('Location: login.php');
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("
    SELECT c.*, p.name as product_name, p.price as product_price, h.name as health_box_name, h.price as health_box_price
    FROM cart c
    LEFT JOIN products p ON c.product_id = p.id
    LEFT JOIN health_boxes h ON c.health_box_id = h.id
    WHERE c.user_id = ?
");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();

$total = 0;
?>

<div class="container mx-auto p-4">
    <h2 class="text-2xl font-bold mb-4"><?php echo translate('cart'); ?></h2>
    <div class="glass p-4">
        <?php foreach ($cart_items as $item): ?>
            <div class="flex justify-between mb-4">
                <div>
                    <h3><?php echo $item['product_name'] ?: $item['health_box_name']; ?></h3>
                    <p><?php echo translate('quantity'); ?>: <?php echo $item['quantity']; ?></p>
                    <p><?php echo translate('price'); ?>: ₹<?php echo $item['product_price'] ?: $item['health_box_price']; ?></p>
                </div>
                <button onclick="removeFromCart(<?php echo $item['id']; ?>)" class="glass p-2"><?php echo translate('remove'); ?></button>
            </div>
            <?php $total += ($item['product_price'] ?: $item['health_box_price']) * $item['quantity']; ?>
        <?php endforeach; ?>
        <div class="flex justify-between">
            <h3><?php echo translate('total'); ?>: ₹<?php echo $total; ?></h3>
            <a href="checkout.php" class="glass p-2"><?php echo translate('proceed_to_checkout'); ?></a>
        </div>
    </div>
</div>

<script>
function removeFromCart(cartId) {
    fetch('remove_from_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ cart_id: cartId })
    }).then(() => location.reload());
}
</script>

<?php require_once 'footer.php';
require_once 'sidebar.php';