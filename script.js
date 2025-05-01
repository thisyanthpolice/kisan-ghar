document.addEventListener('DOMContentLoaded', () => {
    // Theme Toggle
    const themeToggle = document.getElementById('theme-toggle');
    const prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)');
    
    function setTheme(isDark) {
        document.documentElement.classList.toggle('dark', isDark);
        document.body.classList.toggle('dark', isDark);
        themeToggle.textContent = isDark ? '☀️' : '🌙';
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
    }

    // Initialize theme
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
        setTheme(savedTheme === 'dark');
    } else {
        setTheme(prefersDarkScheme.matches);
    }

    themeToggle.addEventListener('click', () => {
        setTheme(!document.body.classList.contains('dark'));
    });

    // Sidebar Toggle with Backdrop
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebar = document.getElementById('sidebar');
    const closeBtn = document.getElementById('close-sidebar');
    const backdrop = document.getElementById('sidebar-backdrop');
    
    function toggleSidebar(show) {
        sidebar.classList.toggle('-translate-x-full', !show);
        document.body.classList.toggle('overflow-hidden', show);
        backdrop.classList.toggle('opacity-0', !show);
        backdrop.classList.toggle('pointer-events-none', !show);
    }
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleSidebar(true);
        });

        if (closeBtn) {
            closeBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                toggleSidebar(false);
            });
        }

        // Close sidebar when clicking backdrop
        backdrop.addEventListener('click', () => {
            toggleSidebar(false);
        });

        // Close sidebar on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !sidebar.classList.contains('-translate-x-full')) {
                toggleSidebar(false);
            }
        });

        // Prevent closing when clicking inside sidebar
        sidebar.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    }

    // Add smooth transitions to cards and links
    const cards = document.querySelectorAll('.card');
    const links = document.querySelectorAll('a');

    cards.forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.style.transform = 'translateY(-5px)';
        });
        card.addEventListener('mouseleave', () => {
            card.style.transform = 'translateY(0)';
        });
    });

    links.forEach(link => {
        if (!link.classList.contains('card')) {
            link.addEventListener('mouseenter', () => {
                link.style.opacity = '0.8';
            });
            link.addEventListener('mouseleave', () => {
                link.style.opacity = '1';
            });
        }
    });

    // Language selector enhancement
    const langSelect = document.getElementById('lang-select');
    if (langSelect) {
        const currentUrl = new URL(window.location.href);
        langSelect.value = currentUrl.searchParams.get('lang') || document.documentElement.lang || 'en';
        
        langSelect.addEventListener('change', (e) => {
            currentUrl.searchParams.set('lang', e.target.value);
            window.location.href = currentUrl.toString();
        });
    }

    // Initialize cart functionality
    window.addToCart = async function(id, type) {
        if (!id || !type) return;
        const qty = document.getElementById(`qty-${id}`)?.value || 1;
        try {
            const response = await fetch('add_to_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id, type, quantity: parseInt(qty) })
            });
            if (response.ok) {
                const result = await response.json();
                alert(result.message || 'Added to cart successfully!');
            } else {
                throw new Error('Failed to add to cart');
            }
        } catch (error) {
            console.error('Error adding to cart:', error);
            alert('Failed to add to cart. Please try again.');
        }
    };

    // Initialize favorites functionality
    window.addToFavorites = async function(id, type) {
        if (!id || !type) return;
        try {
            const response = await fetch('add_to_favorites.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id, type })
            });
            if (response.ok) {
                const result = await response.json();
                alert(result.message || 'Added to favorites successfully!');
            } else {
                throw new Error('Failed to add to favorites');
            }
        } catch (error) {
            console.error('Error adding to favorites:', error);
            alert('Failed to add to favorites. Please try again.');
        }
    };

    // Add toggleDescription function to window object
    window.toggleDescription = function(id) {
        const desc = document.getElementById(`desc-${id}`);
        if (desc) {
            desc.classList.toggle('hidden');
        }
    };
});