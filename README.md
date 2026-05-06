# Competitive Programming Platform

A comprehensive competitive programming platform built with PHP, MySQL, and vanilla JavaScript. Users can solve coding challenges, submit solutions in multiple languages, and get instant automated judging feedback.

## 📋 Overview

This web application provides a fully-functional competitive programming environment where users can:

- Browse and solve coding problems across multiple difficulty levels
- Submit solutions in multiple programming languages (C++, Python, Java, JavaScript, C)
- Get instant automated judging with real-time test results and verdicts
- Manage user profiles with custom avatars and settings
- Mark favorite users to follow their progress
- Compete with other programmers on the global leaderboard
- Access comprehensive admin tools for user and platform management

## ✅ Features

### Core Features (Fully Implemented)

- **Problem Repository**: Browse, filter, and view coding challenges by difficulty and category
- **Multi-Language Support**: Submit solutions in C++, Python, Java, JavaScript, or C
- **Code Submission System**: Upload and submit code with validation and file handling
- **Automated Judging Engine**: Background worker process that:
  - Compiles source code (when required)
  - Executes against all test cases
  - Tracks execution time and memory usage
  - Generates verdicts (AC, WA, TLE, MLE, RE, CE)
  - Handles runtime errors gracefully
- **Submission Tracking**: View submission history with filters (all, mine, favorites)
- **User Authentication**: 
  - Secure registration with strong password validation (8+ chars, mixed case, digits, special chars)
  - Persistent login with session management
  - Password hashing with `password_hash()`
- **User Profiles**: 
  - Customizable profiles with bio and custom avatars (jpg, jpeg, png, gif, webp - max 3MB)
  - Password management with validation
  - User rating/points system
  - Admin badge display
- **Favorite System**: Mark and manage favorite users to track their progress
- **Admin Dashboard**: 
  - User management (view, edit, delete)
  - Role assignment and promotion
  - Point deduction and reward system
  - Account deletion capabilities
- **Global Leaderboard**: Rankings and scores across all users
- **Responsive Design**: Works seamlessly on desktop and mobile devices using Bootstrap 5

### In Progress / Partial Features

- **Search Functionality**: Component exists but backend search endpoints may need wiring
- **Leaderboard Rankings**: Page exists but ranking algorithm needs completion

### Future Features (Not Yet Implemented)

- Problem creation and editing UI
- Contest system and scheduling
- Problem discussions and comments
- Solution editorials and explanations
- Real-time notifications
- WebSocket support for live updates
- Two-factor authentication
- Email notifications
- Advanced statistics dashboard

## 🛠️ Tech Stack

### Frontend

- **HTML5**: Semantic markup structure
- **CSS3**: Custom styling with Bootstrap integration
- **Bootstrap 5**: Responsive UI framework
- **JavaScript**: Client-side interactivity, form validation, real-time updates

### Backend

- **PHP 8.0+**: Server-side logic and request handling
- **MySQL 5.7+**: Relational database with proper indexing
- **PDO**: Database abstraction with prepared statements

### Key Architectural Patterns

- **Repository Pattern**: Abstract repository classes for database operations
- **PSR-4 Autoloading**: Organized class structure with automatic loading
- **Background Worker**: CLI-based judging engine for async processing
- **MVC-style Separation**: Backend logic separated from frontend presentation

## 📁 Project Structure

```
Projet-web/
├── backend/                           # PHP backend logic
│   ├── auth/
│   │   ├── login.php                 # User authentication
│   │   ├── signup.php                # User registration
│   │   └── logout.php                # Session termination
│   ├── class/                        # Repository and database classes
│   │   ├── ConnexionDB.php           # PDO database singleton
│   │   ├── Repository.php            # Abstract base repository
│   │   ├── UserRepository.php        # User data access
│   │   ├── ProblemsRepository.php    # Problem data access
│   │   ├── SubmissionsRepository.php # Submission data access
│   │   ├── TestCasesRepository.php   # Test case data access
│   │   ├── LanguagesRepository.php   # Language configuration
│   │   └── FavoriteRepository.php    # Favorite relationships
│   ├── judging engine/
│   │   ├── worker.php                # Background judging daemon
│   │   └── storage/                  # Compiled executables
│   ├── problemset/
│   │   ├── submit.php                # Code submission handler
│   │   ├── recent-submissions.php    # Recent submission retrieval
│   │   └── submissions_filter.php    # Submission filtering
│   ├── profile/
│   │   ├── update-profile.php        # Profile editing
│   │   ├── update-password.php       # Password change
│   │   ├── update-avatar.php         # Avatar upload
│   │   ├── make-favorite.php         # Add favorite user
│   │   ├── delete-favorite.php       # Remove favorite user
│   │   ├── deduct-points.php         # Admin: point management
│   │   ├── update-role.php           # Admin: role assignment
│   │   ├── delete-account.php        # Admin: user deletion
│   │   └── common.php                # Shared helper functions
│   └── autoloader.php                # PSR-4 autoloader
├── public/                           # Public web files
│   ├── pages/
│   │   ├── index.php                 # Home page
│   │   ├── problemset.php            # Problem listing
│   │   ├── problem.php               # Problem detail + submission
│   │   ├── submissions.php           # Submission history
│   │   ├── leaderboard.php           # Global rankings
│   │   ├── profile.php               # User's own profile
│   │   ├── profileview.php           # View other user's profile
│   │   └── users.php                 # Admin user management
│   ├── components/
│   │   ├── head.php                  # HTML head with meta/CSS/JS
│   │   ├── nav.php                   # Navigation bar
│   │   ├── login-modal.php           # Login dialog
│   │   ├── signup-modal.php          # Signup dialog
│   │   ├── search-bar.php            # Search component
│   │   └── logout-bar.php            # Logout button
│   └── assets/
│       ├── css/
│       │   ├── bootstrap.min.css     # Bootstrap framework
│       │   ├── style.css             # Custom styling
│       │   └── problem.css           # Problem-specific styling
│       └── js/
│           ├── auth.js               # Login/signup validation
│           ├── problem.js            # Problem page logic
│           ├── submission.js         # Submission handling
│           ├── profile.js            # Profile management
│           ├── favorite.js           # Favorite toggling
│           └── index.js              # Home page logic
├── DB_queries/
│   ├── build.sql                     # Database initialization
│   ├── complete_schema.sql           # Full database schema
│   └── build.sql                     # Build script
├── storage/
│   ├── imgs/                         # User avatars
│   └── submission_files/             # Submitted source code files
├── docs/                             # Project documentation
├── index.php                         # Main entry point
└── README.md
```

