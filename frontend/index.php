<?php 
require_once 'header.php';
require_once __DIR__ . '/../backend/db_connect.php'; 
?>

<main class="min-h-screen bg-transparent">
    <!-- Hero Section -->
    <section class="hero-section relative h-[80vh] flex items-center justify-center" style="background: linear-gradient(135deg, #22c55e 0%, #0ea5e9 100%);">
        <div class="hero-bg-image absolute inset-0 bg-[url('image.png')] bg-cover bg-center opacity-70"></div>
        <div class="hero-spotlight" style="background: radial-gradient(circle at 50% 50%, rgba(255,255,255,0.35) 0%, rgba(34,197,94,0.18) 40%, rgba(14,165,233,0.12) 70%, transparent 100%); z-index:2;"></div>
        <div class="hero-content relative z-10 text-center p-8 max-w-6xl">
            <h1 class="hero-title text-4xl md:text-6xl font-bold mb-6">
                <?php echo translate('welcome_to_kisan_ghar'); ?>
            </h1>
            <p class="hero-subtitle text-xl md:text-2xl mb-8 leading-relaxed">
                Kisan Ghar is your trusted bridge between local farmers and health-conscious consumers. 
                We believe in promoting sustainable agriculture while ensuring you get the freshest produce 
                directly from the farms to your table. Our curated selection of fruits, vegetables, 
                and nutritionist-designed health boxes brings the best of nature to your doorstep.
            </p>
            <div class="flex flex-col md:flex-row gap-4 justify-center mt-8">
                <a href="products.php" class="px-8 py-3 rounded-full border border-white bg-white bg-opacity-20 text-white font-semibold backdrop-blur-md hover:bg-opacity-40 transition-all shadow-lg">Explore Products</a>
                <a href="healthboxes.php" class="px-8 py-3 rounded-full border border-white bg-white bg-opacity-20 text-white font-semibold backdrop-blur-md hover:bg-opacity-40 transition-all shadow-lg">Explore Health Boxes</a>
            </div>
        </div>
    </section>

    <!-- Featured Products Section -->
    <section class="py-16 px-4">
        <div class="max-w-8xl mx-auto">
            <h2 class="text-3xl font-bold mb-8 text-center">
                <?php echo translate('featured_products'); ?>
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php
                $stmt = $pdo->query("
                    SELECT p.*, u.name as farmer_name 
                    FROM products p 
                    JOIN users u ON p.farmer_id = u.id 
                    WHERE p.quantity_available > 0 
                    ORDER BY p.created_at DESC 
                    LIMIT 4
                ");
                $emojis = [
                    'vegetables' => '🥬',
                    'fruits' => '🍎',
                    'nuts' => '🥜',
                    'dairy' => '🥛',
                ];
                $i = 0;
                while ($product = $stmt->fetch()): ?>
                    <?php $bgClass = ($i++ % 2 == 0) ? 'grid-blue' : 'grid-green'; ?>
                    <div class="glass p-4 flex flex-col justify-between h-full border border-gray-200 rounded-lg shadow-sm <?php echo $bgClass; ?>" style="background:rgba(255,255,255,0.18);">
                        <a href="products.php?id=<?php echo $product['id']; ?>" class="block mb-2 focus:outline-none" style="cursor:pointer">
                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                                 class="w-full h-48 object-cover rounded mb-2">
                            <h3 class="text-xl font-semibold mb-1 flex items-center gap-2">
                                <?php echo $emojis[$product['category']] ?? '🛒'; ?>
                                <?php echo htmlspecialchars($product['name']); ?>
                            </h3>
                        </a>
                        <a href="farmer_profile.php?id=<?php echo $product['farmer_id']; ?>" class="text-green-500 mb-1" style="cursor:pointer">
                            👨‍🌾 <?php echo htmlspecialchars($product['farmer_name']); ?>
                        </a>
                        <p class="mb-2">💰 <?php echo translate('price'); ?>: ₹<?php echo $product['price']; ?></p>
                    </div>
                <?php endwhile; ?>
            </div>
            <div class="flex justify-center mt-8">
                <a href="products.php" class="blue-gradient-btn px-8 py-3 rounded-full font-semibold shadow-lg transition-all">
                    View More Products
                </a>
            </div>
        </div>
    </section>

    <!-- Featured Health Boxes Section -->
    <section class="py-16 px-4">
        <div class="max-w-8xl mx-auto">
            <h2 class="text-3xl font-bold mb-8 text-center">
                <?php echo translate('health_boxes'); ?>
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php
                $stmt = $pdo->query("
                    SELECT h.*, u.name as nutritionist_name 
                    FROM health_boxes h 
                    JOIN users u ON h.nutritionist_id = u.id 
                    ORDER BY h.created_at DESC 
                    LIMIT 4
                ");
                $i = 0;
                while ($box = $stmt->fetch()): ?>
                    <?php $bgClass = ($i++ % 2 == 0) ? 'grid-blue' : 'grid-green'; ?>
                    <div class="glass p-4 flex flex-col justify-between h-full border border-gray-200 rounded-lg shadow-sm <?php echo $bgClass; ?>" style="background:rgba(255,255,255,0.18);">
                        <a href="healthboxes.php?id=<?php echo $box['id']; ?>" class="block mb-2 focus:outline-none" style="cursor:pointer">
                            <img src="<?php echo htmlspecialchars($box['image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($box['name']); ?>"
                                 class="w-full h-48 object-cover rounded mb-2">
                            <h3 class="text-xl font-semibold mb-1 flex items-center gap-2">🧰 <?php echo htmlspecialchars($box['name']); ?></h3>
                        </a>
                        <a href="farmer_profile.php?id=<?php echo $box['nutritionist_id']; ?>" class="text-green-500 mb-1" style="cursor:pointer">
                            🥗 <?php echo htmlspecialchars($box['nutritionist_name']); ?>
                        </a>
                        <p class="mb-2">💰 <?php echo translate('price'); ?>: ₹<?php echo $box['price']; ?></p>
                    </div>
                <?php endwhile; ?>
            </div>
            <div class="flex justify-center mt-8">
                <a href="healthboxes.php" class="blue-gradient-btn px-8 py-3 rounded-full font-semibold shadow-lg transition-all">
                    View More Health Boxes
                </a>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="py-16 px-4">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl font-bold mb-8 text-center">
                <?php echo translate('browse_categories'); ?>
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <?php
                $categories = ['vegetables', 'fruits', 'nuts', 'dairy'];
                foreach ($categories as $category): ?>
                    <a href="products.php?category=<?php echo $category; ?>" 
                       class="glass p-6 rounded-xl text-center hover:scale-105 transition-all flex flex-col items-center justify-center" style="cursor:pointer">
                        <div class="text-4xl mb-4">
                            <?php
                            $emojis = [
                                'vegetables' => '🥬',
                                'fruits' => '🍎',
                                'nuts' => '🥜',
                                'dairy' => '🥛'
                            ];
                            echo $emojis[$category];
                            ?>
                        </div>
                        <h3 class="text-xl font-semibold">
                            <?php echo translate($category); ?>
                        </h3>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>

<?php require_once 'footer.php'; ?>