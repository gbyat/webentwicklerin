# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.2] - 2026-07-07

### Fixed

- Release ZIP now includes the `styles/` folder (color palette variations were missing from v1.1.0 ZIP)
- Normalize line endings to LF across the theme; add `.gitattributes` to enforce LF
- Cover blur backdrop: preserve cover min-height/aspect ratio; sharp image sized like Post Featured Image (centered, contained)

### Added

- **Blur backdrop** block style for **Cover** blocks (image backgrounds; editor preview included)


## [1.1.1] - 2026-07-07

### Fixed

- Release ZIP now includes the `styles/` folder (color palette variations were missing from v1.1.0 ZIP)
- Cover blur backdrop: preserve cover min-height/aspect ratio; sharp image sized like Post Featured Image (centered, contained)

### Added

- **Blur backdrop** block style for **Cover** blocks (image backgrounds; editor preview included)


## [1.1.0] - 2026-06-29

### Added

- Color palette variations **Warm Earth** and **Cool Slate** (`styles/color/`), selectable in the Site Editor (WordPress 6.6+)

### Changed

- Default color palette updated to **Editorial Plum** (harmonious warm neutrals, burgundy accent, WCAG AA contrast pairs)
- Minimum WordPress version raised to 6.6 (color palette variations)


## [Unreleased]

### Fixed

- Release ZIP now includes the `styles/` folder (color palette variations were missing from v1.1.0 ZIP)
- Normalize line endings to LF across the theme; add `.gitattributes` to enforce LF
- Cover blur backdrop: preserve cover min-height/aspect ratio; sharp image sized like Post Featured Image (centered, contained)

### Added

- Preload theme-bundled fonts from merged `theme.json` settings (same resolver as Core; skips Font Library / external fonts)
- **Blur backdrop** block style for **Cover** blocks (image backgrounds; editor preview included)

## [1.0.1] - 2026-06-16

### Added

- Query Loop block style **Random order** (`is-style-random-order`) for shuffle-without-pagination use cases
- **Random post images** block pattern (Query Loop + blur backdrop featured images)
- Blur backdrop editor preview script (`assets/js/blur-backdrop-editor.js`)
- README section documenting CSS custom properties and theme presets

### Fixed

- Root body text color in `theme.json` uses palette slug `contrast` instead of non-existent `text`
- Scroll-to-top respects `prefers-reduced-motion` (no fade animation; instant show/hide)
- Theme updater accepts package and release URLs only from trusted GitHub hosts (HTTPS)
- Sticky header offset follows visible admin bar height on scroll (fixes logged-in mobile gap at ≤782px; CSS no longer falls back to static 46px on small screens)

### Changed

- README: client project checklist (go-live and theme update deploy)
- Gulp `scripts` task minifies all `assets/js/*.js` to `*.min.js` (terser); included in `npm run build` and watch
- Query Loop random order now applies on the frontend (style is read from the Query Loop parent, not Post Template)
- Move form field styles to `theme.json` (`elements.textInput`, outline button hover)
- Replace frontend-only `.wp-site-blocks` padding reset with root padding in `theme.json` and `useRootPaddingAwareAlignments`
- Style categories list like vertical navigation (`core/categories` in `theme.json`)
- Reduce global button padding (`elements.button`) for a more compact default
- Centralize button border radius in `settings.custom.button.borderRadius` (used by block buttons and native submit)
- Style native form submits consistently (`input`/`button[type="submit"]`, `.submit-button`)
- Replace PHP scroll-to-top control with optional Scroll to top pattern (Icon block on WordPress 7.0+, text arrow fallback)
- Scroll to top positioning via `.scroll-to-top` theme CSS (no block style variant required)
- Enable Group block sticky positioning in `theme.json` (`settings.position.sticky`)
- Sticky support: entire header (template part) or detached nav at template root
- Custom viewport meta tag for mobile (`height=device-height`; less jump with fixed/sticky elements on iOS)
- Register Webentwicklerin block pattern category on `init` (priority 9, before theme patterns)
- Scroll to top pattern in `/patterns/scroll-to-top.php` (Webentwicklerin category; `hidden` applied on frontend via theme script)
- Fix Scroll to top pattern inserter preview (fixed positioning stays in flow inside block previews)
- Split blur-backdrop layout: full-width frame for Post Featured Image; content-sized for Image/Gallery
- Blur-backdrop: block padding insets sharp image only; blur fills full figure frame
- Enable padding controls on Image and Gallery blocks (core omits padding on Image; useful with blur-backdrop)
- Dynamic scroll padding for sticky headers (admin bar + sticky bar height via `--webentwicklerin-scroll-padding-top`)
- Remove built-in navigation breakpoint control (use WE Navigation Breakpoint plugin instead)
- Remove redundant `page-plain` and `page-blank` custom templates
- Remove editor content width cap; editor canvas matches frontend layout
- Standardize `@package` headers and script handles (`webentwicklerin` instead of `webethm`)
- Translate group block style label to English (`No outer spacing`)

