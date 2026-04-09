# QWEN.md — khmer24-php

## Project Overview

**khmer24-php** is a C2C (Consumer-to-Consumer) e-commerce platform inspired by Khmer24, a popular marketplace in Cambodia. It enables users to browse, post, and manage product listings with category filtering, search, pagination, image uploads, likes, and comments.

The project is built with a custom PHP architecture following a repository pattern for data access, controller-based request handling, and vanilla HTML/CSS/JS views. It is fully containerized using Docker and Docker Compose.

### Tech Stack
- **Backend:** PHP 8.2 (Apache)
- **Database:** MySQL 8.0
- **Frontend:** Vanilla HTML/CSS/JS, CSS Variables for theming
- **Containerization:** Docker & Docker Compose
- **Database Admin:** phpMyAdmin

### Architecture
```
index.php          → Redirects to /views/index.php
configs/           → Database connection (PDO)
controllers/       → Handle POST requests, file uploads, session validation
repos/             → Data access layer (Repository pattern with PDO)
views/             → Frontend pages and shared UI components
uploads/           → User-uploaded media (products, categories, profiles)
backups/           → SQL schema and migration scripts
ai/                → Design system specification
```

---

## Building and Running

### Prerequisites
- Docker and Docker Compose installed

### Key Commands
```bash
# Start all services
docker-compose up -d

# Stop all services
docker-compose down

# Rebuild containers (after code changes)
docker-compose up -d --build
```

### Access Points
| Service       | URL                          | Credentials            |
|---------------|------------------------------|------------------------|
| Web App       | http://localhost             | —                      |
| phpMyAdmin    | http://localhost:8080        | root / root            |

### Database Connection (inside Docker)
- **Host:** `mysql_db`
- **Database:** `app_db`
- **User:** `app_user`
- **Password:** `secret`

---

## Development Conventions

### Data Access (Repositories)
Always use repository classes in `/repos` for database operations. Pass the PDO connection (`$conn`) to the constructor.

```php
require_once '../repos/ProductRepository.php';
$productRepo = new ProductRepository($conn);
$products = $productRepo->getAll();
```

### Request Handling (Controllers)
- Form submissions target files in `/controllers`.
- Controllers handle logic, file uploads, session validation, and redirect back to views.
- Use `session_start()` and check `$_SESSION['user_id']` for authentication.
- Admin users have `$_SESSION['is_admin'] == 1`.

### File Uploads
| Type       | Path                        |
|------------|-----------------------------|
| Products   | `/uploads/products/`        |
| Categories | `/uploads/categories/`      |
| Profiles   | `/uploads/profiles/`        |

### Database Schema Management
- Reference `backups/table/default-table.sql` for the base schema.
- Reference `backups/sql_update.sql` for schema migrations/alterations.
- **When modifying the schema:** Add ALTER/UPDATE statements to `backups/sql_update.sql` with a comment at the top describing the change.

### Styling & Design System
The project follows a design system documented in `ai/DESIGN.md` — "The Scholarly Forest Editorial" theme:

| Token     | Color                  | Usage                           |
|-----------|------------------------|---------------------------------|
| Primary   | `#1a3325` (Forest Green) | Navigation, CTAs               |
| Secondary | `#9d7c39` (Sacred Gold)  | Highlights, micro-moments      |
| Tertiary  | `#7e000a` (Academic Red) | Alerts, sale highlights        |
| Background| `#fff9ee` (Soft Cream)   | Global canvas                  |

**Typography:** Manrope (headlines) + Public Sans (body text), loaded via Google Fonts.

**Key Design Rules:**
- No 1px solid black borders — use background color shifts for boundaries.
- No pure black text — use `#201b09`.
- No sharp corners — use `rounded-full` or `border-radius`.
- No `<hr>` divider lines.
- Generous white space with asymmetric editorial layouts.

---

## Repository Index

| Repository             | Key Methods                          |
|------------------------|--------------------------------------|
| `ProductRepository`    | `getAll`, `search`, `getById`, `create`, `update`, `delete`, `toggleVisibility` |
| `CategoryRepository`   | `getAll`, `getById`, `create`, `update`, `delete` |
| `UserRepository`       | `getById`, `getByEmail`, `create`, `update`, `authenticate` |
| `ProfileRepository`    | `getByUserId`, `update`, `create`   |
| `CommentRepository`    | `getByProductId`, `create`, `delete`|
| `LikeRepository`       | `countByProductId`, `toggleLike`, `isLikedByUser` |

---

## Controller Index

| Controller    | Responsibility                           |
|---------------|------------------------------------------|
| `auth.php`    | Login, register, session management      |
| `product.php` | Create, update, delete, toggle visibility|
| `category.php`| Admin CRUD for categories                |
| `profile.php` | User profile management                  |
| `comment.php` | Add/delete product comments              |
| `like.php`    | Toggle product likes                     |
| `user.php`    | Admin user management (approve posting)  |

---

## View Index

| View                    | Purpose                              |
|-------------------------|--------------------------------------|
| `home.php`              | Main product listing page with filters & pagination |
| `product_detail.php`    | Single product view with comments    |
| `product_create.php`    | Form to post a new product           |
| `product_edit.php`      | Form to edit an existing product     |
| `login.php` / `register.php` | Authentication pages          |
| `user_dashboard.php`    | User's own products & profile link   |
| `user_profile.php`      | User profile editing                 |
| `admin.php`             | Admin dashboard                      |
| `admin_product.php`     | Admin product management             |
| `admin_category.php`    | Admin category management            |
| `admin_user.php`        | Admin user management                |

---

## Database Quick Reference

Key tables include:
- `User` — User accounts with `can_post` and `request_post_permission` flags
- `Product` — Product listings with `showed` visibility flag
- `category` — Product categories
- `product_image` — Image references (main_image + 5 additional images)
- `user_profile` — User profile with phone numbers
- `product_likes`, `comment` — Social features

---

## Notes
- The `index.php` at the project root simply redirects to `/views/index.php`.
- Docker volumes mount the entire project directory into `/var/www/html` for live development.
- The `uploads/` directory is git-ignored but tracked via `.dockerignore` rules.
