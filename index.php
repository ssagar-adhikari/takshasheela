<?php
require __DIR__ . '/includes/site.php';
require __DIR__ . '/includes/wellness.php';
require __DIR__ . '/includes/chronicles.php';
render_header('Ayurvedic healing and restorative stays', 'Personalised Ayurvedic programs, therapies, and peaceful accommodation in Kathmandu.', 'dark');
?>
<section class="home-hero">
    <video class="home-hero__video" autoplay muted loop playsinline preload="metadata" poster="assets/images/hero.webp" aria-hidden="true" data-hero-video>
        <source src="assets/images/hero-video.mp4" type="video/mp4">
    </video>
    <div class="shell home-hero__content" data-reveal>
        <p class="eyebrow eyebrow--light">Nature heals — we guide</p>
        <h1>A journey awaits.</h1>
        <div class="home-hero__bottom">
            <p>Authentic Ayurvedic healing, personalised wellness programs, and restorative retreats at Takshasheela Ayurveda Aashram.</p>
            <span class="scroll-note">Kathmandu, Nepal</span>
        </div>
    </div>
</section>

<section class="section section--cream home-about">
    <div class="shell split">
        <div class="split__media" data-reveal>
            <img src="assets/images/inclusivity.jpg" width="900" height="1080" alt="Inclusivity and freedom at Takshasheela" loading="lazy">
        </div>
        <div class="split__content" data-reveal>
            <p class="eyebrow">About Takshasheela</p>
            <h2>Inclusivity</h2>
            <p>Takshasheela Ayurveda Aashram is a place to pause, reconnect, and restore balance through the timeless wisdom of Ayurveda. In peaceful surroundings in Kathmandu, we bring classical healing traditions into a warm and personal retreat experience.</p>
            <p>Our practitioners listen closely and shape therapies, nourishing meals, movement, and rest around your individual constitution and present needs. Every stay is guided with care, allowing the body and mind the space they need to heal naturally.</p>
            <a class="button button--outline" href="about.php">Discover our story</a>
        </div>
    </div>
</section>

<section class="section">
    <div class="shell">
        <div class="home-products__heading" data-reveal><div><p class="eyebrow">Packages</p><h2>Our packages that heal the past and open the future.</h2></div><div><p>From focused therapies to immersive stays, every experience is grounded in individual assessment and gentle progression.</p><a class="text-link" href="programs.php">View all packages</a></div></div>
        <div class="card-grid">
            <?php foreach (packages() as $slug => $program): ?>
                <article class="feature-card" data-reveal><a href="program.php?slug=<?= e($slug) ?>"><img src="<?= e($program['image']) ?>" alt="<?= e($program['alt']) ?>" loading="lazy"></a><div class="feature-card__body"><p class="eyebrow"><?= e($program['category']) ?></p><h3><a class="card-title-link" href="program.php?slug=<?= e($slug) ?>"><?= e($program['name']) ?></a></h3><p><?= e($program['short']) ?></p><a class="text-link" href="program.php?slug=<?= e($slug) ?>">View package details</a></div></article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section home-products">
    <div class="shell">
        <div class="home-products__heading" data-reveal><div><p class="eyebrow">From our apothecary</p><h2>Carry the ritual home.</h2></div><div><p>Botanical essentials inspired by the daily rhythms of Takshasheela, made for moments of care beyond your stay.</p><a class="text-link" href="products.php">Explore all products</a></div></div>
        <?php require_once __DIR__ . '/includes/products.php'; ?>
        <div class="product-grid product-grid--home">
            <?php $homeProducts = array_slice(products(), 0, 3, true); foreach ($homeProducts as $slug => $product): ?>
                <article class="product-card" data-reveal><a class="product-card__image" href="product.php?slug=<?= e($slug) ?>"><img src="<?= e($product['image']) ?>" alt="<?= e($product['alt']) ?>" loading="lazy"></a><div class="product-card__body"><p class="product-card__meta"><?= e($product['category']) ?> · <?= e($product['size']) ?></p><h3><a href="product.php?slug=<?= e($slug) ?>"><?= e($product['name']) ?></a></h3><p><?= e($product['short']) ?></p><div class="product-card__footer"><strong><?= e($product['price']) ?></strong><a class="text-link" href="product.php?slug=<?= e($slug) ?>">View product</a></div></div></article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section--cream">
    <div class="shell">
        <div class="home-products__heading" data-reveal><div><p class="eyebrow">Guest reflections</p><h2>What our clients say about us.</h2></div><div><p>Read how guests describe their experiences of rest, care, and renewal at Takshasheela.</p><a class="text-link" href="testimonials.php">Read all guest reflections</a></div></div>
        <div class="card-grid">
            <?php foreach (array_slice(chronicle_testimonials(), 0, 3) as $testimonial): ?>
            <article class="testimonial-card"><p class="testimonial-card__rating" aria-label="<?= e((string) $testimonial['rating']) ?> out of five stars"><?= str_repeat('★', $testimonial['rating']) ?></p><h3><?= e($testimonial['title']) ?></h3><blockquote>“<?= e($testimonial['quote']) ?>”</blockquote><footer><span><?= e($testimonial['initials']) ?></span><p><strong><?= e($testimonial['guest_name']) ?></strong><?= e($testimonial['guest_location'] ?? '') ?></p></footer></article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="quote-section">
    <div class="shell" data-reveal><blockquote>“Healing is not a rush back to who you were. It is a patient return to what is true.”<cite>Takshasheela philosophy</cite></blockquote></div>
