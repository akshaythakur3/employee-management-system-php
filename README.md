# Employee Manager

A small server-rendered employee CRUD application built with PHP, MySQL, and plain CSS. It supports creating, listing, updating, and deleting employee records.

**GitHub repository name:** `employee-management-system-php`

## Features

- Create, view, update, and delete employee records
- MySQL prepared statements
- CSRF protection for form submissions
- Server-side validation and HTML output escaping
- Responsive styling
- Database configuration through environment variables

## Requirements

- PHP 8.1 or later with the `mysqli` extension
- MySQL or MariaDB
- Apache (XAMPP is suitable for local development)

## Setup with XAMPP

1. Copy the project into `C:\xampp\htdocs\new_project`.
2. Start Apache and MySQL from the XAMPP Control Panel.
3. Open phpMyAdmin at `http://localhost/phpmyadmin` and import `database.sql`.
4. Open `http://localhost/new_project/` in your browser.

The local defaults are `localhost`, user `root`, an empty password, and database `project_db`. Set `DB_HOST`, `DB_USER`, `DB_PASSWORD`, and `DB_NAME` in the server environment to override them. Use a dedicated database user and a strong password outside local development.

## Database

`database.sql` creates the `project_db` database and the `employees` table. Import it before opening the application.

## Project files

- `index.php` - add an employee
- `fetch.php` - list employees and open edit/delete actions
- `update.php` - edit an employee
- `delete.php` - confirm and process deletion
- `db.php` - database connection and CSRF helpers
- `style.css` - page styling
- `database.sql` - database and table setup

## Security note

This is a learning project, not a complete production deployment. Before exposing it publicly, configure production database credentials, disable detailed PHP error display, use HTTPS, and add authentication/authorization if employee data must be restricted.
