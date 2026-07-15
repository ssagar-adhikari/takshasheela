<?php
require __DIR__ . '/includes/site.php';
render_header('Our team', 'Meet the compassionate Ayurvedic practitioners and wellness professionals guiding guests at Takshasheela.');
render_hero('Our leadership and guiding team', 'Compassionate leaders shaping a healing journey.', 'Rooted in service, humility, and warmth, our team creates a safe and nurturing environment for healing and personal growth.', 'hero--founders');
$team = [
    ['anita.webp', 'Anita Gurung', 'A compassionate wellness guide and spiritual counsellor committed to supporting personal healing and growth.'],
    ['bina.jpg', 'Bina Adhikari', 'Bina has long supported healing circles and wellness programs with warmth, wisdom, and deep compassion.'],
    ['sangita.jpg', 'Sangita Tamang', 'Sangita brings calm presence and deep insight to healing journeys, guided reflection, and wellness practice.'],
    ['anita.webp', 'Sushma Shrestha', 'Wellness Program Director guiding retreats, Ayurvedic consultations, and compassionate guest care.'],
    ['bina.jpg', 'Bikash Karki', 'Ayurveda therapist and retreat facilitator focused on healing, balance, and personal renewal.'],
    ['sangita.jpg', 'Pramila Thapa', 'Community care and guest relations lead creating a welcoming and supportive atmosphere.'],
];
?>
<section class="section"><div class="shell intro-grid" data-reveal><div><p class="eyebrow">Our leadership</p><h2>Compassionate leaders shaping a healing journey.</h2></div><div class="intro-copy"><p>At Takshasheela Ayurveda Aashram, our team combines deep spiritual insight with hands-on expertise in Ayurveda, counselling, wellness facilitation, and compassionate care.</p><p>Our leaders are rooted in Nepali values of service, humility, and warmth. They create a safe and nurturing environment where guests can reconnect with themselves through healing practices, retreat experiences, and meaningful personal growth.</p></div></div></section>
<section class="section section--cream"><div class="shell"><div class="section-heading" data-reveal><p class="eyebrow">Meet the team</p><h2>Our Nepalese leadership team.</h2></div><div class="team-grid"><?php foreach ($team as [$image, $name, $description]): ?><article class="person" data-reveal><img src="assets/images/<?= e($image) ?>" alt="<?= e($name) ?>" loading="lazy"><h3><?= e($name) ?></h3><p><?= e($description) ?></p></article><?php endforeach; ?></div></div></section>
<?php render_cta('Meet us in person', 'Connect with the people who will guide your time at Takshasheela.'); render_footer(); ?>
