<?php
require __DIR__ . '/includes/site.php';
require __DIR__ . '/includes/wellness.php';

$slug = (string) ($_GET['slug'] ?? '');
$program = package_by_slug($slug);
if (!$program) {
    http_response_code(404);
    render_header('Package not found', 'The requested wellness package could not be found.');
    ?>
    <section class="section product-not-found"><div class="shell"><p class="eyebrow">404</p><h1>Package not found.</h1><p>The package you are looking for may have moved.</p><a class="button" href="programs.php">View all packages</a></div></section>
    <?php render_footer(); exit;
}

render_header($program['name'], $program['short'], 'detail');
?>
<section class="offering-detail section">
    <div class="shell offering-detail__grid">
        <div class="offering-detail__media" data-reveal><img src="<?= e($program['image']) ?>" alt="<?= e($program['alt']) ?>"></div>
        <div class="offering-detail__content" data-reveal>
            <nav class="breadcrumb" aria-label="Breadcrumb"><a href="index.php">Home</a><span>/</span><a href="programs.php">Packages</a><span>/</span><span><?= e($program['name']) ?></span></nav>
            <p class="eyebrow"><?= e($program['category']) ?></p>
            <h1><?= e($program['name']) ?></h1>
            <p class="offering-detail__meta">Duration <strong><?= e($program['duration']) ?></strong></p>
            <p class="offering-detail__lead"><?= e($program['description']) ?></p>
            <a class="button" href="contact.php?interest=<?= rawurlencode($program['name']) ?>">Enquire about this package</a>
            <div class="offering-facts">
                <details open><summary>What is included</summary><ul><?php foreach ($program['includes'] as $item): ?><li><?= e($item) ?></li><?php endforeach; ?></ul></details>
                <details><summary>Who it may suit</summary><p><?= e($program['ideal_for']) ?></p></details>
                <details><summary>Your daily rhythm</summary><p><?= e($program['rhythm']) ?></p></details>
            </div>
            <p class="offering-note">Every program begins with assessment. Treatments and schedules may be adapted for your needs and safety.</p>
        </div>
    </div>
</section>
<?php render_wellness_video(); ?>
<section class="section section--cream"><div class="shell"><div class="section-heading"><p class="eyebrow">Other packages</p><h2>Find a path that feels right.</h2></div><div class="card-grid">
<?php foreach (packages() as $relatedSlug => $related): if ($relatedSlug === $slug) continue; ?>
<article class="feature-card"><img src="<?= e($related['image']) ?>" alt="<?= e($related['alt']) ?>" loading="lazy"><div class="feature-card__body"><p class="eyebrow"><?= e($related['category']) ?></p><h3><?= e($related['name']) ?></h3><p><?= e($related['short']) ?></p><a class="text-link" href="program.php?slug=<?= e($relatedSlug) ?>">View package</a></div></article>
<?php endforeach; ?></div></div></section>
<?php render_cta('Need thoughtful guidance?', 'Tell us what brings you here and we will help you compare the options.'); render_footer(); ?>
