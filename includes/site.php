<?php

declare(strict_types=1);

const SITE_NAME = 'Takshasheela Ayurveda Aashram';

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function current_file(): string
{
    return basename((string) ($_SERVER['SCRIPT_NAME'] ?? 'index.php'));
}

function nav_active(array $files): bool
{
    return in_array(current_file(), $files, true);
}

function render_header(string $title, string $description, string $theme = 'light'): void
{
    $current = current_file();
    ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= e($description) ?>">
    <meta name="theme-color" content="#173f36">
    <title><?= e($title) ?> | <?= SITE_NAME ?></title>
    <link rel="icon" href="assets/images/favicon.png" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&amp;family=Newsreader:opsz,wght@6..72,400;6..72,500&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/site.css?v=8">
    <script src="assets/js/site.js?v=2" defer></script>
</head>
<body class="site-theme-<?= e($theme) ?>">
<a class="skip-link" href="#main-content">Skip to content</a>
<header class="site-header" data-header>
    <div class="shell site-header__inner">
        <a class="brand" href="index.php" aria-label="Takshasheela home">
            <img src="assets/images/logo.png" width="290" height="80" alt="Takshasheela Ayurveda Aashram">
        </a>
        <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-navigation" data-nav-toggle>
            <span></span><span></span><span></span><span class="sr-only">Menu</span>
        </button>
        <nav class="site-nav" id="site-navigation" aria-label="Primary navigation" data-nav>
            <ul>
                <li><a href="index.php"<?= $current === 'index.php' ? ' aria-current="page"' : '' ?>>Home</a></li>
                <li class="nav-group">
                    <details>
                        <summary<?= nav_active(['about.php', 'ayurveda.php', 'team.php', 'approach.php']) ? ' class="is-active"' : '' ?>>Discover</summary>
                        <div class="nav-panel">
                            <p class="nav-panel__label">Discover us</p>
                            <a href="about.php"><strong>About Takshasheela</strong><span>Our story, purpose, and values</span></a>
                            <a href="ayurveda.php"><strong>About Ayurveda</strong><span>Ancient wisdom for modern life</span></a>
                            <a href="team.php"><strong>Our Team</strong><span>Meet our leaders and practitioners</span></a>
                            <a href="approach.php"><strong>Our Approach</strong><span>How personalised care unfolds</span></a>
                        </div>
                    </details>
                </li>
                <li class="nav-group nav-group--wide">
                    <details>
                        <summary<?= nav_active(['programs.php', 'program.php', 'therapies.php', 'service.php', 'trainings.php', 'training.php']) ? ' class="is-active"' : '' ?>>Wellness Programs</summary>
                        <div class="nav-panel nav-panel--wellness">
                            <p class="nav-panel__label">Choose a wellness path</p>
                            <details class="nav-subgroup">
                                <summary><span><strong>Packages</strong><small>Immersive retreat programs</small></span></summary>
                                <div class="nav-submenu">
                                    <a href="programs.php"><strong>All Packages</strong><span>Compare every retreat package</span></a>
                                    <details class="nav-item-group">
                                        <summary>Panchakarma Rejuvenation</summary>
                                        <div class="nav-item-list"><a href="program.php?slug=panchakarma-rejuvenation">Package details</a><a href="programs.php#panchakarma-highlights">Program highlights</a><a href="contact.php?interest=Panchakarma%20Rejuvenation">Enquire about this package</a></div>
                                    </details>
                                    <details class="nav-item-group">
                                        <summary>Ayurvedic Wellness Immersion</summary>
                                        <div class="nav-item-list"><a href="program.php?slug=ayurvedic-wellness-immersion">Package details</a><a href="programs.php#immersion-highlights">Program highlights</a><a href="contact.php?interest=Ayurvedic%20Wellness%20Immersion">Enquire about this package</a></div>
                                    </details>
                                    <details class="nav-item-group">
                                        <summary>Mind–Body Balance Retreat</summary>
                                        <div class="nav-item-list"><a href="program.php?slug=mind-body-balance-retreat">Package details</a><a href="programs.php#mind-body-highlights">Program highlights</a><a href="contact.php?interest=Mind-Body%20Balance%20Retreat">Enquire about this package</a></div>
                                    </details>
                                </div>
                            </details>
                            <details class="nav-subgroup">
                                <summary><span><strong>Services</strong><small>Focused Ayurvedic care</small></span></summary>
                                <div class="nav-submenu">
                                    <a href="therapies.php"><strong>All Services</strong><span>Explore every healing service</span></a>
                                    <details class="nav-item-group">
                                        <summary>Panchakarma &amp; Detox Therapy</summary>
                                        <div class="nav-item-list"><a href="service.php?slug=panchakarma-detox-therapy">Service details</a><a href="therapies.php#panchakarma-highlights">Therapy highlights</a><a href="contact.php?interest=Panchakarma%20and%20Detox%20Therapy">Enquire about this service</a></div>
                                    </details>
                                    <details class="nav-item-group">
                                        <summary>Ayurvedic Wellness Retreats</summary>
                                        <div class="nav-item-list"><a href="service.php?slug=ayurvedic-wellness-retreats">Service details</a><a href="therapies.php#wellness-highlights">Retreat highlights</a><a href="contact.php?interest=Ayurvedic%20Wellness%20Retreat">Enquire about this service</a></div>
                                    </details>
                                    <details class="nav-item-group">
                                        <summary>Personalized Healing Programs</summary>
                                        <div class="nav-item-list"><a href="service.php?slug=personalized-healing-programs">Service details</a><a href="therapies.php#personalised-highlights">Program highlights</a><a href="contact.php?interest=Personalized%20Healing%20Program">Enquire about this service</a></div>
                                    </details>
                                </div>
                            </details>
                            <details class="nav-subgroup">
                                <summary><span><strong>Training</strong><small>Experiential learning programs</small></span></summary>
                                <div class="nav-submenu">
                                    <a href="trainings.php"><strong>All Training</strong><span>Explore every learning program</span></a>
                                    <details class="nav-item-group">
                                        <summary>Ayurveda Foundations Training</summary>
                                        <div class="nav-item-list"><a href="training.php?slug=ayurveda-foundations">Training details</a><a href="trainings.php#ayurveda-foundations-highlights">Course highlights</a><a href="contact.php?interest=Ayurveda%20Foundations%20Training">Enquire about this training</a></div>
                                    </details>
                                    <details class="nav-item-group">
                                        <summary>Ayurvedic Lifestyle &amp; Nutrition</summary>
                                        <div class="nav-item-list"><a href="training.php?slug=ayurvedic-lifestyle-nutrition">Training details</a><a href="trainings.php#lifestyle-nutrition-highlights">Course highlights</a><a href="contact.php?interest=Ayurvedic%20Lifestyle%20%26%20Nutrition">Enquire about this training</a></div>
                                    </details>
                                    <details class="nav-item-group">
                                        <summary>Mindfulness &amp; Yoga Facilitation</summary>
                                        <div class="nav-item-list"><a href="training.php?slug=mindfulness-yoga-facilitation">Training details</a><a href="trainings.php#mindfulness-yoga-highlights">Course highlights</a><a href="contact.php?interest=Mindfulness%20%26%20Yoga%20Facilitation">Enquire about this training</a></div>
                                    </details>
                                </div>
                            </details>
                        </div>
                    </details>
                </li>
                <li><a href="stay.php"<?= nav_active(['stay.php', 'accommodation.php']) ? ' aria-current="page"' : '' ?>>Accommodations</a></li>
                <li><a href="products.php"<?= nav_active(['products.php', 'product.php']) ? ' aria-current="page"' : '' ?>>Products</a></li>
                <li class="nav-group">
                    <details>
                        <summary<?= nav_active(['testimonials.php', 'journal.php', 'news.php', 'gallery.php']) ? ' class="is-active"' : '' ?>>Chronicles</summary>
                        <div class="nav-panel">
                            <p class="nav-panel__label">Explore stories</p>
                            <a href="testimonials.php"><strong>Testimonials</strong><span>Guest reflections</span></a>
                            <a href="journal.php"><strong>Blogs</strong><span>Ideas for conscious living</span></a>
                            <a href="news.php"><strong>News &amp; Events</strong><span>What is happening at the Aashram</span></a>
                            <a href="gallery.php"><strong>Gallery</strong><span>Moments of healing and serenity</span></a>
                        </div>
                    </details>
                </li>
            </ul>
            <a class="button button--small" href="contact.php">Contact</a>
        </nav>
    </div>
</header>
<main id="main-content">
    <?php
}

