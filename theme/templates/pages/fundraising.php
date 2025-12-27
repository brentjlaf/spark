<?php
// File: fundraising.php
// Template: fundraising
// Variables provided by index.php: $settings, $menus, $page, $scriptBase, $themeBase
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

$heroTitle = $page['title'] ?? 'Support Our Mission';
$heroSubtitle = trim($page['meta_description'] ?? 'Help unlock the next chapter of impact with a gift that fuels programs, people, and place.');
?>
<?php $bodyClass = 'min-h-screen flex flex-col'; ?>
<?php include __DIR__ . '/../partials/head.php'; ?>

    <div id="app" class="min-h-screen bg-slate-50 flex flex-col">

        <header class="sticky top-0 z-40 bg-white/90 backdrop-blur border-b border-slate-200">
            <nav role="navigation">
                <div class="mx-auto flex w-full max-w-6xl items-center justify-between px-4 py-4">
                    <a class="inline-flex items-center gap-3 text-lg font-semibold text-slate-900" href="<?php echo $scriptBase; ?>/">
                        <img src="<?php echo htmlspecialchars($logo); ?>" alt="<?php echo htmlspecialchars($siteName); ?>" class="h-10 w-auto">
                        <span class="hidden sm:inline"><?php echo htmlspecialchars($siteName); ?></span>
                    </a>

                    <button class="md:hidden inline-flex items-center justify-center w-10 h-10 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50" type="button" id="menuToggle" aria-controls="main-nav" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="fa-solid fa-bars"></i>
                    </button>

                    <div class="hidden md:flex md:items-center md:gap-6" id="main-nav">
                        <ul class="flex flex-col gap-3 md:flex-row md:items-center md:gap-6">
                            <?php renderMenu($mainMenu); ?>
                        </ul>

                        <form class="hidden lg:block" action="<?php echo $scriptBase; ?>/search" method="get" role="search">
                            <div class="flex items-center rounded-lg border border-slate-200 bg-white shadow-sm">
                                <input class="w-48 rounded-lg border-0 bg-transparent px-3 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-200" type="search" name="q" placeholder="Search..." aria-label="Search" />
                                <button class="px-3 text-slate-500 hover:text-slate-700" type="submit" aria-label="Search">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>

                        <a href="<?php echo $scriptBase; ?>/contact-us" class="btn-primary">
                            <i class="fas fa-envelope btn-icon" aria-hidden="true"></i>
                            <span class="btn-label">Contact Us</span>
                        </a>
                    </div>
                </div>
            </nav>
        </header>

        <main id="main-area" class="flex-1">
            <section class="py-14 bg-slate-50 border-b border-slate-200">
                <div class="container grid gap-8 lg:grid-cols-[1.2fr,0.8fr] lg:items-center">
                    <div class="space-y-4">
                        <span class="uppercase text-primary-600 font-semibold tracking-[0.2em] text-xs">Fundraising</span>
                        <h1 class="display-5"><?php echo htmlspecialchars($heroTitle); ?></h1>
                        <p class="lead text-slate-600"><?php echo htmlspecialchars($heroSubtitle); ?></p>
                        <div class="flex flex-wrap gap-3">
                            <a class="btn btn-primary" href="#donate">Donate Now</a>
                            <a class="btn btn-outline-primary" href="#campaigns">View Campaigns</a>
                        </div>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-3xl shadow-sm p-6 space-y-4">
                        <h2 class="h4">Quick impact snapshot</h2>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="p-4 rounded-2xl bg-slate-50">
                                <p class="text-sm text-slate-500">Total raised this quarter</p>
                                <p class="h4 mt-2">$425,000</p>
                            </div>
                            <div class="p-4 rounded-2xl bg-slate-50">
                                <p class="text-sm text-slate-500">Active campaigns</p>
                                <p class="h4 mt-2">6</p>
                            </div>
                            <div class="p-4 rounded-2xl bg-slate-50">
                                <p class="text-sm text-slate-500">Monthly supporters</p>
                                <p class="h4 mt-2">1,120</p>
                            </div>
                            <div class="p-4 rounded-2xl bg-slate-50">
                                <p class="text-sm text-slate-500">Average gift</p>
                                <p class="h4 mt-2">$96</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="campaigns" class="py-14">
                <div class="container space-y-8">
                    <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                        <div>
                            <span class="uppercase text-primary-600 font-semibold tracking-[0.2em] text-xs">Current priorities</span>
                            <h2 class="display-6">Campaigns in motion</h2>
                        </div>
                        <a class="btn btn-outline-primary" href="#donate">Support a campaign</a>
                    </div>
                    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                        <article class="card h-full shadow-sm border-0">
                            <div class="card-body space-y-3">
                                <span class="uppercase text-primary-600 font-semibold text-xs">Annual giving</span>
                                <h3 class="h4">Community Renewal Fund</h3>
                                <p class="text-muted">Provide reliable monthly support for core services across our programs.</p>
                                <div>
                                    <div class="d-flex justify-between text-sm text-muted">
                                        <span>Raised $52K</span>
                                        <span>Goal $85K</span>
                                    </div>
                                    <div class="progress mt-2" role="progressbar" aria-valuenow="61" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar" style="width:61%"></div>
                                    </div>
                                </div>
                            </div>
                        </article>
                        <article class="card h-full shadow-sm border-0">
                            <div class="card-body space-y-3">
                                <span class="uppercase text-primary-600 font-semibold text-xs">Capital campaign</span>
                                <h3 class="h4">Future Campus Initiative</h3>
                                <p class="text-muted">Help expand our facilities to serve more families and students.</p>
                                <div>
                                    <div class="d-flex justify-between text-sm text-muted">
                                        <span>Raised $162K</span>
                                        <span>Goal $250K</span>
                                    </div>
                                    <div class="progress mt-2" role="progressbar" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar" style="width:65%"></div>
                                    </div>
                                </div>
                            </div>
                        </article>
                        <article class="card h-full shadow-sm border-0">
                            <div class="card-body space-y-3">
                                <span class="uppercase text-primary-600 font-semibold text-xs">Matching gift</span>
                                <h3 class="h4">Match Day Drive</h3>
                                <p class="text-muted">Double your impact with our corporate partner matching pool.</p>
                                <div>
                                    <div class="d-flex justify-between text-sm text-muted">
                                        <span>Raised $40K</span>
                                        <span>Goal $60K</span>
                                    </div>
                                    <div class="progress mt-2" role="progressbar" aria-valuenow="67" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar" style="width:67%"></div>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
            </section>

            <section class="py-14 bg-slate-50" id="donate">
                <div class="container grid gap-8 lg:grid-cols-[1fr,0.8fr]">
                    <div class="space-y-4">
                        <span class="uppercase text-primary-600 font-semibold tracking-[0.2em] text-xs">Make a gift</span>
                        <h2 class="display-6">Choose how you want to give</h2>
                        <p class="text-muted">Select a giving option that aligns with your goals. Every gift helps us meet the moment and support our community.</p>
                        <ul class="list-unstyled space-y-3">
                            <li class="d-flex gap-3">
                                <i class="fas fa-circle-check text-primary"></i>
                                <div>
                                    <strong>One-time donation</strong>
                                    <p class="text-muted">Support immediate program needs and urgent requests.</p>
                                </div>
                            </li>
                            <li class="d-flex gap-3">
                                <i class="fas fa-circle-check text-primary"></i>
                                <div>
                                    <strong>Monthly membership</strong>
                                    <p class="text-muted">Sustain the mission with recurring impact.</p>
                                </div>
                            </li>
                            <li class="d-flex gap-3">
                                <i class="fas fa-circle-check text-primary"></i>
                                <div>
                                    <strong>Corporate partnerships</strong>
                                    <p class="text-muted">Engage your organisation with matching gifts and sponsorships.</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-3xl shadow-sm p-6 space-y-4">
                        <h3 class="h4">Quick donation form</h3>
                        <form class="space-y-3">
                            <label class="w-100">
                                <span class="text-sm text-muted">Donation amount</span>
                                <select class="form-select">
                                    <option>$25</option>
                                    <option>$50</option>
                                    <option selected>$100</option>
                                    <option>$250</option>
                                </select>
                            </label>
                            <label class="w-100">
                                <span class="text-sm text-muted">Campaign focus</span>
                                <select class="form-select">
                                    <option>Community Renewal Fund</option>
                                    <option>Future Campus Initiative</option>
                                    <option>Match Day Drive</option>
                                </select>
                            </label>
                            <button type="button" class="btn btn-primary w-100">Continue to secure checkout</button>
                            <p class="text-xs text-muted">We will direct you to a secure payment provider.</p>
                        </form>
                    </div>
                </div>
            </section>

            <section class="py-14">
                <div class="container">
                    <div class="grid gap-6 md:grid-cols-3">
                        <div class="p-4 border border-slate-200 rounded-2xl bg-white shadow-sm">
                            <h3 class="h5">Transparent reporting</h3>
                            <p class="text-muted">View real-time reports, list performance, and donation activity in the fundraising module.</p>
                        </div>
                        <div class="p-4 border border-slate-200 rounded-2xl bg-white shadow-sm">
                            <h3 class="h5">Multiple fundraising types</h3>
                            <p class="text-muted">Run annual campaigns, matching gifts, and peer-to-peer initiatives from one hub.</p>
                        </div>
                        <div class="p-4 border border-slate-200 rounded-2xl bg-white shadow-sm">
                            <h3 class="h5">Targeted donor lists</h3>
                            <p class="text-muted">Segment donors and personalise outreach to maximise engagement.</p>
                        </div>
                    </div>
                </div>
            </section>

            <?php if (trim($page['content'] ?? '') !== ''): ?>
            <section class="py-14 border-top">
                <div class="container">
                    <div class="drop-area"></div>
                </div>
            </section>
            <?php endif; ?>
        </main>

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

        <button id="back-to-top-btn" class="fixed bottom-6 right-6 hidden h-12 w-12 items-center justify-center rounded-full bg-primary-600 text-white shadow-lg transition hover:bg-primary-700" style="z-index: 1000;" aria-label="Back to Top">
            <i class="fas fa-chevron-up"></i>
        </button>

    </div>

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

    <script>
        window.addEventListener('scroll', function() {
            const backToTopBtn = document.getElementById('back-to-top-btn');
            if (window.scrollY > 100) {
                backToTopBtn.style.display = 'block';
            } else {
                backToTopBtn.style.display = 'none';
            }
        });

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
