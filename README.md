# webentwicklerin

**Contributors:** webentwicklerin, Gabriele Laesser
**Tags:** full-site-editing, editor-style, block-styles, block-patterns
**Requires at least:** 6.5
**Tested up to:** 6.9
**Requires PHP:** 8.0
**Stable tag:** 0.2.0
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
- Configurable navigation responsive breakpoint and mobile menu icon alignment

### Navigation Responsive Settings

The theme extends the core Navigation block with two additional settings in the block inspector panel **Responsive Settings**:

- **Responsive navigation breakpoint (px)** -- Controls the viewport width at which the navigation switches from the desktop menu to the mobile toggle icon. Accepts values between 320 and 1920. Only available when the Navigation block's Overlay menu is set to *Mobile*. Default: 600.
- **Mobile menu icon alignment** -- Positions the mobile menu toggle icon independently from the menu's content justification. Options: *Inherit* (native behavior), *Left*, *Center*, *Right*. Available when Overlay menu is set to *Mobile* or *Always*. Default: Inherit.

Both settings are stored per Navigation block, so different navigations on the same page can use different values. The alignment is previewed live in the block editor and applied via scoped inline CSS on the frontend.

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

### Watch

```bash
npm run watch
```

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
