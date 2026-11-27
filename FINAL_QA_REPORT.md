# Final QA Report — CompareOxygenConcentrators.com

**Date:** 2026-11-24  
**Environment:** local WordPress at `http://localhost:8080`  
**Verdict:** Functional and editorial QA passes locally. Production launch is conditional on the open items in §10.

---

## 1. Executive summary

The site is a working WordPress installation, not a static mockup. It has a custom theme, a custom content plugin, seeded editorial records, live comparison logic, an admin editing workflow, accessible responsive templates, and generated SEO metadata. Every page in the QA matrix rendered successfully, the accessibility scan returned zero violations, the internal link crawl found no broken links, and no horizontal overflow was detected from 320px to 1440px.

One factual inconsistency was found during final review and corrected: CAIRE FreeStyle Comfort had been labeled “Best Lightweight” while its published weight is 2.3 kg, heavier than the 4.7 lb / 4.74 lb Inogen and OxyGo records. The award now belongs to Inogen One G5, and the affected copy, comparison excerpts, and award rationale were rewritten.

| Check | Result |
|---|---|
| Browser scenarios | **28 / 28 passed, 0 failures** |
| axe-core scans | **22 scans, 0 violations** |
| Internal links | **29 checked, 0 broken** |
| Responsive overflow | **60 route/viewport combinations, 0 overflows** |
| Manufacturer source URLs | **5 / 5 returned HTTP 200** |
| Sitemap | **36 URLs across 7 child sitemaps, all generated** |
| PHP server error log | **No PHP errors** |
| Visible admin errors | **None** (one hidden WordPress heartbeat template matched `.error`) |

---

## 2. What was delivered

### Runtime

The host had no usable PHP, MySQL, WordPress, or Docker runtime. Docker Desktop never connected (`npipe:////./pipe/dockerDesktopLinuxEngine`), so the project runs on a portable fallback stack in `.local/`:

- PHP 8.3.37, portable build with GD enabled
- WordPress 7.1.5
- SQLite through the `sqlite-database-integration` 3.1.3 drop-in
- `wp-cli.phar` for content and configuration operations
- PHP built-in server on port 8080 with `.local/router.php`
- Junctions from `wordpress/wp-content/themes/compareoxygen` and `wordpress/wp-content/plugins/coc-core` to the project source

This is a local verification environment. It is not a production web server.

### Product code

- Theme: `wp-content/themes/compareoxygen/`
  - 19 PHP template files plus 5 template parts
  - One CSS design system (`style.css`)
  - One vanilla JavaScript file (`assets/js/app.js`, 21 KB)
  - Custom inline SVG icon set and technical product illustrations
- Plugin: `wp-content/plugins/coc-core/coc-core.php`
  - `poc_model` and `poc_comparison` post types
  - `poc_brand` and `poc_use_case` taxonomies
  - Per-fact source and verification-date storage
  - Admin meta boxes and validation
  - Comparison rendering, shortcodes, schema, and metadata hooks

No React, Vue, TypeScript, Node runtime, WooCommerce, Elementor, or page builder is used by the product. Node/Playwright exist only in the temporary QA harness.

### Content inventory

| Content | Count |
|---|---|
| Model reviews | 5 |
| Approved comparisons | 3 |
| Standalone pages | 13 |
| Guides | 4 |
| Brand archives | 3 |
| Use-case archives | 4 |
| Possible specification fields per model | 22 |
| Confirmed facts across models | 42 |
| Sitemap URLs | 36 |

Models: Inogen Rove 6, Inogen One G5, OxyGo NEXT, OxyGo NEXT NG, CAIRE FreeStyle Comfort.

Comparisons: Rove 6 vs One G5; OxyGo NEXT vs CAIRE FreeStyle Comfort; Rove 6 vs OxyGo NEXT.

Awards: Best Overall — Inogen Rove 6; Best Lightweight — Inogen One G5; Best for Travel Planning — OxyGo NEXT.

The evidence model is enforced in the UI: a fact is shown as confirmed only when it has a value, a source URL, and a verification date. Otherwise it renders “Not confirmed.” The site does not invent missing values.

---

## 3. Design and front-end behavior

### Design system

The implemented system is documented in `DESIGN_SYSTEM.md` and matches the CSS tokens:

- Deep navy and oxygen teal on cool off-white and mint surfaces
- Manrope/Inter first in the stack, with a guaranteed system-UI fallback and no third-party font request
- Fluid type scale; 1360px wide / 1248px standard / 760px editorial containers
- 8–32px radii, four restrained shadow levels, short easing curves
- Breakpoints at 1120px, 980px, 760px, and 520px
- Visible focus ring, reduced-motion support, and print styles

### Page templates

Implemented and browser-tested:

- Home with hero, trust strip, compare widget, top picks, use-case categories, awards, guides, and FAQ
- Compare tool with two model selectors and pair results
- Best Of with award cards
- Reviews archive and review detail
- Comparisons archive and comparison detail
- Guides archive and guide article
- Brands archive and brand detail
- Use-case archive
- About, How We Rate, Editorial Policy, Disclosure, Contact, Accessibility, Privacy Policy, Terms
- Search results and styled 404

### Responsive behavior

- The comparison table converts to stacked, labeled cards on small screens; no horizontal scrolling is required.
- The compare widget becomes one column on mobile.
- The mobile menu and search panel expose state with `aria-expanded`.
- Model pages, TOCs, sources sections, and award cards were checked at 320, 360, 390, 768, 1024, and 1440px.

### Interactions verified

- Mobile menu opens and closes; `aria-expanded` becomes `true`.
- Search panel opens; submitting “battery” navigates to `/?s=battery` and renders a results page.
- Compare combobox selection submits `?first=…&second=…`; results table renders 11+ rows.
- “Differences only” reduces visible rows from 11 to 9 in the tested pair.
- Sources toggle hides and restores source rows.
- Swap control preserves both selected models.
- Contact form validates, stores the entry, and redirects to `?contact=success` with a visible status notice.
- First Tab focuses the skip link.

---

## 4. Backend and admin experience

- WordPress admin login works.
- The model list shows all 5 model rows.
- The model edit screen uses the classic editor, with **74 `coc_*` input fields** and **10 `.postbox` containers** (including Publish).
- Facts are saved only after nonce, capability, autosave, and post-type checks.
- Values are sanitized by type: text, URL, date, and textarea.
- Taxonomies for brands and use cases are editable in the sidebar.
- Comparison records select two published models through admin controls.
- Awards are stored in the `coc_awards` option and link to real model records.
- Contact submissions are stored in `coc_contact_messages` (last 200), with nonce verification, honeypot handling, input validation, and a minimum message length.
- The only element matching `.notice-error, .error` on the model edit screen is WordPress’s hidden heartbeat template:

  `Connection lost. Saving has been disabled until you are reconnected…`

  It has the `hidden` class and was not visible. No PHP error was written to `.local/server.err`, and no JavaScript `pageerror` occurred. Chromium logged one unrelated `Permissions policy violation: unload is not allowed` message.

---

## 5. SEO and structured data

Checked with `.local/seo-check.php` and direct HTTP requests:

- Unique home title: “Portable Oxygen Concentrator Reviews & Comparisons”
- Per-page meta descriptions on indexable pages
- `og:title`, `og:description`, `og:image`, `og:url`, and `twitter:card` on templates
- Canonical URLs on home, reviews, comparisons, archives, brands, and legal pages
- `noindex,follow` on `?first=&second=` comparison permutations and search results
- Search results intentionally omit a canonical and meta description
- JSON-LD:
  - WebSite, SearchAction, EntryPoint, Organization
  - FAQPage on the home page
  - Product, Brand, PropertyValue, Article, Organization on model pages
  - Article on comparison pages
- No `aggregateRating` anywhere; the site does not fabricate ratings.
- `/wp-sitemap.xml`, `/feed/`, and `/robots.txt` return HTTP 200.
- Sitemap contains 36 URLs across 7 child maps.
- `robots.txt` disallows `/wp-admin/` but allows `admin-ajax.php`, and declares the sitemap.

### Performance observations

The homepage made no third-party requests. It loaded one stylesheet (84 KB) and one JavaScript file (21 KB), totaling about 106 KB uncompressed in the local Chromium run. First Contentful Paint was approximately 600 ms on the local machine. These are local observations, not production Lighthouse scores; no CDN, compression, or object cache was active.

---

## 6. QA methodology and evidence

The persistent evidence bundle is in `project-audit/qa-2026-11-24/`.

### 6.1 Browser suite — 28 scenarios, 0 failures

Desktop routes (19): home, compare, compare pair, best, reviews archive, Rove 6 review, sparse NG review, comparisons archive, OxyGo vs CAIRE comparison, guides archive, guide article, brands archive, brand archive, use-case archive, how-we-rate, contact, privacy, search, and 404.

