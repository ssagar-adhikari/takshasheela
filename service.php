<?php
require __DIR__ . '/includes/site.php';
require __DIR__ . '/includes/wellness.php';

$slug = (string) ($_GET['slug'] ?? '');
$service = service_by_slug($slug);
if (!$service) {
    http_response_code(404);
    render_header('Service not found', 'The requested Ayurvedic service could not be found.');
    ?>
    <section class="section product-not-found"><div class="shell"><p class="eyebrow">404</p><h1>Service not found.</h1><p>The service you are looking for may have moved.</p><a class="button" href="therapies.php">View all services</a></div></section>
    <?php render_footer(); exit;
}

render_header($service['name'], $service['short'], 'detail');
?>
<section class="offering-detail section">
    <div class="shell offering-detail__grid">
        <div class="offering-detail__media" data-reveal><img src="<?= e($service['image']) ?>" alt="<?= e($service['alt']) ?>"></div>
        <div class="offering-detail__content" data-reveal>
            <nav class="breadcrumb" aria-label="Breadcrumb"><a href="index.php">Home</a><span>/</span><a href="therapies.php">Services</a><span>/</span><span><?= e($service['name']) ?></span></nav>
            <p class="eyebrow"><?= e($service['category']) ?></p>
            <h1><?= e($service['name']) ?></h1>
            <p class="offering-detail__lead"><?= e($service['description']) ?></p>
            <a class="button" href="contact.php?interest=<?= rawurlencode($service['interest']) ?>">Enquire about this service</a>
            <div class="offering-facts">
                <details open><summary>What care may include</summary><ul><?php foreach ($service['includes'] as $item): ?><li><?= e($item) ?></li><?php endforeach; ?></ul></details>
                <details><summary>Who it may suit</summary><p><?= e($service['ideal_for']) ?></p></details>
                <details><summary>How it begins</summary><p><?= e($service['process']) ?></p></details>
            </div>
            <p class="offering-note">Ayurvedic wellness care is complementary and does not replace diagnosis or treatment from a qualified medical professional.</p>
        </div>
    </div>
</section>
<?php render_wellness_video(); ?>
<section class="section section--cream"><div class="shell"><div class="section-heading"><p class="eyebrow">Other services</p><h2>Explore personalised care.</h2></div><div class="card-grid">
<?php foreach (services() as $relatedSlug => $related): if ($relatedSlug === $slug) continue; ?>
<article class="feature-card"><img src="<?= e($related['image']) ?>" alt="<?= e($related['alt']) ?>" loading="lazy"><div class="feature-card__body"><p class="eyebrow"><?= e($related['category']) ?></p><h3><?= e($related['name']) ?></h3><p><?= e($related['short']) ?></p><a class="text-link" href="service.php?slug=<?= e($relatedSlug) ?>">View service</a></div></article>
<?php endforeach; ?></div></div></section>
<?php render_cta('Not sure where to begin?', 'Share your needs and our team will suggest a thoughtful next step.'); render_footer(); ?>
