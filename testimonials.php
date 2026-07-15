<?php
require __DIR__ . '/includes/site.php';
render_header('Testimonials', 'Guest reflections on healing, renewal, and inner clarity at Takshasheela Ayurveda Aashram.');
render_hero('Guest reflections', 'Stories of healing, renewal, and lasting peace from our guests.', 'These heartfelt reflections share the calm, transformation, and inner clarity experienced at Takshasheela Ayurveda Aashram.', 'hero--water');
?>
<section class="section section--cream">
    <div class="shell">
        <div class="section-heading section-heading--center"><p class="eyebrow">Guest reflections</p><h2>In their own words.</h2></div>
        <div class="card-grid">
            <article class="testimonial-card"><p class="testimonial-card__rating" aria-label="Five out of five stars">★★★★★</p><h3>A Deep Sense of Calm</h3><blockquote>“The retreat felt like a true reset for my body and mind. Every treatment was thoughtful, grounding, and deeply restorative.”</blockquote><footer><span>AS</span><p><strong>Asha Sharma</strong>Kathmandu, Nepal</p></footer></article>
            <article class="testimonial-card"><p class="testimonial-card__rating" aria-label="Five out of five stars">★★★★★</p><h3>Warmth and Healing</h3><blockquote>“I came seeking peace and left with clarity, balance, and a renewed sense of connection to myself and nature.”</blockquote><footer><span>PK</span><p><strong>Pooja K.C.</strong>Pokhara, Nepal</p></footer></article>
            <article class="testimonial-card"><p class="testimonial-card__rating" aria-label="Five out of five stars">★★★★★</p><h3>A Beautiful Retreat Experience</h3><blockquote>“The atmosphere was serene, the care was genuine, and every moment felt aligned with healing and self-discovery.”</blockquote><footer><span>RB</span><p><strong>Rina Bhandari</strong>Lalitpur, Nepal</p></footer></article>
        </div>
    </div>
</section>
<?php render_cta('Begin your story', 'A restorative stay starts with a thoughtful conversation.'); render_footer(); ?>
