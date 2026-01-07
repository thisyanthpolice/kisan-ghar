<footer class="glass mt-auto py-8">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="space-y-4">
                <h3 class="text-xl font-bold mb-4"><?php echo translate('about_us'); ?></h3>
                <p class="opacity-80">
                    <?php echo translate('footer_about'); ?>
                </p>
            </div>
            <div class="space-y-4">
                <h3 class="text-xl font-bold mb-4"><?php echo translate('quick_links'); ?></h3>
                <nav class="flex flex-col space-y-2">
                    <a href="products.php" class="hover:translate-x-2 transition-transform">
                        <?php echo translate('products'); ?>
                    </a>
                    <a href="healthboxes.php" class="hover:translate-x-2 transition-transform">
                        <?php echo translate('health_boxes'); ?>
                    </a>
                    <?php if (!isLoggedIn()): ?>
                        <a href="login.php" class="hover:translate-x-2 transition-transform">
                            <?php echo translate('login'); ?>
                        </a>
                        <a href="signup.php" class="hover:translate-x-2 transition-transform">
                            <?php echo translate('signup'); ?>
                        </a>
                    <?php endif; ?>
                </nav>
            </div>
            <div class="space-y-4">
                <h3 class="text-xl font-bold mb-4"><?php echo translate('contact_us'); ?></h3>
                <div class="flex items-center space-x-2">
                    <span>📧</span>
                    <a href="mailto:kisanghar@gmail.com" class="hover:opacity-80 transition-opacity">
                        contact@kisanghar.com
                    </a>
                </div>
                <div class="flex items-center space-x-2">
                    <span>📱</span>
                    <a href="tel: +91 93477 78770" class="hover:opacity-80 transition-opacity">
                    +91 93477 78770
                    </a>
                </div>
            </div>
        </div>
        <div class="border-t border-white border-opacity-20 mt-8 pt-8 text-center">
            <p class="opacity-80">
                © <?php echo date('Y'); ?> Kisan Ghar. <?php echo translate('all_rights_reserved'); ?>
            </p>
        </div>
    </div>
</footer>