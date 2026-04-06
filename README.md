# Satori Manifest

A WordPress plugin for creating and managing structured price lists with a Gutenberg block. No ACF. No external PHP dependencies.

**Plugin URI:** https://github.com/ssmason/WP-Plugin-manifest  
**Author:** Stephen Mason (Satori Digital)  
**Requires:** WordPress 6.4+, PHP 8.1+

---

## Getting Started

### Install dependencies

```bash
composer install
npm install
```

### Build assets

```bash
# Development (watch mode)
npm run dev

# Production build
npm run build
```

### Linting

```bash
npm run lint:js      # ESLint
npm run lint:scss    # Stylelint
npm run lint:php     # PHPCS
```

---

## Directory Structure

```
satori-manifest/
├── satori-manifest.php       Main plugin file
├── uninstall.php             Cleanup on delete
├── includes/                 PHP classes (one per file)
│   ├── class-plugin.php      Bootstrap & hook registration
│   ├── class-security.php    Nonce helpers & capability checks
│   ├── class-multisite.php   Network-aware option wrappers
│   ├── class-post-types.php  CPT registration & data helpers
│   ├── class-assets.php      Script/style enqueueing
│   ├── class-admin-menu.php  Admin menu registration
│   ├── class-options.php     Options CRUD
│   ├── class-patterns.php    Block pattern registration
│   └── class-block.php       Block registration & SSR
├── admin/
│   ├── class-admin-ajax.php  AJAX handlers
│   └── views/                Admin page templates
├── block/
│   ├── block.json            Block metadata
│   └── src/                  Block JS source
└── src/scss/                 SCSS source files
    ├── admin/
    ├── editor/
    └── frontend/
```

---

## Data Model

Price list sections are stored as a custom post type (`satori_manifest_section`). Each section's items are stored as a JSON array in post meta (`_satori_manifest_items`).

**Item shape:**

```json
{
  "label": "Highlights",
  "description": "Optional detail line",
  "price": "45.00",
  "price_prefix": "from",
  "sort_order": 1
}
```

---

## Block

The `satori-manifest/price-list` block is server-side rendered. Add it to any page via the block editor.

**Inspector controls:**
- Section selector (multi-select checkboxes)
- Layout preset: Classic List / Split Grid / Card Style / Minimal
- Show/hide prices toggle
- Show/hide descriptions toggle
- Price prefix override

---

## Multisite

The plugin supports network activation. When network-active, all option reads/writes route through `get_site_option()` / `update_site_option()`. The uninstall routine iterates all sites on the network.

---

## Security

- All AJAX handlers verify a per-action nonce via `check_ajax_referer()`
- All admin callbacks check `current_user_can()` before any data operation
- All output is escaped; all input is sanitized
- `ABSPATH` guard in every PHP file
- No direct access to raw superglobals without sanitization

---

## License

GPL-2.0-or-later — see [LICENSE](https://www.gnu.org/licenses/gpl-2.0.html)
