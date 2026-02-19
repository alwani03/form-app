# Form App

A Laravel application with Role-Based Access Control (RBAC) and Audit Logging.

## Prerequisites

Ensure you have the following installed on your macOS or Linux system:

- **PHP** >= 8.2
- **Composer** (PHP Dependency Manager)
- **Node.js** & **NPM**
- **MySQL** (or use SQLite)

## Installation Guide (macOS / Linux)

### 1. Clone the Repository

```bash
git clone <repository_url>
cd form-app
```

### 2. Automated Setup (Recommended)

This project includes a setup script that handles dependency installation, environment setup, database migration, and frontend building.

```bash
composer run setup
```

*Note: Make sure your database configuration in `.env` is correct before the migration runs, or let it use the default SQLite if preferred.*

### 3. Manual Setup (Alternative)

If you prefer to configure manually:

1.  **Install PHP Dependencies**
    ```bash
    composer install
    ```

2.  **Setup Environment File**
    ```bash
    cp .env.example .env
    ```
    Update the `.env` file with your database credentials:
    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=your_database_name
    DB_USERNAME=your_username
    DB_PASSWORD=your_password
    ```

3.  **Generate Application Key**
    ```bash
    php artisan key:generate
    ```

4.  **Run Migrations & Seeders**
    ```bash
    php artisan migrate --seed
    ```

5.  **Install & Build Frontend Assets**
    ```bash
    npm install
    npm run build
    ```

## Running the Application

To start the development server with hot-reloading (Vite) and other services:

```bash
composer run dev
```

Or run them individually:

```bash
# Start PHP Server
php artisan serve

# Watch Frontend Assets
npm run dev
```

Access the application at: `http://localhost:8000`

## Default Credentials

The database seeder creates a default administrator account:

- **Username**: `alwani`
- **Email**: `achmad.alwani@pinjamyuk.co.id`
- **Password**: `password`
- **Role**: `admin`
- **Department**: `IT`

## Deployment

For deployment to production (VPS/Shared Hosting):

1.  Ensure the server meets PHP 8.2+ requirements.
2.  Upload files.
3.  Set document root to `/public`.
4.  Run:
    ```bash
    composer install --optimize-autoloader --no-dev
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan migrate --force
    ```
