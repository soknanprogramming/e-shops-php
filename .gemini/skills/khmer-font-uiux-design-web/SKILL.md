---
name: khmer-font-uiux-design-web
description: >
  Use this skill whenever the user wants to adapt, convert, or redesign an English-language website
  or design system to use Khmer script — while preserving or enhancing the original visual aesthetic.
  Triggers include: "change to Khmer font", "translate UI to Khmer", "Khmer version of my website",
  "add Khmer language support", "my design but in Khmer", "localize for Cambodia", or any request
  that involves Khmer typography in a web UI context. Also trigger for design systems, component
  libraries, landing pages, or dashboards that need Khmer text to look polished. Do NOT trigger for
  general translation tasks that don't involve UI/UX or CSS.
---

# Khmer Font UI/UX Design Skill

This skill guides the creation of beautiful, production-grade web interfaces that use **Khmer script
beautifully**. The core challenge: Khmer glyphs are taller and more complex than Latin characters,
requiring intentional adjustments to line height, spacing, font sizing, and vertical rhythm to
maintain the original design's feel.

---

## 1. The Core Problem (Read This First)

When you naively swap an English font for a Khmer font in CSS, the design **breaks**:
- Text overflows containers
- Line heights clip the subscript/superscript vowel marks (diacritics)
- Font sizes that look elegant in Latin feel cramped in Khmer
- Letter-spacing (`letter-spacing`) that improves Latin readability **harms** Khmer (never apply it)
- Vertical rhythm collapses

Your job is not just a font swap. It is a **typographic recalibration** that preserves the design's
emotional intent — luxury, playful, academic, minimal — in Khmer script.

---

## 2. Khmer Font Selection Guide

Always choose from this curated list. Match the font to the design's personality.

### Tier 1 — Premium / Editorial (high design weight)
| Font | Personality | Google Fonts? | Best For |
|---|---|---|---|
| **Hanuman** | Classic, scholarly, elegant | ✅ | Academic, luxury, editorial |
| **Battambang** | Refined, readable, slightly formal | ✅ | Body + headline hybrid |
| **Moul** | Display, bold identity, ceremonial | ✅ | Hero headings, branding |
| **Moul Light** | Lighter ceremonial weight | ✅ | Large subheadings |

### Tier 2 — Modern / Clean
| Font | Personality | Google Fonts? | Best For |
|---|---|---|---|
| **Kantumruy Pro** | Clean, contemporary, versatile | ✅ | Modern SaaS, dashboards |
| **Noto Sans Khmer** | Neutral, universal, reliable | ✅ | Body text, long-form reading |
| **Koh Santepheap** | Friendly, approachable | ✅ | Consumer apps, informal UI |

### Tier 3 — Decorative / Accent Only
| Font | Personality | Use Sparingly |
|---|---|---|
| **Dangrek** | Geometric, bold | Display only, never body |
| **Bayon** | Traditional, dense | Hero accent, not paragraphs |

### Pairing Strategy
Always pair a **display font** (headlines, CTAs) with a **body font** (paragraphs, labels):
- Scholarly/Luxury: `Moul` + `Hanuman`
- Modern/Clean: `Kantumruy Pro` + `Noto Sans Khmer`
- Friendly/App: `Koh Santepheap` (single font, multiple weights)

---

## 3. Typographic Recalibration Rules

These are **non-negotiable** when introducing Khmer fonts. Apply them systematically.

### 3.1 Line Height — The Most Critical Rule
Khmer has stacked diacritics above AND below the base character. Standard line heights clip them.

```css
/* ❌ NEVER for Khmer */
line-height: 1.2;
line-height: 1.4;

/* ✅ Minimum safe values */
--lh-display: 1.5;   /* Was 1.1–1.2 in English design */
--lh-headline: 1.55;
--lh-body: 1.9;      /* Was 1.6 in English design → add ~0.3 */
--lh-label: 1.7;
```

**Rule of thumb**: Take every `line-height` from the English design spec and add `0.3–0.4`.

### 3.2 Font Size — Optical Sizing Correction
Khmer glyphs appear optically smaller than Latin at identical `font-size`. Compensate:

```css
/* English spec → Khmer adjustment */
/* display: 3.5rem  → 3.5rem (keep, Khmer display is naturally large) */
/* headline: 1.75rem → 1.85rem */
/* body: 1.0rem     → 1.05rem */
/* label: 0.75rem   → 0.85rem  ← most critical, Khmer small text is hard to read */
```

**Rule**: Scale up by ~5–10% for body and label sizes. Display sizes can stay the same.

### 3.3 Letter Spacing — NEVER Apply to Khmer
```css
/* ❌ NEVER — breaks Khmer glyph shaping */
letter-spacing: 0.05em;
letter-spacing: 1px;

/* ✅ Always reset for Khmer text */
letter-spacing: 0;
letter-spacing: normal;
```

Khmer is a complex script where characters are shaped contextually. Any `letter-spacing` breaks the
connection between base characters and their diacritics.

### 3.4 Font Weight Mapping
Khmer fonts have fewer weight variants than Latin. Map carefully:

```css
/* Common available weights per font */
/* Hanuman: 100, 400, 700, 900 */
/* Kantumruy Pro: 100–900 (good coverage) */
/* Noto Sans Khmer: 100–900 */
/* Moul: 400 only (display weight) */

/* Mapping English design weights to Khmer */
/* Semi-Bold (600) → 700 if font lacks 600 */
/* Medium (500)    → 400 or 600 depending on font */
```

### 3.5 Padding & Vertical Spacing Inside Containers
Because Khmer text is taller, containers (buttons, badges, table cells) need more vertical padding.

