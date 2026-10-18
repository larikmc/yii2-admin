# Changelog

All notable changes to `larikmc/yii2-admin` will be documented in this file.

## [2.0.8] - 2026-10-18

### Fixed
- **GridView table styling**: fixed missing outer border and rounded corners on generic tables; `.grid-view .table` now has a unified 1px border (`var(--stroke)` / `#354660` in dark mode) with `border-radius: 14px`, cleanly applying to all ready-made and custom CRUD tables
- **GridView summary**: fixed large font and unwanted thick separator below "Показаны записи 1-3 из 3"; summary is now rendered in clean `12px` muted typography without border interference
- **Table bottom border**: eliminated double-thick line at the bottom of GridView by removing border on the last row's cells (`tbody tr:last-child td { border-bottom: 0 !important; }`)
- **Dashboard console spacing**: eliminated excessive `margin-top: 24px` on `.console-section`, balancing spacing between the top environment cards (PHP / Yii / Timezone) and runtime console widgets
- **Invite link generation**: removed redundant toast notification on generating invite link since the link block appears immediately in place; tightened button spacing (`margin-bottom: 0`)

## [2.0.7] - 2026-10-15

### Fixed
- sidebar menu dropdowns now automatically stay expanded when navigating inside their sub-routes (e.g. RBAC, roles/permissions tabs, invites, security logs, or custom catalog CRUD pages)
- active child submenu links are now clearly highlighted with distinct accent background, border accent and bold font (`.selected`, `.active`)
- parent dropdown item receives active child state indicator (`has-active-child` / `sz-dd--has-active-child`) with highlighted icon
- enhanced route resolution (`$isActive`) with support for normalized admin prefixes, controller-level matching for CRUD actions (create/update/view under index link), and RBAC tab routes

### Changed
- `autoExpandActiveSubmenu` default changed to `true` in `Module` so active dropdown sections stay open seamlessly out of the box

## [2.0.6] - 2026-10-15

### Fixed
- fixed jerky layout twitching and jumping during dropdown animations by eliminating `max-height` conflict and animated margins/paddings
- fixed submenus snapping shut instantly on close; closing animation now plays completely and smoothly down to 0px
- fixed dropdown toggle failing on first click; toggles immediately on first click with responsive chevron rotation
- resolved issue where bottom submenu tab (`SEO` / `Редиректы` and similar) remained permanently open on reload and navigation
- synchronized left guide border (`border-left`) to smoothly expand and collapse alongside the submenu without visual glitching or cutoffs

### Added
- added `autoExpandActiveSubmenu` property (default `false`) to `Module` to keep submenus neatly folded on page load unless explicitly configured with `'open' => true`

## [2.0.5] - 2026-10-15

### Fixed
- sidebar menu dropdowns now cleanly collapse by default on page load and refresh (only the section containing the active child route is expanded automatically)
- fixed dropdown toggle requiring double click to close; toggles immediately on the first click
- eliminated left border line (`border-left`) jump/glitch during and after dropdown expand/collapse animations
- removed leftover 4px border stub and margin gaps for closed submenus (now completely zero-height, hidden and transparent)
- prevented child elements' bubbling `transitionend` events from prematurely interrupting height animation
- fixed rapid clicking on dropdowns to smoothly reverse animation without broken heights or desynchronization

### Added
- supported `'active'` and `'open'` keys in menu configuration array for custom submenu control
- comprehensive documentation for sidebar menu configuration in `README.md`

## [2.0.4] - 2026-10-15

### Added
- modern 2-panel runtime console dashboard with live server telemetry, resource metrics and runtime logs preview
- quick cache flushing and runtime actions directly from dashboard

## [2.0.3] - 2026-10-15

### Changed
- improved admin invite link copying UX with visual feedback
- refined RBAC invite form layout spacing and toast notifications

## [2.0.2] - 2026-10-14

### Added
- modernized RBAC overview stat cards into hero-metrics
- switched default `AdminTabs` style to segmented solid pills (`.nav-pills-solid`)
- added synchronous theme preload script and cookie support to eliminate FOUC (flash of unstyled content)

### Fixed
- fixed security log table display and empty state in dark mode
- fixed sidebar toggle and compact mode persistence across page loads

## [2.0.1] - 2026-10-14

### Added
- aligned auth module UI with the main admin design (ambient lighting, Inter typography, Material Symbols)
- added cross-module dark/light theme synchronization between admin panel and auth pages
- added interactive password visibility toggle (`visibility` / `visibility_off`) on all auth password inputs
- added Caps Lock indicator for password fields
- added quick navigation link to public website from the auth header

### Changed
- redesigned login, password reset and admin invite views with cohesive glassmorphic cards and branding

## [2.0.0] - 2026-10-14

### Added
- added user profile management (`/admin/user/profile`) with username, name, password, and secure two-step email change
- added email change confirmation flow with security token / confirmation code verification
- added placeholder icon asset for UI components
- added dark / light theme switcher with localStorage persistence
- added full-width layout mode toggle
- added topbar notifications popover and user profile dropdown

### Changed
- redesigned admin panel UI with modern Inter typography, sleek surfaces, refined metrics cards, and improved navigation
- updated sidebar collapse UX and mobile drawer behavior
- enhanced `/admin/site/ui-kit` with updated component showcases, badges, form controls, and tab examples

## [1.2.5] - 2026-07-18

### Added
- added built-in guest guard for unknown admin routes: unauthenticated requests to arbitrary `/admin/...` URLs now redirect to the configured login route instead of reaching admin error pages with sidebar/menu
- added configurable module options `guardUnknownAdminRoutesForGuests` and `guestAllowedPaths` for projects that need to disable or extend the default guest whitelist

### Changed
- module now registers the short `login` route automatically alongside reset/invite routes, so the recommended `/admin/login` URL works out of the box when `user.loginUrl = ['/login']`

## [1.2.4] - 2026-06-30

### Added
- added reusable `AdminTabs` widget that preserves the RBAC tabs visual style and supports both link-based navigation and in-page tab panels
- added a live tabs demo with usage example to `/admin/site/ui-kit`

### Changed
- migrated `AdminPage` tabs rendering to the shared `AdminTabs` widget so RBAC and future admin sections use the same tab implementation

## [1.2.2] - 2026-05-26

### Changed
- fixed admin invite generation UX on `/admin/rbac/invite`: generated link is now shown in UI, copy button added, and clipboard fallback works when `navigator.clipboard` is unavailable

## [1.2.1] - 2026-05-04

### Changed
- fixed GridView overflow in admin panels so wide tables scroll inside the panel and do not push the whole layout to the right

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
