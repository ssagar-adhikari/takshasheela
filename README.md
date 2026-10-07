# Takshasheela Laravel website and CMS

The complete public website and administrator CMS now run from the Laravel 13 application in [`backend/`](backend/README.md). Public pages read published About, Accommodation, Product, Blog, News, Testimonial, and Gallery content directly from the CMS database.

## Run locally

```bash
cd backend
php artisan serve --host=127.0.0.1 --port=8088
```

Open **http://127.0.0.1:8088/** for the public website. Administrator login is at **http://127.0.0.1:8088/login**, and the dashboard is `/admin`.

Create another administrator when needed:

```bash
php artisan cms:create-admin your-email@example.com --name="Your Name"
```

## Structure

- `backend/resources/views/site/` — public Blade pages.
- `backend/resources/views/layouts/site.blade.php` — shared navigation and footer.
- `backend/app/Http/Controllers/PublicSiteController.php` — database-backed public pages and contact form.
- `backend/app/Http/Controllers/WellnessController.php` — package, therapy, and training pages.
- `backend/public/assets/` — public CSS, JavaScript, images, and video.
- `backend/storage/app/public/` — CMS-uploaded images exposed through `backend/public/storage`.
- `backend/resources/views/admin/` — CMS screens.

The former root PHP frontend remains in the repository as migration history. Laravel redirects its `.php` URLs to the current clean routes, including `/accommodations/{slug}`, `/products/{slug}`, and `/chronicles/{slug}`. Configure the web server document root as `backend/public`.

See the [Laravel setup and usage guide](backend/README.md) for fresh installation, database seeding, mail configuration, testing, and production hosting.
