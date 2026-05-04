# Changelog

All notable changes to `larikmc/yii2-admin` will be documented in this file.

## [1.2.0] - 2026-05-04

### Added
- added built-in admin password reset flow with `/admin/auth/request-password-reset` and `/admin/auth/reset-password`
- added one-time admin invite flow with `/admin/rbac/invite` and `/admin/auth/invite`
- added invite-based admin signup with automatic `admin` role assignment
- added password recovery link on the admin login screen

### Changed
- updated README with new auth routes, invite routes, integration rules, and guest whitelist requirements
- changed password reset UX to always show a neutral success state without revealing whether an email exists
- limited password reset email delivery to users who have `adminPanel` access
- limited invite generation UI and controller access to the root administrator with `ID=1`

## [1.1.19] - 2026-05-04

### Changed
- fixed wide `GridView` tables inside `.sz-panel` so horizontal overflow stays inside the panel instead of pushing the whole admin layout to the right
- added `min-width: 0` safeguards for `.sz-panel` and `.sz-content__inner` to keep responsive admin pages stable with large tables

## [1.1.18] - 2026-04-22

### Changed
- fixed lazyload re-initialization after Yii2 PJAX updates by adding jQuery `pjax:end` binding in `lazyloader.js` (covers paginated admin grids and filters)

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
