<?php
require __DIR__ . '/includes/site.php';
require __DIR__ . '/includes/chronicles.php';
$articles = chronicle_articles('blog');
$featured = chronicle_featured($articles);
render_header('Blogs', 'Ideas and practical applications for conscious relating, authentic connection, and everyday wellbeing.');
render_hero('Takshasheela chronicles', 'Staying Inspired & Connected', 'The Takshasheela blog gives ideas and practical applications for conscious relating, authentic connection, helping you maintain a robust sense of self in your everyday life.', 'hero--water');
?>
<?php if ($featured): ?>
<section class="section"><div class="shell"><article class="journal-feature">
    <a href="chronicle.php?slug=<?= e($featured['slug']) ?>"><img src="<?= e($featured['image']) ?>" alt="<?= e($featured['alt']) ?>" loading="lazy"></a>
    <div class="journal-feature__copy"><p class="eyebrow"><?= e($featured['category']) ?></p><h2><?= e($featured['title']) ?></h2><p><?= e($featured['excerpt']) ?></p><a class="text-link" href="chronicle.php?slug=<?= e($featured['slug']) ?>">Read featured reflection</a></div>
</article></div></section>
<?php endif; ?>
<section class="section section--cream" id="articles"><div class="shell"><div class="section-heading"><p class="eyebrow">More from the journal</p><h2>Stories of awareness and transformation.</h2></div>
<?php if ($articles): ?><div class="card-grid">
<?php foreach ($articles as $slug => $article): if ($featured && $slug === $featured['slug']) continue; ?>
<article class="feature-card"><a href="chronicle.php?slug=<?= e($slug) ?>"><img src="<?= e($article['image']) ?>" alt="<?= e($article['alt']) ?>" loading="lazy"></a><div class="feature-card__body"><p class="eyebrow"><?= e($article['category']) ?></p><h3><a class="card-title-link" href="chronicle.php?slug=<?= e($slug) ?>"><?= e($article['title']) ?></a></h3><p><?= e($article['excerpt']) ?></p><a class="text-link" href="chronicle.php?slug=<?= e($slug) ?>">Read more</a></div></article>
<?php endforeach; ?></div><?php else: ?><p>No blog articles are published yet.</p><?php endif; ?>
</div></section>
<?php render_cta('Experience it for yourself', 'Explore how these ideas come alive during a Takshasheela retreat.'); render_footer(); ?>
