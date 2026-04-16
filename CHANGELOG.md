# Changelog

All notable changes to `larikmc/yii2-admin` will be documented in this file.

## [1.1.17] - 2026-04-16

### Changed
- removed fixed dimensions from `.sz-thumb--lg` to keep thumbnail sizing fully project-defined

## [1.1.16] - 2026-04-15

### Changed
- release version bump and changelog sync

## [1.1.15] - 2026-04-15

### Changed
- removed scale transform from popup close button hover/focus state

## [1.1.14] - 2026-04-15

### Changed
- made `.sz-thumb` size-neutral with a white background so projects can define thumbnail dimensions themselves
- kept `.sz-thumb--sm` and `.sz-thumb--lg` as optional ready-made size modifiers

## [1.1.13] - 2026-04-15

### Changed
- reinitializes image lazyload automatically after Yii PJAX updates

## [1.1.12] - 2026-04-15

### Added
- added built-in admin image popup viewer with bundled styles and delegated `data-image-viewer` support
- added lazyload support for `data-src` and `data-srcset` images with a default bundled `load.svg` placeholder
- added configurable `lazyloadPlaceholderUrl` and `getLazyloadPlaceholderUrl()` helper on the admin module
- added reusable `.sz-thumb` thumbnail classes for centered placeholders and stable image cells
- added popup/lazyload examples to `ADMIN-UI-KIT`

### Changed
- expanded README and UI README with required UI integration notes, popup/lazyload examples, GridView examples, PJAX notes, and common mistakes
- improved popup closing, hover state, drag prevention, and selection cleanup

## [1.1.8] - 2026-04-05

### Changed
- fixed CAPTCHA validator route in `LoginForm` to use module route `admin/auth/auth/captcha`, preventing `Invalid CAPTCHA action ID` in backend integration
- updated README auth routing instructions with explicit `/admin/login` setup (`loginUrl`, url rules, and `beforeRequest` whitelist) to avoid redirect loops

## [1.1.6] - 2026-03-30

### Changed
- applied admin pagination styles globally to all `LinkPager` lists instead of only the UI Kit demo
- improved cache clearing to clean configured app cache and runtime cache directories for backend, frontend, and common when present
- simplified cache clear success notification text

## [1.1.5] - 2026-03-30

### Changed
- centered the sidebar collapse toggle in collapsed mode
- fixed collapsed sidebar nav item alignment so active and hover states stay visually even
- added a GridView pagination demo block to the UI Kit page
- added isolated UI Kit pagination styles for default, hover, active, and disabled states
