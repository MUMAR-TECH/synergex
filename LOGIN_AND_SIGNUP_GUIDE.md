# Admin Login & Signup Guide

This guide explains how the admin authentication system works and how to troubleshoot issues on cPanel.

## 1. Admin Signup (Initial Setup)

The signup process is handled by `admin/setup.php`.

### How it works
1.  **Access**: Visit `http://yourdomain.com/admin/setup.php`.
2.  **Table Creation**: Automatically creates the `admin_users` table if it doesn't exist.
3.  **ID Generation**: Uses PHP to calculate the next available ID (`MAX(id) + 1`).
    *   *Why?* To prevent the "ID=0" issue common on shared hosting with MySQL AUTO_INCREMENT.
4.  **Password Hashing**: Uses `password_hash()` (bcrypt) for security.

### Troubleshooting Signup
*   **"Table already exists"**: This is fine. The script checks for existing emails.
*   **"ID=0"**: If you see a user with ID=0 in the database, delete it. The new system prevents this.
*   **"Access denied"**: Check your database credentials in `.env`.

## 2. Admin Login

The login process is handled by `admin/index.php`.

### How it works
1.  **Access**: Visit `http://yourdomain.com/admin`.
2.  **Authentication**: Verifies email and password against `admin_users` table.
3.  **Session**:
    *   Destroys any old session.
    *   Starts a new secure session.
    *   Regenerates session ID (prevents session fixation).
    *   Stores `admin_id`, `admin_name`, etc.
4.  **Redirection**: Redirects to `admin/dashboard.php`.

### Troubleshooting Login
*   **"Invalid email or password"**:
    *   Check caps lock.
    *   Verify the user exists in the database.
    *   If you manually inserted the user, ensure the password was hashed using `password_hash()`. You cannot store plain text passwords.
*   **Redirect Loop (Login -> Dashboard -> Login)**:
    *   This means the session is not persisting.
    *   Run the **Session Diagnostic Tool** (`test-session.php`).
    *   Check if your browser is blocking cookies.
    *   Ensure `site_url` in `.env` matches your actual domain.
*   **"Session initialization failed"**:
    *   Check `error_log` file in the root directory.
    *   Ensure `session.save_path` is writable on the server.

## 3. Diagnostic Tools

We have included two tools to help you verify the environment:

1.  **`test-php-id-generation.php`**:
    *   Verifies that the database is ready for PHP-based ID generation.
    *   Checks for the "ID=0" problem.
    *   Run this if signup fails.

2.  **`test-session.php`**:
    *   Tests if PHP sessions are working on your server.
    *   Checks cookie paths and write permissions.
    *   Run this if login fails (redirect loops).

## 4. Security Features

*   **Bcrypt Hashing**: Passwords are never stored in plain text.
*   **Session Regeneration**: Prevents session hijacking.
*   **Secure Cookies**: Cookies are set to HTTPOnly and Secure (on HTTPS).
*   **CSRF Protection**: Forms use CSRF tokens (implemented in `includes/functions.php`).
*   **ID Validation**: Login requires a valid positive integer ID (prevents ID=0 logins).

## 5. Deployment Checklist

Before going live on cPanel:

1.  [ ] Upload all files.
2.  [ ] Create `.env` file with production credentials.
3.  [ ] Run `update_admin_users_remove_autoincrement.sql` in phpMyAdmin.
4.  [ ] Run `test-php-id-generation.php` to verify DB.
5.  [ ] Run `test-session.php` to verify sessions.
6.  [ ] Create your first admin account at `admin/setup.php`.
7.  [ ] Login and verify dashboard access.
8.  [ ] Delete `test-*.php` files when done (optional, for security).