```css
/* Button: English → Khmer */
padding: 0.75rem 1.5rem;   /* English */
padding: 0.9rem 1.5rem;    /* Khmer (increase top/bottom only) */

/* Badge / Label chip */
padding: 0.25rem 0.75rem;  /* English */
padding: 0.4rem 0.75rem;   /* Khmer */
```

---

## 4. Google Fonts Import Pattern

Always use the correct Unicode range and weights:

```html
<!-- Scholarly/Luxury pairing (Moul + Hanuman) -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Moul&family=Hanuman:wght@100;400;700;900&display=swap" rel="stylesheet">

<!-- Modern pairing (Kantumruy Pro + Noto Sans Khmer) -->
<link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@100;300;400;500;600;700&family=Noto+Sans+Khmer:wght@400;600;700&display=swap" rel="stylesheet">
```

In CSS, always set the `lang` attribute fallback:
```css
:lang(km) {
  font-family: 'Hanuman', serif;
}
```

---

## 5. Design System Translation Workflow

When given an English design system spec, follow this process:

### Step 1: Audit the Typography Table
Extract all type roles (Display, Headline, Title, Body, Label) and their current values.

### Step 2: Map to Khmer Font Pair
Based on the design's personality (see Section 2), choose the font pair.

### Step 3: Apply Recalibration Rules (Section 3)
- Add 0.3–0.4 to every `line-height`
- Scale up body/label `font-size` by 5–10%
- Remove all `letter-spacing` > 0
- Adjust container padding vertically

### Step 4: Preserve All Non-Typography Tokens
Colors, shadows, border-radius, spacing scale, grid — **do not change these**.
The brand identity lives in the color and spatial system, not the font alone.

### Step 5: Generate the Output
Output as a CSS custom properties block + updated typography table in Markdown.

---

## 6. Applying to the "Scholarly Forest Editorial" Design System

This is a reference example. When the user provides the Scholarly Forest spec, apply:

**Font Choice**: `Moul` (display) + `Hanuman` (body/label)
Rationale: Moul's ceremonial weight matches the "Scholarly Curator" north star.
Hanuman is the most prestigious Khmer serif, matching Public Sans's academic tone.

**Recalibrated Typography**:

| Role | English Spec | Khmer Font | Size | Line Height | Letter Spacing |
|---|---|---|---|---|---|
| Display | Manrope / 3.5rem / Semi-Bold | Moul | 3.5rem | 1.5 | 0 |
| Headline | Manrope / 1.75rem / Medium | Moul | 1.85rem | 1.55 | 0 |
| Title | Public Sans / 1.375rem / Semi-Bold | Hanuman | 1.45rem | 1.65 | 0 |
| Body | Public Sans / 1.0rem / Regular | Hanuman | 1.05rem | 1.9 | 0 |
| Label | Public Sans / 0.75rem / All Caps | Hanuman | 0.85rem | 1.7 | 0 (remove caps) |

> **Note on Label All Caps**: Do NOT apply `text-transform: uppercase` to Khmer text. Khmer has no
> case. Use `font-weight: 700` + slightly larger size as the visual hierarchy signal instead.

**CSS Output**:
```css
@import url('https://fonts.googleapis.com/css2?family=Moul&family=Hanuman:wght@100;400;700;900&display=swap');

:root {
  /* Khmer Font Stack */
  --font-display: 'Moul', serif;
  --font-body: 'Hanuman', serif;

  /* Recalibrated Type Scale */
  --type-display-size: 3.5rem;
  --type-display-weight: 400;      /* Moul only has 400 */
  --type-display-lh: 1.5;
  --type-display-ls: 0;

  --type-headline-size: 1.85rem;
  --type-headline-weight: 400;     /* Moul 400 */
  --type-headline-lh: 1.55;
  --type-headline-ls: 0;

  --type-title-size: 1.45rem;
  --type-title-weight: 700;
  --type-title-lh: 1.65;
  --type-title-ls: 0;

  --type-body-size: 1.05rem;
  --type-body-weight: 400;
  --type-body-lh: 1.9;
  --type-body-ls: 0;

  --type-label-size: 0.85rem;
  --type-label-weight: 700;        /* Replace uppercase with bold */
  --type-label-lh: 1.7;
  --type-label-ls: 0;
  --type-label-transform: none;    /* Remove text-transform: uppercase */
}
```

---

## 7. Common Pitfalls Checklist

Before delivering output, verify:

- [ ] No `letter-spacing` > 0 on any Khmer text element
- [ ] `line-height` ≥ 1.5 for display; ≥ 1.8 for body
- [ ] Label/small text size ≥ 0.8rem (Khmer is unreadable below this)
- [ ] `text-transform: uppercase` removed (doesn't apply to Khmer)
- [ ] Google Fonts import includes correct weights for chosen fonts
- [ ] Buttons and badge containers have increased vertical padding
- [ ] Color system unchanged from English spec
- [ ] Border radius, spacing, grid unchanged from English spec
- [ ] `lang="km"` attribute present on the `<html>` or container element

---

## 8. Output Formats

Depending on what the user needs, output one or more of:

1. **CSS custom properties block** — for design system token updates
2. **Updated typography table** — Markdown table showing before/after
3. **Full HTML/CSS demo** — a rendered component or page using the Khmer font system
4. **Google Fonts import snippet** — ready to paste into `<head>`
5. **Tailwind config extension** — if the project uses Tailwind CSS

When in doubt, provide both the CSS block and a small rendered HTML demo so the user can see the
result visually.
