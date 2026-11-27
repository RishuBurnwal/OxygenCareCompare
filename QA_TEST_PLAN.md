# QA Test Plan

## Current environment status

The repository currently has no WordPress code or runtime installation. No PHP/WordPress/database/browser test has been run. Do not report unexecuted checks as passing.

## Required checks when runtime is provisioned

### PHP and WordPress

- Lint every PHP file with the target-supported PHP version.
- Activate/deactivate theme and plugin; verify no fatal errors and retained content.
- Verify CPT/taxonomy registration, capability checks, metadata sanitization, and editor saves.
- Test permalink generation, canonical resolver, and reverse-order pair handling.

### Functional and responsive

- Test navigation and keyboard use at mobile/tablet/desktop widths.
- Test model selection, approved comparison navigation, empty values, replace/reset, and no JavaScript fallback.
- Verify editor-created content renders without duplicate specifications.
- Verify outbound CTA tracking carries page type, slug, product, position, and destination.

### Accessibility, SEO, security, performance

- Keyboard-only and screen-reader review; visible focus, contrast, labels, table headers, reduced motion.
- Canonical/meta robots/sitemap/schema match visible published content; search/filter and dynamic selector states are noindex.
- Check escaping, sanitization, nonces, capabilities, safe errors, and absence of committed credentials.
- Measure responsive image sizes, layout shift, scripts, and Core Web Vitals on staging.

### Manual launch checks

- Test staging protection/noindex and production-only analytics configuration.
- Test actual COC → OCS tagged journey and confirm linker/click IDs survive using owner-supplied domains/configuration.
- Review disclosures, medical wording, privacy, terms, and product claims with the appropriate owner/reviewer.