Mobile routes (5): home, compare pair, CAIRE review, best, contact.

Interaction checks (4): mobile menu/search, compare combobox and toggles, contact submission, keyboard skip link.

Each page was checked for:

- Expected HTTP status (404 only for the 404 route)
- Exactly one H1
- Duplicate IDs
- Images without `alt`
- Links and buttons without accessible names
- Unlabeled form controls
- Broken images
- Horizontal overflow
- Console errors, page errors, failed requests, and HTTP 4xx/5xx subresources

Result:

```json
{
  "pages": 28,
  "failures": 0,
  "allIssues": []
}
```

The 404 route returns HTTP 404 by design. The checker excludes that document response from the failure list, but still fails on any missing subresource, console error, or unexpected status. No such failures occurred.

### 6.2 Accessibility — 22 scans, 0 violations

axe-core 4.14.1 scanned 11 routes at desktop (1440px) and mobile (390px) with `wcag2a`, `wcag2aa`, `wcag21aa`, and `best-practice` tags.

Routes: home, compare pair, best, Rove 6, NG, guide, contact, brands, how-we-rate, privacy, 404.

No violations were printed. This complements, but does not replace, manual screen-reader testing.

### 6.3 Internal links — 29 checked, 0 broken

The crawl followed internal links from the main templates and reported:

```
checked 29 broken 0
```

### 6.4 Overflow — 60 combinations, 0 overflows

Ten routes at six widths: 320, 360, 390, 768, 1024, 1440. The checker excludes fixed-position elements and deliberate comparison-scroll containers.

### 6.5 Source reachability — 5 / 5 HTTP 200

Checked on 2026-11-24:

| Source | Status |
|---|---|
| `https://www.caireinc.com/product/freestyle-comfort/` | 200 |
| `https://oxygo.life/oxygo-next-ng` | 200 |
| `https://oxygo.life/oxygo-next` | 200 |
| `https://provider.inogen.com/en/products/inogen-one-g5-system/` | 200 |
| `https://provider.inogen.com/en/products/rove-6-system/` | 200 |

Headline values were cross-checked against the live pages: weights, settings, runtimes, noise, output, warranty length, and CAIRE’s sieve-bed warranty language. The CAIRE URL was canonicalized from `/products/freestyle-comfort/` to `/product/freestyle-comfort/`, and the OxyGo URLs from `www.oxygo.life` to `oxygo.life`; both previously redirected.

Evidence: `project-audit/qa-2026-11-24/source-check.txt`.

### 6.6 Admin checks

- 5 model rows in the list table
- 74 fact inputs on the edit screen
- 10 postboxes including Publish
- Classic editor rendered
- No visible PHP or JavaScript error
- The hidden heartbeat notice is WordPress core markup, not an application fault

---

## 7. Factual corrections made during final QA

These changes were applied to the database and the reproducible seed source (`.local/seed.php`).

1. **Best Lightweight award moved from CAIRE to Inogen One G5.**
   - Old claim: CAIRE at 2.3 kg was the lowest published weight.
   - Actual: 2.3 kg ≈ 5.07 lb; G5 is 4.7 lb, OxyGo NEXT is 4.74 lb, Rove 6 is 4.8 lb.
   - New rationale credits the G5 and explicitly notes that CAIRE’s metric value was not converted.

2. **CAIRE review rewritten to avoid false superlatives.**
   - Removed “lightest record currently documented” and “lowest published weight in our current set.”
   - Reframed the record around its real differentiators: metric-only weight, FAA statement, and a warranty that explicitly names sieve beds.

3. **OxyGo NEXT vs CAIRE comparison corrected.**
   - Removed the claim that the weight gap was the “largest single difference.”
   - Replaced it with an explanation that the weights use different published units and are not silently ranked.

4. **OxyGo NEXT NG excerpt corrected.**
   - Removed “A lighter OxyGo record”; the NG is 4.8 lb, slightly heavier than the NEXT at 4.74 lb.

5. **Rove 6 vs One G5 excerpt corrected.**
   - Removed “one pound apart” and “one hour apart”; the actual published differences are 0.1 lb and 0.25 hours.

6. **Dimension axes corrected.**
   - Rove/G5/OxyGo: changed depth labels to length, matching the manufacturers’ 7.2/7.19 in length.
   - CAIRE: changed “10 (L) × 7.3 (W) × 3.1 (H)” to “10 (H) × 7.3 (W) × 3.1 (D),” matching CAIRE’s stated height, width, and depth.