### Fixed

- Scroll to top pattern block validation (remove unsupported `aria-label` from button markup; add via render filter on the front end)
- Skip link focus visibility and `#site-content` target on all templates
- Stop theme script from stripping skip-link hash from the URL
- Remove jQuery deregistration for third-party plugin compatibility (e.g. Gravity Forms honeypot)
- Remove CF7-specific and legacy `form .columns` layout styles from theme CSS
- Remove legacy `.mobilesonly` / `.desktoponly` utilities and dead skip-link CSS
- Remove unused legacy CSS (post-wrapper, Relevanssi, main-navigation block style, redundant flex helpers, term-description spacing, dead `#menu-icon`, navigation overlay overrides)
- Assign `site-header` class and semantic `<header>` to header template part (stacking context for navigation overlay)
- Move post excerpt and featured image effects to `theme.json`


## [1.0.0] - 2026-06-14

### Added

- Query Loop block style **Random order** (`is-style-random-order`) for shuffle-without-pagination use cases
- **Random post images** block pattern (Query Loop + blur backdrop featured images)
- Blur backdrop editor preview script (`assets/js/blur-backdrop-editor.js`)
- README section documenting CSS custom properties and theme presets

### Fixed

- Root body text color in `theme.json` uses palette slug `contrast` instead of non-existent `text`
- Scroll-to-top respects `prefers-reduced-motion` (no fade animation; instant show/hide)
- Theme updater accepts package and release URLs only from trusted GitHub hosts (HTTPS)

### Changed

- README: client project checklist (go-live and theme update deploy)
- Gulp `scripts` task minifies all `assets/js/*.js` to `*.min.js` (terser); included in `npm run build` and watch
- Query Loop random order now applies on the frontend (style is read from the Query Loop parent, not Post Template)
- Move form field styles to `theme.json` (`elements.textInput`, outline button hover)
- Replace frontend-only `.wp-site-blocks` padding reset with root padding in `theme.json` and `useRootPaddingAwareAlignments`
- Style categories list like vertical navigation (`core/categories` in `theme.json`)
- Reduce global button padding (`elements.button`) for a more compact default
- Centralize button border radius in `settings.custom.button.borderRadius` (used by block buttons and native submit)
- Style native form submits consistently (`input`/`button[type="submit"]`, `.submit-button`)
- Replace PHP scroll-to-top control with optional Scroll to top pattern (Icon block on WordPress 7.0+, text arrow fallback)
- Scroll to top positioning via `.scroll-to-top` theme CSS (no block style variant required)
- Enable Group block sticky positioning in `theme.json` (`settings.position.sticky`)
- Sticky support: entire header (template part) or detached nav at template root
- Custom viewport meta tag for mobile (`height=device-height`; less jump with fixed/sticky elements on iOS)
- Register Webentwicklerin block pattern category on `init` (priority 9, before theme patterns)
- Scroll to top pattern in `/patterns/scroll-to-top.php` (Webentwicklerin category; `hidden` applied on frontend via theme script)
- Fix Scroll to top pattern inserter preview (fixed positioning stays in flow inside block previews)
- Split blur-backdrop layout: full-width frame for Post Featured Image; content-sized for Image/Gallery
- Blur-backdrop: block padding insets sharp image only; blur fills full figure frame
- Enable padding controls on Image and Gallery blocks (core omits padding on Image; useful with blur-backdrop)
- Dynamic scroll padding for sticky headers (admin bar + sticky bar height via `--webentwicklerin-scroll-padding-top`)
- Remove built-in navigation breakpoint control (use WE Navigation Breakpoint plugin instead)
- Remove redundant `page-plain` and `page-blank` custom templates
- Remove editor content width cap; editor canvas matches frontend layout
- Standardize `@package` headers and script handles (`webentwicklerin` instead of `webethm`)
- Translate group block style label to English (`No outer spacing`)

