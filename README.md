# ClearDeal — Lowest Price (EU Omnibus Directive) for PrestaShop

[![PrestaShop](https://img.shields.io/badge/PrestaShop-8.x%20%7C%209.x-blue)](https://www.prestashop.com/)
[![PHP](https://img.shields.io/badge/PHP-8.1%2B-777bb4)](https://www.php.net/)
[![License](https://img.shields.io/badge/License-GPL--3.0-green)](LICENSE)
[![Release](https://img.shields.io/github/v/release/GajewskiMarcin/ClearDeal?include_prereleases)](https://github.com/GajewskiMarcin/ClearDeal/releases)

**ClearDeal** tracks the price history of your products and displays the **lowest price from the last N days** next to the current price — the information required by the **EU Omnibus Directive** (2019/2161) whenever you announce a price reduction.

🇵🇱 [Polska wersja README](README.pl.md)

---

## What it does

When a product goes on sale, the law in the EU requires you to show the lowest price at which the product was offered during a period before the discount (at least 30 days). ClearDeal handles the whole chain automatically:

1. **Records** every price change into its own database table (product, combination, shop, currency, country, customer group).
2. **Calculates** the lowest price within your configured window.
3. **Displays** it on product pages, category listings and quick view — with an optional discount percentage, tooltip and interactive price chart.

No manual work, no spreadsheets.

---

## Features

### Price tracking
- Automatic logging on product creation, product update and any specific-price (discount) add / update / delete.
- Two logging modes: **immediate** (hook-driven) or **CRON only**.
- Multi-dimensional history: per **shop**, **currency**, **country**, **customer group** and **combination** — so multistore and per-country pricing stay correct.
- Configurable **retention** (30–3650 days) with automatic daily cleanup.
- **Import / export CSV** of the price log, plus a "log all prices now" bulk action.

### Display
- Lowest price from a configurable window (**1–365 days**, default 30).
- Display mode: **always** or **only when the product is discounted**.
- **Gross or net** prices, with adjustable decimal precision (0–4).
- Optional **discount percentage** relative to the lowest price.
- Separate, **fully translatable labels** for product page, listing and quick view.
- Info **icon + tooltip** — a Bootstrap icon of your choice or your own uploaded image, placed before or after the text.

### Price history chart
- Interactive **Chart.js** modal showing how the price evolved.
- Trigger it by clicking the icon, the price, or anywhere on the block.
- Configurable line / fill / header colors.

### Appearance
- Three **style presets**: `Default` (background + border), `Minimal` (no background), `Bold` (gradient).
- Per-preset color customization and a **custom CSS** field.
- Automatic contrast calculation for tooltip text.

### Integration
- Works out of the box on standard hooks: `displayProductPriceBlock`, `displayProductAdditionalInfo`, `displayProductListReviews`.
- **Custom hooks** for full control over placement:
  `displayClearDealProduct`, `displayClearDealListing`, `displayClearDealQuickView`.
- **Creative Elements** page-builder widget (under the *Secret Sauce* category).
- **Category exclusions** — skip products you don't want tracked or displayed.
- **24 language translations** included.

---

## Requirements

| | |
|---|---|
| PrestaShop | 8.0 – 9.x |
| PHP | 8.1 or newer |
| MySQL | 5.6+ / MariaDB 10.1+ |

---

## Installation

### From a release (recommended)

1. Download `cleardeal.zip` from the [Releases](https://github.com/GajewskiMarcin/ClearDeal/releases) page.
2. In your PrestaShop back office go to **Modules → Module Manager → Upload a module**.
3. Select the ZIP file and install.
4. Open the module configuration (or **Improve → Secret Sauce → ClearDeal**).

### From source

```bash
git clone https://github.com/GajewskiMarcin/ClearDeal.git
cp -r ClearDeal/cleardeal /path/to/your/prestashop/modules/
```

Then install the module from the Module Manager.

> **Important:** after installing, the price history is empty. Either wait for prices to change, run **Price Logs → Log All Prices Now** once to seed a baseline, or import an existing history from CSV.

---

## Configuration

The module configuration has four tabs:

| Tab | What's inside |
|---|---|
| **Settings** | Days window, display mode, gross/net, percentage, precision, chart, excluded categories, labels, icon & tooltip, logging mode, retention |
| **Appearance** | Style preset, colors, custom CSS |
| **Price Logs** | Statistics, filterable log browser, CSV import/export, cleanup, manual logging |
| **About & Support** | Version info and links |

### Placing the block manually

If your theme needs the block somewhere specific, use one of the custom hooks in your template:

```smarty
{hook h='displayClearDealProduct' product=$product}
{hook h='displayClearDealListing' product=$product}
{hook h='displayClearDealQuickView' product=$product}
```

---

## CRON setup

If you set **Logging mode** to *CRON job only* (recommended for large catalogs), schedule the endpoint:

```
https://your-shop.com/module/cleardeal/cron?token=YOUR_TOKEN&action=all
```

The exact URL with the generated token is shown in the module's **Settings** tab.

| `action` | Effect |
|---|---|
| `log` | Log current prices for all active products |
| `clean` | Remove logs older than the retention period |
| `all` (default) | Both of the above |

Example crontab entry (daily at 3:00):

```cron
0 3 * * * curl -s "https://your-shop.com/module/cleardeal/cron?token=YOUR_TOKEN&action=all" > /dev/null
```

---

## Data storage

The module creates a single table, `PREFIX_cleardeal_price_history`, storing one row per recorded price point (tax incl. + tax excl., shop, currency, country, group, timestamp). It is indexed for fast lowest-price lookups and is dropped on uninstall, together with all module settings.

No data is sent anywhere outside your shop. The only external resources loaded on the front office are Bootstrap Icons and — when the chart is enabled — Chart.js, both from jsDelivr CDN.

---

## Legal note

ClearDeal is a technical tool that records and displays price history. It does not constitute legal advice, and correct Omnibus compliance also depends on how you configure it (window length, which products are excluded, when tracking started). Verify your setup against the regulations applicable in your country.

---

## Contributing

Issues and pull requests are welcome. If you're reporting a bug, please include your PrestaShop version, PHP version and theme.

---

## License

Released under the [GNU General Public License v3.0](LICENSE).

## Author

**Marcin Gajewski** — [marcingajewski.pl](https://marcingajewski.pl)
