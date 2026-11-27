# CompareOxygenConcentrators.com

Independent WordPress research and comparison platform for portable oxygen concentrators. The theme handles presentation; the core plugin provides structured model records, fact-level sourcing, comparisons, awards, metadata, and schema.

## Current status

The site is implemented and running locally. Docker Desktop never became available (`npipe:////./pipe/dockerDesktopLinuxEngine`), so verification uses a portable PHP 8.3.37 + WordPress 7.1.5 + SQLite stack in `.local/`.

- Design system: `DESIGN_SYSTEM.md`
- Final verification: `FINAL_QA_REPORT.md`
- Persistent QA evidence: `project-audit/qa-2026-11-24/`
- Open launch decisions: `DECISIONS_REQUIRED.md`

Latest QA: 28 browser scenarios passed, 22 axe-core scans returned zero violations, 29 internal links checked with zero broken, 60 responsive overflow checks passed, and all five manufacturer source URLs returned HTTP 200.

## Architecture

- `wp-content/themes/compareoxygen/` — templates, design tokens, responsive CSS, and vanilla JavaScript.
- `wp-content/plugins/coc-core/` — `poc_model` and `poc_comparison` content types, brand/use-case taxonomies, fact-level sources and verification dates, comparison logic, awards, metadata, and JSON-LD.
- `.local/` — portable local runtime, router, seed content, and repair scripts. Not part of the deployable theme/plugin.
- `compose.yaml` — an optional WordPress + MariaDB definition; it is not the verified runtime in this workspace.

The product uses WordPress, PHP, and vanilla JavaScript only. No React, Vue, TypeScript, Node product runtime, WooCommerce, Elementor, or page builder is involved. Node/Playwright appear only in the external QA harness.

## Local setup

From the project root, run:

```powershell
python main.py
```

The launcher shows a numbered menu:

```text
1. Start and open in browser
2. Start without opening a browser
3. Show status
4. Stop the server
5. Restart the server
0. Exit
```

Choose a number and press Enter. Start waits for WordPress and opens `http://localhost:8080/` when option 1 is selected. The direct commands (`python main.py up --no-browser`, `status`, `stop`, `restart`) remain available for scripts.

The launcher records the PHP process in `.local/server.pid` and writes server output to `.local/server.log` and `.local/server.err`. It also installs a local-only mu-plugin that disables WP-Cron loopbacks, which would otherwise stall PHP's single-threaded development server; run scheduled tasks manually with WP-CLI when needed. The admin is at `http://localhost:8080/wp-admin/`; the account is local-only and its credentials are intentionally not stored in this README.

WP-CLI is invoked from `.local/wordpress`:

```powershell
..\php\php.exe ..\wp-cli.phar <command>
```

The seed source is `.local/seed.php`. Targeted corrections are in `.local/fix-*.php`. The SQLite database and portable PHP build are local conveniences, not a production deployment.

## Editorial workflow

Create model records under **Oxygen concentrator models** and enter only source-backed fields with a source URL and verification date. Use the classic editor for review prose. Empty or unverified values display as “Not confirmed.” Create comparison records only after both model records are reviewed, then select the two models in the comparison meta box. Awards are editorial options stored in `coc_awards` and must link to published model records.

Do not publish invented specifications, ratings, hands-on testing claims, OCS links, or analytics IDs.

## Before production

1. Replace `http://localhost:8080` with the production domain and run a serialized search-replace.
2. Configure MySQL/MariaDB, HTTPS, transactional email, caching, and backups on a supported web server.
3. Supply approved Oxygen Care Supply destinations, phone behavior, and tracking parameters.
4. Supply GA4/GTM IDs and the consent approach.
5. Obtain legal/editorial sign-off and complete a manual screen-reader pass.
6. Decide whether the current 36-URL quality-first scope is the launch scope or whether more MVP pages will be authored.

See `DECISIONS_REQUIRED.md` and `FINAL_QA_REPORT.md` for detail.

## Deploy (GitHub Actions → server)

`.github/workflows/deploy-siteground.yml` syncs **only** `wp-content/themes/compareoxygen`
to the server over SSH + `rsync` (`--checksum`, never `--delete`; deletions come from the
git diff of the pushed commit).

| Branch | Target | Secret holding the web root |
|---|---|---|
| `main` | staging | `DEPLOY_PATH` |
| `production` | live site | `DEPLOY_PATH_PROD` |

Each run: checkout → verify secrets → guard the target path → install the deploy key and
pin the host key → verify SSH → compute the theme delta → rsync dry-run → apply deletions
→ rsync → verify by checksum over SSH → keep the log as a build artifact.

WordPress core, `wp-admin`, `wp-includes`, `wp-content/plugins`, `wp-content/uploads`,
`wp-config.php` and the SQLite database are never touched by a deploy.

Repository secrets:

| Secret | Meaning |
|---|---|
| `SSH_HOST` | server hostname |
| `SSH_PORT` | SSH port (SiteGround: 18765) |
| `SSH_USER` | SSH user from Site Tools → SSH Keys Manager → SSH Credentials |
| `SSH_PRIVATE_KEY` | private half of the deploy key (passphrase-protected) |
| `SSH_KEY_PASSPHRASE` | passphrase of `SSH_PRIVATE_KEY`; the key is loaded into a per-job ssh-agent |
| `SSH_KNOWN_HOSTS`, `SSH_KNOWN_HOSTS_FINGERPRINT` | pinned host key, so a rebuilt or impersonated host is refused |
| `DEPLOY_PATH` | staging web root; must end in `/public_html` and contain `staging` |
| `DEPLOY_PATH_PROD` | live web root; must end in `/public_html` |

The guards refuse a path that is not a `public_html`, that mixes staging and production,
or that contains `crm`.

Manual run: Actions → *Deploy to server on code update* → Run workflow → pick `target`;
leave `dry_run_only` on for a preview that changes nothing.

Staging is switched off until a staging site exists: the repository variable
`STAGING_ENABLED` must be `true` for the `main` → staging job to run (set it with
`gh variable set STAGING_ENABLED --body true`, and point `DEPLOY_PATH` at the staging
docroot). Until then `main` pushes skip the job instead of failing.

