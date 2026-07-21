# CLAUDE.md — Luxury Homes theme

Read this first. It tells you what this project is, how it's wired to the local
environment, the git workflow (branch **per page**), and the conventions to
follow so you don't break things.

---

## 1. What this is

A **custom WordPress theme** for a luxury custom-home builder — a dark, editorial,
cinematic marketing site. Text set in serif display faces on warm/dark palettes,
scroll-driven animation, hand-drawn SVG motifs.

- Theme slug / folder: `luxury-homes`
- Version constant: `LH_VERSION` in `functions.php`
- Not a block theme — classic PHP templates + one CSS file + one JS file.

### Companion plugin (important)
Content/behaviour that must survive a theme switch lives in a **separate plugin,
"Luxury Homes Core"** (functions prefixed `lhc_`). It provides:

- the `home` custom post type + its meta (`_home_style`, `_home_featured`, etc.)
- helpers the templates call: `lh_home_meta()`, `lh_home_image()`,
  `lh_home_styles()`, `lh_normalize_style()`
- the contact form, chat concierge, Anthropic API integration

If the plugin is inactive, templates **degrade to empty output** rather than
fatalling, and an admin notice warns you. When editing templates, keep calls to
those plugin helpers guarded with `function_exists()` where practical.

---

## 2. How it's connected to local

Development happens **inside Local (by Flywheel)**. The theme folder on disk *is*
the live theme — there is no build/upload step for PHP.

- Site: **luxuryhomes.local** (nginx, PHP 8.2, MySQL 8, WordPress 7.0.x)
- Theme path:
  `~/Local Sites/luxuryhomes/app/public/wp-content/themes/luxury-homes`
- Editor: PhpStorm, opened on that theme folder.

**Workflow:** edit a file → save → refresh the browser. That's it.

- **PHP changes** are instant on refresh.
- **CSS / JS changes** are cached by the **WP Fastest Cache** plugin — you must
  **clear its cache** (and hard-refresh) to see them. This is the #1 cause of
  "my change didn't show." Local itself does not cache PHP.
- A PHP syntax error white-screens the whole site until fixed — normal, just undo.

---

## 3. Git workflow — branch PER PAGE

- Remote: `git@github.com:Ferinmtk/custom-luxury-theme.git` (SSH)
- Production branch: **`main`**
- **Every page / feature gets its own branch**, named `fm/<thing>`
  (e.g. `fm/home-page`, `fm/our-homes`, `fm/contact`).

**Current branch as of this writing: `fm/home-page`.**
Always verify before working:

```bash
git branch --show-current
```

### Because Local serves whatever is checked out
The live `luxuryhomes.local` reflects the **currently checked-out branch**. So
while you're on `fm/home-page`, the site shows that branch's work — that's how
you preview before merging. After merging, check `main` back out.

### Start work on a page
```bash
git checkout main
git pull origin main
git checkout -b fm/<page>          # new branch for this page
# ...edit, commit as you go...
git push -u origin fm/<page>
```

### Merge back when the page is done and approved
```bash
git checkout main
git pull origin main
git merge fm/<page>
git push origin main
git branch -d fm/<page>            # optional cleanup
git push origin --delete fm/<page>
```

`node_modules/` is gitignored. **Do** commit `package-lock.json`.

---

## 4. File map

```
luxury-homes/
├── CLAUDE.md              ← this file
├── functions.php         BOOTSTRAP only — defines LH_VERSION and requires /inc
├── inc/                  theme logic, one responsibility per file:
│   ├── helpers.php       lh_field, lh_asset, lh_company, lh_field_image, portfolio placeholder
│   ├── navigation.php    lh_nav_items, lh_nav_list
│   ├── icons.php         lh_social_icon, lh_option_icon (inline SVGs)
│   ├── homes.php         lh_get_homes, lh_split_featured, lh_home_image_url (Homes CPT)
│   ├── setup.php         theme supports, menus, image sizes, plugin-missing notice
│   ├── enqueue.php       CSS/JS/fonts + resource hints + per-page enqueues
│   └── meta.php          Our Homes <head>: LCP preload + Open Graph
├── header.php  footer.php
├── front-page.php        homepage (hero, philosophy, scrub gallery, how-we-work,
│                          "Start where you are" flip cards)
├── page-our-homes.php    portfolio grid (slug: our-homes)
├── page-about.php  page-how-we-build.php  page-contact.php
├── single-home.php       single `home` CPT
├── index.php  style.css  (style.css only holds the WP theme header)
├── assets/
│   ├── css/main.css      SINGLE stylesheet — see §5
│   ├── js/main.js        SINGLE script — one IIFE, sub-IIFEs per feature
│   ├── img/              incl. blueprint.svg (extracted watermark), cta-planning.jpg
│   └── video/            descent.mp4, build.mp4 (large; consider Git LFS if it grows)
├── package.json  .stylelintrc.json  .stylelintignore   ← CSS linting (§6)
└── .gitignore
```

**Architecture rules (WordPress-aware):**
- Root templates (`front-page.php`, `page-*.php`, `single-*.php`, `header.php`,
  `footer.php`, `index.php`, `functions.php`, `style.css`) **must stay in root** —
  WP's template hierarchy finds them by name/location. Do not move them.
- **Theme logic lives in `/inc`**, one responsibility per module; `functions.php`
  is only the loader. To add a helper/hook: put it in the right `/inc` module (or
  a new one) and, if new, `require` it from `functions.php` in dependency order
  (helpers first).
