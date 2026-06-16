# webentwicklerin

**Contributors:** webentwicklerin, Gabriele Laesser
**Tags:** full-site-editing, editor-style, block-styles, block-patterns
**Requires at least:** 6.5
**Tested up to:** 7.0
**Requires PHP:** 8.0
**Stable tag:** 1.0.1
**License:** GPLv2 or later
**License URI:** https://www.gnu.org/licenses/gpl-2.0.html

---

Modern WordPress Block Theme for Full Site Editing.

## Description

webentwicklerin is a modern WordPress block theme designed for Full Site Editing (FSE) with block styles and block patterns. It provides a clean, flexible foundation for building WordPress websites.

## Features

- Full Site Editing (FSE) support
- Block styles and block patterns
- Modern, clean design
- Responsive layout
- Customizable color palette
- Editor styles support

Navigation responsive breakpoint and mobile menu icon alignment are provided by the optional [WE Navigation Breakpoint](https://github.com/gbyat/we-navigation-breakpoint) plugin (same attribute names; install when needed).

### Scroll to top pattern

Insert the **Scroll to top** pattern from the block inserter (**+** → **Patterns** → category **Webentwicklerin**, or search for `scroll`). Best placed in the footer template part. Positioning uses the theme `.scroll-to-top` class; theme JavaScript handles the fade-in on scroll.

### Sticky header or navigation

Only **Group** blocks support Position → Sticky. Wrap blocks in a Group and set Sticky in the block inspector.

The theme supports **two layouts**:

1. **Entire header sticky** — branding and navigation stay together. Set Sticky on the outermost Group inside the header template part (direct child of the header).
2. **Detached navigation** — header template part for branding only; navigation as a sibling block in the template (Group with nav, or its own template part). Set Sticky on the outermost Group inside that block. The nav then stays visible while scrolling the page.

Give sticky groups a background color so content does not show through.

When sticky elements are present, the theme script sets `--webentwicklerin-scroll-padding-top` on `html` (admin bar height plus sticky bar height). Anchor links, the skip link target, and `:target` elements scroll clear of the sticky area automatically. Updates on resize and when the header height changes.

### Featured image: Blur backdrop

**Blur backdrop** is available on **Post Featured Image**, **Image**, and **Gallery** blocks.

### Post Featured Image

- Full-width **figure frame** (`width: 100%`) — for query loops, archives, and singles.
- Set an **aspect ratio** (e.g. `3/2`) so every tile shares the same frame; the sharp image is centered with `object-fit: contain`; blur fills the frame.
- Block **padding** insets the sharp image only.

### Image block and Gallery

- **With aspect ratio:** figure uses **content width** (`fit-content`, max `100%`) inside the container; use **align wide/full** for a full-width frame.
- **Without aspect ratio:** figure shrink-wraps to the chosen image size; alignment is preserved.
- Block **padding** insets the sharp image only (same as Post Featured Image; enabled in this theme because core omits padding on Image blocks).
- **Gallery:** apply the style on the gallery block (all items) or on individual images; items fill their grid cell.

Standard images **without** this block style are unchanged. Default blur is `15px`; override with `--featured-image-blur` in custom CSS if needed.

### Query Loop: Random order

WordPress does not offer random sorting in the Query Loop UI. This theme adds a **Random order** block style on the **Query Loop** block.

1. Insert a **Query Loop** and choose **Custom query** (not inherit from template).
2. Set **Items per page** to the number of posts you want (e.g. 6).
3. On the Query Loop block, open **Styles** and pick **Random order**.
4. Do **not** add a Query Pagination block.
5. Inside **Post template**, add **Post Featured Image** (link to post URL) or other blocks as needed.

Posts are shuffled on each page load on the frontend. The editor preview may still show the default sort order.

### Random post images pattern

Insert **Random post images** from **Patterns** → **Webentwicklerin**. It includes a Query Loop with **Random order**, a 3-column grid, and linked Post Featured Images with **Blur backdrop** (aspect ratio 3/2, 6 items). Adjust count, columns, or styles after inserting.

## CSS custom properties

Reference for theme-specific variables and the WordPress presets this theme uses most often. Override theme-specific values in **Appearance → Editor → Additional CSS**, in a child theme, or via `theme.json`.

### Theme-specific

| Variable | Default | Set on | Purpose |
|----------|---------|--------|---------|
| `--webentwicklerin-scroll-padding-top` | `0px` (updated at runtime) | `html` | Top offset for anchor scrolling when the admin bar and/or sticky header groups are present. Set by `theme-scripts.js` (admin bar height + outermost sticky bar height). Used for `scroll-padding-top` on `html` and `scroll-margin-top` on `:target`. |

### Blur backdrop (`is-style-blur-backdrop`)

Used on **Post Featured Image**, **Image**, and **Gallery** blocks with the Blur backdrop style.

| Variable | Default | Set by | Purpose |
|----------|---------|--------|---------|
| `--featured-image-blur` | `15px` | Theme CSS on the figure | Blur strength of the backdrop layer. **Override this** to soften or intensify the effect. |
| `--featured-image-url` | — | PHP (frontend) / editor script | Background image URL for the blur layer. Normally automatic; do not set manually unless debugging. |
| `--featured-image-inset-top` | `0` | PHP / editor script from block padding | Insets the sharp image from the top; blur still fills the full frame. |
| `--featured-image-inset-right` | `0` | PHP / editor script from block padding | Insets the sharp image from the right. |
| `--featured-image-inset-bottom` | `0` | PHP / editor script from block padding | Insets the sharp image from the bottom. |
| `--featured-image-inset-left` | `0` | PHP / editor script from block padding | Insets the sharp image from the left. |

Example — stronger blur on all blur-backdrop images:

```css
figure.is-style-blur-backdrop {
	--featured-image-blur: 24px;
}
```

Example — softer blur on a single block (add a custom CSS class in the block’s Advanced settings):

```css
.my-soft-blur.is-style-blur-backdrop {
	--featured-image-blur: 8px;
}
```

### Defined in `theme.json` (`settings.custom`)

| Variable | Default | Purpose |
|----------|---------|---------|
| `--wp--custom--button--border-radius` | `0` | Border radius for block buttons and native submit buttons (`settings.custom.button.borderRadius`). |

Change the default in **theme.json** under `settings.custom.button.borderRadius`, or override in Additional CSS:

```css
:root {
	--wp--custom--button--border-radius: 4px;
}
```

### Optional hook (blur captions)

| Variable | Fallback | Purpose |
|----------|----------|---------|
| `--wp--custom--line-height` | `1.5` | Line height for figcaptions inside blur-backdrop frames. Not defined in `theme.json` by default; set in Additional CSS if needed. |

### Color palette (`settings.color.palette`)

WordPress exposes each swatch as `--wp--preset--color--{slug}`. Default slugs in this theme:

| Slug | Role |
|------|------|
| `base`, `base-2`, `base-3`, `base-4`, `base-5` | Backgrounds and neutrals |
| `contrast`, `contrast-2`, `contrast-3` | Text and strong UI |
| `accent`, `accent-2`, `accent-3`, `accent-4` | Links, headings, buttons, highlights |
| `transparent` | Transparent borders/backgrounds |

Default element pairings in `theme.json`: body text uses `contrast`; links and headings use `accent-4`; buttons use `accent-2` background with `base` text; form fields use `contrast` on `base-2`. Adjust per project in the Site Editor or `theme.json`.

### Spacing presets (`settings.spacing.spacingSizes`)

Available as `--wp--preset--spacing--{slug}`:

| Slug | Name |
|------|------|
| `tiny` | 8px |
| `small` | 16px |
| `medium` | clamp 24–32px |
| `large` | clamp 32–48px |
| `x-large` | clamp 32–64px |
| `xx-large` | clamp 32–80px |
| `xxx-large` | clamp 48–96px |
| `xxxx-large` | clamp 60–150px |

### Layout

| Variable | Source | Purpose |
|----------|--------|---------|
| `--wp--style--global--content-size` | `settings.layout.contentSize` (`960px`) | Default content width |
| `--wp--style--global--wide-size` | `settings.layout.wideSize` (`1280px`) | Wide alignment width |

With `useRootPaddingAwareAlignments: true`, root horizontal padding uses WordPress core variables `--wp--style--root--padding-left` and `--wp--style--root--padding-right` (from `styles.spacing.padding` on the root).

### WordPress admin bar (sticky layouts)

| Variable | Purpose |
|----------|---------|
| `--wp-admin--admin-bar--height` | Admin bar height (logged-in users) |
| `--wp-admin--admin-bar--position-offset` | Sticky offset when the admin bar is visible (`02-template-parts.scss`) |

## Client project checklist

Use this when rolling out or handing off a client site built with webentwicklerin.

### Before go-live

- [ ] Adjust the color palette in the Site Editor or `theme.json`; verify contrast (body text, links, buttons) after changes
- [ ] Add alt text to featured images and content images (especially linked Post Featured Images in Query Loops)
- [ ] Confirm heading order (one H1 per page, logical hierarchy)
- [ ] Install [WE Navigation Breakpoint](https://github.com/gbyat/we-navigation-breakpoint) when the navigation needs a custom mobile breakpoint
- [ ] Run a quick accessibility check (Lighthouse or axe DevTools) on key templates

### Theme update deploy

After updating the theme on a client site, upload at minimum:

- `functions.php`
- `theme.json`
- `assets/css/style.min.css`
- `assets/css/editor-style.min.css`
- `assets/js/theme-scripts.min.js`
- `assets/js/blur-backdrop-editor.min.js`
- Any new or changed files under `inc/`, `patterns/`, and source `assets/js/*.js` (rebuild min files)

Run `npm run build` locally before packaging. Clear page cache on the client site after deploy.

## Requirements

- WordPress 6.5 or higher
- PHP 8.0 or higher

## Installation

1. Download the theme ZIP file
2. Go to WordPress Admin → Appearance → Themes
3. Click "Add New" → "Upload Theme"
4. Select the ZIP file and click "Install Now"
5. Activate the theme

## Development

### Prerequisites

- Node.js 14.0.0 or higher
- npm 6.0.0 or higher

### Setup

```bash
npm install
```

### Build

```bash
npm run build
```

Compiles SCSS to `style.css` / `style.min.css` and `editor-style.css` / `editor-style.min.css`, minifies all `assets/js/*.js` to `*.min.js`, and updates the `.pot` file.

**Note:** `*.min.css` and `*.min.js` are gitignored; run `npm run build` before deploy or release so minified assets exist on client sites.

### Watch

```bash
npm run watch
```

Rebuilds CSS and JS when source files change.

## Release Process

The theme uses automated release scripts for version management:

- `npm run release:patch` - Patch release (1.0.0 → 1.0.1)
- `npm run release:minor` - Minor release (1.0.0 → 1.1.0)
- `npm run release:major` - Major release (1.0.0 → 2.0.0)

The release process will:

1. Bump the version in `package.json`
2. Sync the version to `style.css`
3. Update `CHANGELOG.md`
4. Build theme assets
5. Create a release ZIP file
6. Commit and tag the release
7. Push to GitHub

## License

GPL-2.0-or-later

## Author

**webentwicklerin, Gabriele Laesser**

- Website: https://webentwicklerin.at
- GitHub: [@gbyat](https://github.com/gbyat)
