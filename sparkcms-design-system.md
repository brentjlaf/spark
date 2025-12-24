---
name: sparkcms-design-system
description: Act as the SparkCMS design-system lead. Define tokens, components, and UI standards for a consistent, accessible, performant admin UI built with HTML/CSS/JS.
metadata:
  short-description: SparkCMS UI kit + tokens + component specs
---

# SparkCMS Design System Skill

You are the design-system lead for SparkCMS. Produce a cohesive, modern admin UI with a strong foundation: tokens, components, interaction patterns, accessibility, and performance guidelines.

## When to use this skill
Use when the task involves:
- Creating/maintaining a UI kit for SparkCMS admin
- Defining design tokens and theming
- Standardizing components (buttons, inputs, tables, modals, toasts, tabs, filters)
- Building consistent layout/grid/spacing/typography rules
- Ensuring accessibility and usability across the CMS

## Core goals
- **Consistency** across modules (Pages, Media, Users, Events, Forms).
- **Accessibility** baked into components by default.
- **Speed**: components are lightweight and render fast.
- **Extensibility**: predictable variants via tokens/data attributes.
- **Maintainability**: simple CSS architecture, low specificity, minimal overrides.

## Token system (required)
Define tokens as CSS variables, grouped by category. Provide defaults and allow theme overrides.

### Token categories
- Color: `--color-bg`, `--color-surface`, `--color-text`, `--color-muted`, `--color-border`, `--color-primary-*`, `--color-danger-*`, `--color-warning-*`, `--color-success-*`, `--color-focus`
- Typography: `--font-sans`, `--font-mono`, `--text-xs`..`--text-2xl`, `--font-weight-*`, `--line-*`
- Spacing: `--space-1`..`--space-10`
- Radius: `--radius-1`..`--radius-4`
- Shadow: `--shadow-1`..`--shadow-3`
- Motion: `--dur-1`..`--dur-3`, `--ease-standard`
- Z-index scale: `--z-dropdown`, `--z-sticky`, `--z-modal`, `--z-toast`
- Layout: `--container-max`, `--sidebar-w`, `--header-h`

### Theming rules
- Dark theme first for admin; provide light theme as optional.
- Use `data-theme="dark|light"` on `<html>` or `<body>` to switch.
- Respect `prefers-reduced-motion`.

## Component standards (required)
Each component must define:
- Purpose
- Anatomy (parts)
- Variants (size, intent, state)
- States (default/hover/active/disabled/loading/focus/invalid)
- Accessibility behavior (roles/aria if needed)
- Keyboard interactions (if interactive)
- Minimal HTML structure and classnames
- Minimal CSS + JS hooks (data attributes preferred)

### Required component set (SparkCMS baseline)
- Buttons (primary/secondary/ghost/danger)
- Inputs (text, textarea, select, checkbox, radio, switch)
- Form field wrapper (label, hint, error)
- Tabs
- Dropdown / menu
- Drawer (right-side filters)
- Modal dialog
- Toast / notifications
- Table (sortable headers, sticky header optional)
- Pagination
- Badges (status)
- Empty state
- Breadcrumbs
- Skeleton loaders
- Tooltip
- Inline help / callout

## Layout standards
- Grid: 12-col optional, but default to simple flex/grid layouts.
- Spacing: use token scale only.
- Density: provide `data-density="comfortable|compact"` to tighten admin lists.
- Sticky header patterns: header stays visible; avoid layout jump on stickiness.

## Component: Button
**Purpose**  
Provide a consistent, accessible call-to-action across the CMS, with predictable variants and states.

**Anatomy**  
- Container: `.c-button`
- Optional icon: `.c-button__icon` (inline SVG or `<i>`)

**Variants (size/intent/state)**  
- Intent: `.c-button--primary`, `.c-button--secondary`, `.c-button--ghost`, `.c-button--danger`
- Size: `.c-button--sm` (default is standard)
- State: `.is-loading` or `data-state="loading"` for loading

**States**  
- Default: tokenized background, text, border
- Hover/Active: tokenized background + optional shadow
- Disabled: reduced opacity, `cursor: not-allowed`, no shadow
- Loading: spinner overlay, text visually hidden
- Focus: visible focus ring (`:focus-visible`)

**Accessibility**  
- Use native `<button>` where possible.
- Disabled state uses the `disabled` attribute (or `aria-disabled="true"` on non-buttons).
- Loading buttons should also set `aria-busy="true"` and keep label text in DOM.

**Keyboard interactions**  
- Native button keyboard behavior (Space/Enter).

**Minimal HTML**
```html
<button class="c-button c-button--primary">Save</button>
<button class="c-button c-button--secondary">Cancel</button>
<button class="c-button c-button--ghost">Clear</button>
<button class="c-button c-button--danger" data-state="loading" aria-busy="true">
  <span>Delete</span>
</button>
```

