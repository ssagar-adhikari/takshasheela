<?php
// The four public About Us pages share their rendering but retain their menus,
// content layouts, and independently managed database records.
render_header($content['meta_title'], $content['meta_description'] ?? '');
$heroImage = about_image_url($content['hero_image']);
?>
<section class="page-hero about-managed-hero">
    <?php if ($heroImage): ?><img class="about-hero-image" src="<?= e($heroImage) ?>" alt="<?= e($content['hero_image_alt'] ?? '') ?>" fetchpriority="high"><?php endif; ?>
    <div class="shell page-hero__content" data-reveal>
        <p class="eyebrow eyebrow--light"><?= e($content['hero_eyebrow'] ?? '') ?></p>
        <h1><?= e($content['hero_title']) ?></h1>
        <p><?= e($content['hero_description'] ?? '') ?></p>
    </div>
</section>
<?php
$blocks = array_values($content['blocks']);
for ($i = 0; $i < count($blocks); $i++):
    $block = $blocks[$i];
    $image = about_image_url($block['image']);
    if ($block['kind'] === 'split'):
?>
<section class="section <?= $i % 2 === 0 ? 'section--cream' : '' ?>"><div class="shell split <?= $i % 2 === 1 ? 'split--reverse' : '' ?> <?= ! $image ? 'about-text-only' : '' ?>">
    <?php if ($image): ?><div class="split__media" data-reveal><img src="<?= e($image) ?>" alt="<?= e($block['image_alt'] ?? '') ?>" loading="lazy"></div><?php endif; ?>
    <div class="split__content" data-reveal><p class="eyebrow"><?= e($block['eyebrow'] ?? '') ?></p><h2><?= e($block['title']) ?></h2><?php about_paragraphs($block['body']); ?></div>
</div></section>
<?php
        continue;
    endif;
    $heading = $block['kind'] === 'heading' ? $block : null;
    $kind = $heading ? ($blocks[$i + 1]['kind'] ?? null) : $block['kind'];
    $group = [];
    if (in_array($kind, ['card', 'person', 'principle', 'text'], true)) {
        if ($heading) {
            $i++;
        }
        do {
            $group[] = $blocks[$i];
            $i++;
        } while (isset($blocks[$i]) && $blocks[$i]['kind'] === $kind);
        $i--;
    }
?>
<section class="section <?= in_array($kind, ['person', 'text'], true) ? 'section--cream' : '' ?>"><div class="shell">
    <?php if ($heading): ?>
    <div class="section-heading <?= $kind !== 'person' ? 'section-heading--center' : '' ?>" data-reveal>
        <?php if ($headingImage = about_image_url($heading['image'])): ?><img class="about-heading-image" src="<?= e($headingImage) ?>" alt="<?= e($heading['image_alt'] ?? '') ?>" loading="lazy"><?php endif; ?>
        <p class="eyebrow"><?= e($heading['eyebrow'] ?? '') ?></p><h2><?= e($heading['title']) ?></h2><?php about_paragraphs($heading['body']); ?>
    </div>
    <?php endif; ?>
    <?php if ($group): ?>
    <div class="<?= $kind === 'person' ? 'team-grid' : ($kind === 'text' ? 'split' : 'card-grid') ?>">
        <?php foreach ($group as $index => $entry): $entryImage = about_image_url($entry['image']); ?>
        <article class="<?= match ($kind) { 'person' => 'person', 'principle' => 'number-card', 'text' => 'split__content', default => 'feature-card dosha-card' } ?>" data-reveal>
            <?php if ($entryImage): ?><img class="about-entry-image" src="<?= e($entryImage) ?>" alt="<?= e($entry['image_alt'] ?: $entry['title']) ?>" loading="lazy"><?php endif; ?>
            <?php if ($kind === 'principle'): ?><span class="number-card__number"><?= e(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)) ?></span><?php endif; ?>
            <div<?= $kind === 'card' ? ' class="feature-card__body"' : '' ?>>
                <?php if ($entry['eyebrow']): ?><p class="eyebrow"><?= e($entry['eyebrow']) ?></p><?php endif; ?>
                <h3><?= e($entry['title']) ?></h3><?php about_paragraphs($entry['body']); ?>
            </div>
        </article>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div></section>
<?php endfor; ?>
<?php render_cta($content['cta_eyebrow'] ?? '', $content['cta_title'], $content['cta_body'] ?? ''); render_footer(); ?>
