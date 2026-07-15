<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/wellness.php';
require_once __DIR__ . '/includes/accommodations.php';
require_once __DIR__ . '/includes/products.php';

session_start();

$typeLabels = [
    'wellness_package' => 'Wellness package',
    'ayurvedic_service' => 'Ayurvedic service',
    'wellness_training' => 'Wellness training',
    'accommodation' => 'Accommodation',
    'product' => 'Product',
    'general' => 'General enquiry',
];

$interestGroups = [
    'wellness_package' => ['label' => 'Wellness packages', 'options' => array_column(packages(), 'name')],
    'ayurvedic_service' => ['label' => 'Ayurvedic services', 'options' => array_column(services(), 'name')],
    'wellness_training' => ['label' => 'Wellness training', 'options' => array_column(trainings(), 'name')],
    'accommodation' => ['label' => 'Accommodations', 'options' => array_column(accommodations(), 'name')],
    'product' => ['label' => 'Products', 'options' => array_column(products(), 'name')],
];

$interestToType = [];
foreach ($interestGroups as $type => $group) {
    foreach ($group['options'] as $option) $interestToType[$option] = $type;
}

$legacyTypes = [
    'Wellness program' => 'wellness_package',
    'Ayurvedic therapy' => 'ayurvedic_service',
    'Wellness training' => 'wellness_training',
    'Accommodation' => 'accommodation',
    'General enquiry' => 'general',
];

$legacyInterests = [
    'Mind-Body Balance Retreat' => 'Mind–Body Balance Retreat',
    'Panchakarma and Detox Therapy' => 'Panchakarma & Detox Therapy',
    'Ayurvedic Wellness Retreat' => 'Ayurvedic Wellness Retreats',
    'Personalized Healing Program' => 'Personalized Healing Programs',
];

$sent = false;
$errors = [];
$values = ['name' => '', 'email' => '', 'phone' => '', 'enquiry_type' => '', 'interest' => '', 'message' => ''];

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET') {
    $requestedType = trim((string) ($_GET['type'] ?? ''));
    $requestedInterest = trim((string) ($_GET['interest'] ?? ''));
    if (isset($legacyInterests[$requestedInterest])) $requestedInterest = $legacyInterests[$requestedInterest];
    if (isset($typeLabels[$requestedType])) $values['enquiry_type'] = $requestedType;
    if (isset($interestToType[$requestedInterest])) {
        $values['interest'] = $requestedInterest;
        $values['enquiry_type'] = $interestToType[$requestedInterest];
    } elseif (isset($legacyTypes[$requestedInterest])) {
        $values['enquiry_type'] = $legacyTypes[$requestedInterest];
    }
}

