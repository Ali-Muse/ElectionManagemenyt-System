**Election Management System (EMS2)**

**Overview**

A comprehensive web-based election management platform built with PHP, MySQL, HTML, Tailwind CSS, and JavaScript.  
The system supports **Admins, Voters, and Candidates** with full election, candidate, and voting management capabilities.

---

## Features

🏠 **Public Pages**  
- Landing page with hero section and live statistics  
- Featured candidates  
- Candidate search and overview  
- Registration and login pages  

👤 **Authentication**  
- User registration and login  
- Role-based access control (Admin, Voter, Candidate)  
- Session-based authentication  
- Protected routes  

🎓 **Voter Features**  
- Browse active elections  
- Cast votes securely  
- View election results  

👨‍💼 **Admin Features**  
- Dashboard overview with statistics and analytics  
- Election management (create, edit, delete)  
- Candidate management (add/update with images)  
- Voter management (register and maintain records)  
- Profile management  

👨‍💼 **Candidate Features**  
- Manage profile and photo  
- Track performance and vote share  
- View results and rankings  

🔧 **Technical Features**  
- Responsive design with Tailwind CSS  
- Secure image storage for candidate photos  
- Real-time form validation  
- Error handling and loading states  
- Charts for results visualization (Chart.js)  
- Clean and modern UI design  

---

## Technology Stack

**Backend:**  
- PHP 7.x / 8.x  
- MySQL  

**Frontend:**  
- HTML5  
- Tailwind CSS  
- Font Awesome (icons)  
- JavaScript  

---

## Project Structure

EMS2/
├── admin/ # Admin portal
├── candidate/ # Candidate portal
├── voter/ # Voter portal
├── public/ # Public-facing pages
├── includes/ # Shared code
├── uploads/ # Candidate images
└── assets/ # Static files (SVG, icons)

markdown
Copy code

---

## Getting Started

1. **Environment Setup**  
   - Install XAMPP (Apache, PHP, MySQL)  
   - Place project in `C:\xampp\htdocs\EMS2`  

2. **Database**  
   - Create a database (e.g., `election_db`)  
   - Import/create required tables (schema to be added)  

3. **Configure Connection**  
   - Update database credentials in `includes/db.php`  

4. **Run Application**  
   - Start Apache & MySQL in XAMPP  
   - Open [http://localhost/EMS2/public/index.php](http://localhost/EMS2/public/index.php)  
   - Register or log in to start using the system  

---

## Developed By

**Ali Muse Abshir**