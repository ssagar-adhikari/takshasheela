<?php
require __DIR__ . '/includes/site.php';
require __DIR__ . '/includes/chronicles.php';
$articles = chronicle_articles('news');
$featured = chronicle_featured($articles);
render_header('News & Events', 'Retreats, healing programs, community gatherings, and seasonal moments at Takshasheela.');
render_hero('Takshasheela chronicles', 'News & Events', 'Stay connected with the retreats, healing programs, community gatherings, and seasonal moments unfolding at Takshasheela Ayurveda Aashram.', 'hero--about');
?>
<section class="section"><div class="shell"><div class="section-heading"><p class="eyebrow">Latest updates</p><h2>Stay Connected With Our Latest News &amp; Events</h2><p class="lede">Explore the meaningful experiences unfolding at Takshasheela Ayurveda Aashram.</p></div>
<?php if ($featured): ?><article class="journal-feature"><a href="chronicle.php?slug=<?= e($featured['slug']) ?>"><img src="<?= e($featured['image']) ?>" alt="<?= e($featured['alt']) ?>" loading="lazy"></a><div class="journal-feature__copy"><p class="eyebrow"><?= e($featured['category']) ?></p><h2><?= e($featured['title']) ?></h2><p><?= e($featured['excerpt']) ?></p><a class="text-link" href="chronicle.php?slug=<?= e($featured['slug']) ?>">Read update</a></div></article><?php endif; ?>
</div></section>
<section class="section section--cream"><div class="shell">
<?php if ($articles): ?><div class="card-grid"><?php foreach ($articles as $slug => $article): if ($featured && $slug === $featured['slug']) continue; ?>
<article class="feature-card"><a href="chronicle.php?slug=<?= e($slug) ?>"><img src="<?= e($article['image']) ?>" alt="<?= e($article['alt']) ?>" loading="lazy"></a><div class="feature-card__body"><p class="eyebrow"><?= e($article['category']) ?></p><h3><a class="card-title-link" href="chronicle.php?slug=<?= e($slug) ?>"><?= e($article['title']) ?></a></h3><p><?= e($article['excerpt']) ?></p><a class="text-link" href="chronicle.php?slug=<?= e($slug) ?>">Read more</a></div></article>
<?php endforeach; ?></div><?php else: ?><p>No news or events are published yet.</p><?php endif; ?>
</div></section>
<?php render_cta('Stay connected', 'Contact us for upcoming retreat dates and community gatherings.'); render_footer(); ?>
