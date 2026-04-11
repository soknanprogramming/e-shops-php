# Gemini Context: e-shop-php

This project is a clone of Khmer24, a popular C2C (Consumer-to-Consumer) e-commerce platform in Cambodia. It is built using a custom PHP architecture that follows a separation of concerns through repositories and controllers.

## Project Overview

- **Purpose:** A platform for users to post, search, and manage product listings.
- **Main Technologies:** 
    - **Backend:** PHP 8.2 (Apache)
    - **Database:** MySQL 8.0
    - **Frontend:** Vanilla HTML/CSS (Inter font), Vanilla JavaScript
    - **Containerization:** Docker & Docker Compose
- **Architecture:** 
    - **Repositories (`/repos`):** Handle all database interactions using PDO.
    - **Controllers (`/controllers`):** Process POST requests (e.g., creating products, handling authentication) and perform redirects.
    - **Views (`/views`):** UI templates and page-specific logic.
    - **Routing:** Traditional page-based routing with a front `index.php` that redirects to the views layer.

## Building and Running

The project is fully containerized with Docker.

### Prerequisites
- Docker and Docker Compose installed on your system.

### Key Commands
- **Start Environment:** `docker-compose up -d`
- **Stop Environment:** `docker-compose down`
- **Rebuild Containers:** `docker-compose up -d --build`

### Accessing the Application
- **Web App:** [http://localhost](http://localhost)
- **phpMyAdmin:** [http://localhost:8080](http://localhost:8080) (Host: `mysql`, User: `root`, Password: `root`)

### Database Configuration
The database connection is managed in `configs/connect.php`.
- **Host:** `mysql_db` (inside Docker)
- **Database:** `app_db`
- **User:** `app_user`
- **Password:** `secret`

A SQL schema backup is available at `backups/table/default-table.sql`.

## Development Conventions

### Data Access (Repositories)
Always use the repository classes in `/repos` for database operations. They expect a PDO connection passed to their constructor.
```php
$productRepo = new ProductRepository($conn);
$products = $productRepo->getAll();
```

### Request Handling (Controllers)
Form submissions should target files in `/controllers`. These controllers handle logic, file uploads, and session validation before redirecting back to a view.

### File Uploads
- **Products:** `/uploads/products/`
- **Categories:** `/uploads/categories/`
- **Profiles:** `/uploads/profiles/`

### Authentication
User sessions are managed via `session_start()`.
- Admin users have `is_admin = 1`.
- Posting permission is controlled by `can_post` in the `User` table.

### Styling
The project uses Vanilla CSS with CSS Variables for consistent theming (defined in `:root` in `views/home.php`).

## Directory Structure
- `/backups`: SQL schema and data dumps.
- `/configs`: Database connection settings.
- `/controllers`: Logic for processing form submissions.
- `/repos`: Data access layer (Repositories).
- `/uploads`: User-uploaded media (ignored by git).
- `/views`: Frontend pages and layout assets.
- `/views/assets`: Shared UI components like sidebars and topbars.

## Working with Database easy way
- after look up @/backups/table/default-table.sql you should also  look up @/backups/sql_update.sql too because we have update default schema
- if we have need update database schema please add alter or update code to @backups/sql_update.sql and add comment on top of sql code

## UI Design
- UI should look similar to @prompts/DESIGN.md