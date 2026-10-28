# Changelog

All notable changes to `larikmc/yii2-admin` will be documented in this file.

## [2.1.27] - 2026-10-28

### Fixed & Enhanced
- **Preserve Full Email Address on First Admin Setup**:
  - Fixed an issue in `FirstAdminService` where configurations using `'usernameField' => 'email'` (or user tables without a separate `username` column) had the `email` attribute inadvertently overwritten by the username prefix (`strstr($email, '@', true)`).
  - Ensured the `email` attribute is always preserved with the full validated email address and never overwritten.
- **Robust User Model Lookup in LoginForm**:
  - Added support for `emailField` configuration in `LoginForm` and provided fallback lookup via ActiveRecord `findOne()` when custom `findByEmail()` is not defined.
  - Forwarded `emailField` from `Module` into `LoginForm` in `AuthController`.
- **First Admin Scenario UI & Brute-Force Bypass**:
  - Dynamically updated title, subtitle, and submit button in `views/auth/login.php` when entering credentials for an empty database installation.
  - Excluded first administrator setup from brute-force attempt counters and lockouts.
- **Unified Short Auth Routes & Guest Allowed Paths**:
  - Added short routes (`/login`, `/logout`, `/captcha`, `/request-password-reset`, `/reset-password`, `/two-factor`, `/invite`) to `Module::DEFAULT_GUEST_ALLOWED_PATHS` and default urlManager rules.
  - Updated all auth view templates to link to canonical clean routes (`['/login']`).

## [2.1.26] - 2026-10-26

### Fixed & Enhanced
- **Reliable 2FA Key Regeneration Native Form**:
  - Replaced `Html::a(...)` pseudo-links (`data-method="post"`) with native HTML `<form method="post">` buttons and explicit `onclick="return confirm(...)"` dialogs in both the unconfigured warning alert and the `#twoFactorKeyModal` footer.
  - Fixes the issue where clicking "Сгенерировать новый ключ" inside the detached modal was ignored by JavaScript event delegation.
- **Support for Both POST and GET in VerbFilter**:
  - Allowed both `['post', 'get']` in `UserController::behaviors()` for `regenerate-two-factor-key`, preventing 405 Method Not Allowed errors if triggered directly.
- **Automatic Setup Secret Invalidation & Error Handling**:
  - Added session cache invalidation (`2fa_setup_secret_...`) upon key regeneration, ensuring the TOTP secret and QR code on screen immediately re-generate and encrypt with the new master key.
  - Added write failure checks in `TwoFactorService::generateNewSecretKey()` and try-catch handling in `UserController` with user-facing flash alerts.

## [2.1.25] - 2026-10-26

### Fixed & Enhanced
- **Modal Stacking Context & Backdrop Overlap Fix**:
  - Fixed an issue where `.modal` elements placed inside `.sz-content` were trapped in its stacking context (`position: relative; z-index: 1`), causing the backdrop (`.modal-backdrop` at `z-index: 1050` on `body`) to render in front of the modal and block all mouse interactions.
  - Added automatic DOM relocation (`document.body.appendChild(modal)`) on initialization and before open in both `dashboard.js` (global `show.bs.modal` listener) and `src/views/user/profile.php` (`setupKeyModal`).
  - Added `style="z-index: 1060;"` and CSS `.modal { z-index: 1060 !important; }` / `.modal-backdrop { z-index: 1050 !important; }` in `style.css`.
  - Added `<?= $this->blocks['modals'] ?? '' ?>` in `src/views/layouts/main.php` outside the `.app` container for top-level modal rendering.

## [2.1.24] - 2026-10-26

### Fixed & Enhanced
- **Bootstrap 5 JavaScript Bundle Dependency in AppAsset**:
  - Added `'yii\bootstrap5\BootstrapPluginAsset'` to `$depends` in `AppAsset`. Previously, only `BootstrapAsset` (CSS) was registered, which prevented Bootstrap 5 interactive components (modals, dropdowns, collapses) from responding to `data-bs-*` attributes out-of-the-box.
