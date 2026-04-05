# Changelog

All notable changes to `larikmc/yii2-admin` will be documented in this file.

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
