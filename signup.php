<?php
require_once 'header.php';
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password
    $name = $_POST['name'];
    $phone = $_POST['phone'] ?? null;
    $address = $_POST['address'] ?? null;
    $location = $_POST['location'] ?? null;
    $contact_number = $_POST['contact_number'] ?? null;

    // Check if email already exists
    $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $checkStmt->execute([$email]);
    if ($checkStmt->fetch()) {
        $error = "Email already registered";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (role, email, password, name, phone, address, location, contact_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$role, $email, $password, $name, $phone, $address, $location, $contact_number])) {
            // Set session and redirect based on role
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['role'] = $role;
            
            switch ($role) {
                case 'farmer':
                    header('Location: farmer_dashboard.php');
                    break;
                case 'nutritionist':
                    header('Location: nutritionist_dashboard.php');
                    break;
                default:
                    header('Location: index.php');
            }
            exit();
        } else {
            $error = "Registration failed";
        }
    }
}
?>

<div class="container mx-auto p-4 flex justify-center items-center min-h-screen">
    <div class="glass p-8 w-full max-w-md">
        <h2 class="text-2xl font-bold mb-4 text-center"><?php echo translate('signup'); ?></h2>
        <?php if (isset($error)): ?>
            <p class="text-red-500 mb-4 text-center"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST" class="space-y-4">
            <select name="role" class="glass p-2 w-full" required>
                <option value="customer"><?php echo translate('customer'); ?></option>
                <option value="farmer"><?php echo translate('farmer'); ?></option>
                <option value="nutritionist"><?php echo translate('nutritionist'); ?></option>
            </select>
            <input type="email" name="email" placeholder="<?php echo translate('email'); ?>" class="glass p-2 w-full" required>
            <input type="password" name="password" placeholder="<?php echo translate('password'); ?>" class="glass p-2 w-full" required>
            <input type="text" name="name" placeholder="<?php echo translate('name'); ?>" class="glass p-2 w-full" required>
            <input type="text" name="phone" placeholder="<?php echo translate('phone'); ?>" class="glass p-2 w-full">
            <textarea name="address" placeholder="<?php echo translate('address'); ?>" class="glass p-2 w-full h-24"></textarea>
            <input type="text" name="location" placeholder="<?php echo translate('location'); ?>" class="glass p-2 w-full">
            <input type="text" name="contact_number" placeholder="<?php echo translate('contact_number'); ?>" class="glass p-2 w-full">
            <button type="submit" class="glass p-2 w-full bg-green-500 bg-opacity-20 hover:bg-opacity-30 transition-all">
                <?php echo translate('signup'); ?>
            </button>
        </form>
        <p class="text-center mt-4">
            <?php echo translate('already_have_account'); ?> 
            <a href="login.php" class="text-green-500 hover:text-green-400 transition-colors">
                <?php echo translate('login'); ?>
            </a>
        </p>
    </div>
</div>

<?php require_once 'footer.php'; ?>