- **Fail-Safe Vanilla Modal Controller in User Profile**:
  - Added a dedicated, zero-dependency modal controller in `src/views/user/profile.php` for `#twoFactorKeyModal`.
  - Works seamlessly with native Bootstrap 5 `bootstrap.Modal` when present, and automatically provides vanilla DOM fallback handling (backdrop, Escape key, outside click, and dismiss buttons) even if Bootstrap JavaScript is not loaded or delayed.

## [2.1.23] - 2026-10-26

### Fixed & Enhanced
- **Built-in Zero-Dependency QR Code Generator for 2FA**:
  - Bundled standalone, zero-dependency client-side QR generator (`src/web/js/qrcode.min.js`) into `AppAsset`.
  - Added progressive enhancement in `src/views/user/profile.php` and `TwoFactorService::getOtpAuthUri()`: if server-side `bacon/bacon-qr-code` is not installed on hosting or fallback SVG is returned, client-side JavaScript instantly renders a clean, scannable QR code directly from the `otpauth://` URI.
  - Fixes "QR Code Library required" placeholder on remote hosting and offline environments with zero external composer dependencies.
- **Fixed Layout Overflow & 2FA Key Management Modal**:
  - Replaced the bottom-collapsing `#twoFactorKeyDetails` container with a centered Bootstrap 5 modal (`#twoFactorKeyModal`).
  - Added `word-break: break-all` and centered dialog positioning (`modal-dialog-centered`), eliminating horizontal and vertical off-screen overflow caused by long server directory paths.
- **Enhanced Toast & Alert Viewport Containment**:
  - Added viewport constraints (`max-width: calc(100vw - 32px)`, `word-break: break-word`, `overflow-wrap: anywhere`) to `.sz-toast-stack`, `.sz-toast`, and `.toast` in `style.css` to prevent notifications from sliding outside the screen.

## [2.1.22] - 2026-10-26

### Fixed & Enhanced
- **Strict Frontend Isolation in Global Application Bootstrap**:
  - `Bootstrap::bootstrap($app)` now detects whether `larikmc\admin\Module` is registered in the current application (`$app->getModules()`) before injecting URL rules or modifying `errorHandler->errorAction`.
  - In applications where the admin module is not installed (e.g. `frontend`), `Bootstrap` gracefully skips rule injection and leaves the frontend error handler intact, eliminating `Unable to resolve the request "admin/site/index"` and `admin/site/error` on root frontend visits.
  - Removed generic empty string route `'' => $moduleId . '/site/index'` from `Module::getDefaultUrlRules()`, ensuring application-level `defaultRoute` is cleanly respected.

## [2.1.21] - 2026-10-26

### Added & Enhanced
- **Zero-Friction Automatic 2FA Key Generation & Resolution**:
  - `TwoFactorService::resolveSecretKey()` now provides a seamless 6-tier fallback resolution:
    1. Explicit `$this->secretKey` configuration
    2. `ADMIN_2FA_SECRET_KEY` environment variable
    3. `Yii::$app->params['admin.2fa.secretKey']`
    4. Persistent key file (`@backend/runtime/admin_2fa.key` or `@runtime/admin_2fa.key`)
    5. Automatic generation of a 64-character random key (`bin2hex(random_bytes(32))`) with automatic persistent storage
    6. Cryptographic derivation fallback from `cookieValidationKey` via HMAC-SHA256
  - 2FA is now immediately ready to use out-of-the-box without requiring any manual server or `.env` configuration.
- **In-Admin 2FA Master Key Management**:
  - Added `TwoFactorService::getKeyInfo()` providing key source label, masked key fingerprint, and file path.
  - Added `UserController::actionRegenerateTwoFactorKey()` allowing administrators to generate or rotate the master encryption key directly from the admin panel.
  - Added a status bar and collapsible key management drawer in the Profile view (`/admin/user/profile`) with key details and one-click key generation / rotation.

## [2.1.20] - 2026-10-26

