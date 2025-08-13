```markdown
# ⛑ Smart Helmet for Riders (IoT-Based)

A **PHP + MySQL** web dashboard for monitoring IoT smart helmet sensor data.  
Designed for **rider safety** with features like accident detection, gas leak alerts, and drowsiness monitoring.  
Runs without JavaScript — fully compatible with **Apache, MySQL, and PHP** (XAMPP, LAMP, or WAMP).  

---

## 📌 Features
- 👤 **User & Admin Login**
- 📊 **Live Sensor Data** (gas, rain, vibration, fall, speed, GPS, eye-blink)
- 🚨 **Emergency Alerts**
- 📅 **Historical Log Filtering**
- 🌙 **Dark Theme & Mobile-Friendly**

---

## 🛠 Tech Stack

| Layer       | Technology |
|-------------|------------|
| **Frontend** | HTML, CSS, Bootstrap (via CDN) |
| **Backend**  | PHP (7.4+) |
| **Database** | MySQL / phpMyAdmin |
| **Server**   | Apache (XAMPP/LAMP/WAMP) |

---

## 📂 Project Structure

```

smart-helmet/
├── /user       → Rider dashboard
├── /admin      → Admin panel
├── /backend    → Database connection & logic
├── /assets     → CSS, images
└── README.md   → Project documentation

````

---

## 🗄 Database Schema

**Table:** `helmet_data`

| Column     | Type         | Description               |
|------------|--------------|---------------------------|
| id         | INT          | Primary key               |
| user_id    | INT          | Linked rider ID           |
| gas        | VARCHAR(50)  | Gas sensor status          |
| rain       | VARCHAR(50)  | Rain detection             |
| ir         | VARCHAR(50)  | Infrared reading           |
| vibration  | VARCHAR(50)  | Vibration detection        |
| fall       | VARCHAR(50)  | Accident detection         |
| eye_blink  | VARCHAR(50)  | Drowsiness monitoring      |
| speed      | VARCHAR(50)  | Speed in km/h              |
| gps        | VARCHAR(100) | GPS coordinates            |
| timestamp  | DATETIME     | Log entry time             |

---

## 🚀 Getting Started

### 1️⃣ Clone the Repository
```bash
git clone https://github.com/yourusername/smart-helmet.git
````

### 2️⃣ Place in Web Server Folder

* **XAMPP:** `htdocs/`
* **LAMP/WAMP:** `www/`

### 3️⃣ Database Setup

* Create database: `smart_helmet`
* Import SQL from `/backend/sql/`

### 4️⃣ Configure DB Connection

Edit `/backend/db.php` with your MySQL username and password.

### 5️⃣ Run the Project

Open your browser and go to:

```
http://localhost/smart-helmet/
```

---

## 📅 To-Do

* [ ] Export logs as CSV
* [ ] Add email/SMS alerts
* [ ] Optional real-time dashboard

---

## 📬 Contact

📧 Email: `sakibnghs123@gmail.com`
🔗 LinkedIn: [Your Profile](www.linkedin.com/in/mohammed-sakib-hasan-50ab08362)

---

⭐ **If you find this project helpful, please star the repository on GitHub!**

```

✅ This version will render **beautifully in GitHub** because:  
- Proper use of **tables** for tech stack and DB schema.  
- **Code fences** for folder structure and commands.  
- Clean spacing so no sections get squashed.  

If you want, I can also make you a **GitHub README with a banner image and badges** so it looks even more attractive at the top.  
Do you want me to do that next?
```