- Reusable markup can later be extracted to `/template-parts` and pulled with
  `get_template_part()` — the front-page sections are the obvious candidates.

---

## 5. CSS conventions (`assets/css/main.css`)

It's **one large file, intentionally** (single request). It is organised, not
split. Rules:

- There's a **table of contents** at the top and every section has a **greppable
  banner tag**: `[SHARED]`, `[PAGE: FRONT]`, `[PAGE: OUR HOMES]`,
  `[PAGE: SINGLE HOME]`, `[PAGE: HOW WE BUILD]`, `[PAGE: CONTACT]`, `[PAGE: ABOUT]`.
  To find a page's styles, search (Ctrl/Cmd-F) for its tag.
- **Do NOT reorder rules.** CSS order is significant (later wins ties); annotate
  in place.
- **Scope page-specific styles to a page class** so shared components aren't
  affected on other pages. The templates add these to `<main>`:
  `.our-homes-page`, `.about-page`, `.hwb-page`. Front-page sections use their
  own section classes (`.scrub`, `.hww`, `.ways`, …).
  Example: `.hm-cta` and `.hm-values` are shared across About / How-We-Build /
  Our Homes, so Our Homes overrides them under `.our-homes-page`.

### Design tokens (in `:root`)
Site: `--ink #16130f` · `--bone #efe7da` · `--travertine #c9baa3` ·
`--brass #a8834f` · `--line`.
Hero/light pages: `--hero-white #faf9f6` · `--hero-ink #1b1a16` ·
`--hero-ink-soft #6c6860` · `--hero-forest #3c4a3a` · `--hero-ease`.
Fonts: `--display "Marcellus"` (site display), `--hero-display "Fraunces"`
(page display), `--hero-sans/--body "Inter"`, `--nav-sans "Space Grotesk"`.
The "How we work" section (`.hww`) defines its own scoped tokens (Cormorant
Garamond + Archivo). The blueprint SVG uses "Architects Daughter".

**Brass (`--brass`) is the shared accent** — used on the Our Homes CTA button,
value-card hover accents, the scrub progress bar, etc. Keep new accents in that
family for cohesion.

### Motion
- Scroll reveals use the `data-reveal` attribute → JS adds `.is-visible`.
- Everything must respect `@media (prefers-reduced-motion: reduce)`.
- Hover-only interactions should be gated with `@media (hover: hover)` and have a
  touch fallback (the flip cards toggle `.is-flipped` on tap via JS).

---

## 6. Tooling — always validate before shipping

**Stylelint** is set up (config tuned to ignore this theme's intentional
patterns). Run it before committing CSS:

```bash
npm install          # first time only (creates node_modules/, gitignored)
npm run lint:css     # check
npm run lint:css:fix # auto-fix safe issues
```

Before handing back any change, sanity-check:
- CSS: braces balanced + `npm run lint:css` clean.
- PHP: no syntax errors (the site white-screens otherwise).
- JS: it's one IIFE — a syntax error breaks *all* front-end behaviour. Keep new
  behaviour in its own sub-IIFE and add it **before** the final `})();`.

PhpStorm code style: a scheme (`tom_code_style.xml`) aligns PHP array `=>` pairs —
match the existing aligned-array style in `functions.php`.

---

## 7. Key helpers (in `/inc`)

Defined across the `/inc` modules (see §4 for which file):
`lh_field($name,$default)` (ACF-safe getter, `helpers.php`) · `lh_company()` · `lh_nav_items()` /
`lh_nav_list()` · `lh_asset($path)` · `lh_social_icon($name)` (footer SVGs) ·
`lh_option_icon($name)` (flip-card icons) · `lh_get_homes()` (bounded, cached) ·
`lh_split_featured()` · `lh_home_image_url()`. There are also `wp_head` hooks for
the Our Homes page (LCP image `preload` + Open Graph, skipped if a SEO plugin is
active).

---

## 8. ⚠️ Placeholders — DO NOT DEPLOY as-is

- **Fabricated testimonial** in `front-page.php` (the "How we work" quote — "The
  Halvorsens"). There's a `DO NOT DEPLOY` comment on it. Replace with a real,
  consenting client quote (ACF `hww_quote` / `hww_quote_attr`) or remove the block.
- Placeholder home data (e.g. "55 beds · 555 sq ft"), default company name
  "Tester", `img/philosophy-PLACEHOLDER.jpg`.
- Footer social links default to `#` (set ACF `social_instagram` etc.).
- Confirm phone / service-area ACF fields are populated so footer links aren't dead.

---

## 9. Working style expected here

- **Deliver the FULL, complete file(s) — every time.** When you make a change,
  hand back the entire file(s) ready to drop straight into the theme folder, not
  diffs, snippets, patch fragments, or "add this line here" instructions. The
  human replaces files wholesale in the Local folder, so partial output is not
  usable. If several files change, provide all of them, complete.
- Look at the real file before editing; don't guess at markup or values.
- Keep changes **scoped** (page class) and **validated** (lint + brace/PHP/JS check).
- Prefer editing existing patterns over inventing new ones; reuse the tokens,
  the `data-reveal` reveal system, and the brass accent.
- After a change, remind to **clear WP Fastest Cache** for CSS/JS.
- Commit on the page's branch; merge to `main` only when the page is approved.
