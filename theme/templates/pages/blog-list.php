<?php
// File: blog-list.php
// Template: blog-list
// Variables provided by index.php: $settings, $menus, $page, $scriptBase, $themeBase, $blogPosts
$siteName = $settings['site_name'] ?? 'My Site';
if (!empty($settings['logo'])) {
    $logo = $scriptBase . '/CMS/' . ltrim($settings['logo'], '/');
} else {
    $logo = $themeBase . '/images/logo.png';
}
$faviconSetting = $settings['favicon'] ?? '';
if (is_string($faviconSetting) && $faviconSetting !== '' && preg_match('#^https?://#i', $faviconSetting)) {
    $favicon = $faviconSetting;
} elseif (!empty($settings['favicon'])) {
    $favicon = $scriptBase . '/CMS/' . ltrim($settings['favicon'], '/');
} else {
    $favicon = $themeBase . '/images/favicon.png';
}
$mainMenu = $menus[0]['items'] ?? [];
$footerMenu = $menus[1]['items'] ?? [];
$social = $settings['social'] ?? [];

require_once __DIR__ . '/includes/menu_helpers.php';
require_once __DIR__ . '/includes/blog_helpers.php';

$publishedPosts = array_values(array_filter($blogPosts ?? [], 'sparkcms_is_blog_post_live'));
usort($publishedPosts, function ($a, $b) {
    $aDate = $a['publishDate'] ?? $a['createdAt'] ?? '';
    $bDate = $b['publishDate'] ?? $b['createdAt'] ?? '';
    $aTime = strtotime($aDate) ?: 0;
    $bTime = strtotime($bDate) ?: 0;
    return $bTime <=> $aTime;
});
$heroIntro = trim($page['meta_description'] ?? '');
if ($heroIntro === '') {
    $heroIntro = 'Explore stories, tutorials, and product updates from the SparkCMS team.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Metas & Morweb CMS Assets -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="<?php echo htmlspecialchars($favicon); ?>" type="image/x-icon"/>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,600;0,700;1,400&family=PT+Serif:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">

    <?php include __DIR__ . '/../partials/tailwind.php'; ?>
</head>
<body class="min-h-screen flex flex-col">

    <!-- Default Page -->
    <div id="app" class="page-template default-page flex flex-col min-h-screen">

        <!-- Header -->
        <header class="bg-white border-b border-slate-200">
            <nav class="navbar" role="navigation">
                <div class="container">
                    <!-- Brand/Logo -->
                    <a class="navbar-brand" href="<?php echo $scriptBase; ?>/">
                        <img src="<?php echo htmlspecialchars($logo); ?>" alt="<?php echo htmlspecialchars($siteName); ?>" class="h-10 inline-block">
                        <span class="ml-2 font-semibold hidden sm:inline"><?php echo htmlspecialchars($siteName); ?></span>
                    </a>
                    
                    <!-- Mobile Toggle Button -->
                    <button class="md:hidden inline-flex items-center justify-center w-10 h-10 rounded-lg border border-slate-200" type="button" id="menuToggle" aria-controls="main-nav" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    
                    <!-- Navigation -->
                    <div class="hidden md:flex md:items-center md:gap-6" id="main-nav">
                        <!-- Main Menu -->
                        <ul class="navbar-nav flex flex-col md:flex-row gap-2 md:gap-4">
                            <?php renderMenu($mainMenu); ?>
                        </ul>
                        
                        <!-- Search Form -->
                        <form class="hidden lg:block" action="<?php echo $scriptBase; ?>/search" method="get" role="search">
                            <div class="input-group">
                                <input class="form-control" type="search" name="q" placeholder="Search..." aria-label="Search" />
                                <button class="btn btn-outline-secondary" type="submit" aria-label="Search">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                        
                        <!-- Contact Button -->
                        <a href="<?php echo $scriptBase; ?>/contact-us" class="btn btn-primary">
                            <i class="fas fa-envelope btn-icon" aria-hidden="true"></i>
                            <span class="btn-label">Contact Us</span>
                        </a>
                    </div>
                </div>
            </nav>
        </header>

        <!-- Main Content -->
        <main id="main-area" class="flex-1">
            <section class="py-12 bg-slate-50 border-b border-slate-200">
                <div class="container text-center space-y-3">
                    <span class="uppercase text-primary-600 font-semibold tracking-[0.2em] text-xs">Insights</span>
                    <h1 class="display-5"><?php echo htmlspecialchars($page['title'] ?? 'Latest Posts'); ?></h1>
                    <?php if ($heroIntro !== ''): ?>
                    <p class="lead max-w-3xl mx-auto">
                        <?php echo htmlspecialchars($heroIntro); ?>
                    </p>
                    <?php endif; ?>
                </div>
            </section>

            <section class="py-12">
                <div class="container">
                    <?php if ($publishedPosts): ?>
                    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                        <?php foreach ($publishedPosts as $post):
                            $detailUrl = sparkcms_resolve_blog_detail_url($scriptBase, '/blogs', $post['slug'] ?? '');
                            $dateValue = sparkcms_format_blog_date($post['publishDate'] ?? $post['createdAt'] ?? '');
                            $excerpt = trim(strip_tags($post['excerpt'] ?? ''));
                            if ($excerpt === '') {
                                $excerpt = trim(strip_tags($post['content'] ?? ''));
                            }
                            $excerptTruncated = false;
                            if ($excerpt !== '') {
                                if (function_exists('mb_strlen')) {
                                    $excerptLength = mb_strlen($excerpt);
                                } else {
                                    $excerptLength = strlen($excerpt);
                                }
                                if ($excerptLength > 150) {
                                    if (function_exists('mb_substr')) {
                                        $excerpt = mb_substr($excerpt, 0, 150);
                                    } else {
                                        $excerpt = substr($excerpt, 0, 150);
                                    }
                                    $excerptTruncated = true;
                                }
                            }
                        ?>
                        <div>
                            <article class="card h-full shadow-sm border-0">
                                <div class="card-body flex flex-col gap-3">
                                    <?php if (!empty($post['category'])): ?>
                                    <span class="uppercase text-primary-600 font-semibold text-xs"><?php echo htmlspecialchars($post['category']); ?></span>
                                    <?php endif; ?>
                                    <h3 class="h4">
                                        <a class="text-dark" href="<?php echo htmlspecialchars($detailUrl); ?>">
                                            <?php echo htmlspecialchars($post['title'] ?? 'Untitled Post'); ?>
                                        </a>
                                    </h3>
                                    <?php if ($dateValue !== '' || !empty($post['author'])): ?>
                                    <div class="text-muted text-sm flex flex-wrap gap-3">
                                        <?php if (!empty($post['author'])): ?>
                                        <span class="inline-flex items-center gap-2"><i class="fas fa-user"></i><?php echo htmlspecialchars($post['author']); ?></span>
                                        <?php endif; ?>
                                        <?php if ($dateValue !== ''): ?>
                                        <span class="inline-flex items-center gap-2"><i class="far fa-calendar-alt"></i><?php echo htmlspecialchars($dateValue); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ($excerpt !== ''): ?>
                                    <p class="text-muted flex-1"><?php echo htmlspecialchars($excerpt); ?><?php if ($excerptTruncated): ?>…<?php endif; ?></p>
                                    <?php else: ?>
                                    <p class="text-muted flex-1">Read the full article to learn more.</p>
                                    <?php endif; ?>
                                    <a class="btn btn-outline-primary mt-2" href="<?php echo htmlspecialchars($detailUrl); ?>">
                                        Read More
                                        <span aria-hidden="true">→</span>
                                    </a>
                                </div>
                            </article>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-12 space-y-4">
                        <p class="lead">We haven't published any posts yet. Please check back soon for new updates.</p>
                        <a class="btn btn-primary" href="<?php echo $scriptBase; ?>/">Return to homepage</a>
                    </div>
                    <?php endif; ?>
                </div>
            </section>

            <?php if (trim($page['content'] ?? '') !== ''): ?>
            <section class="py-12 border-top">
                <div class="container">
                    <div class="drop-area"></div>
                </div>
            </section>
            <?php endif; ?>
        </main>

        <!-- Footer -->
        <footer id="footer-area" class="site-footer mt-auto">
            <div class="container py-12 space-y-8">
                <div class="footer-main grid gap-8 md:grid-cols-3">
                    <div class="space-y-3">
                        <a href="<?php echo $scriptBase; ?>/" class="navbar-brand inline-block mb-3">
                            <img src="<?php echo htmlspecialchars($logo); ?>" alt="<?php echo htmlspecialchars($siteName); ?>" class="h-10 filter-invert">
                        </a>
                        <p class="text-sm text-slate-300">Your trusted partner for exceptional service and innovative solutions.</p>
                        <div class="footer-social flex items-center gap-2">
                            <?php if (!empty($social['facebook'])): ?>
                            <a href="<?php echo htmlspecialchars($social['facebook']); ?>" class="btn btn-outline-light btn-sm" aria-label="Facebook" target="_blank">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <?php endif; ?>
                            <?php if (!empty($social['twitter'])): ?>
                            <a href="<?php echo htmlspecialchars($social['twitter']); ?>" class="btn btn-outline-light btn-sm" aria-label="Twitter" target="_blank">
                                <i class="fab fa-x-twitter"></i>
                            </a>
                            <?php endif; ?>
                            <?php if (!empty($social['instagram'])): ?>
                            <a href="<?php echo htmlspecialchars($social['instagram']); ?>" class="btn btn-outline-light btn-sm" aria-label="Instagram" target="_blank">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <?php endif; ?>
                            <?php if (!empty($social['linkedin'])): ?>
                            <a href="<?php echo htmlspecialchars($social['linkedin']); ?>" class="btn btn-outline-light btn-sm" aria-label="LinkedIn" target="_blank">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                            <?php endif; ?>
                            <?php if (!empty($social['youtube'])): ?>
                            <a href="<?php echo htmlspecialchars($social['youtube']); ?>" class="btn btn-outline-light btn-sm" aria-label="YouTube" target="_blank">
                                <i class="fab fa-youtube"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <nav class="footer-menu space-y-3">
                        <h5 class="text-white">Quick Links</h5>
                        <ul class="space-y-2">
                            <?php renderFooterMenu($footerMenu); ?>
                        </ul>
                    </nav>
                    <div class="space-y-3">
                        <h5 class="text-white">Contact Info</h5>
                        <ul class="space-y-2">
                            <?php if (!empty($settings['address'])): ?>
                            <li class="flex items-start gap-2 text-slate-300">
                                <i class="fas fa-map-marker-alt text-primary"></i>
                                <span><?php echo htmlspecialchars($settings['address']); ?></span>
                            </li>
                            <?php endif; ?>
                            <?php if (!empty($settings['phone'])): ?>
                            <li class="flex items-center gap-2 text-slate-300">
                                <i class="fas fa-phone text-primary"></i>
                                <a href="tel:<?php echo htmlspecialchars($settings['phone']); ?>" class="text-slate-300 hover:text-white"><?php echo htmlspecialchars($settings['phone']); ?></a>
                            </li>
                            <?php endif; ?>
                            <?php if (!empty($settings['email'])): ?>
                            <li class="flex items-center gap-2 text-slate-300">
                                <i class="fas fa-envelope text-primary"></i>
                                <a href="mailto:<?php echo htmlspecialchars($settings['email']); ?>" class="text-slate-300 hover:text-white"><?php echo htmlspecialchars($settings['email']); ?></a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
                <div class="footer-copy flex flex-col gap-3 md:flex-row md:items-center md:justify-between text-slate-400 text-sm">
                    <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($siteName); ?>. All rights reserved.</p>
                    <ul class="nav flex gap-4">
                        <li class="nav-item">
                            <a class="nav-link text-slate-400 px-0" href="<?php echo $scriptBase; ?>/privacy-policy">Privacy Policy</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-slate-400 px-0" href="<?php echo $scriptBase; ?>/terms-of-service">Terms of Service</a>
                        </li>
                    </ul>
                </div>
            </div>
        </footer>

        <!-- Back to Top Button -->
        <button id="back-to-top-btn" class="btn btn-primary fixed shadow" style="bottom: 20px; right: 20px; z-index: 1000; display: none;" aria-label="Back to Top">
            <i class="fas fa-chevron-up"></i>
        </button>

    </div>

    <!-- Custom JavaScript -->
    <script>window.cmsBase = <?php echo json_encode($scriptBase); ?>;</script>
    <script src="<?php echo $themeBase; ?>/js/combined.js?v=mw3.2"></script>
    <script>
        (function(){
            const toggle = document.getElementById('menuToggle');
            const nav = document.getElementById('main-nav');
            if(!toggle || !nav) return;
            nav.classList.add('hidden');
            toggle.addEventListener('click', function(){
                const isHidden = nav.classList.toggle('hidden');
                toggle.setAttribute('aria-expanded', String(!isHidden));
            });
        })();
    </script>
    
    <!-- Back to Top Button Script -->
    <script>
        // Show/hide back to top button
        window.addEventListener('scroll', function() {
            const backToTopBtn = document.getElementById('back-to-top-btn');
            if (window.scrollY > 100) {
                backToTopBtn.style.display = 'block';
            } else {
                backToTopBtn.style.display = 'none';
            }
        });
        
        // Smooth scroll to top
        document.getElementById('back-to-top-btn').addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    </script>

</body>
</html>
