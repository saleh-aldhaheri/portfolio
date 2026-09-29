# Project Status — Portfolio

## Current phase
Planning / backlog re-sequencing (Sprint 1 in progress).

## What changed (dependency re-plan)
The user flagged that dark/light theming (US-103) was scheduled in Sprint 1
*before* the terminal engine, but theme switching is driven by terminal
commands (`theme set <name>`, `theme list`) — not a button. The dependency was
inverted. Fixed as follows:

- **US-103** moved from Sprint 1 → Sprint 2 and rewritten as a terminal-command
  story (was a button-toggle story).
- **US-104** (breadcrumbs) moved from Sprint 1 → Sprint 2 (it already declared
  US-202/US-203 as dependencies).
- **US-201 / US-202 / US-203** (terminal engine) moved from Sprint 2 → Sprint 1.
- **US-202** (parser) updated to recognize `theme set <name>` and `theme list`
  in addition to the navigation commands.
- **US-001** and **US-102** got sprint copies created in Sprint 1 (they
  previously only existed in the Product Backlog index).

## Sprint plan (current)

| Sprint | Focus | Stories |
| --- | --- | --- |
| Sprint 1 | Foundations & Terminal Engine | US-001, US-101, US-102, US-201, US-202, US-203 |
| Sprint 2 | Theme & Navigation | US-103, US-104 |
| Sprint 3 | AI Chat Assistant | US-301, US-302, US-303 |
| Sprint 4 | Backend & Integrations | US-401, US-402, US-403 |

## Task statuses

- US-103 (Theme Switching via Terminal): **in progress** — needs to be re-grounded
  against the terminal (parser) before it can be completed.
- All other Sprint 1 stories: **to do**.

## Blockers / open questions

1. **Command prefix**: the parser story uses `/`-prefixed commands
   (`/help`, `/about`, …), but the theme commands were described as
   `theme set` / `theme list` (no slash). Confirm the canonical form so
   US-202 and US-103 are consistent.
2. US-103 is marked "in progress" but depends on the terminal engine, which is
   not built yet — it should not be progressed until US-201/202/203 land.

## Next actions

- Confirm the theme command naming/prefix with the user.
- Build Sprint 1 in order: US-001 → US-101/US-102 → US-201 → US-202 → US-203.
- Then Sprint 2: US-103 (theme) and US-104 (breadcrumbs).

## External tool records

- ClickUp Space: **Portfolio** (`1100390000058800`)
- Product Backlog list: `1100390000075377`
- Sprint 1: `1100390000075462` · Sprint 2: `1100390000075463`
- Sprint 3: `1100390000075465` · Sprint 4: `1100390000075464`
- Key tasks:
  - [US-103 Theme Switching via Terminal](https://app.clickup.com/t/z8tj1hbkzd)
  - [US-202 Command Parser Engine](https://app.clickup.com/t/z8tj1hbkz9)
  - [US-001 Project Setup](https://app.clickup.com/t/z8tj1hbxzm)
  - [US-102 Footer Layout & Quote Section](https://app.clickup.com/t/z8tj1hbxzn)