### Added & Enhanced
- **Topbar Clear Cache Quick Action**: Added dedicated `cached` action button in the topbar header tools (`#clearCacheBtn`) and inside the user account profile dropdown (`#profilePop`).
- **Comprehensive Backend & Frontend Cache Flush**:
  - Automatically flushes all registered cache components across the application (`cache`, `frontendCache`, `backendCache`, `cacheFrontend`, `schemaCache`, and any component containing `cache` in its ID).
  - Automatically refreshes the DB schema cache (`Yii::$app->db->schema->refresh()`).
  - Recursively purges physical cache directories across all application environments (`@frontend/runtime/cache`, `@backend/runtime/cache`, `@common/runtime/cache`, `@console/runtime/cache`, `@runtime/cache`).
- **Instant Visual Feedback**: Added `.toolbtn--spin` CSS animation and JavaScript click handler triggering a smooth rotation on click while the cache is purged, followed by toast notification feedback.
- **Flexible HTTP Verbs**: Updated `SiteController` `VerbFilter` to support both `GET` and `POST` for `clear-cache` and `flush-cache`.

## [2.1.19] - 2026-10-26

### Added & Architecture
- **Zero-Configuration Default URL Rules**: Added `Module::getDefaultUrlRules($moduleId)` providing a complete, turnkey routing suite for all admin module subsystems (Dashboard, Auth, 2FA, RBAC, Profile, UI Kit, Cache, Errors) out-of-the-box.
- **Automatic Rule Injection in Bootstrap**: `Bootstrap::bootstrap($app)` automatically registers `getDefaultUrlRules('admin')` into `urlManager` during application bootstrapping, removing the need for manual admin rule configuration in `backend/config/main.php`.
- **Guest Whitelist Completeness**: Included all auth variations (`auth/auth/*`, `admin/auth/auth/*`, `error`, `admin/error`) in `Module::DEFAULT_GUEST_ALLOWED_PATHS` ensuring guest visitors and error handlers never experience infinite redirect loops.
- **Comprehensive Documentation for AI & Developers**:
  - Restructured `README.md` to highlight the Plug-and-Play / Zero-Boilerplate architecture, minimal `backend/config/main.php` configuration, and full route catalog table.
  - Updated `AI-INTEGRATION-CHECKLIST.md` with explicit instructions preventing AI agents and neural networks from duplicating admin rules in application configs.

## [2.1.18] - 2026-10-26

### Fixed & Enhanced
- **Seamless Admin Theme Consistency for Error Page**: Aligned `.sz-error-card` with native `.sz-panel` design language and CSS tokens (`--sz-surface`, `--sz-line`, `--sz-text`, `--sz-muted`) across both themes:
  - **Light Theme**: Soft translucent gradient (`linear-gradient(180deg, rgba(255, 255, 255, 0.94), rgba(251, 252, 255, 0.88))`) with fine glass border and soft shadow, matching standard panels.
  - **Dark Theme**: Deep surface `#1d2c46` with `#354660` border, matching dark admin panels.
  - Secondary button («Назад») now uses admin panel surface styling and hover transitions in both Light and Dark themes.

## [2.1.17] - 2026-10-26

### Fixed
- **Clean URL Generation for UI Kit and Profile**: Registered URL rules for `ui-kit` / `site/ui-kit` and `profile` / `user/profile` in `Bootstrap::bootstrap($app)` and `Module::init()`. This prevents doubled `/admin/admin/...` paths (e.g. `/admin/admin/site/ui-kit`) when the application has `request.baseUrl = '/admin'`.

## [2.1.16] - 2026-10-26

### Fixed
- **Light/Dark Theme Class Specificity**: Corrected dark mode selectors in `style.css` to `html.dark` / `body.dark`, ensuring crisp white card background with soft shadow in Light theme, and deep blurred glassmorphic surface in Dark theme.

## [2.1.15] - 2026-10-26

