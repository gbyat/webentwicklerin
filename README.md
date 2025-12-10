# webentwicklerin

**Contributors:** webentwicklerin, Gabriele Laesser
**Tags:** full-site-editing, editor-style, block-styles, block-patterns
**Requires at least:** 6.5
**Tested up to:** 6.9
**Requires PHP:** 8.0
**Stable tag:** 0.1.8
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