</section>

<section class="section section--cream">
    <div class="shell">
        <div class="home-products__heading" data-reveal><div><p class="eyebrow">Blogs</p><h2>Staying inspired &amp; connected.</h2></div><div><p>Ideas, reflections, and practical wisdom for a more conscious approach to everyday wellbeing.</p><a class="text-link" href="journal.php">Explore all blogs</a></div></div>
        <div class="card-grid">
            <?php foreach (array_slice(chronicle_articles('blog'), 0, 3, true) as $slug => $article): ?>
            <article class="feature-card"><a href="chronicle.php?slug=<?= e($slug) ?>"><img src="<?= e($article['image']) ?>" alt="<?= e($article['alt']) ?>" loading="lazy"></a><div class="feature-card__body"><h3><a class="card-title-link" href="chronicle.php?slug=<?= e($slug) ?>"><?= e($article['title']) ?></a></h3><p><?= e($article['excerpt']) ?></p><a class="text-link" href="chronicle.php?slug=<?= e($slug) ?>">Read more</a></div></article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<section class="section">
    <div class="shell">
        <div class="home-products__heading" data-reveal><div><p class="eyebrow">News &amp; Events</p><h2>What is unfolding at Takshasheela.</h2></div><div><p>Retreat dates, community gatherings, and seasonal moments from the Aashram.</p><a class="text-link" href="news.php">View all news &amp; events</a></div></div>
        <div class="card-grid">
            <?php foreach (array_slice(chronicle_articles('news'), 0, 3, true) as $slug => $article): ?>
            <article class="feature-card" data-reveal><a href="chronicle.php?slug=<?= e($slug) ?>"><img src="<?= e($article['image']) ?>" alt="<?= e($article['alt']) ?>" loading="lazy"></a><div class="feature-card__body"><p class="eyebrow"><?= e($article['category']) ?></p><h3><a class="card-title-link" href="chronicle.php?slug=<?= e($slug) ?>"><?= e($article['title']) ?></a></h3><p><?= e($article['excerpt']) ?></p><a class="text-link" href="chronicle.php?slug=<?= e($slug) ?>">Read more</a></div></article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php render_cta('Plan your visit', 'Your healing journey can begin with a simple conversation.'); ?>
<?php render_footer(); ?>
