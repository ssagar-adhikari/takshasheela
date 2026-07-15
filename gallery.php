<?php
require __DIR__ . '/includes/site.php';
render_header('Gallery', 'See the spaces, therapies, rooms, and quiet moments that shape Takshasheela.');
render_hero('A glimpse inside', 'Spaces made for quieter days.', 'See the atmosphere, people, and simple details that support a restorative stay.', 'hero--about');
$images = [
    ['assets/images/welcome.png', 'A peaceful wellness space at Takshasheela'],
    ['assets/images/therapy.jpg', 'Ayurvedic therapy setting'],
    ['assets/images/deluxe-room.jpg', 'Deluxe accommodation'],
    ['assets/images/mindfulness.jpg', 'Mindful restorative practice'],
    ['assets/images/founders.jpg', 'Takshasheela community'],
    ['assets/images/emotional.jpg', 'Herbal wellness preparation'],
    ['assets/images/standard-room.jpg', 'Standard accommodation'],
    ['assets/images/inclusivity.jpg', 'Welcoming community atmosphere'],
];
?>
<section class="section"><div class="shell"><div class="section-heading section-heading--center" data-reveal><p class="eyebrow">The Aashram</p><h2>Healing has a texture, a pace, and a place.</h2><p class="lede">Warm light, natural materials, attentive hands, nourishing rituals, and room to breathe.</p></div><div class="gallery-grid"><?php foreach ($images as [$src, $alt]): ?><button class="gallery-item" type="button" data-lightbox="<?= e($src) ?>" data-reveal><img src="<?= e($src) ?>" alt="<?= e($alt) ?>" loading="lazy"></button><?php endforeach; ?></div></div></section>
<dialog class="gallery-dialog" id="gallery-dialog"><button class="dialog-close" type="button" data-dialog-close aria-label="Close image">×</button><img src="assets/images/welcome.png" alt=""></dialog>
<?php render_cta('Picture yourself here', 'Talk with our team about dates, programs, and the kind of stay you need.'); render_footer(); ?>
