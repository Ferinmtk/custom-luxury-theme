# Luxury Homes — WordPress Theme

Custom theme for a luxury home builder. Fraunces/Inter, warm white `#faf9f6`, ink `#1b1a16`, forest `#3c4a3a`.

## Pages / templates
- `front-page.php` — homepage: scroll-scrubbed descent hero, statement, "Homes by style" horizontal scrub, process, contact teaser
- `page-our-homes.php` — featured home, style filters, mixed grid
- `single-home.php` — home detail: hero, specs, story, gallery + lightbox
- `page-how-we-build.php` — scroll-scrubbed build video, blueprint texture, steps
- `page-about.php`, `page-contact.php` (working form via `admin-post.php` + `wp_mail()`)

## Structure
- Functionality (Homes CPT, contact form, chat concierge, Anthropic API) lives in the **Luxury Homes Core** companion plugin — install and activate it alongside this theme.
- `assets/css/main.css`, `assets/js/main.js` — all styles/behavior (vanilla JS)
- `assets/video/`, `assets/img/` — media

## Setup
1. Install theme, activate. Install & activate the **Luxury Homes Core** plugin.
2. Create pages: `our-homes`, `how-we-build`, `about`, `contact`.
3. Settings → Permalinks → Save.
4. Optional: Homes → Demo Content → Create Demo Homes.

Bump `LH_VERSION` in `functions.php` when changing CSS/JS (cache busting).
