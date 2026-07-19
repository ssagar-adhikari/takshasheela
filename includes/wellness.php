<?php

declare(strict_types=1);

function packages(): array
{
    return [
        'panchakarma-rejuvenation' => [
            'anchor' => 'panchakarma',
            'name' => 'Panchakarma Rejuvenation',
            'category' => 'Intensive healing program',
            'duration' => 'Tailored to your assessment',
            'image' => 'assets/images/therapy.jpg',
            'alt' => 'Panchakarma retreat',
            'short' => 'A deeply therapeutic Ayurvedic detox designed to cleanse the body, restore balance, and rejuvenate the mind through personalised treatments.',
            'description' => 'This practitioner-led program draws on classical Panchakarma principles while respecting your present strength, constitution, and health history. Your daily rhythm is planned after consultation and may combine preparatory care, selected therapies, simple nourishing meals, rest, and integration guidance.',
            'highlights' => ['Personalised Ayurvedic treatments', 'Deep cleansing and balance', 'Mind and body rejuvenation'],
            'includes' => ['Initial Ayurvedic consultation', 'A personalised daily therapy plan', 'Dosha-supportive meals during your stay', 'Gentle yoga, breathwork, or meditation as appropriate', 'Closing consultation and home-care guidance'],
            'itinerary' => [
                ['time' => 'First day', 'title' => 'Consultation and preparation', 'description' => 'Meet your practitioner, review your health history, and begin gentle preparation with food, rest, and simple daily guidance.'],
                ['time' => 'Second day', 'title' => 'Personalised cleansing care', 'description' => 'Follow a practitioner-led sequence of Ayurvedic therapies, nourishing meals, quiet rest, and observation of your response.'],
                ['time' => 'Third day', 'title' => 'Recovery and home guidance', 'description' => 'Ease back into a steady rhythm with restorative care, closing consultation, and practical recommendations for continuing at home.'],
            ],
            'ideal_for' => 'Guests seeking a focused reset who are comfortable following a slower, practitioner-guided daily routine. Suitability and program length are confirmed during consultation.',
            'rhythm' => 'Days are intentionally spacious. Treatment periods are balanced with nourishing meals, quiet rest, gentle movement, and time in nature. Therapies may change as your practitioner observes how you respond.',
        ],
        'ayurvedic-wellness-immersion' => [
            'anchor' => 'immersion',
            'name' => 'Ayurvedic Wellness Immersion',
            'category' => '7-day Ayurveda retreat',
            'duration' => '7 days',
            'image' => 'assets/images/wellness.jpg',
            'alt' => 'Ayurvedic wellness retreat',
            'short' => 'Discover the foundations of Ayurveda with daily therapies, sattvic meals, yoga, and meditation—ideal for stress relief, lifestyle reset, and holistic well-being.',
            'description' => 'A gentle introduction to Ayurvedic living, this week-long retreat brings consultation, daily care, nourishing food, movement, and rest into one coherent rhythm. It is designed to help you step away from habitual demands and notice what supports steadier energy and ease.',
            'highlights' => ['Daily Ayurvedic therapies', 'Sattvic meals and yoga', 'Meditation and lifestyle reset'],
            'includes' => ['Arrival wellness consultation', 'Daily Ayurvedic therapy selected for you', 'Fresh sattvic meals', 'Guided yoga and meditation sessions', 'Practical Ayurvedic lifestyle guidance'],
            'itinerary' => [
                ['time' => 'First day', 'title' => 'Arrival and assessment', 'description' => 'Settle into the aashram, meet your practitioner, and receive a simple rhythm for meals, rest, and the week ahead.'],
                ['time' => 'Second day', 'title' => 'Restore daily rhythm', 'description' => 'Begin selected therapies, sattvic meals, yoga, meditation, and quiet space to notice what your body needs.'],
                ['time' => 'Third day', 'title' => 'Deepen Ayurvedic care', 'description' => 'Continue daily therapy, restorative movement, reflection, and quiet nature time at an unhurried pace.'],
                ['time' => 'Fourth day', 'title' => 'Lifestyle guidance', 'description' => 'Explore practical Ayurvedic food, daily rhythm, and self-care guidance shaped around your needs.'],
                ['time' => 'Fifth day', 'title' => 'Rest and observation', 'description' => 'Allow the routine to settle with body care, meditation, nourishing meals, and spacious rest.'],
                ['time' => 'Sixth day', 'title' => 'Integration practice', 'description' => 'Notice what has supported you most and prepare simple practices that can continue after the retreat.'],
                ['time' => 'Seventh day', 'title' => 'Departure guidance', 'description' => 'Close with practical recommendations for food, routine, and self-care you can carry into daily life.'],
            ],
            'ideal_for' => 'First-time Ayurveda guests, people feeling depleted by a busy routine, or anyone wanting a supported week of restorative habits without an intensive cleansing focus.',
            'rhythm' => 'Mornings begin quietly with movement or meditation, followed by meals and therapies at an unhurried pace. Afternoons allow room for rest, reflection, and nature; evenings settle into simple restorative practices.',
        ],
        'mind-body-balance-retreat' => [
            'anchor' => 'mind-body',
            'name' => 'Mind–Body Balance Retreat',
            'category' => '7-day retreat',
            'duration' => '7 days',
            'image' => 'assets/images/mindfulness.jpg',
            'alt' => 'Mind body balance retreat',
            'short' => 'Reconnect with yourself through Ayurvedic therapies, guided mindfulness, emotional support, and restorative daily practices in a peaceful aashram setting.',
            'description' => 'This retreat creates a calm container for reconnecting with your body and inner life. Personalised Ayurvedic care is paired with mindfulness and reflective practices, allowing you to slow down, recognise patterns, and build a more supportive everyday rhythm.',
            'highlights' => ['Guided mindfulness', 'Emotional healing support', 'Restorative daily practices'],
            'includes' => ['Personal wellbeing consultation', 'Selected Ayurvedic body therapies', 'Guided mindfulness and breath practices', 'Nourishing meals and restorative movement', 'A practical plan for continuing at home'],
            'itinerary' => [
                ['time' => 'First day', 'title' => 'Arrive and ground', 'description' => 'Settle in with a wellbeing consultation, gentle orientation, and space to slow down after travel.'],
                ['time' => 'Second day', 'title' => 'Reconnect with the body', 'description' => 'Move through selected Ayurvedic therapies, breath practices, mindful movement, and restorative meals.'],
                ['time' => 'Third day', 'title' => 'Breath and awareness', 'description' => 'Deepen gentle breathwork, mindfulness, and quiet body awareness with supportive practitioner guidance.'],
                ['time' => 'Fourth day', 'title' => 'Reflective practice', 'description' => 'Balance selected therapies with journaling, stillness, emotional support, and restorative nature time.'],
                ['time' => 'Fifth day', 'title' => 'Restorative rhythm', 'description' => 'Continue nourishing meals, mindful movement, and generous unscheduled rest to let the work settle.'],
                ['time' => 'Sixth day', 'title' => 'Prepare for home', 'description' => 'Identify the practices, routines, and boundaries that can support steadiness after the retreat.'],
                ['time' => 'Seventh day', 'title' => 'Return with steadiness', 'description' => 'Review what supported you most and leave with a gentle plan for continuing the rhythm at home.'],
            ],
            'ideal_for' => 'Guests experiencing stress, emotional fatigue, or disconnection who want gentle structure, private time, and compassionate guidance rather than an intensive clinical program.',
            'rhythm' => 'Each day alternates supported practices with generous quiet time. The pace is deliberately gentle, with opportunities for mindful movement, therapy, reflection, nourishing meals, and rest.',
        ],
    ];
}