if (empty($_SESSION['contact_csrf'])) {
    $_SESSION['contact_csrf'] = bin2hex(random_bytes(32));
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    foreach ($values as $key => $unused) $values[$key] = trim((string) ($_POST[$key] ?? ''));

    if ($values['name'] === '' || mb_strlen($values['name']) > 100) $errors[] = 'Please enter your name.';
    if (!filter_var($values['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';
    if (!isset($typeLabels[$values['enquiry_type']])) $errors[] = 'Please select an enquiry type.';
    if ($values['interest'] !== '' && !isset($interestToType[$values['interest']])) $errors[] = 'Please select a valid program, room, or product.';
    if ($values['interest'] !== '' && isset($interestToType[$values['interest']]) && $interestToType[$values['interest']] !== $values['enquiry_type']) $errors[] = 'The selected item does not match the enquiry type.';
    if (mb_strlen($values['message']) < 10) $errors[] = 'Please include a little more detail in your message.';
    if ((string) ($_POST['website'] ?? '') !== '') $errors[] = 'Your message could not be submitted.';
    if (!hash_equals((string) $_SESSION['contact_csrf'], (string) ($_POST['csrf_token'] ?? ''))) $errors[] = 'Your session expired. Please refresh and try again.';
    if (!isset($_POST['consent'])) $errors[] = 'Please confirm that we may use your details to respond.';
    if ((int) ($_SESSION['contact_last_submission'] ?? 0) > time() - 30) $errors[] = 'Please wait a moment before sending another enquiry.';

    if ($errors === []) {
        $recipient = (string) (getenv('CONTACT_RECIPIENT') ?: 'info@takshasheela.com');
        $safeName = str_replace(["\r", "\n"], '', $values['name']);
        $safeEmail = str_replace(["\r", "\n"], '', $values['email']);
        $typeName = $typeLabels[$values['enquiry_type']];
        $specificInterest = $values['interest'] !== '' ? $values['interest'] : 'Not specified';
        $body = "Name: {$values['name']}\nEmail: {$values['email']}\nPhone: {$values['phone']}\nEnquiry type: {$typeName}\nSpecific interest: {$specificInterest}\n\nMessage:\n{$values['message']}";
        $headers = ['From: website@takshasheela.com', 'Reply-To: ' . $safeEmail, 'Content-Type: text/plain; charset=UTF-8'];
        $subjectDetail = $values['interest'] !== '' ? ' — ' . $values['interest'] : '';
        if (@mail($recipient, 'Website enquiry: ' . $typeName . $subjectDetail . ' from ' . $safeName, $body, implode("\r\n", $headers))) {
            $_SESSION['contact_last_submission'] = time();
            $_SESSION['contact_csrf'] = bin2hex(random_bytes(32));
            $sent = true;
        } else {
            $errors[] = 'We could not send your message right now. Please contact us by email or phone.';
        }
    }
}

require __DIR__ . '/includes/site.php';
render_header('Contact', 'Contact Takshasheela to plan a stay, compare programs, enquire about products, or ask a question.');
render_hero('Begin a conversation', 'Tell us what support would feel useful.', 'Choose the kind of enquiry and, if you know it, the specific program, room, or product. We will route your message to the right team.', 'hero--contact');
?>
<section class="section"><div class="shell contact-grid"><div class="contact-form" data-reveal><p class="eyebrow">Your enquiry</p><h2>How can we help?</h2><?php if ($sent): ?><div class="notice" role="status">Thank you. Your <?= e(strtolower($typeLabels[$values['enquiry_type']])) ?> enquiry has been received. Our team will respond shortly.</div><?php else: ?><?php if ($errors): ?><div class="notice" role="alert"><?= e(implode(' ', $errors)) ?></div><?php endif; ?><form method="post" action="contact.php"><input type="hidden" name="csrf_token" value="<?= e((string) $_SESSION['contact_csrf']) ?>"><div class="sr-only" aria-hidden="true"><label for="website">Website</label><input id="website" name="website" tabindex="-1" autocomplete="off"></div><div class="form-grid">
<div class="field"><label for="name">Full name</label><input id="name" name="name" value="<?= e($values['name']) ?>" autocomplete="name" required></div>
<div class="field"><label for="email">Email</label><input id="email" name="email" type="email" value="<?= e($values['email']) ?>" autocomplete="email" required></div>
<div class="field"><label for="phone">Phone <span>(optional)</span></label><input id="phone" name="phone" value="<?= e($values['phone']) ?>" autocomplete="tel"></div>
<div class="field"><label for="enquiry_type">Enquiry type</label><select id="enquiry_type" name="enquiry_type" data-enquiry-type required><option value="">Choose a type</option><?php foreach ($typeLabels as $value => $label): ?><option value="<?= e($value) ?>"<?= $values['enquiry_type'] === $value ? ' selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?></select></div>
<div class="field field--full"><label for="interest">Specific program, room, or product <span>(optional)</span></label><select id="interest" name="interest" data-enquiry-interest><option value="">Not sure yet</option><?php foreach ($interestGroups as $type => $group): ?><optgroup label="<?= e($group['label']) ?>"><?php foreach ($group['options'] as $option): ?><option value="<?= e($option) ?>" data-enquiry-option-type="<?= e($type) ?>"<?= $values['interest'] === $option ? ' selected' : '' ?>><?= e($option) ?></option><?php endforeach; ?></optgroup><?php endforeach; ?></select></div>
<div class="field field--full"><label for="message">What would you like support with?</label><textarea id="message" name="message" required><?= e($values['message']) ?></textarea></div>
<div class="field field--full consent-field"><label><input type="checkbox" name="consent" value="1" required> I agree that Takshasheela may use these details to respond to my enquiry.</label></div>
<div class="field field--full"><button class="button" type="submit">Send enquiry</button></div>
</div></form><?php endif; ?></div><aside class="contact-aside" data-reveal><p class="eyebrow">Contact details</p><h2>We are here to listen.</h2><p>Your enquiry type helps us send your message to the right person. Share relevant health, mobility, dietary, delivery, or timing needs in your message.</p><ul class="contact-list"><li><span>Email</span><a href="mailto:info@takshasheela.com">info@takshasheela.com</a></li><li><span>Phone</span><a href="tel:+9779800000000">+977 9800 000 000</a></li><li><span>Location</span>Kathmandu, Nepal</li><li><span>Response time</span>Usually within two working days</li></ul></aside></div></section>
<section class="section section--cream location-section"><div class="shell"><div class="location-map" data-reveal><iframe title="Map of Kathmandu, Nepal" src="https://www.openstreetmap.org/export/embed.html?bbox=85.2740%2C27.6740%2C85.3740%2C27.7540&amp;layer=mapnik" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe></div></div></section>
<?php render_footer(); ?>
