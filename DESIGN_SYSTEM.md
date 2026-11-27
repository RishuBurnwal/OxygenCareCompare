# Design System

## Direction

An editorial consumer-research publication: calm, precise, and evidence-led. Deep navy carries authority, oxygen teal marks action and verification, and a pale mint field separates research tools from reading surfaces. Product facts and comparison controls take priority over decoration.

## Color tokens

Defined in `wp-content/themes/compareoxygen/style.css`.

- Navy: `#123b55`; deep navy `#0c2c41`; soft navy `#1c4b68`
- Teal: `#087f79`; hover `#066a65`; light accent `#24a69a`
- Mint: `#e8f5f3`; strong mint `#d7edea`
- Canvas: `#f6f8fa`; surface `#ffffff`
- Ink: `#172b3a`; slate `#586b7a`; dark slate `#4c6272`
- Border: `#dce5e9`; strong border `#c6d4db`
- Amber `#e9a72f`, success `#267a51`, error `#b84747`

Body text is slate-dark or ink on white/mint. The previous `#647887` slate was darkened to `#586b7a` so small secondary text clears WCAG AA on the surfaces used.

## Type

- Display stack: Manrope, then Inter, then the system UI stack.
- Body/UI stack: Inter, then Manrope, then the system UI stack.
- No third-party font request is made. Manrope/Inter are used when installed; otherwise the site renders in Segoe UI/system UI, which is the guaranteed fallback and keeps the page free of external font latency.
- Base body: `1.0625rem` / 1.68; editorial prose is constrained to `760px` and 17–18px.
- Heading scale is fluid via `clamp()`; H1 tops out at 4rem with tight tracking.
- Labels and eyebrows use uppercase, wide tracking, and small sizes; they never carry meaning alone.

## Layout

- Wide container: `1360px`; standard content: `1248px`; editorial: `760px`.
- Gutters: `clamp(1.05rem, 3vw, 2rem)`.
- Breakpoints: `1120px`, `980px`, `760px`, `520px`.
- Mobile-first reading column; product grids collapse to one column, award cards stack, and comparison tables become labeled cards rather than horizontal scroll.
- Sticky review navigation and TOC use `scroll-padding-top` equal to header height plus spacing.

## Shape, depth, motion

- Radii: 8, 12, 16, 24, 32px.
- Shadows: four restrained navy-tinted levels; cards lift 3px on hover.
- Easing: `cubic-bezier(0.22, 0.61, 0.36, 1)`.
- Motion is short and optional. `prefers-reduced-motion: reduce` removes transitions, smooth scrolling, and non-essential transforms.

## Components

- **Header**: utility bar, wordmark, primary nav, search toggle, compare CTA, mobile disclosure menu with `aria-expanded`.
- **Compare widget**: accessible comboboxes with keyboard support, swap control, submit; one-column on mobile.
- **Product card**: technical illustration, brand, title, verified fact count, award badge when applicable, core specs, review/compare actions.
- **Award card**: label, model, rationale, weight/battery/settings, link to review.
- **Spec table**: value, verification state, and source disclosure per row; missing facts render “Not confirmed”.
- **Comparison table**: sticky model headers, difference highlighting, differences-only and sources toggles, stacked cards below 760px.
- **Evidence panel**: source link, verification date, and confirmed/unconfirmed state.
- **Editorial callouts**: key takeaways, TOC, disclosure, and inline source notes.
- **Forms**: visible labels, errors tied to fields, status region after submission, no placeholder-only labeling.

## Interaction and accessibility

- Visible focus ring: white inner ring plus teal-light outer ring.
- Desktop interactions are keyboard-operable; mobile menu and search expose state through ARIA.
- Skip link is the first tab stop.
- Contrast, headings, landmarks, form labels, and link names are covered by axe-core checks in `FINAL_QA_REPORT.md`.
- No essential information is hidden behind hover or animation.

## Editorial constraints

- A fact is displayed only with a value, source URL, and verification date.
- Unverified fields display “Not confirmed”; they are never filled by inference.
- Awards are editorial, limited, and rationale-bearing; they are not generated from a formula.
- Weight comparisons keep the manufacturer’s published units. Metric-only values are labeled and are never silently converted or given false precision.
- Illustrations are labeled “Technical illustration” and are not presented as manufacturer photography.
