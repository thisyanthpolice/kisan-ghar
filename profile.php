<?php
require_once 'header.php';
require_once 'db_connect.php';

if (!isLoggedIn()) {
    header('Location: login.php');
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $location = $_POST['location'];
    $contact_number = $_POST['contact_number'];

    $stmt = $pdo->prepare("UPDATE users SET name = ?, phone = ?, address = ?, location = ?, contact_number = ? WHERE id = ?");
    $stmt->execute([$name, $phone, $address, $location, $contact_number, $user_id]);
    header('Location: profile.php');
}
?>

<div class="container mx-auto p-4">
    <h2 class="text-2xl font-bold mb-4"><?php echo translate('profile'); ?></h2>
    <div class="glass p-8 max-w-md mx-auto">
        <form method="POST">
            <input type="text" name="name" value="<?php echo $user['name']; ?>" placeholder="<?php echo translate('name'); ?>" class="glass p-2 w-full mb-4" required>
            <input type="text" name="phone" value="<?php echo $user['phone']; ?>" placeholder="<?php echo translate('phone'); ?>" class="glass p-2 w-full mb-4">
            <textarea name="address" placeholder="<?php echo translate('address'); ?>" class="glass p-2 w-full mb-4"><?php echo $user['address']; ?></textarea>
            <input type="text" name="location" value="<?php echo $user['location']; ?>" placeholder="<?php echo translate('location'); ?>" class="glass p-2 w-full mb-4">
            <input type="text" name="contact_number" value="<?php echo $user['contact_number']; ?>" placeholder="<?php echo translate('contact_number'); ?>" class="glass p-2 w-full mb-4">
            <button type="submit" class="glass p-2 w-full"><?php echo translate('update'); ?></button>
        </form>
    </div>
</div>

<?php require_once 'footer.php'; ?>
<?php require_once 'sidebar.php'; ?>