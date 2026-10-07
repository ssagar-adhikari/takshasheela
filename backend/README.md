# Takshasheela CMS

A unified Laravel 13 website with a public frontend, administrator authentication, and a responsive CMS dashboard.

## Included

- Administrator login, remember me, logout, login throttling, and password reset.
- Protected dashboard with saved site details and administrator count.
- Four independent About Us editors: About Takshasheela, About Ayurveda, Our Team, and Our Approach.
- Accommodation CRUD for the public listing and detail pages, with drafts, display order, and image uploads.
- Product CRUD for the catalogue, homepage products, and detail pages, with drafts, display order, prices, ingredients, and image uploads.
- Wellness Program management with categories, nested subcategories, offerings, listing/detail content, drafts, ordering, and image uploads.
- Chronicle management for Blogs, News and Events, Testimonials, and Gallery, including shared article detail pages and image uploads.
- Device image uploads, previews, replacement/removal, and live frontend content updates.
- Business identity, logo, contacts, location, map, hours, legal details, SEO defaults, and social settings, displayed dynamically across the site.
- Homepage hero and philosophy CMS, with hero poster/video uploads and shared Inclusivity content from About Takshasheela.
- Profile editing and password changes, protected by the current password.
- Interactive administrator provisioning with no default passwords or public registration.

The homepage, About Us, accommodations, products, Wellness Programs, Blogs, News and Events, Testimonials, Gallery, contact form, and shared frontend shell now run inside Laravel. Published CMS changes appear directly on public pages, while drafts stay hidden. The generic Pages module remains removed.

## Requirements

PHP 8.3 or newer, Composer, and SQLite or MySQL. Enable the PDO driver for your database, plus Laravel's standard PHP extensions. The committed dependency lock is resolved for PHP 8.3 compatibility.

No Node.js, npm, Vite process, or frontend build is required. The CMS uses Blade templates and local CSS/JavaScript in `public/`.

## Public frontend

The public website is available at `/`. Main routes include `/about`, `/accommodations`, `/products`, `/blogs`, `/news`, `/testimonials`, `/gallery`, and `/contact`. Detail pages use readable paths such as `/products/{slug}` and `/chronicles/{slug}`. Legacy `.php` URLs redirect to their Laravel equivalents.

Public templates live in `resources/views/site/`, shared navigation and footer markup lives in `resources/views/layouts/site.blade.php`, and frontend assets live in `public/assets/`. Uploaded CMS images are served through the `public/storage` link.

## Run this workspace

The existing `.env` and configured MySQL database have been preserved. The CMS migrations and site settings have already been applied.

```bash
cd /var/www/takshasheela/backend
php artisan serve --host=127.0.0.1 --port=8088
```

Open **http://127.0.0.1:8088/** for the public website. Administrator login is `/login`, and the dashboard is `/admin`.

The existing `admin@example.com` administrator account is preserved, including its password. To create another administrator with your own credentials:

```bash
php artisan cms:create-admin your-email@example.com --name="Your Name"
```

The command prompts securely for a password and confirmation. It requires at least 12 characters with letters and numbers and refuses to overwrite existing accounts.

## Seed an administrator login

Set the following variables in `.env`:

```dotenv
ADMIN_NAME="Administrator"
ADMIN_EMAIL=admin@takshasheela.com
ADMIN_PASSWORD="your-unique-password-with-at-least-12-characters-and-a-number"
```

Then run:

```bash
php artisan config:clear
php artisan db:seed --class=AdminUserSeeder
```

`php artisan db:seed` also runs this seeder through `DatabaseSeeder`. The password is hashed automatically, and the account receives the `admin` role. Existing accounts retain their password and role when seeding is repeated. Missing credentials skip administrator creation. Use the seeded email and password on `/login`.

## Fresh installation

From this directory:

```bash
composer install
cp .env.example .env
php artisan key:generate
php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
php artisan migrate --seed
php artisan storage:link
php artisan cms:create-admin your-email@example.com --name="Your Name"
php artisan serve --host=127.0.0.1 --port=8088
```
The **Our Team** editor has an **Add team member** button and a **Delete member** action on every profile. New profiles can include a role, full name, biography, profile image, and image description. A deletion takes effect when **Save Our Team** is pressed. You may store up to 50 team members; the public team grid updates automatically.