### Added & Modernized
- **Modern 404 / 403 / 500 Error Page**: Complete redesign of `views/site/error.php` into a modern card component featuring gradient status codes, contextual rounded icon badges (`search_off`, `lock`, `error_outline`), clean typography, action buttons («На главную» and «Назад»), and comprehensive theme support for both Light and Dark modes.
- **Automatic ErrorHandler Routing**: `Bootstrap::bootstrap($app)` and `Module::init()` automatically switch default `site/error` to `admin/site/error` so all 404/403/500 errors in admin panels automatically render the polished admin error view.
- **Theme-Adaptive Alert & Fallback Styles**: Added Dark/Light theme rules for `.alert-danger` and fallback `.site-error` containers in `style.css`, eliminating jarring unstyled Bootstrap pink alert banners.
- **Layout Menu State on Errors**: When an error page is displayed, sidebar items are no longer mistakenly highlighted as active, and the topbar title shows friendly Russian error titles instead of raw technical strings.

## [2.1.14] - 2026-10-26

### Fixed
- **Auth Routes Auto-Registration**: Registered wildcard auth URL rules (`admin/auth/<action>` and `auth/<action>`) in `Bootstrap::bootstrap($app)` for web applications and in `Module::init()`. This resolves `404 Not Found` for routes like `/admin/auth/request-password-reset`, `/admin/auth/reset-password`, `/admin/auth/two-factor`, etc., even when the admin module is not loaded during early request routing.

## [2.1.13] - 2026-10-26

### Fixed
- **Filter Presets Margin Zero**: Set `margin: 0 !important` on `.stat-filter-presets` and `.karandash-filter-presets` so they do not inherit the default `14px` tab bottom margin when embedded inside flex toolbars.

## [2.1.12] - 2026-10-26

### Fixed
- **Layout Spacing Robustness**: Sanitized `$content` in `main.php` layout by trimming leading UTF-8 BOM (`\xEF\xBB\xBF`) and whitespace. This prevents the flex container `.sz-content__inner` from generating an anonymous flex item that caused excessive gap (52px instead of 14px) above content/tabs.

## [2.1.11] - 2026-10-26

### Added
- **Unified Preset Filter Button Styling**: Extended the segmented capsule tray design (`.nav-pills-solid`, `.an-segments`) to statistics page filter buttons — `.stat-filter-presets` / `.stat-preset-btn` and `.karandash-filter-presets` / `.karandash-preset-btn`. Active state uses crisp white pill with soft blue shadow in light theme and `linear-gradient(125deg, #3189f7, #3466d8)` with ambient glow (`0 4px 14px rgba(49, 137, 247, 0.35)`) in dark theme, matching the Primary Action button.

## [2.1.10] - 2026-10-26

### Fixed
- **Active Tab Styling Alignment**: Aligned dark theme active tab background gradient and ambient glow with the Primary Action button (`linear-gradient(125deg, #3189f7, #3466d8)` and `box-shadow: 0 4px 14px rgba(49, 137, 247, 0.35)`).

## [2.1.9] - 2026-10-26

### Changed
- **Unified Segmented Tabs Design**: Migrated all admin tab variants (`.nav-pills-solid`, `.sz-tabs__nav`, `AdminTabs`, and `.nav-tabs`) to a unified, modern capsule-segmented style modeled after the analytics dashboard (`an-segments`).
- **UI Kit Modernization**: Removed obsolete underline and flat pill variants in the UI Kit (`admin/site/ui-kit`), presenting a single unified interactive segmented tab component.
- **Tabs Spacing Optimization**: Fixed excessive spacing below tabs in `AdminPage` by eliminating phantom empty `sz-page__actions` container rendering and setting tab bottom margin to `14px`.
- **Theme Consistency**: Crisp white active pill with soft blue elevation in light theme (`#1e40af` text), and matching Primary Action blue gradient (`linear-gradient(125deg, #3189f7, #3466d8)`) with ambient glow in dark theme.

## [2.1.3] - 2026-10-20

### Fixed
- **GridView Pagination Spacing**: Added top margin (`margin-top: 16px`) to `.pagination` below tables to provide clean visual separation between the grid view container and page switch buttons.

## [2.1.2] - 2026-10-20

### Fixed
- **Unified GridView Pagination Styling**: Default Yii2 `LinkPager` (`.pagination li a` / `.pagination li span`) now seamlessly adopts the compact, rounded UI kit design (`32px` square buttons, 8px border radius, smooth hover, primary active state and dimmed disabled states) in both light and dark themes without requiring custom LinkPager widget configurations.