function render_footer(): void
{
    ?>
</main>
<footer class="site-footer">
    <div class="shell footer-grid">
        <div class="footer-intro">
            <img src="assets/images/logo.png" width="290" height="80" alt="Takshasheela Ayurveda Aashram">
            <p>Personalised Ayurvedic care, restorative stays, and nature-led wellbeing in Kathmandu.</p>
        </div>
        <div>
            <p class="footer-title">Explore</p>
            <a href="about.php">Our story</a>
            <a href="approach.php">Our approach</a>
            <a href="team.php">Our team</a>
            <a href="gallery.php">Gallery</a>
        </div>
        <div>
            <p class="footer-title">Visit</p>
            <a href="programs.php">Wellness programs</a>
            <a href="therapies.php">Therapies</a>
            <a href="trainings.php">Training</a>
            <a href="stay.php">Accommodation</a>
            <a href="products.php">Ayurvedic products</a>
            <a href="contact.php">Contact us</a>
        </div>
        <div class="footer-contact">
            <p class="footer-title">Takshasheela</p>
            <p>Kathmandu, Nepal</p>
            <a href="mailto:info@takshasheela.com">info@takshasheela.com</a>
            <a href="tel:+9779800000000">+977 9800 000 000</a>
        </div>
    </div>
    <div class="shell footer-bottom">
        <p>&copy; <?= date('Y') ?> Takshasheela Ayurveda Aashram</p>
        <div><a href="privacy.php">Privacy</a><a href="contact.php">Enquiries</a></div>
    </div>
</footer>
</body>
</html>
    <?php
}

