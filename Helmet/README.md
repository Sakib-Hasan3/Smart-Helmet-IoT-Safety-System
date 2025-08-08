# Smart Helmet for Riders (IoT-Based)

This is a PHP-based web dashboard for monitoring IoT smart helmet data.
No JavaScript or frontend frameworks are used. Works with Apache, MySQL, and PHP (e.g., via XAMPP).

## 📁 Features
- User and Admin login system
- Live helmet sensor data display
- Emergency alert system
- Historical log filtering
- Dark theme and mobile-friendly UI

## 💽 Tech Stack
- HTML, CSS, PHP (no JS)
- MySQL / phpMyAdmin
- Bootstrap (via CDN)

## 📂 Folder Structure
- `/user` – rider interface
- `/admin` – admin dashboard
- `/backend` – database and controller logic
- `/assets` – CSS and images

## 🚀 Getting Started
1. Import the SQL tables from the documentation.
2. Set up Apache and MySQL (XAMPP recommended).
3. Place the folder inside `htdocs/`.
4. Navigate to `http://localhost/smart-helmet/`.

## ✅ To Do
- Add export to CSV
- Add email/SMS alerts
- Make the dashboard realtime (optional JavaScript version)