function services(): array
{
    return [
        'panchakarma-detox-therapy' => [
            'anchor' => 'panchakarma',
            'name' => 'Panchakarma & Detox Therapy',
            'interest' => 'Panchakarma and Detox Therapy',
            'category' => 'Ayurvedic cleansing care',
            'image' => 'assets/images/therapy.jpg',
            'alt' => 'Panchakarma therapy',
            'short' => 'Classical Ayurvedic therapies selected to support cleansing, restoration, and internal balance under practitioner guidance.',
            'description' => 'Panchakarma is not a single treatment. It is an individualised sequence that may include preparation, selected cleansing procedures, restorative bodywork, dietary guidance, and a gradual return to regular routines. We begin with assessment before recommending any therapy.',
            'highlights' => ['Classical Ayurvedic treatments', 'Deep cleansing support', 'Restore internal balance'],
            'includes' => ['Constitution and current-state assessment', 'A therapy sequence selected for your needs', 'Preparation and recovery guidance', 'Food and daily-routine recommendations', 'Ongoing review during a multi-day course'],
            'itinerary' => [
                ['time' => 'First day', 'title' => 'Assessment and preparation', 'description' => 'Begin with constitution and current-state assessment, then receive preparation guidance for food, rest, and routine.'],
                ['time' => 'Second day', 'title' => 'Selected therapy care', 'description' => 'Move through practitioner-selected therapies with simple meals, rest, and careful observation of your response.'],
                ['time' => 'Third day', 'title' => 'Recovery guidance', 'description' => 'Review the therapy response and receive food, routine, and recovery recommendations for the days ahead.'],
            ],
            'ideal_for' => 'People looking for structured Ayurvedic cleansing support. It is not appropriate for everyone; your practitioner will consider strength, age, health history, medication, and current symptoms first.',
            'process' => 'Your first consultation establishes whether Panchakarma is suitable. If recommended, we explain the preparation, therapy days, food routine, rest requirements, and recovery period before you commit.',
        ],
        'ayurvedic-wellness-retreats' => [
            'anchor' => 'wellness',
            'name' => 'Ayurvedic Wellness Retreats',
            'interest' => 'Ayurvedic Wellness Retreat',
            'category' => 'Restorative retreat care',
            'image' => 'assets/images/wellness.jpg',
            'alt' => 'Ayurvedic wellness retreat',
            'short' => 'Holistic retreat care combining Ayurvedic therapies, yoga, meditation, and mindful living practices to support sustainable well-being.',
            'description' => 'Our wellness retreats bring several supportive practices together without forcing a one-size-fits-all schedule. After consultation, we shape a balanced combination of body therapies, food, movement, meditation, nature, and rest around your needs and available time.',
            'highlights' => ['Ayurvedic therapies', 'Yoga and meditation', 'Mindful living practices'],
            'includes' => ['Personal wellness consultation', 'A tailored schedule of body therapies', 'Yoga, meditation, and breath practices', 'Nourishing Ayurvedic meals', 'Lifestyle suggestions for home'],
            'itinerary' => [
                ['time' => 'First day', 'title' => 'Consultation and settling in', 'description' => 'Share your goals, present routine, and health considerations so your care rhythm can be shaped thoughtfully.'],
                ['time' => 'Second day', 'title' => 'Therapies and restorative practices', 'description' => 'Receive selected body therapies alongside yoga, meditation, breath practices, and nourishing Ayurvedic meals.'],
                ['time' => 'Third day', 'title' => 'Lifestyle integration', 'description' => 'Gather practical food, movement, rest, and routine suggestions that can continue beyond your visit.'],
            ],
            'ideal_for' => 'Guests wanting to restore energy, establish healthier routines, or experience Ayurveda in a calm residential setting without undertaking an intensive cleansing program.',
            'process' => 'We start by listening to your goals, present routine, and any health considerations. Your schedule is then built around an achievable balance of guided care and unscheduled restorative time.',
        ],
        'personalized-healing-programs' => [
            'anchor' => 'personalised',
            'name' => 'Personalized Healing Programs',
            'interest' => 'Personalized Healing Program',
            'category' => 'Individual Ayurvedic care',
            'image' => 'assets/images/mindfulness.jpg',
            'alt' => 'Personalised healing program',
            'short' => 'Individualised Ayurvedic support shaped around your constitution, health concerns, lifestyle, and capacity for change.',
            'description' => 'This service is for needs that do not fit neatly into a standard retreat. A practitioner considers your constitution, digestion, sleep, energy, stress, health history, and daily responsibilities, then recommends a realistic combination of therapies and lifestyle support.',
            'highlights' => ['Dosha-based assessment', 'Individual health considerations', 'Experienced practitioner guidance'],
            'includes' => ['Detailed Ayurvedic consultation', 'A prioritised personal care plan', 'Recommended therapies where appropriate', 'Food, sleep, and routine guidance', 'Review and adjustment options'],
            'itinerary' => [
                ['time' => 'First day', 'title' => 'Detailed consultation', 'description' => 'Discuss your constitution, digestion, sleep, energy, stress, health history, and daily responsibilities.'],
                ['time' => 'Second day', 'title' => 'Personal care plan', 'description' => 'Receive a prioritised plan with therapies, food, sleep, and routine guidance suited to your capacity.'],
                ['time' => 'Third day', 'title' => 'Review and adjustment', 'description' => 'Reflect on what feels realistic and adjust the plan so it can support your life outside the aashram.'],
            ],
            'ideal_for' => 'Anyone seeking a more individual starting point, including guests with specific wellbeing goals, limited time, or health considerations that call for a carefully adapted approach.',
            'process' => 'Your care begins with conversation and assessment, not a predetermined treatment. We explain our recommendations, agree on practical priorities, and adjust the plan according to your response and circumstances.',
        ],
    ];
}

