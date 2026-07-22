# Changelog

All notable changes to ClearDeal are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] — 2026-07-22

First public release.

### Added
- Automatic price history logging on product add/update and specific price add/update/delete.
- Two logging modes: immediate (hook-driven) and CRON-only, with a token-protected CRON endpoint.
- Multi-dimensional history per shop, currency, country, customer group and combination.
- Lowest-price display over a configurable window (1–365 days) on product pages, listings and quick view.
- Display modes: always, or only when the product is discounted; gross/net prices; configurable precision.
- Optional discount percentage relative to the lowest recorded price.
- Interactive Chart.js price history modal with configurable trigger and colors.
- Info icon with translatable tooltip — Bootstrap Icons or a custom uploaded image.
- Three style presets (Default, Minimal, Bold) with per-preset colors and a custom CSS field.
- Custom hooks `displayClearDealProduct`, `displayClearDealListing`, `displayClearDealQuickView`.
- Creative Elements page builder widget.
- Category exclusions.
- Price log browser with filters, CSV import/export, bulk logging and cleanup.
- Configurable log retention (30–3650 days) with automatic daily cleanup.
- 24 language translations.

[1.0.0]: https://github.com/GajewskiMarcin/ClearDeal/releases/tag/v1.0.0
