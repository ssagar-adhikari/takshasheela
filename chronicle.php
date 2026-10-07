<?php
require __DIR__ . '/includes/site.php';
require __DIR__ . '/includes/chronicles.php';

$slug = (string) ($_GET['slug'] ?? '');
$article = chronicle_by_slug($slug);
if (!$article) {
    http_response_code(404);
    render_header('Chronicle not found', 'The requested Chronicle article could not be found.');
    ?>
    <section class="section product-not-found"><div class="shell"><p class="eyebrow">404</p><h1>Chronicle not found.</h1><p>The article you are looking for may have moved or is no longer published.</p><a class="button" href="journal.php">Explore the Chronicles</a></div></section>
    <?php render_footer(); exit;
}

$listing = $article['type'] === 'news' ? 'news.php' : 'journal.php';
$listingLabel = $article['type'] === 'news' ? 'News & Events' : 'Blogs';
render_header($article['title'], $article['excerpt'], 'detail');
?>
<section class="offering-detail section">
    <div class="shell offering-detail__grid">
        <div class="offering-detail__media" data-reveal><img src="<?= e($article['image']) ?>" alt="<?= e($article['alt']) ?>"></div>
        <div class="offering-detail__content" data-reveal>
            <nav class="breadcrumb" aria-label="Breadcrumb"><a href="index.php">Home</a><span>/</span><a href="<?= e($listing) ?>"><?= e($listingLabel) ?></a><span>/</span><span><?= e($article['title']) ?></span></nav>
            <p class="eyebrow"><?= e($article['category']) ?></p>
            <h1><?= e($article['title']) ?></h1>
            <?php if ($article['published_at']): ?><p class="chronicle-date"><?= e($article['published_at']) ?></p><?php endif; ?>
            <p class="offering-detail__lead"><?= e($article['excerpt']) ?></p>
        </div>
    </div>
</section>
<section class="section section--cream"><article class="shell chronicle-article-body"><?php chronicle_paragraphs($article['body']); ?></article></section>
<?php render_cta('Continue your journey', 'Talk with our team about retreats, gatherings, and personalised care.'); render_footer(); ?>