7. **Model card summaries shortened.**
   - Removed mid-sentence truncation on cards (“…and a…”, “…double…”).
   - Cards now display complete, readable summaries.

8. **Source URLs canonicalized.**
   - Removed unnecessary 301 hops for CAIRE and OxyGo; all five sources now return 200 directly.

An automated scan of model, comparison, page, and guide content found no remaining unsupported superlatives or unit contradictions. The corrected award rationale still uses “lightest” deliberately, for the G5’s 4.7 lb figure, and notes that CAIRE’s metric value was not converted.

---

## 8. Screenshots and artifacts

`project-audit/qa-2026-11-24/` contains:

- `README.md` — exact commands and environment
- `results.json` — all 28 scenario results
- `source-check.txt` — source URL status log
- `home-desktop.png`
- `best-desktop.png`
- `model-desktop.png`
- `compare-pair-desktop.png`
- `mobile-home.png`
- `mobile-compare-pair.png`
- `mobile-best.png`
- `interaction-compare-result.png`
- `interaction-contact-success.png`
- `admin-model-current.png`
- `awards-final.png`

---

## 9. Reproduction

Open the front end at `http://localhost:8080/` and the admin at `http://localhost:8080/wp-admin/`.

The local project is started from the project root with:

```powershell
python main.py
```

The launcher presents numbered options to start with or without a browser, show status, stop, restart, or exit. Direct commands remain available for automation. The process ID is stored in `.local/server.pid`; output goes to `.local/server.log` and `.local/server.err`.

WP-CLI is run from `.local/wordpress` as:

```powershell
..\php\php.exe ..\wp-cli.phar <command>
```

Seed content lives in `.local/seed.php`; targeted repair scripts are in `.local/fix-*.php`. The admin account is local-only and its credentials are intentionally not reproduced in this report.

QA commands are listed in `project-audit/qa-2026-11-24/README.md`. The temporary harness lives outside the product in the system temp directory and is not part of the WordPress runtime.

---

## 10. Known limitations and launch conditions

These are honest gaps, not hidden failures.

1. **Local URL in metadata.** Canonicals, Open Graph URLs, the sitemap, and robots.txt currently point to `http://localhost:8080`. Replace the site URL and run a serialized search-replace on production.
2. **No production mail transport.** The contact form stores submissions and calls `wp_mail()`, but no MTA/SMTP service was configured locally. Email delivery is therefore unverified. QA verified storage, validation, and the success redirect.
3. **No OCS commercial CTA.** Approved Oxygen Care Supply product/call URLs, phone behavior, and tracking parameters were not supplied. No destination was invented. The disclosure page explains the ownership relationship, but outbound commercial conversion links remain a launch decision.
4. **No analytics tag.** GA4/GTM IDs and a consent approach were not supplied. The site currently makes no third-party requests.
5. **Scope is quality-first, not the exhaustive 30-page MVP list.** The build publishes 36 sitemap URLs: 13 pages, 5 reviews, 3 comparisons, 4 guides, and taxonomy archives. The 30-page list was not filled with thin content. This is recorded as an open decision.
6. **Product visuals are illustrations.** Custom SVG technical illustrations are labeled as such. No manufacturer photography or 3D product scan was licensed or used.
7. **SQLite and the PHP built-in server are local conveniences.** Production should use a supported web server, MySQL/MariaDB, HTTPS, compression, caching, and backups. The built-in server is single-threaded and not suitable for production traffic.
8. **Facts can change.** Source pages were live on 2026-11-24, but manufacturers can revise specifications. Verification dates are displayed; the editorial team should recheck on a schedule.
9. **Accessibility testing was automated.** axe-core covered structural and contrast rules. A manual screen-reader and keyboard session is still recommended before launch.
10. **Awards and reviews are editorial, not clinical or hands-on.** The site states this in the methodology, award rationale, and medical limitation. No clinical suitability claim is made.

---

## 11. Final decision

**Local functional/editorial QA: PASS.** The site is running, navigable, responsive, accessible under automated testing, internally linked, source-backed, and editable in WordPress.

**Production launch: CONDITIONAL PASS.** Resolve the domain, email transport, OCS destinations, analytics/consent, production hosting, and scope sign-off in §10. The factual correction in §7 should be treated as mandatory because it removes a contradiction between an award label and the published weights.
