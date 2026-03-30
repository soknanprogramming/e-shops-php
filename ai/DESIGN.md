# Design System Specification: Royal Academic Editorial

## 1. Overview & Creative North Star
The Creative North Star for this design system is **"The Scholarly Curator."** 

This system moves beyond a standard e-commerce grid to create a digital experience that feels like an upscale academic journal or a high-end institutional archive. We achieve this by blending the prestige of Cambodian academic heritage—represented by deep Royal Blues and ceremonial Golds—with a modern, editorial layout characterized by intentional asymmetry, generous white space, and layered depth. 

The goal is to evoke trust and "quiet luxury." We do not use loud, vibrating colors or heavy borders. Instead, we use "tonal weight" and sophisticated typography to guide the user’s eye, ensuring the experience feels curated, not automated.

---

## 2. Color Architecture
Our palette is rooted in the "Royal Blue" of institutional authority, balanced by the "Sacred Gold" of Cambodian tradition.

### Primary Palette
- **Primary (`#003883`)**: The anchor of our identity. Used for high-level navigation and primary calls to action.
- **Secondary (`#775a00`)**: Derived from the Gold request. This is our "Lustre" color, used for secondary interactions, highlights, and status indicators.
- **Tertiary/Error (`#7e000a`)**: The "Academic Red." Used sparingly for critical alerts, notifications, or "Sale" highlights to maintain its psychological impact.

### Surface & Background (The Cream Foundation)
We avoid pure white (`#FFFFFF`) in favor of a "Soft Cream" hierarchy to reduce eye strain and feel more like premium parchment.
- **Surface/Background (`#fff9ee`)**: The global canvas.
- **Surface Container Low (`#fef3d5`)**: For large sectioning.
- **Surface Container Highest (`#ede2c5`)**: For nested elements that need to "recede" or provide deep contrast.

### Strategic Color Rules
*   **The "No-Line" Rule:** Explicitly prohibit 1px solid `#000` or high-contrast borders for sectioning. Boundaries must be defined through background color shifts. For example, a `surface-container-low` product grid sitting on a `surface` background.
*   **Signature Textures:** For Hero sections and Primary CTAs, use a subtle linear gradient transitioning from `primary` (`#003883`) to `primary_container` (`#1e4fa3`) at a 135-degree angle. This adds "visual soul" and prevents the UI from looking flat or "templated."

---

## 3. Typography: The Editorial Voice
We utilize a pairing of **Manrope** (for authoritative, modern headlines) and **Public Sans** (for highly legible, professional body text).

| Role | Token | Font | Size | Case/Style |
| :--- | :--- | :--- | :--- | :--- |
| **Display** | `display-lg` | Manrope | 3.5rem | Semi-Bold, Tight Tracking |
| **Headline** | `headline-md` | Manrope | 1.75rem | Medium |
| **Title** | `title-lg` | Public Sans | 1.375rem | Semi-Bold |
| **Body** | `body-lg` | Public Sans | 1.0rem | Regular, Leading 1.6 |
| **Label** | `label-md` | Public Sans | 0.75rem | All Caps (0.05em tracking) |

**Editorial Intent:** Use `display-lg` for hero product titles, but offset them slightly from the grid (asymmetric placement) to create an "Academic Journal" cover feel.

---

## 4. Elevation & Depth (The Layering Principle)
We reject standard drop shadows in favor of **Tonal Layering** and **Ambient Light.**

*   **Layering:** Achieve depth by "stacking" surface tiers. Place a `surface_container_lowest` card on a `surface_container_low` background. The slight shift in cream hues creates a soft, natural lift.
*   **Ambient Shadows:** When an element must "float" (e.g., a Quick-Buy Modal), use a shadow with a `24px` blur and `4%` opacity, tinted with the `on_surface` color (`#201b09`). This mimics natural light passing through paper.
*   **Glassmorphism:** For the Top Navbar, use `primary` color at 85% opacity with a `backdrop-filter: blur(12px)`. This keeps the "Royal Blue" presence while allowing the cream-colored content to flow underneath as the user scrolls.
*   **The "Ghost Border":** If a container requires a border (e.g., a card), use the `outline_variant` token at **20% opacity**. Never use a 100% opaque border.

---

## 5. Components & UI Elements

### Buttons
*   **Primary:** Background: `primary` gradient; Text: `on_primary`. Shape: `rounded-full` (9999px).
*   **Secondary (The Gold Glow):** Background: Transparent; Border: 1.5px `secondary_fixed`. On Hover: Background becomes `secondary_fixed` with a soft gold outer glow (`box-shadow: 0 0 15px #f3bf32`).
*   **Tertiary:** Text-only in `primary` with a 2px underline in `secondary`.

### Cards (Product & Content)
*   **Structure:** No divider lines. Use `spacing-6` (1.5rem) to separate the image from the text.
*   **Accent:** A `2px` top border in `secondary` (`#E6B325`) only appears on hover to signal interactivity.
*   **Background:** Use `surface_container_lowest` (`#ffffff`) to make the card pop against the cream background.

### Input Fields
*   **Style:** Minimalist. No four-sided box. Use a bottom-border only (`outline_variant`) that transitions to `primary` on focus.
*   **Labels:** Always use `label-md` in `on_surface_variant` positioned 8px above the input.

### Chips & Badges
*   **Academic Badges:** (e.g., "New Arrival," "Limited Edition"). Use `secondary_container` with `on_secondary_container` text. Use `rounded-sm` (0.25rem) to mimic the look of a library call-number tag.

---

## 6. Do’s and Don’ts

### Do:
*   **Do** use intentional white space. If you think a section needs more room, use `spacing-20` (5rem).
*   **Do** use `secondary` (Gold) for "Micro-Moments"—a hover state, a bullet point, or a price tag.
*   **Do** treat the "Primary Blue" as a signal of institutional authority. Use it for navigation and footers to "bookend" the experience.

### Don’t:
*   **Don’t** use divider lines (`<hr>`). Use a background color shift between `surface` and `surface_container_low` instead.
*   **Don’t** use "pure black" for text. Always use `on_surface` (`#201b09`) or `on_background` to maintain the soft academic aesthetic.
*   **Don’t** use sharp corners. Use the `DEFAULT` (0.5rem) or `lg` (1rem) roundedness to keep the interface feeling "approachable" rather than "clinical."

---

## 7. Spacing & Grid Logic
Avoid the "Standard 12-column" look where every box is the same width.
*   **The Power Gap:** Use `spacing-12` (3rem) for horizontal padding on mobile and `spacing-24` (6rem) on desktop to create an editorial "margin."
*   **Asymmetric Blocks:** Try a 2/3 vs 1/3 layout for hero sections. Let the product image break the container and bleed into the margin to create movement.