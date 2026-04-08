# Satori Manifest

A WordPress plugin for creating and managing structured price lists organised into sections and items. Output via a custom Gutenberg block. No ACF. No external PHP dependencies.

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
├── satori-manifest.php              Main plugin file — constants, requires, boot
├── uninstall.php                    Cleanup on plugin delete
├── includes/
│   ├── class-plugin.php             Bootstrap & hook registration (singleton)
│   ├── class-security.php           Nonce helpers & capability checks
│   ├── class-sanitizer.php          Input sanitization for sections & items
│   ├── class-multisite.php          Network-aware option wrappers
│   ├── class-post-types.php         CPT & post meta registration
│   ├── class-manifest-repository.php  Read-only data access for manifest CPT
│   ├── class-assets.php             Script/style enqueueing
│   ├── class-admin-menu.php         Admin menu registration
│   ├── class-meta-box.php           Meta box wiring (register + save)
│   └── class-block.php              Block registration
├── admin/
│   └── views/
│       └── meta-box-sections.php    Sections & Items meta box template
├── block/
│   ├── block.json                   Block metadata & attribute definitions
│   ├── render.php                   Server-side block render template
│   └── src/
│       ├── index.js                 Block registration
│       ├── edit.js                  Editor component
│       ├── inspector.js             Sidebar controls
│       ├── utils.js                 Shared JS utilities (parseSections, formatPrice)
│       └── admin.js                 Meta box JS (sections/items UI)
└── src/scss/
    ├── admin/
    │   ├── admin.scss               Admin stylesheet entry point
    │   ├── _variables.scss          Design tokens
    │   └── _meta-box.scss           Meta box component styles
    ├── editor/
    │   └── editor.scss              Block editor styles
    └── frontend/
        └── frontend.scss            Public frontend styles
```

---

## Data Model

```
Manifest (CPT: sm_manifest)
  └── Sections (JSON, stored in post meta)
        └── Items (nested JSON within each section)
```

**Meta key:** `_satori_manifest_sections`

**Section shape:**
```json
{
  "title": "Colouring",
  "sort_order": 0,
  "items": [
    {
      "label": "Highlights",
      "description": "Optional detail",
      "price": "45.00",
      "price_prefix": "from",
      "sort_order": 0
    }
  ]
}
```

All section and item data for a manifest is stored as a single JSON string in one meta key. There is no second CPT for sections.

---

## Block

The `satori-manifest/price-list` block is server-side rendered via `block/render.php`. Add it to any page via the block inserter (search "Manifest").

**Inspector controls:**
- Manifest selector (checkboxes — select one or more)
- Layout preset: Classic List / Split Grid / Card Style / Minimal
- Show/hide prices toggle
- Show/hide item descriptions toggle
- Price prefix override (overrides per-item prefix globally)

The editor renders a live preview of real manifest data fetched via the REST API. Sidebar changes (toggles, prefix) are reflected immediately.

---

## Permissions

### WordPress capabilities used

| Capability | Where | Why |
|---|---|---|
| `edit_posts` | Admin menu registration | Controls visibility of the Manifest menu item and CPT list table |
| `edit_posts` | REST API `auth_callback` | Required to read `_satori_manifest_sections` and `_satori_manifest_order` meta via the REST API (used by the block editor preview) |
| `edit_post` (with post ID) | Meta box save | Required to save sections on a specific manifest post — checked per-post, not globally |

### WordPress capabilities not used

| Capability | Notes |
|---|---|
| `manage_options` | Not required — manifests are content, not settings |
| `install_plugins` | Not used |
| `edit_theme_options` | Not used |
| `unfiltered_html` | Not used — all output is escaped, all input is sanitized |

### REST API exposure

The `sm_manifest` CPT is registered with `show_in_rest: true`, making manifests queryable via `/wp-json/wp/v2/sm_manifest`. This is required for the block editor to fetch manifest data for the live preview.

The following post meta fields are also exposed via REST:

| Meta key | Exposed to | Auth requirement |
|---|---|---|
| `_satori_manifest_sections` | Authenticated requests | `edit_posts` capability |
| `_satori_manifest_order` | Authenticated requests | `edit_posts` capability |

Unauthenticated requests cannot read these meta fields. The underscore prefix marks them as protected; the `auth_callback` enforces the capability check.

### Nonces

All form saves use a nonce verified through the `Security` class. The nonce action is prefixed with `satori_manifest_` to avoid collisions with other plugins.

| Context | Nonce action | Verification method |
|---|---|---|
| Meta box save | `satori_manifest_save_sections` | `wp_verify_nonce()` via `Security::verify_form_nonce()` |

### What the plugin does not do

- Makes no external HTTP requests
- Accesses no filesystem paths outside its own plugin directory
- Creates no database tables (uses standard post meta)
- Stores no user data beyond WordPress post content
- Has no network admin UI or network-level capability requirements

---

## Multisite

The plugin supports network activation. When network-active, option reads and writes route through `get_site_option()` / `update_site_option()`. The uninstall routine iterates all sites on the network via `get_sites()` and cleans each independently.

---

## Security

- All form saves verify a nonce via `Security::verify_form_nonce()`
- All admin callbacks check `current_user_can()` before any data operation
- Raw JSON from `$_POST` receives only `wp_unslash()` before decode; each field is sanitized individually after decode via `Sanitizer`
- All output is escaped at the point of echo (`esc_html()`, `esc_attr()`)
- `ABSPATH` guard at the top of every PHP file

---

## License

GPL-2.0-or-later — see [LICENSE](https://www.gnu.org/licenses/gpl-2.0.html)
