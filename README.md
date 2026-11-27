# CompareOxygenConcentrators.com

An independent, source-backed research and comparison platform for **portable oxygen
concentrators (POCs)** — built as a WordPress site with a purpose-written theme and core
plugin. Every published specification is tied to a manufacturer source and a verification
date, so the site can make claims a comparison shopper can actually check.

| | |
|---|---|
| **Live site** | <https://anuranjanv19.sg-host.com> |
| **Repository** | `RishuBurnwal/OxygenCareCompare` (public) |
| **Platform** | WordPress 6.6+ on PHP 8.3 |
| **Theme** | `compareoxygen` 1.0.0 |
| **Core plugin** | `coc-core` 0.2.0 |

> The intended public domain (`compareoxygenconcentrators.com`) is referenced in the theme
> header but is not yet pointed at this deployment.

---

## What the site does

- **Model records** for each concentrator: weight, dimensions, oxygen delivery, battery
  runtime, noise, warranty and other specification groups — each field captured with its own
  source URL and verification date.
- **Head-to-head comparisons** between two reviewed models, generated from the structured
  records rather than hand-written prose.
- **Editorial awards** and brand/use-case taxonomies.
- **Structured data** (JSON-LD) and metadata derived from the same records, so search and
  social previews never drift from the page content.
- Anything not backed by a source is rendered as **“Not confirmed.”** — no invented specs,
  ratings, or hands-on claims.

## Repository layout

```
wp-content/
  themes/compareoxygen/     Presentation: templates, design tokens, responsive CSS, vanilla JS
  plugins/coc-core/         Content model: poc_model, poc_comparison, brands, use cases,
                            fact-level sources, comparison logic, awards, metadata, JSON-LD
docs & reports              Design, architecture, QA and decision records (see below)
main.py                     Local launcher menu (start / stop / status / restart)
.github/workflows/          GitHub Actions deployment pipeline
LICENSE                     Proprietary licence — see the end of this file
```

## Architecture at a glance

**Theme — `compareoxygen`.** Server-rendered PHP templates, one design-token file
(`style.css`), progressive-enhancement JavaScript. No React, Vue, TypeScript, WooCommerce,
Elementor or any page builder. Front-end assets ship as-is; there is no build step.

**Plugin — `coc-core`.** Owns the data layer:

| Piece | Purpose |
|---|---|
| `poc_model` | One record per concentrator, with grouped specification fields |
| `poc_comparison` | Two reviewed models, compared from their records |
| `poc_brand`, `poc_use_case` | Taxonomies for filtering and grouping |
| Fact-level sources | Each specification carries a source URL + verification date |
| `coc_awards` | Editorial award options that must link to a published model |
| JSON-LD / meta | Generated from the same records — one source of truth |

The separation is deliberate: content outlives design. A redesign touches the theme only;
adding a specification group touches the plugin only.

## Requirements

- **Production:** WordPress 6.6+, PHP 8.3+, MySQL/MariaDB, HTTPS, a caching layer, backups.
- **Local:** the repository ships a portable PHP 8.3 + WordPress + SQLite stack in `.local/`
  (not tracked by git), so no Docker or web server is needed.

## Local development

```powershell
python main.py
```

The launcher offers a numbered menu:

```text
1. Start and open in browser
2. Start without opening a browser
3. Show status
4. Stop the server
5. Restart the server
0. Exit
```

Option 1 waits for WordPress to answer and opens <http://localhost:8080/>. Non-interactive
equivalents (`python main.py up --no-browser`, `status`, `stop`, `restart`) exist for
scripts. The launcher records the PHP process in `.local/server.pid` and writes output to
`.local/server.log` and `.local/server.err`. It also registers a local-only mu-plugin that
disables WP-Cron loopbacks, which would otherwise stall PHP’s single-threaded server.

WP-CLI runs from `.local/wordpress`:

```powershell
..\php\php.exe ..\wp-cli.phar <command>
```

Seed content lives in `.local/seed.php`; targeted corrections in `.local/fix-*.php`. The
SQLite database and portable PHP build are local conveniences — never part of a deployment.

## Editorial workflow

1. Create a **model record** and enter only source-backed values, each with its source URL
   and verification date.
