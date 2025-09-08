---
description: Repository Information Overview
alwaysApply: true
---

# Election Management System (EMS) Information

## Summary
The Election Management System (EMS) is a web-based application designed to manage elections, candidates, voters, and results. It provides a secure platform for conducting elections with different user roles (admin, voter, candidate) and features like candidate registration, election management, and result tracking.

## Structure
- **admin/**: Admin dashboard and management interfaces
- **candidate/**: Candidate-specific pages and functionality
- **includes/**: Core functionality, database connection, and authentication
- **public/**: Public-facing pages including login and registration
- **uploads/**: Storage for uploaded candidate images
- **voter/**: Voter-specific pages and functionality

## Language & Runtime
**Language**: PHP
**Version**: Compatible with PHP 7.x/8.x
**Database**: MySQL/MariaDB
**Frontend**: HTML, CSS (Tailwind CSS 2.2.19), JavaScript (Chart.js)

## Dependencies
**Main Dependencies**:
- Tailwind CSS (2.2.19) - loaded via CDN
- Chart.js - for results visualization
- MySQL/MariaDB database
- PHP Session handling

## Installation & Setup
```bash
# Database setup
# Create a MySQL database named 'election_db'
# Import the database schema (not provided in the repository)

# Web server configuration
# Configure web server (Apache/Nginx) to point to the EMS2 directory
# Ensure PHP is properly configured with MySQL support
```

## Authentication
**Mechanism**: PHP Session-based authentication
**User Roles**: 
- Admin: Full system management
- Voter: Vote in elections, view results
- Candidate: Register for elections, view results

## Main Components
**Core Files**:
- `includes/db.php`: Database connection
- `includes/auth.php`: Authentication and authorization
- `public/login.php`: User authentication entry point
- `admin/dashboard.php`: Admin control panel
- `voter/dashboard.php`: Voter interface
- `candidate/dashboard.php`: Candidate interface

## User Interfaces

### Admin Interface
- Complete election management dashboard
- View and manage candidates with their profile images
- View comprehensive election results with visual charts (bar and pie charts)
- Manage voters and system settings

### Voter Interface
- Dashboard showing election statistics
- View active elections to participate in
- Register as a candidate
- View results of all candidates they voted for
- Access their voting history and profile

### Candidate Interface
- Profile management
- Election participation
- View election results

## Image Management
- Candidate images are stored in the `/uploads/` directory
- Images are displayed in candidate listings and profiles
- Admin can view candidate photos in the candidate management section
- Image paths are stored in the database and referenced in HTML
- Default placeholder image is shown when no image is available

## Results Visualization
- Admin can view comprehensive results with visual charts (bar and pie)
- Voters can view results of candidates they voted for
- Results include vote counts and percentages
- Chart.js is used for graphical representation of voting data

## Known Issues and Fixes

### Voter Results Display Issue
**Problem**: Voters cannot see results after voting for candidates.
**Location**: `voter/results.php`
**Issue Details**: The SQL query in the results page has a problem with the JOIN conditions and filtering.

**Fix**:
```php
// Replace the current SQL query in voter/results.php with:
$sql = "
    SELECT 
        c.full_name AS candidate_name,
        c.party AS party,
        e.title AS election_title,
        c.profile_image,
        COUNT(v2.id) AS total_votes
    FROM votes v
    JOIN candidates c ON v.candidate_id = c.id
    JOIN elections e ON v.election_id = e.id
    LEFT JOIN votes v2 ON c.id = v2.candidate_id
    WHERE v.voter_id = ?
    GROUP BY c.id, c.full_name, c.party, e.title, c.profile_image
    ORDER BY e.id DESC
";
```

**Additional UI Fix**:
```php
// Add this to display candidate images in the results table:
<td class="border-b py-2 px-4">
    <?php if (!empty($row['profile_image'])): ?>
        <img src="../uploads/<?= htmlspecialchars($row['profile_image']) ?>" 
             alt="<?= htmlspecialchars($row['candidate_name']) ?>" 
             class="w-10 h-10 rounded-full object-cover">
    <?php else: ?>
        <img src="https://via.placeholder.com/40?text=No+Photo" 
             alt="No Photo" class="w-10 h-10 rounded-full">
    <?php endif; ?>
</td>
```

## Security Considerations
- Password storage uses plain text (security vulnerability)
- SQL queries are vulnerable to SQL injection
- No CSRF protection implemented
- Session management is basic