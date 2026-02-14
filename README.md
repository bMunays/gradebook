
---

# 📘 **Gradebook — A Modern, Modular School Information System (SIS)**  
*A custom lightweight, extensible, teacher‑friendly personal web application for managing classes, assessments, attendance, timetables, and more.*

---


## License

This project is **proprietary** and is not open-source. All rights are reserved by the author. 

Access is provided primarily for personal development and to facilitate the use of AI helper tools. For full details on restrictions, please refer to the [LICENSE](./LICENSE) file.


## 🌍 Overview

**Gradebook** is a custom‑built School Information System designed for real classroom environments in Zimbabwean secondary schools.  
It focuses on **simplicity, autonomy, and long‑term data ownership**, avoiding vendor lock‑in and giving teachers full control over their academic records.

The system is built with:

- **PHP (modular, file‑based architecture)**
- **MySQL/MariaDB**
- **HTML/CSS (no heavy frameworks)**
- **A clean, responsive UI**

The project is actively evolving into a **full teacher workflow platform**, including:

- Class management  
- Student records  
- Assessments  
- Timetables  
- Attendance (daily + per‑period)  
- Lesson planning  
- Reporting  
- Resource management  
- Seating plans  
- Teacher dashboards  

This repository contains the **core application**, with sensitive configuration files intentionally excluded.

---

## 🎯 Project Goals

Gradebook is designed to:

- Empower teachers with **simple, fast, reliable tools**  
- Support **Zimbabwean curriculum workflows**  
- Provide **offline‑friendly** and **low‑resource‑friendly** operation  
- Ensure **data ownership** (no cloud lock‑in)  
- Scale into a full **school‑wide MIS**  
- Remain modular, readable, and easy to extend  

---

## 🧩 Current Features

### ✔ **1. Class Management**
- Create, edit, and manage classes  
- Assign class teachers  
- View class lists  
- Navigate to class‑specific modules  

---

### ✔ **2. Student Management**
- Add and manage student profiles  
- Assign students to classes  
- View student details  
- Prepare for attendance, assessments, and reports  

---

### ✔ **3. Assessment Module**
- Create assessments per class and subject  
- Record marks  
- View assessment summaries  
- Export or integrate into reports (future)  

---

### ✔ **4. Timetable Module (Functional)**
- Create class timetables  
- Assign subjects, teachers, and periods  
- View timetables per class  
- Foundation for teacher‑specific timetable view  

---

### ✔ **5. Authentication System (Hybrid)**
A secure login system with:

- Teacher self‑registration  
- Admin approval workflow  
- Admin ability to enable/disable accounts  
- Session‑based access control  
- Navigation that adapts to login state  

This system is the backbone for all teacher‑specific features.

---

## 🚧 In Progress / Known Issues

### ⚠ **Login Redirect Loop**
A redirect loop currently affects the login page under certain conditions.  
This is being debugged and will be resolved in the next update.

---

## 🛠 Upcoming Modules (Planned & Designed)

These modules have been fully architected in conversation and will be implemented next.

### 🔜 **1. Attendance Module (Hybrid System)**  
Supports both:

- **Daily attendance** (class teacher)  
- **Period attendance** (subject teacher)  

Includes:

- Attendance by date  
- Attendance by student  
- Attendance by class  
- Conflict detection (daily vs period)  
- Printable sheets  

---

### 🔜 **2. Student Report by Name / ID**
A clean, teacher‑friendly report view showing:

- Assessments  
- Attendance  
- Comments  
- Progress indicators  

---

### 🔜 **3. Teacher Timetable View**
A personal timetable for the logged‑in teacher:

- Auto‑filtered by teacher ID  
- No need to navigate through classes  
- Foundation for dashboard widgets  

---

### 🔜 **4. “Next Lesson” Dashboard Widget**
A smart dashboard card showing:

- The teacher’s next class  
- Time and period  
- Subject  
- Planned lesson  
- Button: **Mark as Taught**

This will integrate with future lesson‑planning features.

---

### 🔜 **5. Lesson Planning + Tracking**
Teachers will be able to:

- Create lesson plans  
- Attach them to timetable periods  
- Mark lessons as taught  
- Track progress through the syllabus  

---

## 📁 Repository Structure

```
gradebook/
│
├── index.php               # Main router
├── includes/               # Navigation, header, shared components
    ├── footer.php
    ├── header.php
    ├── nav.php
├── modules/                # Feature modules (classes, students, assessments, etc.)
|   ├──
|   ├──
|   ├──
├── api/
|   ├── auth.php
|   ├── get_assessments.php
|   ├── get_attendance.php
|   ├── get_classes.php
|   ├── get_marks.php
|   ├── get_students.php
|   ├── login.php
|   ├── post_attendance.php
|   ├── post_marks.php
├── assets/                 # CSS, JS, images
├── style.css               # Global styling
├── README.md               # This file
│
├── (excluded folders)
│   ├── config/             # Contains config.php and db.php (sensitive)
|   |   ├── config.php
|   |   ├── db.php
│   ├── uploads/            # File uploads
|   |   ├── resources
│   ├── hash/               # Temporary password hashing utilities
|   |   ├── hash.php
|   |   ├── hash.txt
│   └── _old/               # Legacy code and backups

gradebook/
│
├── config/
│   ├── config.php
│   └── db.php
│
├── includes/
│   ├── header.php
│   ├── nav.php
│   └── footer.php
│
├── modules/
│   ├── dashboard.php
│   ├── classes.php
│   ├── students.php
│   ├── assessments.php
│   ├── attendance.php
│   ├── reports.php
│   └── settings.php
│
├── public/
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── app.js
│
└── index.php


```

The excluded folders contain sensitive information and should **never** be committed to GitHub.

---

## 🔐 Security Notes

- Database credentials are stored in `config/db.php` (excluded from repo).  
- Passwords use PHP’s `password_hash()` and `password_verify()`.  
- Admin approval prevents unauthorized access.  
- Sensitive folders are intentionally omitted from GitHub.  

---

## 🧪 Development Status

Gradebook is under **active development**, with new modules being added iteratively.  
The architecture is intentionally modular to support:

- Future Android app integration  
- Multi‑teacher environments  
- Multi‑school deployments  
- Ministry‑ready reporting  

---

## 🤝 Contributions

The repository is public for visibility and collaboration, but licensed under a **proprietary license**.  
Contributions are welcome via:

- Issues  
- Discussions  
- Pull requests (subject to approval)  

---

## 📜 License

This project is licensed under a **Proprietary License**.  
Unauthorized redistribution or commercial use is prohibited.

---

## 👤 Author

**Brighton Munezi**  
Educational practitioner, systems thinker, and developer of scalable educational tools.

---
