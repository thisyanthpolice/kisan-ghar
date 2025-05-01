<?php
require_once 'header.php';
require_once 'db_connect.php';

$user_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role IN ('farmer', 'nutritionist')");
$stmt->execute([$user_id]);
$profile = $stmt->fetch();

if (!$profile) {
    header('Location: index.php');
}
?>

<div class="container mx-auto p-4">
    <h2 class="text-2xl font-bold mb-4"><?php echo $profile['name']; ?>'s <?php echo translate('profile'); ?></h2>
    <div class="glass p-8 max-w-md mx-auto">
        <p><strong><?php echo translate('name'); ?>:</strong> <?php echo $profile['name']; ?></p>
        <p><strong><?php echo translate('location'); ?>:</strong> <?php echo $profile['location']; ?></p>
        <p><strong><?php echo translate('address'); ?>:</strong> <?php echo $profile['address']; ?></p>
        <p><strong><?php echo translate('contact_number'); ?>:</strong> <?php echo $profile['contact_number']; ?></p>
    </div>
</div>

<?php require_once 'footer.php'; ?>
<?php require_once 'sidebar.php'; ?>