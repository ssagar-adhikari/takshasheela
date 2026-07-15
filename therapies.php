<?php
require __DIR__ . '/includes/site.php';
require __DIR__ . '/includes/wellness.php';
render_header('Ayurvedic healing services', 'Explore Panchakarma, wellness, and personalised Ayurvedic healing services at Takshasheela.');
render_hero('Ayurvedic healing services', 'Explore our Ayurvedic healing services.', 'At Takshasheela Ayurveda Aashram, we offer powerful, soul-nourishing services inspired by authentic Ayurvedic tradition.', 'hero--therapy');
?>
<section class="section"><div class="shell"><div class="section-heading" data-reveal><p class="eyebrow">Our services</p><h2>Authentic therapies and personalised support.</h2></div><div class="card-grid">
<?php foreach (services() as $slug => $service): ?>
<article class="feature-card" id="<?= e($service['anchor']) ?>" data-reveal><img src="<?= e($service['image']) ?>" alt="<?= e($service['alt']) ?>" loading="lazy"><div class="feature-card__body"><p class="eyebrow"><?= e($service['category']) ?></p><h3><?= e($service['name']) ?></h3><p><?= e($service['short']) ?></p><ul class="feature-list" id="<?= e($service['anchor']) ?>-highlights"><?php foreach ($service['highlights'] as $highlight): ?><li><?= e($highlight) ?></li><?php endforeach; ?></ul><a class="text-link" href="service.php?slug=<?= e($slug) ?>">View service details</a></div></article>
<?php endforeach; ?>
</div></div></section>
<section class="section section--ink"><div class="shell intro-grid" data-reveal><div><p class="eyebrow eyebrow--light">Important to know</p><h2>Therapy follows assessment.</h2></div><div class="intro-copy"><p>Every treatment is selected after a practitioner considers your constitution, health needs, and present condition.</p><p>Ayurvedic wellness care is complementary and does not replace diagnosis or treatment from a qualified medical professional.</p></div></div></section>
<?php render_cta('Explore personalised care', 'Tell us what brings you here and our team will guide you.', 'Every therapy starts with a conversation and assessment.', 'contact.php?type=ayurvedic_service'); render_footer(); ?>
