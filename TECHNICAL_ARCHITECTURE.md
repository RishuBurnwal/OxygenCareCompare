# Technical Architecture

## Runtime

- WordPress on ordinary PHP-compatible hosting with MySQL/MariaDB.
- No Node.js, TypeScript, React, bundler, or frontend build is required.
- HTML is server-rendered; small vanilla JavaScript enhancements are progressively layered on top.

## Package boundaries

### `wp-content/themes/compareoxygen`

Owns layout, typography, responsive styles, navigation, and templates. It reads WordPress content and plugin-provided helpers but does not own product facts or comparison business rules.

### `wp-content/plugins/coc-core`

Owns `poc_model` and `comparison` post types, brand/feature/use-case taxonomies, structured model specifications, per-field source metadata, approved comparison pair resolution, CTA tracking hooks, and admin validation. Deactivation must leave stored content intact.

## Data model

- `poc_model`: WordPress post plus registered metadata for identity, physical, oxygen, battery, travel, operation, warranty, editorial, source, and commercial attributes.
- `poc_brand`: taxonomy for product brands.
- `comparison`: editorial post referencing two model IDs, with a stable canonical slug and human-written analysis. Only approved/published records resolve as canonical comparisons.
- Per-spec source data must include source name/URL, source type, verification date, and status/notes. Unknown values stay empty and render as “Not confirmed”.
- Editorial content remains editable in Gutenberg. No model specifications are copied into comparison records.

## URL and indexing rules

- Approved comparisons use permanent editorial URLs; arbitrary selector states use noindex utility state and do not enter the sitemap.
- Reverse-order model pairs resolve to the same approved canonical record.
- Search/filter states are noindex. Staging is noindex and should be access-controlled.
- Use one SEO plugin for titles, canonical, sitemap, and schema. `coc-core` must not duplicate plugin output.

## External configuration

OCS destination URLs, contact destinations, GTM/GA4 IDs, cross-domain domains, and verification tokens are owner configuration, never hard-coded guesses. Do not place secrets in browser JavaScript.

## Deployment boundary

Deploy only the theme and plugin through staging. Do not overwrite WordPress core, `wp-config.php`, uploads, database contents, or unrelated plugins. Back up before release and document rollback.
