# PHPLab02 – Workshop Booking Portal

Chạy: `php -S localhost:8000` trong thư mục project, mở http://localhost:8000/
(hoặc XAMPP: http://localhost/php-s07-workshop/). Không mở bằng file://.

Chức năng: Catalog + GET search/filter, booking POST (validation, CSRF, PRG),
Session dashboard, theme cookie, View Booking Detail, Edit Booking.
Không dùng database/login/framework; booking lưu trong PHP Session.

# Workshop Booking Portal (PHP Lab 02)

A dynamic web-based workshop booking application built with PHP, featuring server-side validation, session state management, catalog searching, and user preference customization.

## 🚀 Features

* **Workshop Catalog & Filtering:** Browse available workshops categorized under Web, AI, and Design. Use the real-time search and category filters to narrow down results via HTTP GET requests.
* **Booking System:** Register for workshops securely with server-side validation and booking seat limits enforced per workshop.
* **Session State Management:** Track user actions, bookings, and preferences across sessions.
* **Appearance Customization:** Switch between Light and Dark mode preferences and save them using POST requests.

---

## 📋 Available Workshops

| Category | Workshop Title | Price | Booking Limit |
| :--- | :--- | :--- | :--- |
| **Web** | [PHP Web Essentials](http://localhost/HCMUS-PHP/PHP-Lab/PHP-Lab02/php-s07-workshop/index.php?id=2) | 450,000 VND | 15 seats |
| **AI** | [Practical AI Automation](http://localhost/HCMUS-PHP/PHP-Lab/PHP-Lab02/php-s07-workshop/index.php?id=3) | 650,000 VND | 10 seats |
| **Design** | [UX Design Sprint](http://localhost/HCMUS-PHP/PHP-Lab/PHP-Lab02/php-s07-workshop/index.php?id=4) | 550,000 VND | 12 seats |

---

## 🛠️ Tech Stack

* **Backend:** PHP (Server-side validation, Sessions, GET/POST request handling)
* **Frontend:** HTML5, CSS3 / Modern UI components
* **Server Environment:** Local development server (Apache / XAMPP recommended)

---

## ⚙️ Installation & Running Locally

1. **Clone or Copy** the project directory into your local server root folder (e.g., `htdocs` in XAMPP):
   ```bash
   cd C:/xampp/htdocs/
   # Place the php-s07-workshop folder here
2. **Start Local Server:** Open your control panel and start Apache.
3. **Access in Browser:** Open your browser and navigate to: http://localhost/HCMUS-PHP/PHP-Lab/PHP-Lab02/php-s07-workshop/index.php

## Project Structure
php-s07-workshop/
│
├── index.php           # Main portal landing page (Catalog, Search & Filter)
├── catalog.php         # Workshop listing handler
├── booking.php         # POST registration & server-side validation logic
├── my-bookings.php     # User booking management page
└── assets/             # Stylesheets and images