### Fixed

- Scroll to top pattern block validation (remove unsupported `aria-label` from button markup; add via render filter on the front end)
- Skip link focus visibility and `#site-content` target on all templates
- Stop theme script from stripping skip-link hash from the URL
- Remove jQuery deregistration for third-party plugin compatibility (e.g. Gravity Forms honeypot)
- Remove CF7-specific and legacy `form .columns` layout styles from theme CSS
- Remove legacy `.mobilesonly` / `.desktoponly` utilities and dead skip-link CSS
- Remove unused legacy CSS (post-wrapper, Relevanssi, main-navigation block style, redundant flex helpers, term-description spacing, dead `#menu-icon`, navigation overlay overrides)
- Assign `site-header` class and semantic `<header>` to header template part (stacking context for navigation overlay)
- Move post excerpt and featured image effects to `theme.json`

## [0.3.5] - 2026-06-04

- Version update


## [0.3.4] - 2026-06-04

- Version update


## [0.3.3] - 2026-06-04

- Version update


## [0.3.2] - 2026-05-19

- Remove grayscale filter from non-hovered post featured images in CSS files


## [0.3.1] - 2026-05-15

- Version update


## [0.3.0] - 2026-04-20

- Enhance accessibility features and update translations
  - Added accessibility helpers and labels by including a new file for improved user experience.
  - Removed custom skip link functionality from functions.php to streamline code.
  - Updated German translations in .po and .mo files, including new entries for pagination and accessibility labels.
  - Adjusted POT creation date in language files for consistency.


## [0.2.2] - 2026-04-17

- Add font-display swap for Font Library and remove Dashicons for guests
  - Introduced a new file to enforce `font-display: swap` for Font Library fonts, enhancing loading performance.
  - Added functionality to dequeue Dashicons for non-logged-in users, reducing render-blocking CSS while maintaining accessibility for logged-in users.


## [0.2.1] - 2026-03-28

- Update font handling and CSS styles
  - Replaced "Roboto Flex" with "Roboto" in theme.json and CSS files for consistent typography.
  - Updated font files to include specific weights for "Roboto" and removed the "Roboto Flex" variable font.
  - Cleaned up CSS by removing unnecessary font-stretch properties and ensuring consistent font-family usage across stylesheets.
- Refactor jQuery handling in functions.php
  - Simplified the condition for dequeuing jQuery by removing the check for the customizer preview, improving performance for non-admin users.


## [0.2.0] - 2026-03-19

- Enhance navigation and layout features
  - Added configurable navigation responsive breakpoint and mobile menu icon alignment settings to the theme.
  - Updated CSS to apply box-sizing border-box for better layout consistency.
  - Increased the size of the "back to top" button for improved visibility.
  - Updated README to reflect new navigation settings and their usage.


## [0.1.10] - 2026-01-21

