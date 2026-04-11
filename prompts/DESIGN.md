# Design System Specification: Scholarly Forest Editorial

## 1. Overview & Creative North Star
The Creative North Star for this design system is **"The Scholarly Curator."** 

This system moves beyond a standard e-commerce grid to create a digital experience that feels like an upscale academic journal or a high-end institutional archive. We achieve this by blending the prestige of archival wisdom—represented by Deep Forest Greens and ceremonial Golds—with a modern, editorial layout characterized by intentional asymmetry, generous white space, and layered depth. 

The goal is to evoke trust and "quiet luxury." We do not use loud, vibrating colors or heavy borders. Instead, we use "tonal weight" and sophisticated typography to guide the user’s eye, ensuring the experience feels curated, not automated.

---

## 2. Color Architecture
Our palette is rooted in "Deep Forest Green" (representing growth and endurance), balanced by the "Sacred Gold" of Cambodian tradition.

### Primary Palette
- **Primary (`#1a3325`)**: The anchor of our identity. Used for high-level navigation and primary calls to action.
- **Secondary (`#9d7c39`)**: Our "Lustre" color, used for secondary interactions, highlights, and status indicators.
- **Tertiary/Error (`#7e000a`)**: The "Academic Red." Used sparingly for critical alerts, notifications, or "Sale" highlights to maintain its psychological impact.

### Surface & Background (The Cream Foundation)
We avoid pure white (`#FFFFFF`) in favor of a "Soft Cream" hierarchy to reduce eye strain and feel more like premium parchment.
- **Surface/Background (`#fff9ee`)**: The global canvas.
- **Surface Container Low (`#fef3d5`)**: For large sectioning.
- **Surface Container Highest (`#ede2c5`)**: For nested elements that need to "recede" or provide deep contrast.

### Strategic Color Rules
*   **The "No-Line" Rule:** Explicitly prohibit 1px solid `#000` or high-contrast borders for sectioning. Boundaries must be defined through background color shifts.
*   **Signature Textures:** For Hero sections and Primary CTAs, use a subtle linear gradient transitioning from `primary` (`#1a3325`) to a slightly lighter forest tone (`#2a5038`) at a 135-degree angle.

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

---

## 4. Components & UI Elements

### Buttons
*   **Primary:** Background: `primary` gradient; Text: `#fef3d5`. Shape: `rounded-full`.
*   **Secondary (The Gold Glow):** Background: Transparent; Border: 1.5px `secondary`.
*   **Tertiary:** Text-only in `primary` with a 2px underline in `secondary`.

### Cards (Product & Content)
*   **Structure:** No divider lines. Use background color shifts.
*   **Accent:** A `2px` top border in `secondary` (`#9d7c39`) only appears on hover.

---

## 5. Do’s and Don’ts

### Do:
*   **Do** use intentional white space.
*   **Do** use `secondary` (Gold) for "Micro-Moments."
*   **Do** treat the "Deep Forest Green" as a signal of endurance and stability.

### Don’t:
*   **Don’t** use divider lines (`<hr>`).
*   **Don’t** use "pure black" for text. Use `#201b09`.
*   **Don’t** use sharp corners unless for specific archival-style elements.

---

## 6. Spacing & Grid Logic
*   **The Power Gap:** Use `spacing-24` (6rem) on desktop to create an editorial "margin."
*   **Asymmetric Blocks:** Let the product image break the container and bleed into the margin.