## 📦 Prerequisites

Before running this project, ensure you have:

- **PHP 8.0+** with PDO and MySQL extensions enabled
- **MySQL 5.7+** or MariaDB equivalent
- **A local web server** (Apache/Nginx) or PHP built-in server
- **Git** (to clone the repository)
- **C++ compiler** (g++) for C++ submissions (optional, depending on deployment)
- **Python 3** (if running Python submissions)

## ⚙️ Installation

### 1. Clone the Repository

```bash
git clone <repository-url>
cd Projet-web
```

### 2. Configure Database Connection

Update MySQL credentials in `backend/class/ConnexionDB.php`:

```php
private static $_dbname = "your_database_name";
private static $_user = "your_mysql_user";
private static $_pwd = "your_mysql_password";
private static $_host = "localhost";
```

### 3. Set Up the Database

Create the database:

```bash
mysql -u your_mysql_user -p
```

Then in MySQL:

```sql
CREATE DATABASE your_database_name;
USE your_database_name;
SOURCE path/to/DB_queries/complete_schema.sql;
```

### 4. Create Storage Directories

```bash
mkdir -p storage/imgs
mkdir -p storage/submission_files
mkdir -p backend/judging\ engine/storage
chmod 755 storage/imgs
chmod 755 storage/submission_files
chmod 755 backend/judging\ engine/storage
```

### 5. Run the Application

**Option A - PHP Built-in Server** (for development):

```bash
php -S localhost:8000
```

**Option B - Apache/Nginx**:

Configure your web server to point to the project root and set `index.php` as the default file.

### 6. Start the Judging Engine

In a separate terminal, start the background worker for automated judging:

```bash
php backend/judging\ engine/worker.php
```

This worker continuously processes pending submissions and updates their verdicts.

### 7. Access the Application

Open your browser and navigate to:

```
http://localhost:8000
```

## 🎮 Usage

### For Users

1. **Create an Account**
   - Click "Sign Up" and fill in the registration form
   - Password must be at least 8 characters with uppercase, lowercase, digit, and special character

2. **Browse Problems**
   - Navigate to the "Problems" section
   - Filter by difficulty (Easy, Medium, Hard) or category
   - Click on a problem to view details and test cases

3. **Submit a Solution**
   - Write your code in the editor
   - Select the programming language
   - Click "Submit"
   - Wait for the automated verdict

4. **View Submission History**
   - Go to "Submissions" to view all your submissions
   - Filter by status (all, accepted, failed)
   - Click on a submission to see detailed results

5. **Manage Your Profile**
   - Update your username, email, and bio
   - Upload a custom avatar
   - Change your password
   - View your statistics

6. **Follow Other Users**
   - Visit another user's profile
   - Click the star icon to mark them as a favorite
   - View your favorite users' submissions

7. **Check Leaderboard**
   - View the global leaderboard to see rankings
   - Compare your score with other users

### For Admins

1. **Access Admin Panel**
   - Navigate to the "Users" page (admin-only access)
   - View all registered users in a table

2. **Manage Users**
   - Edit user profiles
   - Promote users to admin status
   - Deduct or reward points
   - Delete user accounts

3. **Monitor Activity**
   - View all submissions and verdicts
   - Check problem statistics and acceptance rates

## 🔧 Development

### Database Schema

The platform uses the following core tables:

- **users**: User accounts with ratings and roles
- **problems**: Coding problems with descriptions and constraints
- **submissions**: User submissions with verdicts and execution stats
- **test_cases**: Input/output test cases for problems
- **languages**: Supported programming languages
- **user_favorites**: User-to-user favorite relationships
- **verdict_status**: Verdict enum values (AC, WA, TLE, MLE, RE, CE, PENDING)

### Adding a New Language

To add support for a new programming language:

1. Add a row to the `languages` table with compiler command and file extension
2. Update the judging engine (`worker.php`) if compilation logic differs
3. Test with sample problems

### Adding New Problems

Insert rows into the `problems` and `test_cases` tables:

```sql
INSERT INTO problems (title, description, difficulty, category, time_limit, memory_limit) 
VALUES ('Problem Name', 'Description', 'Easy', 'Category', 1000, 256);

INSERT INTO test_cases (problem_id, input, expected_output, is_sample) 
VALUES (problem_id, 'input', 'output', 1);
```

## 📝 Notes

- **Contest System**: The contest concept has been dropped for now. The platform focuses on problem solving and leaderboards. Contest features may be revisited in the future.
- **Worker Process**: The judging engine must be running as a background process for submissions to be judged. In production, consider using a process manager like `supervisor` to keep it running.
- **Real-time Updates**: Currently uses polling; WebSocket support for real-time updates may be added in the future.
- **Educational Project**: This is an educational competitive programming platform suitable for learning and practice environments.

## 📄 License

This project is provided as-is for educational purposes.

---

**Last Updated**: May 2026
