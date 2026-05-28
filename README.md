# GroupHup 🎓

> **Team Formation System** — Jadara University  
> Department of Software Engineering | Software Development & Documentation  
> Supervised by: **Dr. Zahi Abusarhan**

---

## 📌 Overview

**GroupHup** is a web-based team formation system designed for Jadara University. It allows instructors to manage courses, sections, and student groups, while students can view their courses and join or create groups depending on the section's formation method.

The system supports three group formation methods:
- **Manual** — Instructor assigns students to groups
- **Student Choice** — Students create and join groups themselves
- **Random** — System automatically generates balanced groups

---

## 👥 Team Members

| Name | ID |
|------|----|
| Safa Osama | 202311234 |
| Malak Salah | 202210706 |
| Bashar Mohsen | 202310043 |
| Abdullah Marashdeh | 202310090 |
| Zakaria Ababneh | 202312368 |


---

## 🚀 Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 11 (PHP) |
| Frontend | Blade Templates, Custom CSS |
| Database | MySQL (via XAMPP) |
| Auth | Laravel Session-based Auth |
| Server | Apache (XAMPP) |

---

## ✨ Features

### 👨‍🏫 Instructor
- Register and login with university ID
- Create, manage, and delete courses (with min/max group size)
- Create sections per course with a formation method (manual / student choice / random)
- Upload students via CSV file or add by student ID
- Generate random groups automatically (locked after first generation)
- Create groups manually in any section type
- Add or remove members from any group
- View all groups and members per section
- View Teams email links for each student
- Receive notifications when students join groups
- Download sample CSV file for bulk upload


### 🎓 Student
- Register and login with university ID
- View enrolled courses and sections
- View available groups in enrolled sections
- Create a group (student_choice sections only)
- Join an available group (student_choice only)
- Leave a group (student_choice only)
- Receive notifications when added to a group
- View Teams email link for group members

### 🔔 Notifications
- Triggered when: added to a group, joined a group, created a group
- Mark as read
- View all notifications

---

## 🗄️ Database Schema

```
users
├── id
├── name
├── student_id (unique, used for login)
├── role (student | instructor)
├── teams_email
├── password
└── timestamps

courses
├── id
├── name
├── min_students
├── max_students
├── user_id → users.id (instructor)
└── timestamps

sections
├── id
├── course_id → courses.id
├── name
├── formation_method (manual | student_choice | random)
├── group_size
├── random_locked (boolean)
└── timestamps

groups
├── id
├── name
├── section_id → sections.id
├── created_by → users.id
├── is_random (boolean)
└── timestamps

section_user (pivot)
├── section_id
└── user_id

course_user (pivot)
├── course_id
└── user_id

group_user (pivot)
├── group_id
└── user_id

notifications
├── id
├── user_id → users.id
├── title
├── message
├── type
├── read_at (nullable)
└── timestamps
```

---

## ⚙️ Installation

### Requirements
- PHP 8.2+
- Composer
- XAMPP (MySQL + Apache)
- Node.js (optional)

### Steps

```bash
# 1. Clone or extract the project
cd C:/xampp/htdocs

# 2. Install PHP dependencies
composer install

# 3. Copy environment file
cp .env.example .env

# 4. Generate app key
php artisan key:generate
```

### Configure `.env`
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=group_hup
DB_USERNAME=root
DB_PASSWORD=
```

```bash
# 5. Create database in phpMyAdmin named: group_hup

# 6. Run migrations
php artisan migrate

# 7. (Optional) Seed with sample data
php artisan db:seed

# 8. Start the server
php artisan serve
```

Open: **http://127.0.0.1:8000**

---

## 📂 Project Structure

```
GroupHup/
├── app/
│   ├── Http/Controllers/
│   │   ├── AuthController.php       # Login, Register, Logout
│   │   ├── DashboardController.php  # All dashboard pages
│   │   ├── CourseController.php     # Course CRUD
│   │   ├── SectionController.php    # Section management + CSV upload
│   │   └── GroupController.php      # Group creation, join, leave, members
│   └── Models/
│       ├── User.php
│       ├── Course.php
│       ├── Section.php
│       ├── Group.php
│       └── Notification.php
├── resources/views/
│   ├── layouts/app.blade.php        # Main layout + sidebar + CSS
│   ├── login.blade.php
│   ├── register.blade.php
│   ├── instructor/
│   │   ├── dashboard.blade.php
│   │   ├── courses.blade.php
│   │   ├── course-details.blade.php
│   │   └── notifications.blade.php
│   └── student/
│       ├── dashboard.blade.php
│       ├── courses.blade.php
│       ├── groups.blade.php
│       └── notifications.blade.php
├── routes/web.php
└── database/migrations/
```

---

## 📚 References & Learning Resources

### 📖 Official Documentation
- [Laravel 11 Documentation](https://laravel.com/docs/11.x) — Official Laravel framework documentation.
- [PHP 8.2 Documentation](https://www.php.net/docs.php) — PHP language reference and functions.
- [MySQL Documentation](https://dev.mysql.com/doc/) — Database management system documentation.

---

### 🎥 Learning Tutorials
- [Laravel Tutorial (Arabic) — Mohamed Qatish](https://www.youtube.com/watch?v=6Uf6ybu3W2g&list=PLmhb7ed0Oj8mV7gsjxtKZqNh_uqnbuEpd) — Introduction to Laravel basics in Arabic.
- [Laravel Advanced Tutorial (Arabic) — Atef Soft](https://www.youtube.com/watch?v=K-mw3EPdBVk&list=PL6XRLlEsQ_7Xy0fwWHhmo5H_RCI1RbtqG) — Advanced Laravel concepts and practices.

---

### 📘 Articles & Guides
- [Laravel Brain — Medium Article](https://medium.com/@developerawam/laravel-brain-the-fastest-way-to-understand-a-laravel-codebase-you-didnt-write-af286c944439) — Guide to understanding Laravel project structure quickly.

---

### 🛠️ Tools Used for Learning
- [Claude AI by Anthropic](https://claude.ai) — Used for learning support and clarifying Laravel concepts.
- [XAMPP](https://www.apachefriends.org/) — Local development environment (Apache + MySQL + PHP).
---

## 🏫 Academic Information

| Field | Details |
|-------|---------|
| University | Jadara University |
| Department | Software Engineering |
| Course | Software Development & Documentation |
| Supervisor | Dr. Zahi Abusarhan |
| Semester | 2025/2026 |

---

## 📄 License
All rights reserved to the team and Jadara University.
