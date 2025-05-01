<?php
require_once 'header.php';
require_once 'db_connect.php';

if (!isLoggedIn()) {
    header('Location: login.php');
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("
    SELECT o.*, oi.product_id, oi.health_box_id, oi.quantity, oi.price, 
           p.name as product_name, h.name as health_box_name, 
           o.status, o.created_at
    FROM orders o 
    JOIN order_items oi ON o.id = oi.order_id 
    LEFT JOIN products p ON oi.product_id = p.id 
    LEFT JOIN health_boxes h ON oi.health_box_id = h.id 
    WHERE o.user_id = ?
    ORDER BY o.created_at DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_GROUP);
?>

<div class="container mx-auto p-4">
    <h2 class="text-2xl font-bold mb-4"><?php echo translate('orders'); ?></h2>
    <?php foreach ($orders as $order_id => $items): ?>
        <div class="glass p-4 mb-4">
            <h3><?php echo translate('order'); ?> #<?php echo $order_id; ?></h3>
            <p><?php echo translate('total'); ?>: ₹<?php echo $items[0]['total_price']; ?></p>
            <p><?php echo translate('status'); ?>: 
                <span class="<?php echo $items[0]['status'] === 'delivered' ? 'text-green-500' : 
                    ($items[0]['status'] === 'confirmed' ? 'text-blue-500' : 'text-yellow-500'); ?>">
                    <?php echo ucfirst($items[0]['status']); ?>
                </span>
            </p>
            <p><?php echo translate('ordered_on'); ?>: <?php echo date('F j, Y', strtotime($items[0]['created_at'])); ?></p>
            <?php foreach ($items as $item): ?>
                <div class="pl-4 mt-2">
                    <p><?php echo $item['product_name'] ?: $item['health_box_name']; ?> x <?php echo $item['quantity']; ?> - ₹<?php echo $item['price']; ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</div>

<?php
require_once 'footer.php';
require_once 'sidebar.php';