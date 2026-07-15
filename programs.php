<?php
require __DIR__ . '/includes/site.php';
require __DIR__ . '/includes/wellness.php';
render_header('Wellness programs', 'Explore rejuvenation, wellness immersion, and mind-body balance retreat packages at Takshasheela.');
render_hero('Wellness programs', 'Explore our natural healing packages.', 'At Takshasheela Ayurveda Aashram, we offer powerful, soul-nourishing retreats inspired by authentic Ayurvedic tradition. Staying true to our motto Nature Heals — We Guide, our signature seven-day healing process and focused wellness workshops bring deep purification, emotional harmony, and holistic transformation.', 'hero--water');
?>
<section class="section"><div class="shell"><div class="section-heading" data-reveal><p class="eyebrow">Packages</p><h2>Personalised paths to healing and renewal.</h2></div><div class="card-grid">
<?php foreach (packages() as $slug => $program): ?>
<article class="feature-card" id="<?= e($program['anchor']) ?>" data-reveal><img src="<?= e($program['image']) ?>" alt="<?= e($program['alt']) ?>" loading="lazy"><div class="feature-card__body"><p class="eyebrow"><?= e($program['category']) ?></p><h3><?= e($program['name']) ?></h3><p><?= e($program['short']) ?></p><ul class="feature-list" id="<?= e($program['anchor']) ?>-highlights"><?php foreach ($program['highlights'] as $highlight): ?><li><?= e($highlight) ?></li><?php endforeach; ?></ul><a class="text-link" href="program.php?slug=<?= e($slug) ?>">View package details</a></div></article>
<?php endforeach; ?>
</div></div></section>
<?php render_cta('Choose your retreat', 'Speak with our team about the package that best matches your needs.', 'Tell us what you need. We will help you choose a thoughtful next step.', 'contact.php?type=wellness_package'); render_footer(); ?>
