# Restore Tools

Restore missing placeholder images for categories and products.

## Prerequisites

- Node.js 18+ installed

## Setup

```bash
npm install
```

---

## Restore Categories

Restores missing category placeholder images from `restore_img/` to `categories/`.

### Run

```bash
npm run restore:categories
```

Restores all configured category images (currently: `default.jpg`).

### Restore specific file

```bash
npm run restore:categories -- default.jpg
```

### How It Works

| Source | Target |
|--------|--------|
| `uploads/categories/restore_img/default.jpg` | `uploads/categories/categories/default.jpg` |

- If target file exists: skips
- If target file missing: copies from source

### Output Example

```
File Restore Tool
────────────────────────────────────────
  [skip] default.jpg (already exists)

Restore complete
Restored: 0
Skipped: 1
Total available: 1
```

---

## Restore Products

Restores missing product placeholder images from `restore_img/` to `fake/`.

### Run

```bash
npm run restore:products
```

Restores all configured product images (currently: `fake1.jpg`).

### Restore specific file

```bash
npm run restore:products -- fake1.jpg
```

### How It Works

| Source | Target |
|--------|--------|
| `uploads/products/restore_img/fake1.jpg` | `uploads/products/fake/fake1.jpg` |

- If target file exists: skips
- If target file missing: copies from source

### Output Example

```
File Restore Tool
────────────────────────────────────────
  [1] fake1.jpg

Restore complete
Restored: 1
Skipped: 0
Total available: 1
```

---

## Restore All

Runs both category and product restore tools sequentially.

### Run

```bash
npm run restore:all
```

### Output Example

```
File Restore Tool
────────────────────────────────────────
  [skip] default.jpg (already exists)

Restore complete
Restored: 0
Skipped: 1
Total available: 1

File Restore Tool
────────────────────────────────────────
  [1] fake1.jpg

Restore complete
Restored: 1
Skipped: 0
Total available: 1
```

---

## Adding New Files to Restore

To add more files to the restore list, edit the corresponding tool in `tools/` and update the `files` array in the config:

```typescript
const config: RestoreConfig = {
  sourceDir: path.resolve(__dirname, '../uploads/categories/restore_img'),
  targetDir: path.resolve(__dirname, '../uploads/categories/categories'),
  files: ['default.jpg', 'another-image.jpg'], // Add more files here
};
```

---

## File Structure

```
tools/
├── restore-categories.ts      # Category restore tool
└── restore-products.ts        # Product restore tool

uploads/
├── categories/
│   ├── restore_img/           # Source images for categories
│   │   └── default.jpg
│   └── categories/            # Target directory for categories
│       └── default.jpg
└── products/
    ├── restore_img/           # Source images for products
    │   └── fake1.jpg
    └── fake/                  # Target directory for products
        └── fake1.jpg
```

---

## Available Scripts

| Command | Description |
|---------|-------------|
| `npm run restore:categories` | Restore category images |
| `npm run restore:products` | Restore product images |
| `npm run restore:all` | Run both restore tools |