function trainings(): array
{
    return [
        'ayurveda-foundations' => [
            'anchor' => 'ayurveda-foundations',
            'name' => 'Ayurveda Foundations Training',
            'category' => 'Foundational training',
            'duration' => '5 days',
            'image' => 'assets/images/wellness.jpg',
            'alt' => 'Ayurveda foundations training',
            'short' => 'An accessible introduction to Ayurvedic principles, daily routines, food, and self-care for balanced everyday living.',
            'description' => 'This practical training introduces the core ideas that shape Ayurvedic living. Through guided lessons, observation, discussion, and simple daily practices, participants learn how constitution, digestion, season, food, sleep, and routine influence wellbeing.',
            'highlights' => ['Core Ayurvedic principles', 'Dosha and constitution basics', 'Practical daily routines'],
            'includes' => ['Guided teaching sessions', 'Introduction to the doshas and constitution', 'Daily-routine and seasonal-care practices', 'Ayurvedic food principles', 'Course notes and reflection exercises'],
            'itinerary' => [
                ['time' => 'First day', 'title' => 'Ayurveda foundations', 'description' => 'Explore the basic language of Ayurveda, the doshas, constitution, and how observation supports daily wellbeing.'],
                ['time' => 'Second day', 'title' => 'Food and digestion', 'description' => 'Learn how digestion, taste, meal rhythm, and simple food choices influence energy and balance.'],
                ['time' => 'Third day', 'title' => 'Daily routine', 'description' => 'Study daily practices for sleep, cleansing, movement, rest, and seasonal adjustment.'],
                ['time' => 'Fourth day', 'title' => 'Applying the principles', 'description' => 'Translate the teachings into practical self-care choices through discussion, observation, and reflection.'],
                ['time' => 'Fifth day', 'title' => 'Review and integration', 'description' => 'Gather course notes, ask questions, and shape a realistic personal routine for continued practice.'],
            ],
            'ideal_for' => 'Beginners, wellness practitioners exploring Ayurveda, and anyone wanting a clear framework for applying traditional principles responsibly in daily life.',
            'format' => 'Teaching is balanced with discussion and experiential practice. Each day focuses on a small set of ideas, giving participants time to observe, practise, and ask questions.',
        ],
        'ayurvedic-lifestyle-nutrition' => [
            'anchor' => 'lifestyle-nutrition',
            'name' => 'Ayurvedic Lifestyle & Nutrition',
            'category' => 'Practical training',
            'duration' => '3 days',
            'image' => 'assets/images/therapy.jpg',
            'alt' => 'Ayurvedic lifestyle and nutrition training',
            'short' => 'Learn practical approaches to food, digestion, daily rhythm, and seasonal living through an Ayurvedic lens.',
            'description' => 'A focused course on making Ayurvedic lifestyle principles useful and realistic. Participants explore digestion, tastes, meal rhythm, simple food preparation, seasonal adjustments, sleep, and self-observation without relying on rigid universal rules.',
            'highlights' => ['Food and digestion principles', 'Seasonal lifestyle guidance', 'Simple meal and routine planning'],
            'includes' => ['Interactive theory sessions', 'Introduction to Ayurvedic tastes and digestion', 'Food and spice demonstrations', 'Daily and seasonal routine planning', 'Take-home reference materials'],
            'itinerary' => [
                ['time' => 'First day', 'title' => 'Digestion and tastes', 'description' => 'Learn the Ayurvedic view of digestion, tastes, meal rhythm, and how food can support different constitutions.'],
                ['time' => 'Second day', 'title' => 'Food practice', 'description' => 'Work with simple preparation, spices, seasonal choices, and practical ways to make meals more supportive.'],
                ['time' => 'Third day', 'title' => 'Routine planning', 'description' => 'Build a realistic food and lifestyle plan with take-home references for continued practice.'],
            ],
            'ideal_for' => 'Home cooks, yoga and wellness practitioners, and people wanting grounded tools for building more supportive food and lifestyle habits.',
            'format' => 'Short lessons are paired with practical demonstrations, reflective exercises, and opportunities to translate the principles into a personal, achievable routine.',
        ],
        'mindfulness-yoga-facilitation' => [
            'anchor' => 'mindfulness-yoga',
            'name' => 'Mindfulness & Yoga Facilitation',
            'category' => 'Facilitator development',
            'duration' => '7 days',
            'image' => 'assets/images/mindfulness.jpg',
            'alt' => 'Mindfulness and yoga facilitation training',
            'short' => 'Develop the presence, structure, and practical skills needed to guide gentle mindfulness and restorative movement sessions.',
            'description' => 'This experiential training supports participants in guiding calm, inclusive practices with clarity and care. The curriculum brings together personal practice, session structure, communication, breath awareness, gentle movement, boundaries, and supervised facilitation.',
            'highlights' => ['Guided mindfulness practice', 'Restorative session design', 'Supervised facilitation'],
            'includes' => ['Daily personal practice', 'Facilitation principles and session planning', 'Breath and gentle movement guidance', 'Inclusive language and professional boundaries', 'Peer practice and constructive feedback'],
            'itinerary' => [
                ['time' => 'First day', 'title' => 'Personal practice', 'description' => 'Begin with grounding practice, group orientation, and the principles of calm, inclusive facilitation.'],
                ['time' => 'Second day', 'title' => 'Breath awareness', 'description' => 'Study breath guidance, pacing, language, and how to support participants with clarity and care.'],
                ['time' => 'Third day', 'title' => 'Gentle movement', 'description' => 'Explore restorative movement structures, modifications, and safe transitions for varied bodies.'],
                ['time' => 'Fourth day', 'title' => 'Session design', 'description' => 'Build simple session plans that balance intention, sequence, timing, and accessibility.'],
                ['time' => 'Fifth day', 'title' => 'Communication and boundaries', 'description' => 'Practise inclusive language, listening skills, professional boundaries, and supportive group presence.'],
                ['time' => 'Sixth day', 'title' => 'Supervised facilitation', 'description' => 'Guide short peer sessions and receive constructive feedback to strengthen confidence and clarity.'],
                ['time' => 'Seventh day', 'title' => 'Integration and next steps', 'description' => 'Reflect on your facilitation style and leave with practical next steps for continued development.'],
            ],
            'ideal_for' => 'Yoga teachers, retreat hosts, community facilitators, and committed practitioners who want to guide gentle wellbeing sessions with greater confidence.',
            'format' => 'Participants move between personal practice, teaching, observation, peer work, and supervised facilitation. This is a development course and not a clinical qualification.',
        ],
    ];
}

function package_by_slug(string $slug): ?array
{
    $items = packages();
    return isset($items[$slug]) ? ['slug' => $slug] + $items[$slug] : null;
}

function service_by_slug(string $slug): ?array
{
    $items = services();
    return isset($items[$slug]) ? ['slug' => $slug] + $items[$slug] : null;
}

function training_by_slug(string $slug): ?array
{
    $items = trainings();
    return isset($items[$slug]) ? ['slug' => $slug] + $items[$slug] : null;
}
