# Decisions Required

Status as of 2026-11-24. The site now runs locally; the items below are the remaining launch decisions, not build blockers for the local environment.

## Resolved during the build

- **Runtime:** WordPress 7.1.5, PHP 8.3.37, SQLite via the `sqlite-database-integration` drop-in. Docker, host PHP, and MySQL were unavailable, so a portable stack runs from `.local/`.
- **SEO approach:** no SEO plugin was installed. Metadata, canonical URLs, robots directives, JSON-LD, and the XML sitemap are emitted by the theme and `coc-core`, avoiding a plugin dependency.
- **Model data:** five models have source URLs and verification dates. Facts without both display as “Not confirmed.” Source URLs were rechecked on 2026-11-24.
- **Publishing scope:** 13 standalone pages, 5 reviews, 3 comparisons, 4 guides, and taxonomy archives (36 sitemap URLs) are published. This follows the quality-first 12–18 page recommendation rather than the MVP’s exhaustive 30-page list.

## Open before launch

1. **Approve final scope.** Confirm whether the 36-URL set is the launch scope or whether the remaining MVP pages must be authored. Do not add thin pages solely to reach a page count.
2. **OCS destinations.** Supply approved Oxygen Care Supply product/call URLs, phone behavior, UTM/affiliate parameters, and any cross-domain requirements. No outbound commercial CTA was invented without these.
3. **Analytics and consent.** Supply GA4/GTM IDs and the approved consent approach. No analytics tag is installed, so the site currently makes no third-party requests.
4. **Legal and editorial sign-off.** A named owner must approve the privacy policy, terms, disclosure, medical limitation, methodology, and corrections workflow.
5. **Production platform.** Choose the production host, domain, TLS, MySQL/MariaDB service, transactional email transport, caching, and backup policy. The PHP built-in server and SQLite are for local verification only.
6. **Product imagery rights.** Manufacturer photography was not used. If real product images are approved, their rights and alt text must be recorded. Current visuals are labeled technical illustrations.
