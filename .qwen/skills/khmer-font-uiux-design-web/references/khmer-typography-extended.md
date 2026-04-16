# Khmer Typography Reference: Extended Examples

## Tailwind CSS v4 Integration (Vite Plugin)

For projects using Tailwind CSS v4 (CSS-first config), add to your main CSS:

```css
@import url('https://fonts.googleapis.com/css2?family=Moul&family=Hanuman:wght@100;400;700;900&display=swap');

@theme {
  --font-display: 'Moul', serif;
  --font-body: 'Hanuman', serif;

  /* Override default line heights for Khmer */
  --leading-tight: 1.5;
  --leading-snug: 1.6;
  --leading-normal: 1.9;
  --leading-relaxed: 2.0;
}
```

Then use utility classes as normal: `font-display`, `font-body`, `leading-normal` etc.

---

## NestJS / Express API: Serving Font Metadata

If your backend needs to serve available Khmer font options (e.g. for a font selector UI):

```typescript
// fonts.dto.ts
export const KHMER_FONTS = [
  { id: 'moul-hanuman', label: 'Scholarly (Moul + Hanuman)', display: 'Moul', body: 'Hanuman' },
  { id: 'kantumruy-noto', label: 'Modern (Kantumruy Pro + Noto Sans Khmer)', display: 'Kantumruy Pro', body: 'Noto Sans Khmer' },
  { id: 'koh-santepheap', label: 'Friendly (Koh Santepheap)', display: 'Koh Santepheap', body: 'Koh Santepheap' },
] as const;
```

---

## React Component: Khmer Typography Token Provider

```tsx
import { createContext, useContext } from 'react';

type KhmerFontTheme = {
  fontDisplay: string;
  fontBody: string;
  lineHeightDisplay: number;
  lineHeightBody: number;
};

const scholarlyTheme: KhmerFontTheme = {
  fontDisplay: "'Moul', serif",
  fontBody: "'Hanuman', serif",
  lineHeightDisplay: 1.5,
  lineHeightBody: 1.9,
};

const KhmerThemeContext = createContext(scholarlyTheme);

export function KhmerThemeProvider({ children }: { children: React.ReactNode }) {
  return (
    <KhmerThemeContext.Provider value={scholarlyTheme}>
      <style>{`
        :root {
          --font-display: ${scholarlyTheme.fontDisplay};
          --font-body: ${scholarlyTheme.fontBody};
          --lh-display: ${scholarlyTheme.lineHeightDisplay};
          --lh-body: ${scholarlyTheme.lineHeightBody};
        }
      `}</style>
      {children}
    </KhmerThemeContext.Provider>
  );
}
```

---

## Full Demo HTML: Scholarly Forest + Khmer Fonts

```html
<!DOCTYPE html>
<html lang="km">
<head>
  <meta charset="UTF-8">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Moul&family=Hanuman:wght@100;400;700;900&display=swap" rel="stylesheet">
  <style>
    :root {
      /* Scholarly Forest Colors (unchanged) */
      --primary: #1a3325;
      --secondary: #9d7c39;
      --surface: #fff9ee;
      --surface-low: #fef3d5;
      --text: #201b09;

      /* Khmer Typography */
      --font-display: 'Moul', serif;
      --font-body: 'Hanuman', serif;
    }

    body {
      background: var(--surface);
      color: var(--text);
      font-family: var(--font-body);
      font-size: 1.05rem;
      line-height: 1.9;
      letter-spacing: 0;
      margin: 0;
      padding: 2rem;
    }

    h1 {
      font-family: var(--font-display);
      font-size: 3.5rem;
      line-height: 1.5;
      letter-spacing: 0;
      color: var(--primary);
    }

    h2 {
      font-family: var(--font-display);
      font-size: 1.85rem;
      line-height: 1.55;
      letter-spacing: 0;
      color: var(--primary);
    }

    .label {
      font-family: var(--font-body);
      font-size: 0.85rem;
      font-weight: 700;
      line-height: 1.7;
      letter-spacing: 0;
      /* NO text-transform: uppercase */
      color: var(--secondary);
    }

    .btn-primary {
      background: linear-gradient(135deg, #1a3325, #2a5038);
      color: #fef3d5;
      border: none;
      border-radius: 9999px;
      padding: 0.9rem 1.5rem;   /* Increased vertical for Khmer */
      font-family: var(--font-body);
      font-size: 1.05rem;
      font-weight: 700;
      letter-spacing: 0;
      cursor: pointer;
    }
  </style>
</head>
<body>
  <span class="label">ផលិតផលថ្មី</span>
  <h1>ព្រៃឈើដ៏ស្រស់ស្អាត</h1>
  <h2>ម្ហូបអាហារដែលមានគុណភាព</h2>
  <p>ប្រព័ន្ធការរចនានេះផ្តល់នូវបទពិសោធន៍ដ៏ប្រណីតសម្រាប់អ្នកប្រើប្រាស់ទាំងអស់។</p>
  <button class="btn-primary">ចូលមើលបន្ថែម</button>
</body>
</html>
```

---

## Khmer Text Samples for Testing

Use these to test your font rendering across all type roles:

- **Display**: ព្រៃឈើដ៏ស្រស់ស្អាត (Beautiful Forest)
- **Headline**: ម្ហូបអាហារដែលមានគុណភាព (Quality Food)
- **Body**: ប្រព័ន្ធការរចនានេះផ្តល់នូវវិធីដ៏ប្រណីតក្នុងការបង្ហាញព័ត៌មាន។ (This design system provides an elegant way to present information.)
- **Label**: ផលិតផលថ្មី (New Product) | ការបញ្ចុះតម្លៃ (Sale) | ពេញនិយម (Popular)
- **Button CTA**: ចូលមើលបន្ថែម (View More) | ទិញឥឡូវ (Buy Now)

---

## Accessibility Notes

- Minimum readable Khmer font size on screen: **14px (0.875rem)**. Never go below this for body text.
- For users with low vision, prefer `Noto Sans Khmer` or `Kantumruy Pro` over serif fonts at small sizes.
- Always set `lang="km"` on the `<html>` element or the container with Khmer text — screen readers use this to select the correct Khmer TTS voice.
- Khmer text benefits from slightly higher contrast ratios than WCAG minimum — aim for 5:1 for body text instead of 4.5:1.
