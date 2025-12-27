    <!-- Footer -->
    <footer id="footer-area" class="mt-auto bg-slate-950 text-slate-100">
        <div class="mx-auto w-full max-w-6xl px-4 py-12 space-y-10">
            <div class="grid gap-10 md:grid-cols-3">
                <div class="space-y-4">
                    <a href="<?php echo $scriptBase; ?>/" class="inline-flex items-center gap-3 text-lg font-semibold text-white">
                        <img src="<?php echo htmlspecialchars($logo); ?>" alt="Logo" class="h-10 w-auto filter invert">
                        <span class="sr-only"><?php echo htmlspecialchars($siteName); ?></span>
                    </a>
                    <p class="text-sm text-slate-300">Your trusted partner for exceptional service and innovative solutions.</p>
                    <div class="flex items-center gap-2">
                        <?php if (!empty($social['facebook'])): ?>
                        <a href="<?php echo htmlspecialchars($social['facebook']); ?>" class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-white/20 text-white/80 transition hover:border-white/40 hover:text-white" aria-label="Facebook" target="_blank">
                            <i class="fa-brands fa-facebook-f"></i>
                        </a>
                        <?php endif; ?>
                        <?php if (!empty($social['twitter'])): ?>
                        <a href="<?php echo htmlspecialchars($social['twitter']); ?>" class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-white/20 text-white/80 transition hover:border-white/40 hover:text-white" aria-label="Twitter" target="_blank">
                            <i class="fa-brands fa-x-twitter"></i>
                        </a>
                        <?php endif; ?>
                        <?php if (!empty($social['instagram'])): ?>
                        <a href="<?php echo htmlspecialchars($social['instagram']); ?>" class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-white/20 text-white/80 transition hover:border-white/40 hover:text-white" aria-label="Instagram" target="_blank">
                            <i class="fa-brands fa-instagram"></i>
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
                <div class="space-y-4 text-sm text-slate-300">
                    <h5 class="text-white">Stay Connected</h5>
                    <p>Sign up to receive updates, news, and more from our team.</p>
                    <form class="flex flex-col gap-3">
                        <input type="email" placeholder="Enter your email" class="input" aria-label="Email address">
                        <button type="button" class="btn-primary">Subscribe</button>
                    </form>
                </div>
            </div>
            <div class="flex flex-col gap-4 border-t border-white/10 pt-6 text-sm text-slate-400 md:flex-row md:items-center md:justify-between">
                <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($siteName); ?></p>
                <p>Built with SparkCMS</p>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button id="back-to-top-btn" class="fixed bottom-6 right-6 hidden h-12 w-12 items-center justify-center rounded-full bg-primary-600 text-white shadow-lg transition hover:bg-primary-700" style="z-index: 1000;" aria-label="Back to Top">
        <i class="fa-solid fa-chevron-up" aria-hidden="true"></i>
    </button>
</div>

<!-- Javascript -->
<script src="<?php echo $themeBase; ?>/js/combined.js?v=mw3.2"></script>

    </body>
</html>
