<?php
require __DIR__ . '/includes/site.php';
require __DIR__ . '/includes/chronicles.php';
$images = chronicle_gallery();
render_header('Gallery', 'See the spaces, therapies, rooms, and quiet moments that shape Takshasheela.');
render_hero('A glimpse inside', 'Spaces made for quieter days.', 'See the atmosphere, people, and simple details that support a restorative stay.', 'hero--about');
?>
<section class="section"><div class="shell"><div class="section-heading section-heading--center" data-reveal><p class="eyebrow">The Aashram</p><h2>Healing has a texture, a pace, and a place.</h2><p class="lede">Warm light, natural materials, attentive hands, nourishing rituals, and room to breathe.</p></div>
<?php if ($images): ?><div class="gallery-grid"><?php foreach ($images as $image): ?><button class="gallery-item" type="button" data-lightbox="<?= e($image['image']) ?>" data-lightbox-alt="<?= e($image['alt']) ?>" data-reveal><img src="<?= e($image['image']) ?>" alt="<?= e($image['alt']) ?>" loading="lazy"></button><?php endforeach; ?></div><?php else: ?><p>No gallery images are published yet.</p><?php endif; ?>
</div></section>
<?php if ($images): ?><dialog class="gallery-dialog" id="gallery-dialog"><button class="dialog-close" type="button" data-dialog-close aria-label="Close image">×</button><img src="<?= e($images[0]['image']) ?>" alt="<?= e($images[0]['alt']) ?>"></dialog><?php endif; ?>
<?php render_cta('Picture yourself here', 'Talk with our team about dates, programs, and the kind of stay you need.'); render_footer(); ?>
