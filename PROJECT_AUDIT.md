# Project Audit

**Audit date:** 22 November 2026  
**Repository:** `C:\Users\rishu\Desktop\oxygencaresupply`

## Confirmed starting state

- The workspace contains exactly three files: `Prompt.txt`, `CompareOxygenConcentrators.docx`, and `CompareOxygenConcentrators - MVP.docx`.
- No application source, WordPress installation, theme, plugin, image assets, or configuration is present in the workspace.
- The folder is not currently a Git repository.
- Graphify is installed (`0.9.67`) but its attempted document-semantic scan failed with `Connection error`; no valid graph outputs exist.
- `Prompt.txt` defines a WordPress/PHP/MySQL product research platform, a custom `compareoxygen` theme, and a `coc-core` plugin. It explicitly disallows a required Node/build pipeline and fabricated product facts.
- The two DOCX files were extracted and read as source requirements. The MVP document specifies a 30-page public sitemap plus Privacy Policy and Terms.

## Confirmed requirements

- Build an independent consumer research and comparison platform, not an Oxygen Care Supply store or bridge page.
- Keep presentation in the theme and product/comparison/admin/tracking data logic in `coc-core`.
- Make product specifications structured, reusable, source-traceable, and absent when unverified.
- Use editorially approved canonical comparison pairs; do not index generated permutations or query-string combinations.
- Treat medical information carefully; make disclosures visible and do not imply hands-on testing or individualized medical advice.
- Make pages responsive, accessible, fast, server-rendered, and usable without WebGL.
- Keep GA4/GTM and Oxygen Care Supply attribution configurable; do not invent IDs, credentials, or domain settings.

## Conflicts and dependencies

1. **Launch page scope:** the SOP says approximately 12–18 useful pages is a reasonable MVP target and that quality outranks count. The separate MVP specification requires exactly 30 public pages plus two legal utility pages. This is a publishing-scope decision. The architecture can support the larger sitemap, but the final publication count must be confirmed before creating/publishing all editorial pages.
2. **Business configuration:** Oxygen Care Supply URLs, phone destinations, affiliate wording, measurement IDs, and cross-domain setup need owner-supplied values and legal review where appropriate.
3. **Product facts:** official sources and verification dates are required before publishing model specifications or editorial rankings. No product data/assets were supplied.
4. **Environment:** PHP, WP-CLI, Composer, MySQL/MariaDB, a WordPress instance, and browser QA availability were not confirmed in the repository. They must be checked in the target development environment before runtime QA/deployment.
5. **GitHub:** no Git repository or remote is configured; remote access and branch policy remain unknown.
6. **Graph map:** blocked on semantic backend/network availability. The tool exited 0 but explicitly reported that all semantic chunks failed, so the empty cache is not a graph.

## Initial implementation boundary

Begin with portable theme/plugin foundations and project documentation. Keep content empty until reliable sources/configuration exist. The workspace cannot demonstrate WordPress activation or database behavior without a WordPress/PHP/database runtime.
