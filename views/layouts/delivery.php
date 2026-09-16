<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Deliveries'; ?> - Sodai Dorkar</title>
    <link href="/sodai-dorkar/public/css/output.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body, button, input, select, textarea { font-family: 'Hind Siliguri', 'Outfit', sans-serif; }
    </style>
    <link rel="manifest" href="/sodai-dorkar/public/manifest.json">
    <meta name="theme-color" content="#16a34a">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <!-- Leaflet CSS & JS for Delivery GPS Tracking -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-secondary-50 font-sans text-secondary-900 min-h-screen pb-safe">

    <!-- Simple Mobile Header or None (Content has its own header) -->
    
    <!-- Main Content -->
    <main class="p-6 pb-24">
        <?php echo $content ?? ''; ?>
    </main>

    <!-- Bottom Navigation -->
    <nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-secondary-200 flex justify-around items-center pb-safe-bottom z-50 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
        <a href="/sodai-dorkar/public/delivery/dashboard" class="flex flex-col items-center py-3 px-6 active-nav-link text-secondary-400 hover:text-primary-600 transition-colors">
            <ion-icon name="bicycle" class="text-2xl mb-1"></ion-icon>
            <span class="text-[10px] font-bold uppercase tracking-wide">Orders</span>
        </a>
        <a href="/sodai-dorkar/public/delivery/profile" class="flex flex-col items-center py-3 px-6 text-secondary-400 hover:text-primary-600 transition-colors">
            <ion-icon name="person" class="text-2xl mb-1"></ion-icon>
            <span class="text-[10px] font-bold uppercase tracking-wide">Profile</span>
        </a>
        <a href="/sodai-dorkar/public/delivery/settings" class="flex flex-col items-center py-3 px-6 text-secondary-400 hover:text-primary-600 transition-colors">
            <ion-icon name="settings" class="text-2xl mb-1"></ion-icon>
            <span class="text-[10px] font-bold uppercase tracking-wide">Settings</span>
        </a>
    </nav>

    <script>
        // Simple script to highlight active link based on URL
        const currentPath = window.location.pathname;
        document.querySelectorAll('nav a').forEach(link => {
            if(link.getAttribute('href').includes(currentPath.split('/').pop())) {
                link.classList.remove('text-secondary-400');
                link.classList.add('text-primary-600');
            }
        });
    </script>
</body>
</html>