- Update CSS styles for improved link behavior and layout adjustments
  - Added `text-decoration: none !important;` to `#gototop a` and its hover state for consistent link appearance.
  - Removed unnecessary flex-direction property from `.wp-block-cover` to streamline layout.
  - Updated copyright year in `webentwicklerin.pot` and adjusted POT creation date.


## [0.1.9] - 2025-12-10

- Remove PHP setup step from release workflow


## [0.1.8] - 2025-12-10

- Version update


## [0.1.7] - 2025-12-03

- Version update


## [0.1.6] - 2025-12-03

- Version update


## [0.1.5] - 2025-12-03

- Version update


## [0.1.4] - 2025-12-03

- Version update


## [0.1.3] - 2025-12-03

- Update README.md to reflect compatibility with WordPress 6.9


## [0.1.2] - 2025-11-27

- Fix: Standardize quotes in release.yml and update npm install command


## [0.1.1] - 2025-11-27

- Fix: Update translation strings in webentwicklerin.pot for accuracy and consistency
- Fix: Correct version formatting in style.css
- Enhancement: Update theme configuration and build process
- Remove package-lock.json file to streamline dependency management
- Refactor: Remove icon block and related functionalities
- Enhancement: Update theme structure and functionality
- Refactor: Remove debugging functions from functions.php
- Refactor theme to 'webentwicklerin' and add new block patterns
- Merge branch 'main' of https://github.com/gbyat/webentwicklerin


## [0.1.0] - 2025-11-26

### Added

- Initial release of webentwicklerin theme with Full Site Editing support
- Block styles and block patterns
- Modern color palette
- Editor styles support
- Release automation scripts
[0.1.1]: https://github.com/gbyat/webentwicklerin/releases/tag/v0.1.1
[0.1.2]: https://github.com/gbyat/webentwicklerin/releases/tag/v0.1.2
[0.1.3]: https://github.com/gbyat/webentwicklerin/releases/tag/v0.1.3
[0.1.4]: https://github.com/gbyat/webentwicklerin/releases/tag/v0.1.4
[0.1.5]: https://github.com/gbyat/webentwicklerin/releases/tag/v0.1.5
[0.1.6]: https://github.com/gbyat/webentwicklerin/releases/tag/v0.1.6
[0.1.7]: https://github.com/gbyat/webentwicklerin/releases/tag/v0.1.7
[0.1.8]: https://github.com/gbyat/webentwicklerin/releases/tag/v0.1.8
[0.1.9]: https://github.com/gbyat/webentwicklerin/releases/tag/v0.1.9
[0.1.10]: https://github.com/gbyat/webentwicklerin/releases/tag/v0.1.10
[0.2.0]: https://github.com/gbyat/webentwicklerin/releases/tag/v0.2.0
[0.2.1]: https://github.com/gbyat/webentwicklerin/releases/tag/v0.2.1
[0.2.2]: https://github.com/gbyat/webentwicklerin/releases/tag/v0.2.2
[0.3.0]: https://github.com/gbyat/webentwicklerin/releases/tag/v0.3.0
[0.3.1]: https://github.com/gbyat/webentwicklerin/releases/tag/v0.3.1
[0.3.2]: https://github.com/gbyat/webentwicklerin/releases/tag/v0.3.2
[0.3.3]: https://github.com/gbyat/webentwicklerin/releases/tag/v0.3.3
[0.3.4]: https://github.com/gbyat/webentwicklerin/releases/tag/v0.3.4
[0.3.5]: https://github.com/gbyat/webentwicklerin/releases/tag/v0.3.5
[1.0.0]: https://github.com/gbyat/webentwicklerin/releases/tag/v1.0.0
[1.0.1]: https://github.com/gbyat/webentwicklerin/releases/tag/v1.0.1
[1.1.0]: https://github.com/gbyat/webentwicklerin/releases/tag/v1.1.0
[1.1.1]: https://github.com/gbyat/webentwicklerin/releases/tag/v1.1.1
[1.1.2]: https://github.com/gbyat/webentwicklerin/releases/tag/v1.1.2
