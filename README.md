<<<<<<< HEAD
# Gym Management System - Quick Start Guide

## Prerequisites
- **PHP 7.4+** (with PDO MySQL extension)
- **MySQL/MariaDB** server
- **Apache** (optional - for production) OR use PHP built-in server (for development)

## Quick Setup (3 Steps)

### Step 1: Start MySQL Server
If using **XAMPP/WAMP**:
- Open XAMPP/WAMP Control Panel
- Start **MySQL** service

### Step 2: Set Up Database
Open a terminal in the project root and run:
```bash
php setup_database.php
```

This will:
- Create the `smart_gym` database
- Create all necessary tables
- Insert sample data

### Step 3: Start the Server

#### Option A: Using the Startup Script (Recommended)
Double-click `run.bat` or run in terminal:
```bash
run.bat
```

#### Option B: Manual PHP Server
```bash
cd public
php -S localhost:8000
```

#### Option C: Using XAMPP/WAMP
- Place project in `htdocs/gymm` folder
- Access at: `http://localhost/gymm/public/login`

## Access the Application

After starting the server, open your browser:
- **PHP Built-in Server**: http://localhost:8000/login
- **XAMPP/WAMP**: http://localhost/gymm/public/login

## Login Credentials

### Admin Account
- **Email**: admin@gym.com
- **Password**: password123

### Trainer Accounts
- **Email**: mike@gym.com | **Password**: password123
- **Email**: sarah@gym.com | **Password**: password123
- **Email**: david@gym.com | **Password**: password123

### Member Accounts
- **Email**: alice@example.com | **Password**: password123
- **Email**: bob@example.com | **Password**: password123
- **Email**: carol@example.com | **Password**: password123

## Project Structure

```
gymm/
├── app/
│   ├── config/          # Database configuration
│   ├── controllers/     # Application controllers
│   ├── models/          # Data models
│   ├── views/           # HTML templates
│   └── helpers/         # Helper functions
├── database/
│   ├── schema.sql       # Database structure
│   ├── sample_data.sql  # Sample data
│   └── useful_queries.sql
├── public/
│   ├── assets/          # CSS, JS, images
│   ├── index.php        # Front controller (routing)
│   └── .htaccess        # Apache rewrite rules
├── setup_database.php   # Database setup script
├── run.bat              # Windows startup script
└── index.php            # Root entry point

```

## Available Routes

### Public Routes
- `/login` - Login page
- `/register` - Registration page
- `/logout` - Logout

### Admin Routes
- `/admin/users` - Manage users
- `/admin/plans` - Manage membership plans
- `/admin/payments` - View all payments

### Trainer Routes
- `/trainer/members` - View assigned members
- `/trainer/workouts` - Manage workouts
- `/trainer/schedule` - View/manage schedule

### Member Routes
- `/member/profile` - View/edit profile
- `/member/password` - Change password
- `/member/membership` - View membership details
- `/member/workouts` - View assigned workouts
- `/member/payments` - View payment history

### API Routes
All API routes return JSON and are used by AJAX calls:
- `GET/PUT /api/profile` - Get/update profile
- `GET/POST /api/users` - List/create users
- `GET/POST /api/plans` - List/create plans
- `GET /api/payments` - List payments
- `GET/POST /api/workouts` - List/create workouts
- And more...

## Troubleshooting

### "Database connection failed"
- Make sure MySQL is running
- Check credentials in `app/config/db_connect.php`
- Run `php setup_database.php` to create the database

### "404 Not Found" for routes
- If using Apache: Make sure mod_rewrite is enabled
- If using PHP server: Make sure you're in the `public` folder
- Check that `.htaccess` files exist

### "PHP is not recognized"
- Install PHP or XAMPP
- Add PHP to your system PATH

### Port 8000 already in use
- Change the port: `php -S localhost:3000`
- Or stop the other application using port 8000

## Database Configuration

The database connection is configured in `app/config/db_connect.php`:
- **Host**: localhost
- **Database**: smart_gym
- **Username**: root
- **Password**: (empty by default)

To change these settings, edit the `db_connect.php` file.

## Development Notes

- All passwords in sample data are: `password123`
- The routing system supports both GET and POST methods
- AJAX calls are handled through the ApiController
- Session management is automatic
- Role-based access control is implemented

## Next Steps

1. Login with any of the provided credentials
2. Explore the different user roles (Admin, Trainer, Member)
3. Test the CRUD operations
4. Customize the application as needed

## Need Help?

Check the following files for more information:
- `database/README.md` - Database documentation
- `database/useful_queries.sql` - Example queries
- `public/index.php` - Routing configuration
=======
# Smart-Gym-Management-System
>>>>>>> 0682a5be81e84b55afdc085ce3c30b05bcdafdd1
Collaborator test update