The example environment uses SQLite. To use MySQL instead, set `DB_CONNECTION=mysql`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` before migrating. Do not copy `.env.example` over an already configured environment.

Set `APP_URL` to the website URL so frontend links, uploaded image URLs, and password recovery links use the correct host.

## Password recovery

The configured mail transport delivers password reset links. A valid reset token expires after 60 minutes. The recovery screen always returns the same message regardless of whether an account exists. Only administrators may request a CMS reset. Account password changes invalidate remembered logins and other CMS sessions on their next request.

A valid reset token expires after 60 minutes. The recovery screen always returns the same message regardless of whether an account exists. Only administrators may request a CMS reset. Account password changes invalidate remembered logins and other CMS sessions on their next request.

## Business and site settings

Use **Settings** in the admin sidebar to manage the public business profile. Settings include the website and legal names, tagline, business description, default search description, uploaded logo and description, public and enquiry email addresses, primary and alternate phone numbers, WhatsApp, full location, business hours, response time, registration number, map embed and directions URLs, and Facebook, Instagram, YouTube, and LinkedIn links.

The uploaded logo appears in the public header and footer. JPG, PNG, and WebP files up to 2 MB are stored under `storage/app/public/site-settings/logo/`; replacing or removing the logo deletes the superseded upload. If no uploaded logo is selected, the original design logo is used.

The contact page reads its email, phones, WhatsApp link, address, hours, response message, registration number, map, and social profiles from these settings. Contact-form notifications are sent to the enquiry email when provided and otherwise fall back to the public contact email. The footer and default page metadata also update immediately.

Seed missing defaults without replacing saved business information with:

```bash
php artisan db:seed --class=SiteSettingsSeeder
```

For local demonstrations, fill only currently empty contact, hours, registration, and social fields with sample values:

```bash
php artisan db:seed --class=DemoSiteSettingsSeeder
```

The demo seeder never replaces a non-empty saved value. Replace its sample phone numbers, registration number, email, and social URLs before launch.

## Contact enquiries

Every valid `/contact` submission is stored in the `enquiries` table before Laravel attempts email delivery. The protected **Enquiries** screen at `/admin/enquiries` supports search, status filters, full message details, workflow states, notification retries, and deletion. Opening a new enquiry marks it as read; administrators can later archive it.

The admin notification includes the sender's name, email, phone, enquiry type, selected interest, message, and submission time. Its Reply-To address is the visitor's email. Notifications go to the Site Settings enquiry email, falling back to the public contact email. A mail failure is recorded on the enquiry and never discards the submitted message.

Gmail SMTP is configured at `smtp.gmail.com` on port `587`. Complete these private values in `.env` and clear the configuration cache:

```dotenv
MAIL_USERNAME=your-account@gmail.com
MAIL_PASSWORD="your-google-app-password"
MAIL_FROM_ADDRESS="${MAIL_USERNAME}"
```

Use a Google App Password rather than the Gmail account password, then run `php artisan config:clear`.

## Rich-text descriptions

Long-form CMS content uses a local WYSIWYG editor with paragraph, heading, bold, italic, list, quotation, undo, redo, and clear-formatting controls. It is enabled for homepage descriptions, About introductions and bodies, accommodation and product details, Wellness category/subcategory/offering descriptions, and Chronicle article bodies. Short menu summaries, SEO descriptions, structured list inputs, addresses, and image descriptions remain plain text.

Submitted HTML is sanitized on the server. Only structural text formatting is retained; scripts, embedded frames, forms, event handlers, inline styles, and arbitrary attributes are removed before saving. Existing plain-text records remain compatible and are rendered as paragraphs automatically.

## Homepage management

Use **Homepage** in the admin sidebar to edit the public hero and philosophy sections. The hero record stores its eyebrow, heading, description, location note, poster image, accessible image description, and optional background video. The philosophy record stores the quotation and attribution. These records live in the `home_sections` table.

The homepage **Inclusivity** section is not duplicated. It reads the first content block from **About Us → About Takshasheela**, including its eyebrow, heading, paragraphs, image, and image description. Saving that About block updates both the About page and homepage.

Hero poster uploads accept JPG, PNG, and WebP files up to 2 MB. Hero video uploads accept MP4 and WebM files up to 80 MB and are stored under `storage/app/public/homepage/hero/`. Configure PHP `upload_max_filesize` to at least `80M` and `post_max_size` above `80M` when replacing the video through the CMS. The existing bundled video remains available until it is replaced or removed.

Apply and seed the homepage records with:

```bash
php artisan migrate
php artisan db:seed --class=HomeSectionSeeder
```

## About Us content and image uploads

Use the **About Us** group in the admin sidebar. Each menu has its own record and editor:

| Editor | Admin URL | Public frontend |
| --- | --- | --- |
| About Takshasheela | `/admin/about-us/about-takshasheela` | `/about` |
| About Ayurveda | `/admin/about-us/about-ayurveda` | `/ayurveda` |
| Our Team | `/admin/about-us/our-team` | `/team` |
| Our Approach | `/admin/about-us/our-approach` | `/approach` |

Each editor includes its hero banner, individual content sections or team profiles, closing invitation, and search metadata. The current frontend content is imported by `AboutSectionSeeder`; reseeding preserves existing edits and uploads.

```bash
php artisan migrate
php artisan db:seed --class=AboutSectionSeeder
php artisan storage:link
```

Use **Choose image from device** to upload a JPG, PNG, or WebP file. Images may be up to **2 MB** and **6000 × 6000 pixels**, with a maximum of **6 MB total uploads per save**. The screen previews the selected image. Saving retains current images unless a replacement is selected or **Remove current image** is checked.

Files are stored under `storage/app/public/about-us/{section}/`; the database contains relative image paths. `public/storage` exposes uploaded images to the backend. Replaced images are deleted after a successful save, except when another block still uses the same image. Failed saves clean up newly uploaded files and preserve the original content.

The four public About pages use one reusable Blade renderer and read the `about_sections` table directly. Text is escaped and paragraph breaks are retained. Static design images come from `public/assets/images`; editor uploads use `public/storage`.

Grant the Laravel PHP process read and write access to public storage. PHP must allow at least `upload_max_filesize=2M` and `post_max_size=8M`. The public storage directory must persist across deployments.

## Accommodation management

Use **Accommodations** in the admin sidebar. `/admin/accommodations` lists every room and suite, while the create and edit screens manage the content shown at `/accommodations` and `/accommodations/{slug}`.

Each record stores its name, automatically generated URL slug, listing heading, category, short and full descriptions, listing highlights, room features, suitability note, booking note, publishing status, display order, and image. Draft records are hidden from the public website. Lower display-order numbers appear first. Slugs are created from names and remain unchanged when content is renamed, keeping published links stable.

The migration and default content can be applied with:

```bash
php artisan migrate
php artisan db:seed --class=AccommodationSeeder
```

The seeder imports the existing Standard Room and Deluxe Room without overwriting records that already exist. Uploaded JPG, PNG, and WebP images are stored under `storage/app/public/accommodations/{slug}/`; replaced and deleted images are removed after the database change succeeds. Laravel serves these files through the public storage link.

## Product management

Use **Products** in the admin sidebar. `/admin/products` lists the catalogue, while the create and edit screens manage content shared by `/products`, the homepage product section, and `/products/{slug}`.

Each product stores its name, automatically generated URL slug, category, size, displayed price, short and full descriptions, ingredients, usage instructions, publishing status, display order, image, and accessible image description. Draft products are hidden from the public catalogue, homepage, related-product cards, product enquiry options, and direct detail lookup. Slugs are created from product names and remain unchanged when products are renamed.

The migration and existing catalogue can be applied with:

```bash
php artisan migrate
php artisan db:seed --class=ProductSeeder
```

The seeder imports the four existing products without overwriting existing records. Uploaded JPG, PNG, and WebP images are stored under `storage/app/public/products/{slug}/`; replaced and deleted images are removed after the database change succeeds. Laravel serves these files through the public storage link.

## Wellness Program management

Use **Wellness Programs** in the admin sidebar. The content hierarchy is **Category → Subcategory → Offering**:

1. **Categories** manages top-level navigation groups such as Packages, Services, and Training.
2. **Subcategories** manages the groups displayed inside each category, such as Cleansing & Rejuvenation or Ayurveda Education.
3. **All offerings** manages the final programs and services shown beneath a subcategory and on public detail pages.

Each category stores its navigation label, listing and hero content, information band, call to action, hero image, publishing status, and display order. Each subcategory stores its parent category, menu name, short navigation description, full detail description, publishing status, and order. On offering detail pages, the parent subcategory description appears first, followed by the selected offering description. Categories cannot be deleted while they contain subcategories, and subcategories cannot be deleted or moved while they contain offerings.

Each offering stores its subcategory, name, automatically generated URL slug, card and detail content, image, highlights, inclusions, itinerary, ideal guest description, secondary details, note, publishing status, and display order. Its top-level category is derived from the selected subcategory. Slugs remain stable when an offering is renamed and are made unique when it moves across top-level categories.

Published categories, subcategories, and offerings populate the public header dropdowns, homepage Packages section, contact enquiry options, grouped category listings, and offering detail pages. The existing URLs `/programs`, `/therapies`, and `/trainings` remain available. New categories use `/wellness/{category-slug}` and offering detail pages use `/wellness/{category-slug}/{offering-slug}`.

Apply the tables and import the existing Wellness content with:

```bash
php artisan migrate
php artisan db:seed --class=WellnessSeeder
```

The seeder imports three categories, six subcategories, and nine offerings without overwriting existing content. `config/wellness.php` supplies only the initial offering content; live pages read from the database. Uploaded JPG, PNG, and WebP images are stored under `storage/app/public/wellness/categories/` and `storage/app/public/wellness/offerings/{slug}/` and served through the public storage link.

## Chronicle management

Use **Chronicles** in the admin sidebar. The group contains **Blogs & News**, **Testimonials**, and **Gallery**.

Blog and News records share one database table and the public `/chronicles/{slug}` detail page. Each article includes its section, title, automatically generated URL slug, category, excerpt, body, featured image, accessible image description, optional publish date, featured status, publishing status, and display order. Only one article is featured within each section. Slugs are created from article titles and remain unchanged when titles are edited.

Testimonials store the guest name, location, title, quote, star rating, publishing status, and display order. Published testimonials appear on both the homepage and `/testimonials`.

Gallery records store an uploaded image, accessible description, caption, publishing status, and display order. Published images appear in the `/gallery` lightbox.

Apply the tables and import the existing Chronicle content with:

```bash
php artisan migrate
php artisan db:seed --class=ChronicleSeeder
```

The seeder imports four Blogs, four News articles, three Testimonials, and eight Gallery images without overwriting existing records. Uploaded article images are stored under `storage/app/public/chronicles/{slug}/`; Gallery images are stored under `storage/app/public/gallery/`. Laravel serves these files through the public storage link.

## Database compatibility

The original CMS migration is retained for existing installations. The former `pages` table and any stored content are preserved, but page management and publishing routes are no longer available.

## Verification

```bash
php artisan test
vendor/bin/pint --test
composer validate --strict
```

Feature tests use an isolated, in-memory SQLite database and never migrate or clear the configured MySQL database. They cover authentication, authorization, throttling, password recovery, settings, account changes, administrator provisioning, CMS CRUD and publishing, Wellness category, subcategory, and offering relationships, validated uploads, image cleanup, every public Laravel page, draft visibility, legacy redirects, and contact delivery. File tests use a fake public storage disk.

## Hosting

Point the website web server's document root at **`backend/public`**, with PHP requests routed to `public/index.php`. Never expose `backend/` as a document root. Use HTTPS and configure:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://www.example.com
SESSION_SECURE_COOKIE=true
```

Allow the web-server user to write `storage/` and `bootstrap/cache/`, then run `php artisan migrate --force`, `php artisan db:seed --class=AboutSectionSeeder --force`, `php artisan storage:link`, and `php artisan optimize`. Do not commit `.env`, database files, `vendor/`, logs, or generated caches. The included `public/.htaccess` routes public requests through Laravel when using Apache.