2. Review the record; unverified or empty values display as “Not confirmed.”
3. Publish a **comparison** only once both models are reviewed.
4. Attach **awards** only to published model records.

Do not publish invented specifications, ratings, hands-on testing claims, unapproved
retailer links, or analytics IDs.

## Quality and verification

Latest full pass (see `FINAL_QA_REPORT.md`):

- 28 browser scenarios across desktop and mobile
- 22 axe-core accessibility scans with zero violations
- 29 internal links checked, zero broken
- 60 responsive overflow checks
- All five manufacturer source URLs returning HTTP 200

## Deployment

`.github/workflows/deploy-siteground.yml` publishes the theme over SSH + `rsync`.

| Branch | Target | Web root secret |
|---|---|---|
| `main` | staging (when enabled) | `DEPLOY_PATH` |
| `production` | live site | `DEPLOY_PATH_PROD` |

Every run: checkout → verify secrets → guard the target path → install the deploy key and
pin the host key → verify SSH → compute the theme delta for this commit → `rsync` dry-run →
apply deletions → `rsync` → verify checksums over SSH → upload the log as an artifact.

**Safety guarantees**

- Only `wp-content/themes/compareoxygen` is ever written. WordPress core, `wp-admin`,
  `wp-includes`, plugins, uploads, `wp-config.php` and the database are untouched.
- `rsync` runs with `--checksum` and **without `--delete`**: a file that did not change is
  never re-uploaded or modified, and an empty checkout cannot wipe the site.
- Deletions come exclusively from the git diff of the pushed commit.
- The target path must end in `/public_html`, must not contain `crm`, and staging and
  production roots must differ — a bad secret cannot deploy to the wrong place.
- The host key is pinned by fingerprint, and the private key is passphrase-protected: it is
  loaded into a per-job `ssh-agent`, never written unencrypted to a long-lived file.

**Repository secrets**

| Secret | Purpose |
|---|---|
| `SSH_HOST`, `SSH_PORT`, `SSH_USER` | Server connection details |
| `SSH_PRIVATE_KEY` | Passphrase-protected deploy key (public half authorised on the server) |
| `SSH_KEY_PASSPHRASE` | Passphrase for the deploy key |
| `SSH_KNOWN_HOSTS`, `SSH_KNOWN_HOSTS_FINGERPRINT` | Pinned host key |
| `DEPLOY_PATH` | Staging web root |
| `DEPLOY_PATH_PROD` | Live web root |

**Staging is switched off** until a staging site exists: the repository variable
`STAGING_ENABLED` must be `true` (and `DEPLOY_PATH` must point at the staging docroot).
Until then, `main` pushes skip the job instead of failing.

**Manual run:** Actions → *Deploy to server on code update* → *Run workflow* → choose the
target; leave `dry_run_only` on for a preview that changes nothing.

## Before public launch

1. Point the production domain at the site and run a serialized search-replace from
   `http://localhost:8080`.
2. Configure transactional email, caching and backup retention on the server.
3. Supply approved retailer destinations, phone behaviour and tracking parameters.
4. Supply GA4/GTM IDs and the consent approach.
5. Obtain legal/editorial sign-off and complete a manual screen-reader pass.
6. Confirm whether the current 36-URL quality-first scope is the launch scope.

See `DECISIONS_REQUIRED.md` and `FINAL_QA_REPORT.md` for the detail behind each item.

## Documentation

| File | Contents |
|---|---|
| `TECHNICAL_ARCHITECTURE.md` | System design, data model, rendering flow |
| `DESIGN_SYSTEM.md` | Tokens, typography, components, spacing rules |
| `IMPLEMENTATION_PLAN.md` | Build plan and sequencing |
| `FINAL_QA_REPORT.md` | Final verification results and evidence |
| `QA_TEST_PLAN.md` | Scenario and accessibility test plan |
| `PROJECT_AUDIT.md` | Audit of the build against requirements |
| `DECISIONS_REQUIRED.md` | Open launch decisions awaiting sign-off |
| `EOD_REPORT.md` | Build-session reporting |

## License

**Proprietary — all rights reserved.** Copyright © 2026 Oxygen Care Supply. The theme,
plugin, documentation and assets in this repository may not be copied, modified,
distributed or used without written permission. Third-party components, where present,
remain under their own licences. See [`LICENSE`](LICENSE) for the full notice.
