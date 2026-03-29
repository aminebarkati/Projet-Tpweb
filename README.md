# Competitive Programming Platform

A simple yet powerful competitive programming platform built with native web technologies. This platform allows users to solve coding challenges, submit solutions, and track their progress.

## 📋 Overview

This web application provides a competitive programming environment where users can:

- Browse coding problems by difficulty and category
- Submit solutions in multiple programming languages
- View real-time test results and verdicts
- Track personal statistics and rankings
- Compete with other programmers on the leaderboard
- Participate in time-bound programming contests
- View live contest standings and rankings

## 🚀 Features

- **Problem Repository**: Browse and filter programming challenges
- **Code Submission**: Submit solutions and get instant feedback
- **Automated Judging**: Test solutions against predefined test cases
- **Contests System**: Participate in time-bound competitive programming contests
  - Create and manage contests with start/end times
  - Contest-specific problem sets
  - Real-time contest leaderboard with penalty scoring
  - Virtual participation for past contests
- **User Dashboard**: Track submissions, scores, and progress
- **Global Leaderboard**: See overall rankings and compete with others
- **Responsive Design**: Works seamlessly on desktop and mobile devices

## 🛠️ Tech Stack

### Frontend

- **HTML5**: Semantic markup structure
- **CSS3**: Custom styling
- **Bootstrap 5**: Responsive UI framework
- **JavaScript**: Client-side interactivity and dynamic content

### Backend

- **PHP**: Server-side logic and API endpoints
- **MySQL**: Relational database for storing users, problems, and submissions

## 📁 Project Structure

```
Projet-web/
├── backend/                        # PHP backend logic
│   ├── auth/                       # Authentication handlers
│   ├── class/                      # DB and repository classes
│   └── autoloader.php
├── public/                         # Public web files
│   ├── assets/
│   │   ├── css/                    # Bootstrap + custom styles
│   │   ├── js/                     # Client-side scripts
│   │   └── media/                  # Images and icons
│   ├── components/                 # Reusable PHP UI components
│   └── pages/                      # Main platform pages
├── docs/                           # Project documentation
├── index.php                       # Entry point
├── favicon.ico
└── README.md
```

## 📦 Prerequisites

Before running this project, make sure you have:

- **PHP 8.0+** with PDO and MySQL extensions enabled
- **MySQL 5.7+** (or MariaDB equivalent)
- **A local web server** (Apache/Nginx) or PHP built-in server
- **Git** (to clone the repository)

## ⚙️ Installation

### 1. Clone the Repository

```bash
git clone <repository-url>
cd Projet-web
```

### 2. Configure the Database Connection

Update your MySQL credentials in `backend/class/ConnexionDB.php`:

```php
private static $_dbname = "your_database_name";
private static $_user = "your_mysql_user";
private static $_pwd = "your_mysql_password";
private static $_host = "localhost";
```

### 3. Create and Prepare the Database

Create the database in MySQL:

```sql
CREATE DATABASE your_database_name;
```

Then import your SQL schema/data file if available.

### 4. Run the Project

Option A - PHP built-in server (quick local setup):

```bash
php -S localhost:8000
```

Option B - Apache/Nginx:

- Point your web server to the project root.
- Make sure `index.php` is used as the default entry file.

### 5. Open in Browser

```bash
# with built-in server
http://localhost:8000
```

## 🎮 Usage

1. **Register/Login**
   - Create a new account or login with existing credentials

2. **Browse Problems**
   - Explore available coding challenges

3. **Submit Solutions**
   - Write your code in the editor
   - Submit and wait for the verdict

4. **View Results**
   - Check test results and feedback
   - Review your submission history

5. **Join Contests**
   - Browse upcoming and active contests
   - Register for contests before they start
   - Solve problems during the contest duration
   - Track your rank in real-time
   - Practice with past contests virtually

---

**Note**: This is a simple educational project.
