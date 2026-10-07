<?php
require __DIR__ . '/includes/site.php';
require __DIR__ . '/includes/chronicles.php';
$testimonials = chronicle_testimonials();
render_header('Testimonials', 'Guest reflections on healing, renewal, and inner clarity at Takshasheela Ayurveda Aashram.');
render_hero('Guest reflections', 'Stories of healing, renewal, and lasting peace from our guests.', 'These heartfelt reflections share the calm, transformation, and inner clarity experienced at Takshasheela Ayurveda Aashram.', 'hero--water');
?>
<section class="section section--cream"><div class="shell"><div class="section-heading section-heading--center"><p class="eyebrow">Guest reflections</p><h2>In their own words.</h2></div>
<?php if ($testimonials): ?><div class="card-grid"><?php foreach ($testimonials as $testimonial): ?>
<article class="testimonial-card"><p class="testimonial-card__rating" aria-label="<?= e((string) $testimonial['rating']) ?> out of five stars"><?= str_repeat('★', $testimonial['rating']) ?></p><h3><?= e($testimonial['title']) ?></h3><blockquote>“<?= e($testimonial['quote']) ?>”</blockquote><footer><span><?= e($testimonial['initials']) ?></span><p><strong><?= e($testimonial['guest_name']) ?></strong><?= e($testimonial['guest_location'] ?? '') ?></p></footer></article>
<?php endforeach; ?></div><?php else: ?><p>No guest reflections are published yet.</p><?php endif; ?>
</div></section>
<?php render_cta('Begin your story', 'A restorative stay starts with a thoughtful conversation.'); render_footer(); ?>
