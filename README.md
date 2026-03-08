# ICT Project

Laravel application with PostgreSQL, full-text search, and modern frontend tooling (Vite, Tailwind CSS, Alpine.js).

---

## Table of Contents

- [Prerequisites](#prerequisites)
- [Quick Start](#quick-start)
- [Step-by-Step Setup](#step-by-step-setup)
- [Running the Application](#running-the-application)
- [Database Seeding](#database-seeding)
- [PostgreSQL Requirement](#postgresql-requirement)
- [Troubleshooting](#troubleshooting)

---

## Prerequisites

Before starting, ensure you have:

| Requirement   | Version / Notes                                      |
|----------------|------------------------------------------------------|
| PHP            | 8.2 or higher                                        |
| Composer       | Latest                                               |
| Node.js        | 20 or higher                                         |
| Docker Desktop | For Laravel Sail (recommended setup)                 |
| Git            | For cloning the repository                           |

---

## Quick Start

If you are familiar with Laravel Sail, run these commands:

```bash
composer install
cp .env.example .env
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate
npm install && npm run build
./vendor/bin/sail artisan db:seed
```

Then open [http://localhost](http://localhost) in your browser.

---

## Step-by-Step Setup

### 1. Clone or download the project

```bash
git clone <repository-url> ict
cd ict
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Copy the environment file

```bash
cp .env.example .env
```

No changes to `.env` are required for a new setup. The `.env.example` file is pre-configured for PostgreSQL and Laravel Sail.

### 4. Start Laravel Sail (PostgreSQL + PHP containers)

```bash
./vendor/bin/sail up -d
```

This starts the PostgreSQL database and the Laravel app container. Wait a few seconds for the database to be ready.

### 5. Generate the application key

```bash
./vendor/bin/sail artisan key:generate
```

This populates `APP_KEY` in your `.env` file.

### 6. Run database migrations

```bash
./vendor/bin/sail artisan migrate
```

All database commands must run through Sail so that `DB_HOST=pgsql` (the Docker service name) resolves correctly.

### 7. Install frontend dependencies

```bash
npm install
```

Or, to run inside the Sail container:

```bash
./vendor/bin/sail npm install
```

### 8. Build frontend assets

```bash
npm run build
```

Or:

```bash
./vendor/bin/sail npm run build
```

### 9. Seed the database (recommended)

```bash
./vendor/bin/sail artisan db:seed
```

Required for the homepage to show sample posts. Creates a test user and sample posts. See [Database Seeding](#database-seeding).

---

## Running the Application

### With Sail (recommended)

Sail serves the app automatically when the containers are running. Visit:

- **Web app:** [http://localhost](http://localhost) (or `http://localhost:8080` if you set `APP_PORT=8080`)

For local development with hot reload and queue processing, run the combined script inside Sail:

```bash
./vendor/bin/sail composer run dev
```

This starts the Vite dev server, queue listener, and log tail. The web app is still served by Sail's web server on port 80.

---

## Database Seeding

To populate the database with sample data:

```bash
./vendor/bin/sail artisan db:seed
```

This creates:

- A test user: `test@example.com` (check User factory for password)
- 50 sample posts via `PostSeeder`

---

## PostgreSQL Requirement

This project uses **PostgreSQL** and is not compatible with SQLite or MySQL. The search feature relies on PostgreSQL full-text search (`search_vector` on the `posts` table). Migrations will skip full-text search setup on non-PostgreSQL connections.

---

## Troubleshooting

### Port already in use

If port 80 or 5432 is taken, set in `.env`:

```env
APP_PORT=8080
FORWARD_DB_PORT=5433
```

Then use `http://localhost:8080` and connect to PostgreSQL on port 5433 if needed from the host.

### Permission denied on Linux

If you get permission errors with Sail:

1. Set `WWWUSER` and `WWWGROUP` in `.env` to your user and group IDs:
   ```bash
   id -u   # use as WWWUSER
   id -g   # use as WWWGROUP
   ```
2. Avoid running Sail with `sudo`.

### Database connection failed

- Ensure Sail is running: `./vendor/bin/sail up -d`
- Ensure `DB_HOST=pgsql` when running commands through Sail (for hostname resolution inside Docker)
- If running `php artisan` directly on the host (without Sail), use `DB_HOST=127.0.0.1` and expose the DB port (e.g. `FORWARD_DB_PORT=5432`)

### Sail command not found

Use the full path:

```bash
./vendor/bin/sail up -d
```

Or add an alias:

```bash
alias sail='./vendor/bin/sail'
```

---

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
