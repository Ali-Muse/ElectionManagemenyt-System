Election Management System (EMS2)
Overview
The Election Management System (EMS2) is a web-based platform that streamlines the entire election process. It centralizes candidate management, voter registration, voting, and results visualization into a secure and responsive application. By providing role-based portals for Admins, Voters, and Candidates, EMS2 improves transparency, efficiency, and trust in digital elections.
Solution
EMS2 addresses these challenges by offering a unified and digital approach:
•	Centralized Management – Admins handle elections, voters, and candidates from one portal.
•	Role-Based Access – Separate dashboards for Admins, Voters, and Candidates.
•	Secure Voting – Session-based authentication ensures only registered voters can participate.
•	Results Visualization – Real-time charts and tables (via Chart.js) make results easy to understand.
•	Image Handling – Candidate photos stored securely with fallback support.
•	Responsive Design – Tailwind CSS ensures the platform works on both desktop and mobile.
Features
•	Authentication & Roles – Secure login for Admins, Voters, and Candidates.
•	Election Management – Create, edit, and manage elections (active, upcoming, ended).
•	Candidate Management – Add/update candidates with profiles and images.
•	Voter Management – Register and maintain voter records.
•	Voting System – Cast and track votes securely per candidate/election.
•	Results & Analytics –
o	Admin: Summary tables and charts (bar + pie).
o	Candidate: Election ranking and personal vote share.
•	Public Landing Page – Displays live statistics (candidates, voters, elections, votes) and featured candidates.
•	Image Storage – Candidate images stored in uploads/ with .htaccess protection.
•	Modern UI – Clean design powered by Tailwind CSS and Font Awesome.
Technology Stack
Backend
•	PHP 7.x / 8.x
•	MySQL 
Frontend
•	HTML5 
•	Tailwind CSS 
•	Font Awesome (icons)
•	javascript
Project Structure
EMS2/
├── admin/             # Admin portal
│   ├── dashboard.php
│   ├── elections.php
│   ├── candidates.php
│   ├── voters.php
│   ├── results.php
│   └── ...
├── candidate/         # Candidate portal
│   ├── dashboard.php
│   ├── profile.php
│   ├── results.php
│   └── ...
├── voter/             # Voter portal
│   ├── dashboard.php
│   ├── active_elections.php
│   ├── results.php
│   └── ...
├── public/            # Public-facing pages
│   ├── index.php
│   ├── login.php
│   ├── register.php
│   └── ...
├── includes/          # Shared code
│   ├── db.php
│   ├── auth.php
│   ├── image_utils.php
│   ├── navbar.php
│   ├── footer.php
│   └── ...
├── uploads/           # Candidate images
│   └── .htaccess
└── assets/            # Static files (SVG, icons)

Usage
Public Users
•	Access the landing page: /EMS2/public/index.php.
•	View live stats and featured candidates.
•	Register or log in via /EMS2/public/register.php or /EMS2/public/login.php.
Admin Workflow
•	Create/manage elections (admin/elections.php).
•	Add/manage candidates and voters (admin/candidates.php, admin/voters.php).
•	View analytics and results (admin/results.php).
Voter Workflow
•	View active elections (voter/active_elections.php).
•	Cast votes and later view results (voter/results.php).
Candidate Workflow
•	Manage profile and photo (candidate/profile.php).
•	Track performance and vote share (candidate/results.php).
Results Visualization
•	Libraries: Chart.js (via CDN).
•	Locations:
o	admin/results.php: Bar and pie charts (votes per candidate, vote share).
o	candidate/results.php: Horizontal bar chart (ranking) and pie chart (personal vote share).
Getting Started
1.	Environment Setup
o	Install XAMPP (Apache, PHP, MySQL).
o	Place project in C:\xampp\htdocs\EMS2.
2.	Database
o	Create a database (e.g., election_db).
o	Import/create required tables (schema to be added).
3.	Configure Connection
o	Update database credentials in includes/db.php.
4.	Run Application
o	Start Apache & MySQL in XAMPP.
o	Visit: http://localhost/EMS2/public/index.php.
o	Register or log in to begin using the system.

 The project developed by   Ali Muse Abshir 
