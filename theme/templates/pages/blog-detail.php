<?php
// File: blog-detail.php
// Template: blog-detail
// Variables provided by index.php: $settings, $menus, $page, $scriptBase, $themeBase, $blogPost
$siteName = $settings['site_name'] ?? 'My Site';
if (!empty($settings['logo'])) {
    $logo = $scriptBase . '/CMS/' . ltrim($settings['logo'], '/');
} else {
    $logo = $themeBase . '/images/logo.png';
}
$mainMenu = $menus[0]['items'] ?? [];
$footerMenu = $menus[1]['items'] ?? [];
$social = $settings['social'] ?? [];

require_once __DIR__ . '/includes/menu_helpers.php';
require_once __DIR__ . '/includes/blog_helpers.php';

$activePost = $blogPost ?? null;
if (!$activePost) {
    $activePost = [
        'title' => $page['title'] ?? 'Blog Post',
        'content' => '<p>This blog post could not be loaded.</p>',
    ];
}
$detailTitle = $activePost['title'] ?? ($page['title'] ?? 'Blog Post');
$detailCategory = trim((string) ($activePost['category'] ?? ''));
$detailAuthor = trim((string) ($activePost['author'] ?? ''));
$detailDate = sparkcms_format_blog_date($activePost['publishDate'] ?? $activePost['createdAt'] ?? '');
$detailContent = $activePost['content'] ?? '';
if ($detailContent === '') {
    $detailContent = '<p>This article does not have any published content yet.</p>';
}
$tagSource = $activePost['tags'] ?? '';
if (is_array($tagSource)) {
    $detailTags = array_filter(array_map('trim', $tagSource));
} else {
    $tagString = trim((string) $tagSource);
    $detailTags = $tagString === '' ? [] : array_filter(array_map('trim', explode(',', $tagString)));
}
$backUrl = rtrim($scriptBase, '/') . '/blogs';
?>
<?php $bodyClass = 'min-h-screen flex flex-col'; ?>
<?php include __DIR__ . '/../partials/head.php'; ?>

    <!-- Default Page -->
    <div id="app" class="min-h-screen bg-slate-50 flex flex-col">

        <!-- Header -->
        <header class="sticky top-0 z-40 bg-white/90 backdrop-blur border-b border-slate-200">
            <nav role="navigation">
                <div class="mx-auto flex w-full max-w-6xl items-center justify-between px-4 py-4">
                    <!-- Brand/Logo -->
                    <a class="inline-flex items-center gap-3 text-lg font-semibold text-slate-900" href="<?php echo $scriptBase; ?>/">
                        <img src="<?php echo htmlspecialchars($logo); ?>" alt="<?php echo htmlspecialchars($siteName); ?>" class="h-10 w-auto">
                        <span class="hidden sm:inline"><?php echo htmlspecialchars($siteName); ?></span>
                    </a>

                    <!-- Mobile Toggle Button -->
                    <button class="md:hidden inline-flex items-center justify-center w-10 h-10 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50" type="button" id="menuToggle" aria-controls="main-nav" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="fa-solid fa-bars"></i>
                    </button>

                    <!-- Navigation -->
                    <div class="hidden md:flex md:items-center md:gap-6" id="main-nav">
                        <!-- Main Menu -->
                        <ul class="flex flex-col gap-3 md:flex-row md:items-center md:gap-6">
                            <?php renderMenu($mainMenu); ?>
                        </ul>

                        <!-- Search Form -->
                        <form class="hidden lg:block" action="<?php echo $scriptBase; ?>/search" method="get" role="search">
                            <div class="flex items-center rounded-lg border border-slate-200 bg-white shadow-sm">
                                <input class="w-48 rounded-lg border-0 bg-transparent px-3 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-200" type="search" name="q" placeholder="Search..." aria-label="Search" />
                                <button class="px-3 text-slate-500 hover:text-slate-700" type="submit" aria-label="Search">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>

                        <!-- Contact Button -->
                        <a href="<?php echo $scriptBase; ?>/contact-us" class="btn-primary">
                            <i class="fas fa-envelope btn-icon" aria-hidden="true"></i>
                            <span class="btn-label">Contact Us</span>
                        </a>
                    </div>
                </div>
            </nav>
        </header>

        <!-- Main Content -->
        <main id="main-area" class="flex-1">
            <section class="border-b border-slate-200 bg-white py-14">
                <div class="mx-auto w-full max-w-6xl px-4 space-y-4">
                    <a class="text-primary-600 text-sm font-semibold inline-flex items-center gap-2" href="<?php echo htmlspecialchars($backUrl); ?>">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to all posts
                    </a>
                    <?php if ($detailCategory !== ''): ?>
                    <span class="inline-block uppercase text-primary-600 font-semibold text-xs"><?php echo htmlspecialchars($detailCategory); ?></span>
                    <?php endif; ?>
                    <h1 class="text-4xl md:text-5xl font-semibold tracking-tight"><?php echo htmlspecialchars($detailTitle); ?></h1>
                    <?php if ($detailAuthor !== '' || $detailDate !== ''): ?>
                    <div class="text-slate-500 mt-3 flex flex-wrap gap-4 text-sm">
                        <?php if ($detailAuthor !== ''): ?>
                        <span class="inline-flex items-center gap-2"><i class="fas fa-user"></i><?php echo htmlspecialchars($detailAuthor); ?></span>
                        <?php endif; ?>
                        <?php if ($detailDate !== ''): ?>
                        <span class="inline-flex items-center gap-2"><i class="far fa-calendar-alt"></i><?php echo htmlspecialchars($detailDate); ?></span>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </section>

            <section class="section">
                <div class="mx-auto w-full max-w-6xl px-4">
                    <article class="mx-auto max-w-3xl space-y-6">
                        <div class="mw-rich-text blog-detail-content text-slate-600">
                            <?php echo $detailContent; ?>
                        </div>
                        <?php if ($detailTags): ?>
                        <div class="mt-8 space-y-3">
                            <span class="uppercase text-slate-500 text-xs font-semibold">Tags</span>
                            <div class="flex flex-wrap gap-2">
                                <?php foreach ($detailTags as $tag): ?>
                                <span class="inline-flex items-center rounded-full border border-primary-200 bg-primary-50 px-3 py-1 text-xs font-semibold text-primary-700">#<?php echo htmlspecialchars($tag); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="mt-8">
                            <a class="btn-outline" href="<?php echo htmlspecialchars($backUrl); ?>">
                                <i class="fas fa-arrow-left" aria-hidden="true"></i>
                                Back to all posts
                            </a>
                        </div>
                    </article>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer id="footer-area" class="mt-auto bg-slate-950 text-slate-100">
            <div class="mx-auto w-full max-w-6xl px-4 py-12 space-y-10">
                <div class="grid gap-10 md:grid-cols-3">
                    <div class="space-y-4">
                        <a href="<?php echo $scriptBase; ?>/" class="inline-flex items-center gap-3 text-lg font-semibold text-white">
                            <img src="<?php echo htmlspecialchars($logo); ?>" alt="<?php echo htmlspecialchars($siteName); ?>" class="h-10 w-auto filter invert">
                            <span class="sr-only"><?php echo htmlspecialchars($siteName); ?></span>
                        </a>
                        <p class="text-sm text-slate-300">Your trusted partner for exceptional service and innovative solutions.</p>
                        <div class="flex items-center gap-2">
                            <?php if (!empty($social['facebook'])): ?>
                            <a href="<?php echo htmlspecialchars($social['facebook']); ?>" class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-white/20 text-white/80 transition hover:border-white/40 hover:text-white" aria-label="Facebook" target="_blank">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <?php endif; ?>
                            <?php if (!empty($social['twitter'])): ?>
                            <a href="<?php echo htmlspecialchars($social['twitter']); ?>" class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-white/20 text-white/80 transition hover:border-white/40 hover:text-white" aria-label="Twitter" target="_blank">
                                <i class="fab fa-x-twitter"></i>
                            </a>
                            <?php endif; ?>
                            <?php if (!empty($social['instagram'])): ?>
                            <a href="<?php echo htmlspecialchars($social['instagram']); ?>" class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-white/20 text-white/80 transition hover:border-white/40 hover:text-white" aria-label="Instagram" target="_blank">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <?php endif; ?>
                            <?php if (!empty($social['linkedin'])): ?>
                            <a href="<?php echo htmlspecialchars($social['linkedin']); ?>" class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-white/20 text-white/80 transition hover:border-white/40 hover:text-white" aria-label="LinkedIn" target="_blank">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                            <?php endif; ?>
                            <?php if (!empty($social['youtube'])): ?>
                            <a href="<?php echo htmlspecialchars($social['youtube']); ?>" class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-white/20 text-white/80 transition hover:border-white/40 hover:text-white" aria-label="YouTube" target="_blank">
                                <i class="fab fa-youtube"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <nav class="space-y-4">
                        <h5 class="text-white">Quick Links</h5>
                        <ul class="space-y-2">
                            <?php renderFooterMenu($footerMenu); ?>
                        </ul>
                    </nav>
                    <div class="space-y-4">
                        <h5 class="text-white">Contact Info</h5>
                        <ul class="space-y-2">
                            <?php if (!empty($settings['address'])): ?>
                            <li class="flex items-start gap-2 text-slate-300">
                                <i class="fas fa-map-marker-alt text-primary-400"></i>
                                <span><?php echo htmlspecialchars($settings['address']); ?></span>
                            </li>
                            <?php endif; ?>
                            <?php if (!empty($settings['phone'])): ?>
                            <li class="flex items-center gap-2 text-slate-300">
                                <i class="fas fa-phone text-primary-400"></i>
                                <a href="tel:<?php echo htmlspecialchars($settings['phone']); ?>" class="text-slate-300 hover:text-white"><?php echo htmlspecialchars($settings['phone']); ?></a>
                            </li>
                            <?php endif; ?>
                            <?php if (!empty($settings['email'])): ?>
                            <li class="flex items-center gap-2 text-slate-300">
                                <i class="fas fa-envelope text-primary-400"></i>
                                <a href="mailto:<?php echo htmlspecialchars($settings['email']); ?>" class="text-slate-300 hover:text-white"><?php echo htmlspecialchars($settings['email']); ?></a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
                <div class="flex flex-col gap-4 border-t border-white/10 pt-6 text-sm text-slate-400 md:flex-row md:items-center md:justify-between">
                    <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($siteName); ?>. All rights reserved.</p>
                    <ul class="flex gap-4">
                        <li>
                            <a class="text-slate-400 hover:text-white transition" href="<?php echo $scriptBase; ?>/privacy-policy">Privacy Policy</a>
                        </li>
                        <li>
                            <a class="text-slate-400 hover:text-white transition" href="<?php echo $scriptBase; ?>/terms-of-service">Terms of Service</a>
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
