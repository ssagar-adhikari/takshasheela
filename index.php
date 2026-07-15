<?php
require __DIR__ . '/includes/site.php';
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
        <div class="section-heading" data-reveal><p class="eyebrow">Packages</p><h2>Our packages that heal the past and open the future.</h2><p class="lede">From focused therapies to immersive stays, every experience is grounded in individual assessment and gentle progression.</p></div>
        <div class="card-grid">
            <article class="feature-card" data-reveal><img src="assets/images/wellness.jpg" alt="Ayurvedic wellness program" loading="lazy"><div class="feature-card__body"><h3>Wellness programs</h3><p>Multi-day journeys combining consultation, therapies, nourishing food, movement, and deep rest.</p><a class="text-link" href="programs.php">Explore programs</a></div></article>
            <article class="feature-card" data-reveal><img src="assets/images/therapy.jpg" alt="Personalised Ayurvedic therapy" loading="lazy"><div class="feature-card__body"><h3>Ayurvedic therapies</h3><p>Focused, practitioner-guided treatments selected for your constitution and wellbeing goals.</p><a class="text-link" href="therapies.php">View therapies</a></div></article>
            <article class="feature-card" data-reveal><img src="assets/images/deluxe-room.jpg" alt="Peaceful room at Takshasheela" loading="lazy"><div class="feature-card__body"><h3>Restorative stays</h3><p>Quiet rooms and a supportive daily rhythm designed to help the nervous system settle.</p><a class="text-link" href="stay.php">Explore your stay</a></div></article>
        </div>
    </div>
</section>

<section class="section home-products">
    <div class="shell">
        <div class="home-products__heading" data-reveal><div><p class="eyebrow">From our apothecary</p><h2>Carry the ritual home.</h2></div><div><p>Botanical essentials inspired by the daily rhythms of Takshasheela, made for moments of care beyond your stay.</p><a class="text-link" href="products.php">Explore all products</a></div></div>
        <?php require_once __DIR__ . '/includes/products.php'; ?>
        <div class="product-grid product-grid--home">
            <?php $homeProducts = array_slice(products(), 0, 3, true); foreach ($homeProducts as $slug => $product): ?>
                <article class="product-card" data-reveal><a class="product-card__image" href="product.php?slug=<?= e($slug) ?>"><img src="<?= e($product['image']) ?>" alt="<?= e($product['name']) ?>" loading="lazy"></a><div class="product-card__body"><p class="product-card__meta"><?= e($product['category']) ?> · <?= e($product['size']) ?></p><h3><a href="product.php?slug=<?= e($slug) ?>"><?= e($product['name']) ?></a></h3><p><?= e($product['short']) ?></p><div class="product-card__footer"><strong><?= e($product['price']) ?></strong><a class="text-link" href="product.php?slug=<?= e($slug) ?>">View product</a></div></div></article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section--cream">
    <div class="shell">
        <div class="section-heading" data-reveal><p class="eyebrow">Guest reflections</p><h2>What our clients say about us.</h2></div>
        <div class="card-grid">
            <article class="testimonial-card"><p class="testimonial-card__rating" aria-label="Five out of five stars">★★★★★</p><h3>A Deep Sense of Calm</h3><blockquote>“The retreat felt like a true reset for my body and mind. Every treatment was thoughtful, grounding, and deeply restorative.”</blockquote><footer><span>AS</span><p><strong>Asha Sharma</strong>Kathmandu, Nepal</p></footer></article>
            <article class="testimonial-card"><p class="testimonial-card__rating" aria-label="Five out of five stars">★★★★★</p><h3>Warmth and Healing</h3><blockquote>“I came seeking peace and left with clarity, balance, and a renewed sense of connection to myself and nature.”</blockquote><footer><span>PK</span><p><strong>Pooja K.C.</strong>Pokhara, Nepal</p></footer></article>
            <article class="testimonial-card"><p class="testimonial-card__rating" aria-label="Five out of five stars">★★★★★</p><h3>A Beautiful Retreat Experience</h3><blockquote>“The atmosphere was serene, the care was genuine, and every moment felt aligned with healing and self-discovery.”</blockquote><footer><span>RB</span><p><strong>Rina Bhandari</strong>Lalitpur, Nepal</p></footer></article>
        </div>
        <p class="section-link"><a class="text-link" href="testimonials.php">Read all guest reflections</a></p>
    </div>
</section>

<section class="quote-section">
    <div class="shell" data-reveal><blockquote>“Healing is not a rush back to who you were. It is a patient return to what is true.”<cite>Takshasheela philosophy</cite></blockquote></div>
</section>

<section class="section section--cream">
    <div class="shell">
        <div class="section-heading" data-reveal><p class="eyebrow">Blogs</p><h2>Staying inspired &amp; connected.</h2></div>
        <div class="card-grid">
            <article class="feature-card"><img src="assets/images/journal-one.png" alt="A reflective Takshasheela healing retreat" loading="lazy"><div class="feature-card__body"><h3>What Actually Happens on a Takshasheela Healing Retreat</h3><p>Making room to reconnect with yourself during a time of major change.</p><a class="text-link" href="journal.php">Read more</a></div></article>
            <article class="feature-card"><img src="assets/images/journal-two.png" alt="A parent taking restorative time" loading="lazy"><div class="feature-card__body"><h3>Why Every Parent Deserves Seven Days of Me Time</h3><p>A restorative pause for people whose care for others rarely stops.</p><a class="text-link" href="journal.php#articles">Read more</a></div></article>
            <article class="feature-card"><img src="assets/images/journal-three.png" alt="A journey home to wholeness" loading="lazy"><div class="feature-card__body"><h3>The Magic of Healing – Coming Home to Wholeness</h3><p>Ines Schönenberg reflects on how her Takshasheela retreat enriched her life.</p><a class="text-link" href="journal.php#articles">Read more</a></div></article>
        </div>
    </div>
</section>
<section class="section">
    <div class="shell">
        <div class="section-heading" data-reveal><p class="eyebrow">News &amp; Events</p><h2>What is unfolding at Takshasheela.</h2><p class="lede">Retreat dates, community gatherings, and seasonal moments from the Aashram.</p></div>
        <div class="card-grid">
            <article class="feature-card" data-reveal><img src="assets/images/therapy.jpg" alt="Ayurvedic healing program" loading="lazy"><div class="feature-card__body"><p class="eyebrow">Healing program</p><h3>Panchakarma &amp; Detox Programs</h3><p>Deep cleansing and rejuvenation therapies designed to restore balance, vitality, and inner calm.</p><a class="text-link" href="therapies.php#panchakarma">Learn more</a></div></article>
            <article class="feature-card" data-reveal><img src="assets/images/mindfulness.jpg" alt="Community wellness gathering" loading="lazy"><div class="feature-card__body"><p class="eyebrow">Community</p><h3>Meditation Circles &amp; Wellness Gatherings</h3><p>Join mindful conversations, breathing practices, and peaceful gatherings that help you reconnect with yourself.</p><a class="text-link" href="contact.php">Join the circle</a></div></article>
            <article class="feature-card" data-reveal><img src="assets/images/journal-three.png" alt="Seasonal celebration at Takshasheela" loading="lazy"><div class="feature-card__body"><p class="eyebrow">Seasonal</p><h3>Seasonal &amp; Spiritual Gatherings</h3><p>Meaningful moments of reflection, ritual, and connection with the Aashram community.</p><a class="text-link" href="news.php">Discover more</a></div></article>
        </div>
    </div>
</section>
<?php render_cta('Plan your visit', 'Your healing journey can begin with a simple conversation.'); ?>
<?php render_footer(); ?>