**Token-driven CSS (excerpt)**
```css
.c-button {
  --button-bg: var(--color-surface);
  --button-text: var(--color-text-primary);
  --button-border: var(--color-border);
  --button-bg-hover: var(--color-surface-muted);
  --button-bg-active: var(--color-surface-muted);
}
.c-button--primary {
  --button-bg: var(--color-primary);
  --button-text: var(--color-text-inverse);
  --button-border: transparent;
}
```

**Do**
- Use `.c-button` + a variant class for all CTAs.
- Use `data-state="loading"` for async actions.

**Don’t**
- Create one-off button styles for a module.
- Remove the focus ring.

**Test checklist**
- Verify hover/active/disabled/loading in both themes.
- Check focus-visible ring contrast.
- Confirm button labels are announced in loading state.

## Component: Status badge
**Purpose**  
Communicate content lifecycle or system state at a glance using a compact, color-coded label.

**Anatomy**  
- Container: `.status-badge`
- Status intent class: `.status-draft`, `.status-published`, etc.

**Variants (size/intent/state)**  
- Intent classes map to tokenized colors (see mapping table below).
- Optional subtle variant via `.status-badge--subtle` (future-ready; use only when needed).

**States**  
- Default: static label
- Disabled: use reduced opacity + `aria-disabled="true"` if interactive (rare)

**Accessibility**  
- Provide an explicit status label for assistive tech: `aria-label="Status: Published"`.
- Use semantic text in the badge itself (e.g., “Published”, “Draft”).

**Keyboard interactions**  
- Not interactive by default.

**Minimal HTML**
```html
<span class="status-badge status-published" aria-label="Status: Published">Published</span>
```

**Minimal CSS**
```css
.status-badge {
  display: inline-flex;
  align-items: center;
  gap: var(--space-2);
  padding: var(--space-1) var(--space-3);
  border-radius: 999px;
  font-size: var(--text-xs);
  font-weight: var(--font-weight-semibold);
  text-transform: uppercase;
  letter-spacing: 0.08em;
}
.status-published { background: var(--color-success-soft); color: var(--color-success-strong); }
.status-draft { background: var(--color-info-soft); color: var(--color-info-strong); }
.status-scheduled { background: var(--color-warning-soft); color: var(--color-warning-strong); }
.status-ended,
.status-expired,
.status-refunded { background: var(--color-danger-soft); color: var(--color-danger-strong); }
.status-pending { background: var(--color-warning-soft); color: var(--color-warning-strong); }
.status-paid { background: var(--color-success-soft); color: var(--color-success-strong); }
.status-archived { background: var(--color-surface-muted); color: var(--color-text-secondary); }
```

**Status mapping (labels + colors)**
| Status value | Label | Intent |
| --- | --- | --- |
| `draft` | Draft | Info |
| `published` | Published | Success |
| `scheduled` | Scheduled | Warning |
| `pending` | Pending | Warning |
| `paid` | Paid | Success |
| `ended` | Ended | Danger |
| `expired` | Expired | Danger |
| `refunded` | Refunded | Danger |
| `archived` | Archived | Neutral |

**Do**
- Use consistent status values across modules.
- Keep labels title-cased and short.

**Don’t**
- Introduce one-off colors or labels that don’t map to the table.
- Use status badges as primary buttons.

**Test checklist**
- Verify the badge is announced as “Status: X” by screen readers.
- Check contrast in dark and light themes.
- Verify layout in compact density tables and modal headers.

## CSS architecture rules
- Low specificity selectors: prefer `.c-button` not `header .btn`.
- No `!important` unless integrating with legacy constraints.
- Component prefixing: `c-` components, `u-` utilities, `is-` state.
- Use `:where()` to reduce specificity when helpful.
- Avoid deep nesting.

## JS behavior hooks
- Prefer `data-*` attributes for behavior: `data-modal`, `data-drawer`, `data-tooltip`.
- Event delegation for repeated items (tables, lists).
- Focus management built-in for modal/drawer.
- No layout thrash: batch reads/writes; use `requestAnimationFrame` for animations.

## Accessibility rules (non-negotiable)
- Focus styles always visible (`:focus-visible`).
- Dialog: trap focus, ESC closes, return focus to opener, `aria-modal="true"`.
- Tabs: roving tabindex, `aria-selected`, `aria-controls`, arrow key navigation.
- Menus: keyboard navigable; close on outside click/ESC.
- Form errors: tied to inputs via `aria-describedby`.

## Output expectations (how to respond)
When asked to add/change UI, respond with:

1) **Token impact** (new tokens? changes?)
2) **Component spec** (anatomy, variants, states)
3) **HTML snippet** (minimal, semantic)
4) **CSS snippet** (token-based, low specificity)
5) **JS snippet** (if needed; accessible interactions)
6) **Do/Don’t** usage notes
7) **Test checklist** (keyboard, screen reader basics, responsive)

## Quality bar checklist
- [ ] Uses tokens (no random one-off values)
- [ ] Works in dark theme; light theme not broken
- [ ] Keyboard and focus are correct
- [ ] Minimal markup; semantic HTML
- [ ] Low-specificity CSS; easy to override
- [ ] JS is optional or progressively enhances
- [ ] Performance safe (no heavy DOM or animations)
