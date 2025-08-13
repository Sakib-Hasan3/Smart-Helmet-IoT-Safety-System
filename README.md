```markdown
# ⛑ Smart Helmet for Riders (IoT-Based)

A **PHP-based IoT web dashboard** for monitoring smart helmet sensor data in real-time.  
Built for **rider safety**, it provides **alerts, logs, and analytics** without relying on JavaScript or heavy frontend frameworks.  

Works seamlessly with **Apache**, **MySQL**, and **PHP** (e.g., via XAMPP).

---

## 📌 Overview

The **Smart Helmet for Riders** project is designed to monitor and log various safety-related parameters for motorbike riders, including:

- Gas leakage detection  
- Rain detection  
- Infrared (IR) proximity detection  
- Vibration and fall detection  
- Eye blink detection for drowsiness monitoring  
- Speed tracking and GPS location logging  

Data from the helmet is sent to the server, stored in a MySQL database, and displayed on a **user-friendly dashboard**.

---

## 📁 Features

- **User & Admin Login**  
  Separate access control for riders and system administrators.
  
- **Live Sensor Data**  
  Displays helmet data including gas levels, rain, vibration, fall, and eye-blink status.

- **Emergency Alerts**  
  Sends visual alerts when abnormal readings are detected.

- **Historical Data Logs**  
  Review past readings, filter by date, and export data (planned).

- **Mobile-Friendly & Dark Theme**  
  Responsive design optimized for all devices.

---

## 💽 Tech Stack

- **Frontend:** HTML, CSS, Bootstrap (via CDN)  
- **Backend:** PHP (Procedural or OOP as preferred)  
- **Database:** MySQL / phpMyAdmin  
- **Hosting:** Apache (XAMPP / LAMP / WAMP)  

---

## 📂 Folder Structure

```

/smart-helmet/
│
├── /user       → Rider interface
├── /admin      → Admin dashboard
├── /backend    → Database connections, controllers
├── /assets     → CSS, images, and icons
└── README.md   → Project documentation

```

---

## 🗄 Database Schema

**helmet_data**  

| Field       | Type         | Description                           |
|-------------|--------------|---------------------------------------|
| id          | INT          | Auto-increment primary key            |
| user_id     | INT          | Associated rider ID                   |
| gas         | VARCHAR(50)  | Gas level status                      |
| rain        | VARCHAR(50)  | Rain detection status                 |
| ir          | VARCHAR(50)  | Infrared proximity reading            |
| vibration   | VARCHAR(50)  | Helmet vibration status               |
| fall        | VARCHAR(50)  | Fall detection status                 |
| eye_blink   | VARCHAR(50)  | Eye blink detection                   |
| speed       | VARCHAR(50)  | Current riding speed                  |
| gps         | VARCHAR(100) | Latitude and Longitude                |
| timestamp   | DATETIME     | Time of data entry                    |

---

## 🚀 Getting Started

1. **Clone or Download**  
   Place the project folder into your `htdocs/` directory.

2. **Import the Database**  
   - Open **phpMyAdmin**  
   - Create a new database (e.g., `smart_helmet`)  
   - Import the `.sql` file provided in `/backend/sql/`

3. **Configure Database Connection**  
   Edit `/backend/db.php` to match your MySQL credentials.

4. **Run the Application**  
   Visit `http://localhost/smart-helmet/`

---

## 🛡 Security Recommendations

- Change default admin credentials before deployment.  
- Use HTTPS for secure data transfer.  
- Restrict admin panel access to trusted IPs (optional).  

---

## ✅ To Do / Future Improvements

- [ ] Export logs to CSV / Excel  
- [ ] Email & SMS alert integration  
- [ ] Real-time updates with WebSockets / AJAX  
- [ ] Multi-language support  
- [ ] Admin analytics dashboard with charts  

---

## 📞 Contact

For questions, suggestions, or collaboration:  
📧 **sakibnghs123@gmail.com**  
🔗 **[LinkedIn](www.linkedin.com/in/mohammed-sakib-hasan-50ab08362)**

---

⭐ **If you find this project helpful, consider giving it a star on GitHub!**
```


