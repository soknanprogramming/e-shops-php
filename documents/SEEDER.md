# Database Seeder

Seed fake category and product data for the khmer24-php platform.

## Prerequisites

- Node.js 18+ installed
- Docker containers running (`docker-compose up -d`)
- At least one user in the database (for product seeding)

## Setup

```bash
npm install
```

---

## Seed Categories

### All categories

```bash
npm run seed:categories
```

Creates all 15 Khmer marketplace categories. Skips categories that already exist.

### With count

```bash
npm run seed:categories -- --count=5
```

Creates only 5 new categories (skips existing ones).

### Options

| Flag | Description | Default |
|------|-------------|---------|
| `--count` | Number of new categories to create | `0` (all) |

### Categories Included

| # | Khmer | English |
|---|-------|---------|
| 1 | គ្រឿងអេឡិចត្រូនិច | Electronics |
| 2 | រថយន្ត | Cars |
| 3 | ម៉ូតូ | Motorcycles |
| 4 | អចលនទ្រព្យ | Real Estate |
| 5 | ផ្ទះជួល | House for Rent |
| 6 | ដីធ្លី | Land |
| 7 | ទូរស័ព្ទ | Phones |
| 8 | កុំព្យូទ័រ | Computers |
| 9 | គ្រឿងសង្ហារឹម | Furniture |
| 10 | សម្លៀកបំពាក់ | Clothing |
| 11 | កីឡា | Sports |
| 12 | ការងារ | Jobs |
| 13 | សេវាកម្ម | Services |
| 14 | អាជីវកម្ម | Business |
| 15 | សត្វចិញ្ចឹម | Pets |

### Output Example

```
Starting category seeder...
Categories to create: 5

Found 1 existing categories

Creating categories...

  [1] គ្រឿងអេឡិចត្រូនិច (Electronics)
  [2] រថយន្ត (Cars)
  [3] ម៉ូតូ (Motorcycles)
  [4] អចលនទ្រព្យ (Real Estate)
  [5] ផ្ទះជួល (House for Rent)

Category seeding complete
Created: 5
Skipped: 0
Total available: 15
```

---

## Seed Products

### Basic

```bash
npm run seed -- --username=soknan
```

Creates 10 fake products for the user `soknan`.

### With count

```bash
npm run seed -- --username=soknan --count=50
```

Creates 50 fake products for the user `soknan`.

### Options

| Flag | Description | Default |
|------|-------------|---------|
| `--username` | Target user by username (required) | - |
| `--count` | Number of products to create | `10` |

### Output Example

```
Starting seeder...
Username: soknan
Products to create: 5

Found user: soknan (ID: 1)
Found 12 categories

Creating products...

  [1/5] iPhone Ergonomic ទំនើប $4459
  [2/5] Samsung Practical ស្អាតណាស់ $2743
  [3/5] Honda Small ថ្មី 100% $1931 (was $2200.00)

Successfully created 5 products
Owner: soknan (ID: 1)
Total: 5 products
```

### What Gets Created

Each product includes:
- Random brand name (iPhone, Samsung, Honda, etc.)
- Random price ($10 - $5000)
- Random discount (40% chance)
- Random category from existing categories
- Random Cambodian location
- Khmer/English description
- Placeholder image (`fake/fake1.jpg`)

---

## Environment

The seeder reads database credentials from `.env`:

```env
DB_HOST=localhost
DB_USER=app_user
DB_PASSWORD=secret
DB_NAME=app_db
DB_PORT=3307
```

> **Note:** `DB_HOST=localhost` and `DB_PORT=3307` are used when running from the Windows host. Inside Docker, use `DB_HOST=mysql_db` and `DB_PORT=3306`.

---

## File Structure

```
seeders/
├── seed-categories.ts      # Category seeder
└── seed-products.ts        # Product seeder

uploads/
├── categories/
│   └── default.jpg         # Category placeholder image
└── fake/
    └── fake1.jpg           # Product placeholder image
```

---

## Notes

- Category images use `uploads/categories/default.jpg`
- Product images use `uploads/fake/fake1.jpg`
- Product seeder auto-creates user profile if missing
- Product seeder reuses `liked` and `comment` records (creates if needed)
- Category seeder skips duplicates
