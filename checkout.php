<?php
require_once 'header.php';
require_once 'db_connect.php';

if (!isLoggedIn()) {
    header('Location: login.php');
}

$user_id = $_SESSION['user_id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = $_POST['address'];
    $payment_method = 'cod';

    $stmt = $pdo->prepare("SELECT c.*, p.price as product_price, h.price as health_box_price, p.farmer_id, h.nutritionist_id FROM cart c LEFT JOIN products p ON c.product_id = p.id LEFT JOIN health_boxes h ON c.health_box_id = h.id WHERE c.user_id = ?");
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll();

    $total = 0;
    foreach ($cart_items as $item) {
        $total += ($item['product_price'] ?: $item['health_box_price']) * $item['quantity'];
    }

    $pdo->beginTransaction();
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_price, address, payment_method) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $total, $address, $payment_method]);
    $order_id = $pdo->lastInsertId();

    foreach ($cart_items as $item) {
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, health_box_id, quantity, price, farmer_id, nutritionist_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$order_id, $item['product_id'], $item['health_box_id'], $item['quantity'], $item['product_price'] ?: $item['health_box_price'], $item['farmer_id'], $item['nutritionist_id']]);
    }

    $pdo->prepare("DELETE FROM cart WHERE user_id = ?")->execute([$user_id]);
    $pdo->commit();
    header('Location: orders.php');
}
?>

<div class="container mx-auto p-4">
    <h2 class="text-2xl font-bold mb-4"><?php echo translate('checkout'); ?></h2>
    <div class="glass p-8 max-w-md mx-auto">
        <form method="POST">
            <textarea name="address" placeholder="<?php echo translate('address'); ?>" class="glass p-2 w-full mb-4" required></textarea>
            <select name="payment_method" class="glass p-2 w-full mb-4">
                <option value="cod"><?php echo translate('cod'); ?></option>
            </select>
            <button type="submit" class="glass p-2 w-full"><?php echo translate('place_order'); ?></button>
        </form>
    </div>
</div>

<?php require_once 'footer.php';
require_once 'sidebar.php'; ?>