## [2.1.1] - 2026-10-20

### Fixed
- **Automatic Yii2 Default GridView ActionColumn Styling**: Default Yii2-generated `ActionColumn` buttons and FontAwesome SVG icons (`eye-open`, `pencil`, `trash`) are now automatically styled into the unified segmented button-group (`.btn-group-action` style), without requiring manual view modifications or custom `sz-row-action` classes.
- **Delete Button Danger Hover**: Auto-detected `data-method="post"` and `data-confirm` action buttons in default GridView tables now seamlessly transition to danger/red hover state in both light and dark themes.

## [2.1.0] - 2026-10-19

### Added
- **Two-Factor Authentication (2FA TOTP)**: Standard RFC 6238 TOTP two-factor authentication compatible with Google Authenticator, Aegis, 1Password, and Bitwarden.
- **Secure Secret Encryption**: Two-factor secrets and unconsumed backup codes are encrypted at rest using AES-256-CBC via OpenSSL with cryptographically strong keys configured through `admin.2fa.secretKey`. Weak default fallbacks are strictly prohibited.
- **Single-Use Backup Codes**: 8-character single-use emergency backup recovery codes with hashed and salted verification; automatically invalidated upon use.
- **Atomic Replay Protection**: Two-layer replay defense combining time window caching (`admin:2fa_used:`) and persistent atomic DB recording (`{{%admin_2fa_used_code}}`) to completely prevent replay attacks within time drift windows.
- **QR Code Support**: Built-in SVG QR code generation (supporting `bacon/bacon-qr-code`) for scanning in mobile authenticator apps.
- **Automatic First Administrator Creation**: On a completely clean installation where the user table is empty, the first login attempt seamlessly registers the primary administrator without requiring external tokens, invite links, or manual SQL seeds (`allowFirstAdminRegistration` enabled by default).
- **Hardened Concurrency Protection**: High-concurrency race condition protection during first administrator creation using dual-layer synchronization: application-level Mutex (`Yii::$app->mutex`) and database row-level locking (`SELECT COUNT(*) ... FOR UPDATE`) inside an isolated transaction.
- **Emergency CLI Reset**: Built-in console controller command `php yii admin/security/reset-2fa <username|email|id>` to quickly disable 2FA and revoke current sessions during lockout emergencies.
- **Session Revocation Guard**: Centralized session revocation on 2FA modification, password change, and emergency reset (`auth_key` rotation coupled with session `__auth_key_hash` verification across both session and rememberMe cookie logins).

### Security
- **Eliminated rootUserId Auto-Admin Privilege**: Replaced implicit hardcoded ID=1 auto-superadmin assumption with safe, explicit role assignment and validation against empty tables.
- **Prevented RememberMe 2FA Bypass**: Fixed auto-login cookie bypass vulnerability; intermediate 2FA authentication state is strictly enforced before granting full application identity.

### Fixed
- **Dropdown & Navigation Alignment**: Polished sidebar layout and dark theme UI components.
- **Global Bootstrap Collapse Isolation**: Scoped `.collapse` selectors in `dashboard.css` to `.sz-sidebar__header .collapse` and `.brand .collapse`, eliminating severe layout deformation on generic Bootstrap collapse containers (such as 2FA backup code and confirmation forms).
- **Toast Notifications Filtering**: Filtered session flashes in main layout to strictly valid alert levels (`success`, `warning`, `danger`, `error`, `info`), preventing raw array data flashes (e.g. backup codes) from spamming separate notifications across the screen.
- **Auth Screen Theme Switcher**: Replaced static oval button with an interactive Day/Night celestial toggle (sun/moon with animated sliding thumb, clouds and stars) matching `.auth-back-link` height and geometry.
- **Mobile Captcha Sizing**: Constrained captcha image and verification input row on screens $\le 576\text{px}$ using `object-fit: contain`, preventing bloated stretched image banner distortion.
- **Two-Factor Authentication Form Input**: Removed distracting multi-format placeholder from 2FA confirmation input for clean user entry.

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
