<?php

declare(strict_types=1);

function accommodations(): array
{
    return [
        'standard-room' => [
            'name' => 'Standard Room',
            'category' => 'Simple restorative comfort',
            'heading' => 'Comfortable and peaceful.',
            'image' => 'assets/images/standard-room.jpg',
            'alt' => 'Standard room at Takshasheela',
            'short' => 'A cosy and peaceful room for guests seeking simplicity, comfort, and a restful stay during their wellness journey.',
            'description' => 'Our Standard Room offers a calm, uncluttered place to settle between therapies, meals, and retreat activities. Its simple atmosphere supports quiet rest and gives you a comfortable private base throughout your time at the aashram.',
            'highlights' => ['Peaceful private space', 'Comfortable everyday essentials', 'A restful retreat setting'],
            'features' => ['Comfortable sleeping space', 'Private room for rest and reflection', 'Fresh linen and essential amenities', 'Access to shared aashram spaces', 'Support from the hospitality team'],
            'ideal_for' => 'Solo guests or companions who value simplicity and plan to spend much of their day participating in treatments, practices, and the wider aashram rhythm.',
            'stay_note' => 'Room setup, occupancy options, and accessibility needs are confirmed before booking. Tell us about any mobility, sleep, or other practical requirements when you enquire.',
        ],
        'deluxe-room' => [
            'name' => 'Deluxe Room',
            'category' => 'Elevated space and comfort',
            'heading' => 'Elevated comfort and space.',
            'image' => 'assets/images/deluxe-room.jpg',
            'alt' => 'Deluxe room at Takshasheela',
            'short' => 'A spacious room with a serene atmosphere, designed to offer additional comfort throughout a restorative stay.',
            'description' => 'Our Deluxe Room provides a more spacious setting for unhurried mornings, private reflection, and deep rest. Thoughtful furnishings and a quiet atmosphere make it a supportive choice for a longer retreat or for guests who appreciate additional room around them.',
            'highlights' => ['More spacious interior', 'Serene private atmosphere', 'Designed for extended rest'],
            'features' => ['Generous sleeping and relaxation space', 'Private room for quiet restorative time', 'Fresh linen and essential amenities', 'Access to shared aashram spaces', 'Support from the hospitality team'],
            'ideal_for' => 'Guests planning a longer stay, travelling with a companion, or simply wanting more private space as part of their retreat experience.',
            'stay_note' => 'Room setup, occupancy options, and accessibility needs are confirmed before booking. Tell us about any mobility, sleep, or other practical requirements when you enquire.',
        ],
    ];
}

function accommodation_by_slug(string $slug): ?array
{
    $rooms = accommodations();
    return isset($rooms[$slug]) ? ['slug' => $slug] + $rooms[$slug] : null;
}
