<?php
require __DIR__ . '/includes/site.php';
require __DIR__ . '/includes/accommodations.php';

$slug = (string) ($_GET['slug'] ?? '');
$room = accommodation_by_slug($slug);
if (!$room) {
    http_response_code(404);
    render_header('Accommodation not found', 'The requested accommodation could not be found.');
    ?>
    <section class="section product-not-found"><div class="shell"><p class="eyebrow">404</p><h1>Accommodation not found.</h1><p>The room you are looking for may have moved.</p><a class="button" href="stay.php">View all accommodations</a></div></section>
    <?php render_footer(); exit;
}

render_header($room['name'], $room['short'], 'detail');
?>
<section class="offering-detail section">
    <div class="shell offering-detail__grid">
        <div class="offering-detail__media" data-reveal><img src="<?= e($room['image']) ?>" alt="<?= e($room['alt']) ?>"></div>
        <div class="offering-detail__content" data-reveal>
            <nav class="breadcrumb" aria-label="Breadcrumb"><a href="index.php">Home</a><span>/</span><a href="stay.php">Accommodations</a><span>/</span><span><?= e($room['name']) ?></span></nav>
            <p class="eyebrow"><?= e($room['category']) ?></p>
            <h1><?= e($room['name']) ?></h1>
            <p class="offering-detail__lead"><?= e($room['description']) ?></p>
            <a class="button" href="contact.php?interest=<?= rawurlencode($room['name']) ?>">Check availability</a>
            <div class="offering-facts">
                <details open><summary>Room features</summary><ul><?php foreach ($room['features'] as $feature): ?><li><?= e($feature) ?></li><?php endforeach; ?></ul></details>
                <details><summary>Who it may suit</summary><p><?= e($room['ideal_for']) ?></p></details>
                <details><summary>Before you book</summary><p><?= e($room['stay_note']) ?></p></details>
            </div>
            <p class="offering-note">Accommodation availability depends on your retreat dates. Our team will confirm the room arrangement and what is included with your stay.</p>
        </div>
    </div>
</section>
<section class="section section--cream"><div class="shell"><div class="section-heading"><p class="eyebrow">Another option</p><h2>Explore our rooms.</h2></div><div class="card-grid card-grid--compact">
<?php foreach (accommodations() as $relatedSlug => $related): if ($relatedSlug === $slug) continue; ?>
<article class="feature-card"><img src="<?= e($related['image']) ?>" alt="<?= e($related['alt']) ?>" loading="lazy"><div class="feature-card__body"><p class="eyebrow"><?= e($related['category']) ?></p><h3><?= e($related['name']) ?></h3><p><?= e($related['short']) ?></p><a class="text-link" href="accommodation.php?slug=<?= e($relatedSlug) ?>">View accommodation</a></div></article>
<?php endforeach; ?></div></div></section>
<?php render_cta('Plan your stay', 'Ask about availability, retreat dates, and the room setup you need.'); render_footer(); ?>