function render_hero(string $eyebrow, string $title, string $description, string $variant = 'hero--about'): void
{
    ?>
<section class="page-hero <?= e($variant) ?>">
    <div class="shell page-hero__content" data-reveal>
        <p class="eyebrow eyebrow--light"><?= e($eyebrow) ?></p>
        <h1><?= e($title) ?></h1>
        <p><?= e($description) ?></p>
    </div>
</section>
    <?php
}

function render_cta(string $eyebrow, string $title, string $copy = 'Tell us what you need. We will help you choose a thoughtful next step.', string $href = 'contact.php'): void
{
    ?>
<section class="cta-band">
    <div class="shell cta-band__inner" data-reveal>
        <div><p class="eyebrow eyebrow--light"><?= e($eyebrow) ?></p><h2><?= e($title) ?></h2><p><?= e($copy) ?></p></div>
        <a class="button button--cream" href="<?= e($href) ?>">Start a conversation</a>
    </div>
</section>
    <?php
}

function render_wellness_video(): void
{
    ?>
<section class="section section--ink video-section">
    <div class="shell">
        <div class="video-embed" data-reveal>
            <iframe src="https://www.youtube-nocookie.com/embed/v7AYKMP6rOE?rel=0" title="Yoga for Complete Beginners — 20 minute practice" loading="lazy" referrerpolicy="strict-origin-when-cross-origin" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
        </div>
    </div>
</section>
    <?php
}
