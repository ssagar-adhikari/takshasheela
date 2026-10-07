<?php
require __DIR__ . '/includes/site.php';
require __DIR__ . '/includes/products.php';
$slug = isset($_GET['slug']) ? (string) $_GET['slug'] : '';
$product = product_by_slug($slug);
if (!$product) {
    http_response_code(404);
    render_header('Product not found', 'The requested product could not be found.');
    ?>
    <section class="section product-not-found"><div class="shell"><p class="eyebrow">404</p><h1>Product not found.</h1><p>The item you are looking for may have moved or is no longer available.</p><a class="button" href="products.php">View all products</a></div></section>
    <?php render_footer(); exit;
}
render_header($product['name'], $product['short']);
?>
<section class="product-detail section">
    <div class="shell product-detail__grid">
        <div class="product-detail__media" data-reveal><img src="<?= e($product['image']) ?>" alt="<?= e($product['alt']) ?>"></div>
        <div class="product-detail__content" data-reveal>
            <nav class="breadcrumb" aria-label="Breadcrumb"><a href="index.php">Home</a><span>/</span><a href="products.php">Products</a><span>/</span><span><?= e($product['name']) ?></span></nav>
            <p class="eyebrow"><?= e($product['category']) ?></p><h1><?= e($product['name']) ?></h1><p class="product-detail__price"><?= e($product['price']) ?> <span><?= e($product['size']) ?></span></p><p class="product-detail__lead"><?= e($product['description']) ?></p>
            <a class="button" href="contact.php?interest=<?= rawurlencode($product['name']) ?>">Enquire to order</a>
            <div class="product-facts"><details open><summary>Key ingredients</summary><ul><?php foreach ($product['ingredients'] as $ingredient): ?><li><?= e($ingredient) ?></li><?php endforeach; ?></ul></details><details><summary>How to use</summary><p><?= e($product['use']) ?></p></details></div>
            <p class="product-note">Handcrafted in small batches. Appearance may vary naturally.</p>
        </div>
    </div>
</section>
<section class="section section--cream"><div class="shell"><div class="section-heading"><p class="eyebrow">You may also like</p><h2>Continue the ritual.</h2></div><div class="product-grid product-grid--related">
<?php $shown = 0; foreach (products() as $relatedSlug => $related): if ($relatedSlug === $slug || $shown >= 3) continue; $shown++; ?>
<article class="product-card"><a class="product-card__image" href="product.php?slug=<?= e($relatedSlug) ?>"><img src="<?= e($related['image']) ?>" alt="<?= e($related['alt']) ?>" loading="lazy"></a><div class="product-card__body"><p class="product-card__meta"><?= e($related['category']) ?></p><h3><a href="product.php?slug=<?= e($relatedSlug) ?>"><?= e($related['name']) ?></a></h3><div class="product-card__footer"><strong><?= e($related['price']) ?></strong><a class="text-link" href="product.php?slug=<?= e($relatedSlug) ?>">View product</a></div></div></article>
<?php endforeach; ?></div></div></section>
<?php render_footer(); ?>
