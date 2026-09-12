# Admin-Panel-Shemmy-Gaviyao-Official-
# Discipleship Nation Web Platform
A dynamic PHP and MySQL content management platform built for **Discipleship Nation** and **Shemmy Gaviyawo Official**, deployed on cPanel hosting.
## Project File Structure
```text
/public_html/
├── config.php                  # Global PDO database connection and dynamic BASE_URL
├── index.php                   # Public frontend view (devotionals, videos, audio, books, partnership)
├── uploads/                    # Directory for uploaded images, audio tracks, and PDF documents
└── admin/                      # Secure administrative portal
    ├── auth.php                # Session verification and security guard
    ├── login.php               # Admin login screen interface
    ├── logout.php              # Session destroyer and logout handler
    ├── index.php               # Main admin dashboard UI and list management table
    └── actions/                # Server-side upload and deletion processors
        ├── save-audio.php      # Processes audio and thumbnail uploads
        ├── save-video.php      # Processes YouTube video URL extraction
        ├── save-devotion.php   # Processes daily devotions and image uploads
        ├── save-book.php       # Processes book covers and PDF uploads
        └── delete-item.php     # Removes database records and local storage files

```
## Installation & Deployment Guide
 1. **Upload Files:** Upload your project files into your cPanel File Manager root or subdomain folder (e.g., public_html/discipleship-nation).
 2. **Create MySQL Database:**
   * Open **MySQL Database Wizard** in cPanel.
   * Create your database (e.g., upnodeco_discipleship_nation) and a database user with full privileges.
 3. **Run SQL Schema:**
   * Access **phpMyAdmin** from cPanel, select your database, click the **Import** tab, and execute your table creation queries (devotionals, videos, audio_tracks, books, partnership_content).
 4. **Configure config.php:** Update your database credentials ($db_user, $db_pass, $db_name) inside config.php.
 5. **Access Portal:** Navigate to your live URL. Access the backend management interface at /admin/login.php using username admin and password ShemmyAdmin2026!.
