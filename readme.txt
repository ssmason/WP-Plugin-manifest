=== Satori Manifest ===
Contributors: ssmason
Tags: price list, services, menu, block, gutenberg
Requires at least: 6.4
Tested up to: 6.8
Requires PHP: 8.1
Stable tag: 1.0.1
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Create and manage structured price lists organised into sections. Output anywhere via a Gutenberg block.

== Description ==

Satori Manifest lets you build and maintain structured price lists for any type of business — hairdressers, restaurants, building merchants, and more.

**Key features:**

* Create unlimited price list sections (e.g. Colouring, Nails, Hairdressing)
* Add unlimited items to each section with label, description, price, and a "from" prefix
* Move up / move down reordering of sections and items
* Gutenberg block with full inspector controls
* Four built-in display patterns: Classic List, Split Grid, Card Style, Minimal
* Customise colour scheme, typography, and background options per block instance
* Customise patterns and edit them in Appearance > Patterns
* Multisite compatible — works network-activated
* No external PHP dependencies — fully self-contained
* Clean uninstall — removes all data on deletion

**Block controls:**

* Choose which manifests to display
* Pick a layout preset (Classic List / Split Grid / Card Style / Minimal)
* Select a colour scheme or define a fully custom palette
* Control section title and list item typography (family, size, weight)
* Toggle background, section title background colour, price and description visibility
* Override the price prefix globally

== Installation ==

1. Upload the `satori-manifest` folder to `/wp-content/plugins/`
2. Activate the plugin through the Plugins menu in WordPress
3. Go to **Manifest → All Manifests** to create your first price list
4. Add the **Manifest** block to any page or post

== Frequently Asked Questions ==

= Does this work with any theme? =

Yes. The block outputs semantic HTML with BEM-style class names. Styles are scoped and will not conflict with your theme.

= Can I use this on a multisite network? =

Yes. The plugin is fully multisite-compatible and can be network-activated.

= Does this require ACF or any other plugin? =

No. Satori Manifest is completely self-contained with no PHP dependencies.

== Screenshots ==

1. The Manifest block displayed using the Classic List layout.
2. The block inspector controls showing layout, colour scheme, and typography options.
3. The manifest editor — sections and items with move-up / move-down reordering.

== Changelog ==

= 1.0.1 =
* Added colour scheme customisation with custom palette support.
* Added typography controls for section titles and list items.
* Added show/hide background and section title background colour options.
* Fixed section title font overrides against theme styles.

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 1.0.1 =
Adds colour scheme, typography, and background customisation options to the block inspector.

= 1.0.0 =
Initial release.
