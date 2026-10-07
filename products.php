<?php
require __DIR__ . '/includes/site.php';
require __DIR__ . '/includes/products.php';
render_header('Ayurvedic products', 'Explore Takshasheela botanical oils, herbal teas, daily tonics, and mindful skin care.');
render_hero('The Takshasheela apothecary', 'Everyday rituals, rooted in Ayurveda.', 'Thoughtfully made botanical essentials to extend the rhythm of the Aashram into your daily life.', 'hero--products');
?>
<section class="section">
    <div class="shell">
        <div class="catalogue-intro" data-reveal><div><p class="eyebrow">Our collection</p><h2>Simple care for daily balance.</h2></div><p>Explore small-batch oils, teas, tonics, and skin care inspired by the rituals we share at Takshasheela. For personal guidance, our practitioners are always happy to help.</p></div>
        <div class="product-grid">
            <?php foreach (products() as $slug => $product): ?>
                <article class="product-card" data-reveal>
                    <a class="product-card__image" href="product.php?slug=<?= e($slug) ?>"><img src="<?= e($product['image']) ?>" alt="<?= e($product['alt']) ?>" loading="lazy"></a>
                    <div class="product-card__body"><p class="product-card__meta"><?= e($product['category']) ?> · <?= e($product['size']) ?></p><h3><a href="product.php?slug=<?= e($slug) ?>"><?= e($product['name']) ?></a></h3><p><?= e($product['short']) ?></p><div class="product-card__footer"><strong><?= e($product['price']) ?></strong><a class="text-link" href="product.php?slug=<?= e($slug) ?>">View product</a></div></div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php render_cta('Need a recommendation?', 'Let us help you choose a ritual suited to your needs.', 'Ask about ingredients, use, availability, or ordering.', 'contact.php?type=product'); render_footer(); ?>
