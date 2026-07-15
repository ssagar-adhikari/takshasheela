<?php

declare(strict_types=1);

function products(): array
{
    return [
        'abhyanga-body-oil' => [
            'name' => 'Abhyanga Body Oil', 'category' => 'Body care', 'size' => '200 ml', 'price' => 'NPR 1,850',
            'image' => 'assets/images/product-abhyanga-oil.png',
            'short' => 'A warming daily massage oil to nourish skin and support a grounded routine.',
            'description' => 'A slow infusion of sesame oil and traditional Ayurvedic botanicals created for the daily practice of self-massage. Its warming character helps soften the skin while turning an everyday ritual into a quiet moment of care.',
            'ingredients' => ['Sesame oil', 'Ashwagandha', 'Bala', 'Manjistha'],
            'use' => 'Warm a small amount between the palms. Massage over the body with long strokes on the limbs and circular movements at the joints. Leave for 15–20 minutes before bathing.',
        ],
        'tulsi-calm-tea' => [
            'name' => 'Tulsi Calm Tea', 'category' => 'Herbal teas', 'size' => '60 g', 'price' => 'NPR 950',
            'image' => 'assets/images/product-tulsi-tea.png',
            'short' => 'A fragrant caffeine-free infusion for unhurried afternoons and restful evenings.',
            'description' => 'A gentle blend led by tulsi, the much-loved Ayurvedic herb traditionally used to support resilience and clarity. Balanced with warming spices, it makes a comforting cup at any time of day.',
            'ingredients' => ['Tulsi leaf', 'Cinnamon', 'Green cardamom', 'Fennel seed'],
            'use' => 'Steep one teaspoon in freshly boiled water for 5–7 minutes. Strain and enjoy without milk, or add a little honey once the tea has cooled slightly.',
        ],
        'amla-chyawanprash' => [
            'name' => 'Amla Chyawanprash', 'category' => 'Daily wellness', 'size' => '300 g', 'price' => 'NPR 2,250',
            'image' => 'assets/images/product-chyawanprash.png',
            'short' => 'A traditional herbal preserve made with amla and aromatic restorative spices.',
            'description' => 'A rich, slow-cooked herbal preparation centred on amla. This traditional preserve brings together fruit, ghee, honey, and spices in a deeply aromatic daily tonic inspired by classical Ayurvedic practice.',
            'ingredients' => ['Amla fruit', 'Raw honey', 'Cultured ghee', 'Long pepper'],
            'use' => 'Enjoy one teaspoon in the morning, followed by warm water or milk. If you are pregnant, taking medication, or managing a health condition, consult your practitioner first.',
        ],
        'shanti-night-serum' => [
            'name' => 'Shanti Night Serum', 'category' => 'Face care', 'size' => '30 ml', 'price' => 'NPR 1,650',
            'image' => 'assets/images/product-night-serum.png',
            'short' => 'A silky botanical face oil designed for a calming end-of-day ritual.',
            'description' => 'A lightweight evening blend that cushions the skin with botanical oils and a subtle herbaceous aroma. A few drops invite slower breathing, gentle facial massage, and a softer transition into rest.',
            'ingredients' => ['Jojoba oil', 'Rosehip seed oil', 'Brahmi', 'Vetiver'],
            'use' => 'After cleansing, press 2–3 drops onto slightly damp face and neck. Massage gently with upward movements. Use in the evening and avoid the immediate eye area.',
        ],
    ];
}

function product_by_slug(string $slug): ?array
{
    $catalogue = products();
    return isset($catalogue[$slug]) ? ['slug' => $slug] + $catalogue[$slug] : null;
}
