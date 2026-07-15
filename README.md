# Takshasheela — New Website

This directory is a standalone rebuild of the Takshasheela website. It does not load the legacy Drupal or Bootstrap theme files.

## Run locally

```bash
php -S 127.0.0.1:8780 -t takshasheela-new
```

Then open `http://127.0.0.1:8780/`.

## Structure

- `includes/site.php` — shared document shell, navigation, footer, hero, and CTA components
- `assets/css/site.css` — design tokens, components, and responsive behavior
- `assets/js/site.js` — mobile navigation, dropdown behavior, scroll state, video accessibility, and gallery dialog
- `assets/images/` — curated local imagery copied for this standalone build
- root PHP files — public pages

## Design rules

- Newsreader is used for editorial headings and DM Sans for interface/body text.
- Every page uses the same shell, spacing scale, color palette, and responsive breakpoints.
- Page templates contain no inline or embedded CSS.
- The homepage hero uses the original Takshasheela video with a still-image fallback and reduced-motion handling.
- The original site content and information architecture are retained in the rebuilt page system.
