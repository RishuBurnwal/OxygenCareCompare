# Implementation Plan

## Phase 1 — Audit and decisions

- [x] Read the project prompt and both specifications.
- [x] Inspect workspace contents and detect the absence of application source/Git metadata.
- [x] Record the 30-page versus 12–18-page publishing-scope conflict; keep both open.
- [x] Create the project brain and audit records from the bootstrap prompt.
- [x] Defer Graphify until the initial WordPress project exists, per user instruction; then update it.
- [x] Create a localhost-bound Docker Compose definition for WordPress/PHP and MariaDB.
- [x] Add `main.py` to start Docker Desktop/Compose, wait for HTTP readiness, and open the local project.
- [ ] Start the Docker engine and verify local runtime.
- [ ] Keep production page count, OCS configuration, and product sources open; do not guess.

## Phase 2 — WordPress foundation

- [x] Create the `compareoxygen` presentation-only theme foundation.
- [x] Create the `coc-core` data/functionality plugin foundation.
- [x] Implement accessible navigation, responsive design tokens, and server-rendered shell.
- [x] Implement model and approved-comparison post types with fact-level source/date metadata.
- [x] Implement model selection, unknown-value display, and noindex handling for query-selected pairs.
- [ ] Manually activate and verify these flows in a running WordPress instance.

## Phase 3 — Design and core UX

- [ ] Complete reusable components and research-first homepage.
- [ ] Complete model archive/review, brand, guide, and approved comparison templates.
- [ ] Complete comparison mobile behavior and editorial review flow.
- [ ] Add optional verified-asset product viewing only after suitable assets are supplied.

## Phase 4 — Trust, SEO, and measurement

- [ ] Complete disclosure, medical limitation, methodology, and correction/contact pages.
- [ ] Integrate with one selected SEO plugin without duplicate metadata/schema.
- [ ] Add configurable event/CTA attribution after approved destination and analytics details are provided.
- [ ] Verify canonical/indexing rules for approved pages and dynamic utility states.

## Phase 5 — Editorial and release preparation

- [ ] Build the approved sitemap; leave 12–18 versus 30 public pages open pending owner decision.
- [ ] Complete privacy/terms through owner-reviewed legal text.
- [ ] Add hosting-specific staging, backup, and rollback instructions after target selection.
- [ ] Run manual functional, accessibility, responsive, SEO, security, and performance checks in a provisioned environment.

## Current implementation slice

Initial theme/plugin source, local Compose setup, and one-command Python launcher exist. The current Graphify code-only graph contains 222 nodes and 223 edges, including the launcher call paths. Docker Engine remains unavailable, so full runtime/browser proof is still outstanding. Keep model claims source-backed and avoid guessed project configuration.
