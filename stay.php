<?php
require __DIR__ . '/includes/site.php';
require __DIR__ . '/includes/accommodations.php';
render_header('Accommodations', 'Explore comfortable, peaceful rooms designed to support rest and rejuvenation during your Takshasheela stay.');
render_hero('Accommodations', 'Stay in comfort and tranquillity.', 'Explore thoughtfully designed accommodations created to provide comfort, tranquillity, and a restful connection with the aashram environment.', 'hero--stay');
?>
<section class="section"><div class="shell intro-grid" data-reveal><div><p class="eyebrow">Our Rooms &amp; Suites</p><h2>Discover Your Perfect Stay</h2></div><div class="intro-copy"><p>Each room at Takshasheela is thoughtfully arranged to support rest, comfort, and renewal throughout your retreat experience.</p></div></div></section>
<?php $position = 0; foreach (accommodations() as $slug => $room): ?>
<section class="section<?= $position % 2 === 0 ? ' section--cream' : '' ?>"><div class="shell split<?= $position % 2 !== 0 ? ' split--reverse' : '' ?>"><div class="split__media split__media--landscape" data-reveal><img src="<?= e($room['image']) ?>" alt="<?= e($room['alt']) ?>" loading="lazy"></div><div class="split__content" data-reveal><p class="eyebrow"><?= e($room['name']) ?></p><h2><?= e($room['heading']) ?></h2><p><?= e($room['short']) ?></p><ul class="feature-list"><?php foreach ($room['highlights'] as $highlight): ?><li><?= e($highlight) ?></li><?php endforeach; ?></ul><a class="text-link" href="accommodation.php?slug=<?= e($slug) ?>">View accommodation details</a></div></div></section>
<?php $position++; endforeach; ?>
<?php render_cta('Plan your stay', 'Ask about room availability, retreat dates, and room features.', 'Tell us your dates, room preference, and any practical needs.', 'contact.php?type=accommodation'); render_footer(); ?>
