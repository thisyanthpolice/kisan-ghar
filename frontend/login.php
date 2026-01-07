<?php
require_once 'header.php';
require_once __DIR__ . '/../backend/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // For the admin user with non-hashed password in the database
    if ($user && $user['role'] === 'admin' && $user['password'] === 'thisyanth@123') {
        // Update the admin's password to be hashed for future logins
        $hashedPassword = password_hash('thisyanth@123', PASSWORD_DEFAULT);
        $updateStmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $updateStmt->execute([$hashedPassword, $user['id']]);
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        header('Location: admin_dashboard.php');
        exit();
    }
    // For all other users or admin after password has been hashed
    else if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        
        // Role-based redirections
        switch ($user['role']) {
            case 'admin':
                header('Location: admin_dashboard.php');
                break;
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
        $error = "Invalid credentials";
    }
}
?>

<div class="container mx-auto p-4 flex justify-center items-center min-h-screen">
    <div class="glass p-8 w-full max-w-md">
        <h2 class="text-2xl font-bold mb-4 text-center"><?php echo translate('login'); ?></h2>
        <?php if (isset($error)): ?>
            <p class="text-red-500 mb-4 text-center"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="email" placeholder="<?php echo translate('email_or_phone'); ?>" class="glass p-2 w-full mb-4" required>
            <input type="password" name="password" placeholder="<?php echo translate('password'); ?>" class="glass p-2 w-full mb-4" required>
            <button type="submit" class="glass p-2 w-full"><?php echo translate('login'); ?></button>
        </form>
        <p class="text-center mt-4"><?php echo translate('new_here'); ?> <a href="signup.php" class="text-green-500"><?php echo translate('signup'); ?></a></p>
    </div>
</div>

<?php require_once 'footer.php'; ?>