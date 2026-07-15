<?php
declare(strict_types=1);
session_start();
$sent = false;
$errors = [];
$values = ['name' => '', 'email' => '', 'phone' => '', 'interest' => '', 'message' => ''];
$allowedInterests = ['Wellness program', 'Ayurvedic therapy', 'Accommodation', 'General enquiry', 'Panchakarma Rejuvenation', 'Ayurvedic Wellness Immersion', 'Mind-Body Balance Retreat', 'Panchakarma and Detox Therapy', 'Ayurvedic Wellness Retreat', 'Personalized Healing Program'];
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET') {
    $requestedInterest = trim((string) ($_GET['interest'] ?? ''));
    if (in_array($requestedInterest, $allowedInterests, true)) $values['interest'] = $requestedInterest;
}
if (empty($_SESSION['contact_csrf'])) {
    $_SESSION['contact_csrf'] = bin2hex(random_bytes(32));
}
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    foreach ($values as $key => $unused) {
        $values[$key] = trim((string) ($_POST[$key] ?? ''));
    }
    if ($values['name'] === '' || mb_strlen($values['name']) > 100) $errors[] = 'Please enter your name.';
    if (!filter_var($values['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';
    if (mb_strlen($values['message']) < 10) $errors[] = 'Please include a little more detail in your message.';
    if ((string) ($_POST['website'] ?? '') !== '') $errors[] = 'Your message could not be submitted.';
    if (!hash_equals((string) $_SESSION['contact_csrf'], (string) ($_POST['csrf_token'] ?? ''))) $errors[] = 'Your session expired. Please refresh and try again.';
    if (!isset($_POST['consent'])) $errors[] = 'Please confirm that we may use your details to respond.';
    if ((int) ($_SESSION['contact_last_submission'] ?? 0) > time() - 30) $errors[] = 'Please wait a moment before sending another enquiry.';
    if ($errors === []) {
        $recipient = (string) (getenv('CONTACT_RECIPIENT') ?: 'info@takshasheela.com');
        $safeName = str_replace(["\r", "\n"], '', $values['name']);
        $safeEmail = str_replace(["\r", "\n"], '', $values['email']);
        $body = "Name: {$values['name']}\nEmail: {$values['email']}\nPhone: {$values['phone']}\nInterest: {$values['interest']}\n\nMessage:\n{$values['message']}";
        $headers = ['From: website@takshasheela.com', 'Reply-To: ' . $safeEmail, 'Content-Type: text/plain; charset=UTF-8'];
        if (@mail($recipient, 'Website enquiry from ' . $safeName, $body, implode("\r\n", $headers))) {
            $_SESSION['contact_last_submission'] = time();
            $_SESSION['contact_csrf'] = bin2hex(random_bytes(32));
            $sent = true;
        } else {
            $errors[] = 'We could not send your message right now. Please contact us by email or phone.';
        }
    }
}
require __DIR__ . '/includes/site.php';
render_header('Contact', 'Contact Takshasheela to plan a stay, compare programs, or ask a question.');
render_hero('Begin a conversation', 'Tell us what support would feel useful.', 'You do not need to know which program or therapy to choose. Start with what brings you here.', 'hero--contact');
?>
<section class="section"><div class="shell contact-grid"><div class="contact-form" data-reveal><p class="eyebrow">Your enquiry</p><h2>How can we help?</h2><?php if ($sent): ?><div class="notice" role="status">Thank you. Your enquiry has been received. Our team will respond shortly.</div><?php else: ?><?php if ($errors): ?><div class="notice" role="alert"><?= e(implode(' ', $errors)) ?></div><?php endif; ?><form method="post" action="contact.php"><input type="hidden" name="csrf_token" value="<?= e((string) $_SESSION['contact_csrf']) ?>"><div class="sr-only" aria-hidden="true"><label for="website">Website</label><input id="website" name="website" tabindex="-1" autocomplete="off"></div><div class="form-grid"><div class="field"><label for="name">Full name</label><input id="name" name="name" value="<?= e($values['name']) ?>" autocomplete="name" required></div><div class="field"><label for="email">Email</label><input id="email" name="email" type="email" value="<?= e($values['email']) ?>" autocomplete="email" required></div><div class="field"><label for="phone">Phone <span>(optional)</span></label><input id="phone" name="phone" value="<?= e($values['phone']) ?>" autocomplete="tel"></div><div class="field"><label for="interest">I am interested in</label><select id="interest" name="interest"><option value="">Not sure yet</option><?php foreach ($allowedInterests as $interest): ?><option value="<?= e($interest) ?>"<?= $values['interest'] === $interest ? ' selected' : '' ?>><?= e($interest) ?></option><?php endforeach; ?></select></div><div class="field field--full"><label for="message">What would you like support with?</label><textarea id="message" name="message" required><?= e($values['message']) ?></textarea></div><div class="field field--full consent-field"><label><input type="checkbox" name="consent" value="1" required> I agree that Takshasheela may use these details to respond to my enquiry.</label></div><div class="field field--full"><button class="button" type="submit">Send enquiry</button></div></div></form><?php endif; ?></div><aside class="contact-aside" data-reveal><p class="eyebrow">Contact details</p><h2>We are here to listen.</h2><p>Share any relevant health, mobility, dietary, or timing needs so we can guide you responsibly.</p><ul class="contact-list"><li><span>Email</span><a href="mailto:info@takshasheela.com">info@takshasheela.com</a></li><li><span>Phone</span><a href="tel:+9779800000000">+977 9800 000 000</a></li><li><span>Location</span>Kathmandu, Nepal</li><li><span>Response time</span>Usually within two working days</li></ul></aside></div></section>
<?php render_footer(); ?>
