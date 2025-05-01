<?php
require_once 'functions.php';
$lang_file = getLanguage() . '.php';
if (file_exists($lang_file)) {
    require_once $lang_file;
} else {
    require_once 'en.php';
}
?>
<!DOCTYPE html>
<html lang="<?php echo getLanguage(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kisan Ghar</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <script src="script.js" defer></script>
</head>
<body class="min-h-screen transition-colors duration-300">
    <nav class="glass sticky top-0 z-50 p-4 flex justify-between items-center backdrop-blur-md">
        <div class="flex items-center gap-2">
            <img src="image copy 2.png" alt="Kisan Ghar Logo" class="h-8 w-8 rounded-full object-cover">
            <a href="index.php" class="text-2xl font-bold">Kisan Ghar</a>
        </div>
        <div class="flex items-center space-x-4">
            <button id="theme-toggle" class="glass p-2 rounded-full hover:scale-110 transition-transform" aria-label="Toggle Theme">
                🌙
            </button>
            <select id="lang-select" class="glass p-2 rounded-lg cursor-pointer hover:scale-105 transition-transform">
                <option value="en" <?php echo getLanguage() === 'en' ? 'selected' : ''; ?>>English</option>
                <option value="ta" <?php echo getLanguage() === 'ta' ? 'selected' : ''; ?>>தமிழ்</option>
                <option value="te" <?php echo getLanguage() === 'te' ? 'selected' : ''; ?>>తెలుగు</option>
                <option value="ml" <?php echo getLanguage() === 'ml' ? 'selected' : ''; ?>>മലയാളം</option>
                <option value="kn" <?php echo getLanguage() === 'kn' ? 'selected' : ''; ?>>ಕನ್ನಡ</option>
            </select>
            <?php if (isLoggedIn()): ?>
                <a href="nutrition_coach.php" class="glass p-2 rounded-lg hover:scale-105 transition-transform">
                    <?php echo translate('nutrition_coach'); ?>
                </a>
                <?php if (getUserRole() === 'customer'): ?>
                    <a href="cart.php" class="glass p-2 rounded-lg hover:scale-105 transition-transform">
                        🛒 <?php echo translate('cart'); ?>
                    </a>
                    <a href="favourites.php" class="glass p-2 rounded-lg hover:scale-105 transition-transform">
                        ❤️ <?php echo translate('favorites'); ?>
                    </a>
                    <a href="orders.php" class="glass p-2 rounded-lg hover:scale-105 transition-transform">
                        📦 <?php echo translate('orders'); ?>
                    </a>
                <?php endif; ?>
                <?php if (getUserRole() === 'farmer'): ?>
                    <a href="farmer_dashboard.php" class="glass p-2 rounded-lg hover:scale-105 transition-transform">
                        <?php echo translate('farmer_dashboard'); ?>
                    </a>
                <?php elseif (getUserRole() === 'nutritionist'): ?>
                    <a href="nutritionist_dashboard.php" class="glass p-2 rounded-lg hover:scale-105 transition-transform">
                        <?php echo translate('nutritionist_dashboard'); ?>
                    </a>
                <?php elseif (getUserRole() === 'admin'): ?>
                    <a href="admin_dashboard.php" class="glass p-2 rounded-lg hover:scale-105 transition-transform">
                        <?php echo translate('admin_dashboard'); ?>
                    </a>
                <?php endif; ?>
                <a href="profile.php" class="glass p-2 rounded-lg hover:scale-105 transition-transform">
                    <?php echo translate('profile'); ?>
                </a>
                <a href="logout.php" class="glass p-2 rounded-lg hover:scale-105 transition-transform">
                    <?php echo translate('logout'); ?>
                </a>
            <?php else: ?>
                <a href="login.php" class="glass p-2 rounded-lg hover:scale-105 transition-transform">
                    <?php echo translate('login'); ?>
                </a>
            <?php endif; ?>
        </div>
    </nav>