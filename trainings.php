<?php
require __DIR__ . '/includes/site.php';
require __DIR__ . '/includes/wellness.php';
render_header('Ayurveda and wellness training', 'Explore practical Ayurveda, lifestyle, mindfulness, and yoga training at Takshasheela.');
render_hero('Wellness training', 'Learn, practise, and share with confidence.', 'Our experiential training programs bring traditional wellbeing principles into a clear, practical learning environment guided by experienced facilitators.', 'hero--founders');
?>
<section class="section"><div class="shell"><div class="section-heading" data-reveal><p class="eyebrow">Our training</p><h2>Grounded learning for everyday life and practice.</h2><p class="lede">Choose a foundational course, explore Ayurvedic lifestyle in more depth, or develop your skills as a wellbeing facilitator.</p></div><div class="card-grid">
<?php foreach (trainings() as $slug => $training): ?>
<article class="feature-card" id="<?= e($training['anchor']) ?>" data-reveal><img src="<?= e($training['image']) ?>" alt="<?= e($training['alt']) ?>" loading="lazy"><div class="feature-card__body"><p class="eyebrow"><?= e($training['category']) ?></p><h3><?= e($training['name']) ?></h3><p><?= e($training['short']) ?></p><ul class="feature-list" id="<?= e($training['anchor']) ?>-highlights"><?php foreach ($training['highlights'] as $highlight): ?><li><?= e($highlight) ?></li><?php endforeach; ?></ul><a class="text-link" href="training.php?slug=<?= e($slug) ?>">View training details</a></div></article>
<?php endforeach; ?>
</div></div></section>
<section class="section section--ink"><div class="shell intro-grid" data-reveal><div><p class="eyebrow eyebrow--light">Learning approach</p><h2>Understanding grows through practice.</h2></div><div class="intro-copy"><p>Each course combines clear teaching with observation, reflection, discussion, and guided experience.</p><p>Training is educational and does not qualify participants to diagnose medical conditions or replace regulated professional credentials.</p></div></div></section>
<?php render_cta('Find your course', 'Tell us about your experience and learning goals, and we will help you choose.', 'Share your background, preferred dates, and learning goals.', 'contact.php?type=wellness_training'); render_footer(); ?>
