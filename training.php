<?php
require __DIR__ . '/includes/site.php';
require __DIR__ . '/includes/wellness.php';

$slug = (string) ($_GET['slug'] ?? '');
$training = training_by_slug($slug);
if (!$training) {
    http_response_code(404);
    render_header('Training not found', 'The requested wellness training could not be found.');
    ?>
    <section class="section product-not-found"><div class="shell"><p class="eyebrow">404</p><h1>Training not found.</h1><p>The training you are looking for may have moved.</p><a class="button" href="trainings.php">View all training</a></div></section>
    <?php render_footer(); exit;
}

render_header($training['name'], $training['short'], 'detail');
?>
<section class="offering-detail section">
    <div class="shell offering-detail__grid">
        <div class="offering-detail__media" data-reveal><img src="<?= e($training['image']) ?>" alt="<?= e($training['alt']) ?>"></div>
        <div class="offering-detail__content" data-reveal>
            <nav class="breadcrumb" aria-label="Breadcrumb"><a href="index.php">Home</a><span>/</span><a href="trainings.php">Training</a><span>/</span><span><?= e($training['name']) ?></span></nav>
            <p class="eyebrow"><?= e($training['category']) ?></p>
            <h1><?= e($training['name']) ?></h1>
            <p class="offering-detail__meta">Duration <strong><?= e($training['duration']) ?></strong></p>
            <p class="offering-detail__lead"><?= e($training['description']) ?></p>
            <a class="button" href="contact.php?interest=<?= rawurlencode($training['name']) ?>">Enquire about this training</a>
            <div class="offering-facts">
                <details open><summary>What is included</summary><ul><?php foreach ($training['includes'] as $item): ?><li><?= e($item) ?></li><?php endforeach; ?></ul></details>
                <details><summary>Who it may suit</summary><p><?= e($training['ideal_for']) ?></p></details>
                <details><summary>Learning format</summary><p><?= e($training['format']) ?></p></details>
            </div>
            <p class="offering-note">Course schedules and content may be adapted to the group. Please ask us about dates, prerequisites, and availability.</p>
        </div>
    </div>
</section>
<?php render_wellness_video(); ?>
<section class="section section--cream"><div class="shell"><div class="section-heading"><p class="eyebrow">Other training</p><h2>Continue your learning.</h2></div><div class="card-grid">
<?php foreach (trainings() as $relatedSlug => $related): if ($relatedSlug === $slug) continue; ?>
<article class="feature-card"><img src="<?= e($related['image']) ?>" alt="<?= e($related['alt']) ?>" loading="lazy"><div class="feature-card__body"><p class="eyebrow"><?= e($related['category']) ?></p><h3><?= e($related['name']) ?></h3><p><?= e($related['short']) ?></p><a class="text-link" href="training.php?slug=<?= e($relatedSlug) ?>">View training</a></div></article>
<?php endforeach; ?></div></div></section>
<?php render_cta('Have a question?', 'Share your background and learning goals, and our team will guide you.'); render_footer(); ?>
