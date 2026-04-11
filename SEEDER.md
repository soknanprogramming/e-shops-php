# Database Seeder

Seed fake product data for the khmer24-php platform.

## Prerequisites

- Node.js 18+ installed
- Docker containers running (`docker-compose up -d`)
- At least one user and one category in the database

## Setup

```bash
npm install
```

## Usage

### Basic

```bash
npm run seed -- --username=soknan
```

Creates 10 fake products for the user `soknan`.

### With Count

```bash
npm run seed -- --username=soknan --count=50
```

Creates 50 fake products for the user `soknan`.

### Options

| Flag | Description | Default |
|------|-------------|---------|
| `--username` | Target user by username (required) | - |
| `--count` | Number of products to create | `10` |

## Environment

The seeder reads database credentials from `.env`:

```env
DB_HOST=localhost
DB_USER=app_user
DB_PASSWORD=secret
DB_NAME=app_db
DB_PORT=3307
```

## Output

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

## What Gets Created

Each product includes:
- Random Khmer-inspired product name
- Random price ($10 - $5000)
- Random discount (40% chance)
- Random category from existing categories
- Random Cambodian location
- Khmer/English description
- Placeholder image (`fake/fake1.jpg`)

## Notes

- Images reference `uploads/fake/fake1.jpg` as a placeholder
- Auto-creates user profile if missing
- Uses existing `liked` and `comment` records (creates if needed)
