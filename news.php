<?php
require __DIR__ . '/includes/site.php';
render_header('News & Events', 'Retreats, healing programs, community gatherings, and seasonal moments at Takshasheela.');
render_hero('Takshasheela chronicles', 'News & Events', 'Stay connected with the retreats, healing programs, community gatherings, and seasonal moments unfolding at Takshasheela Ayurveda Aashram.', 'hero--about');
?>
<section class="section">
    <div class="shell">
        <div class="section-heading"><p class="eyebrow">Latest updates</p><h2>Stay Connected With Our Latest News &amp; Events</h2><p class="lede">Explore the meaningful experiences unfolding at Takshasheela Ayurveda Aashram. From immersive wellness retreats and healing programs to community gatherings and seasonal celebrations, this page keeps you informed about the moments that bring our sanctuary to life.</p></div>
        <article class="journal-feature">
            <img src="assets/images/journal-one.png" alt="Featured wellness retreat at Takshasheela" loading="lazy">
            <div class="journal-feature__copy"><p class="eyebrow">Featured update</p><h2>New Wellness Retreats &amp; Healing Experiences</h2><p>Discover upcoming retreat journeys that combine authentic Ayurvedic therapies, guided yoga, meditation, and restorative care in a calm and nurturing setting.</p><a class="text-link" href="programs.php">Explore now</a></div>
        </article>
    </div>
</section>
<section class="section section--cream">
    <div class="shell"><div class="card-grid">
        <article class="feature-card"><img src="assets/images/therapy.jpg" alt="Ayurvedic healing program" loading="lazy"><div class="feature-card__body"><p class="eyebrow">Healing program</p><h3>Panchakarma &amp; Detox Programs</h3><p>Deep cleansing and rejuvenation therapies designed to restore balance, vitality, and inner calm.</p><a class="text-link" href="therapies.php#panchakarma">Learn more</a></div></article>
        <article class="feature-card"><img src="assets/images/mindfulness.jpg" alt="Community wellness gathering" loading="lazy"><div class="feature-card__body"><p class="eyebrow">Community</p><h3>Meditation Circles &amp; Wellness Gatherings</h3><p>Join mindful conversations, breathing practices, and peaceful gatherings that help you reconnect with yourself.</p><a class="text-link" href="contact.php">Join the circle</a></div></article>
        <article class="feature-card"><img src="assets/images/journal-three.png" alt="Seasonal celebration at Takshasheela" loading="lazy"><div class="feature-card__body"><p class="eyebrow">Seasonal</p><h3>Seasonal Celebrations &amp; Spiritual Gatherings</h3><p>Celebrate meaningful moments of reflection, ritual, and connection with the ashram community throughout the year.</p><a class="text-link" href="journal.php">Discover more</a></div></article>
    </div></div>
</section>
<?php render_cta('Stay connected', 'Contact us for upcoming retreat dates and community gatherings.'); render_footer(); ?>
