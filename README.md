# Meddy Voting System

A modern and secure web-based voting system built with PHP and MySQL, featuring a responsive design and real-time vote tracking.

## Tech Stack

### Backend
- **PHP 7.4+** - Server-side scripting language
- **MySQL 5.7+** - Database management system
- **Apache 2.4+** - Web server
- **PDO** - Database abstraction layer
- **Bootstrap 5** - Frontend framework
- **Chart.js** - Data visualization
- **Bootstrap Icons** - Icon library

### Frontend
- **HTML5** - Markup language
- **CSS3** - Styling
- **JavaScript** - Client-side scripting
- **Bootstrap 5** - UI components and layout
- **jQuery** - DOM manipulation and AJAX
- **Font Awesome** - Icons

## Features

- **User Authentication**
  - Secure login and registration
  - Role-based access control (Admin and Voter roles)
  - Session management

- **Voting Management**
  - Real-time vote tracking
  - Multiple position support
  - Candidate profiles with images and bios
  - Vote time restrictions
  - One vote per user enforcement

- **Admin Features**
  - Dashboard with voting statistics
  - Candidate management (Add, Edit, Delete)
  - Position management
  - Voting time settings
  - User management
  - Results monitoring

- **Security Features**
  - Password hashing
  - SQL injection prevention
  - XSS protection
  - CSRF protection
  - Session security

## System Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache Web Server
- Modern web browser with JavaScript enabled
- mod_rewrite enabled (Apache)
- PHP Extensions:
  - PDO
  - PDO_MySQL
  - GD (for image processing)
  - mbstring
  - json

## Installation

1. **Database Setup**
   ```sql
   CREATE DATABASE meddy_voting_system;
   USE meddy_voting_system;
   ```

2. **Configure Database Connection**
   - Navigate to `config/config.php`
   - Update database credentials:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'your_username');
     define('DB_PASS', 'your_password');
     define('DB_NAME', 'meddy_voting_system');
     ```

3. **File Permissions**
   ```bash
   # Set directory permissions
   chmod 755 -R /path/to/meddy_voting_system
   chmod 777 -R /path/to/meddy_voting_system/uploads
   chmod 777 -R /path/to/meddy_voting_system/logs
   ```

4. **Initial Setup**
   - Import the database schema
   - Create an admin account
   - Configure voting settings

## Running the System

### Local Development Setup

1. **Using XAMPP/WAMP/MAMP**
   ```bash
   # 1. Install XAMPP/WAMP/MAMP
   # 2. Place the project in the web server directory:
   #    XAMPP: C:\xampp\htdocs\meddy_voting_system
   #    WAMP: C:\wamp\www\meddy_voting_system
   #    MAMP: /Applications/MAMP/htdocs/meddy_voting_system
   
   # 3. Start the servers
   #    - Start Apache
   #    - Start MySQL
   
   # 4. Access the application
   http://localhost/meddy_voting_system
   ```

2. **Using Docker (Alternative)**
   ```bash
   # 1. Install Docker and Docker Compose
   
   # 2. Create docker-compose.yml
   version: '3'
   services:
     web:
       image: php:7.4-apache
       ports:
         - "8080:80"
       volumes:
         - ./:/var/www/html
     db:
       image: mysql:5.7
       environment:
         MYSQL_ROOT_PASSWORD: root
         MYSQL_DATABASE: meddy_voting_system
       ports:
         - "3306:3306"
   
   # 3. Run the containers
   docker-compose up -d
   
   # 4. Access the application
   http://localhost:8080
   ```

### Production Deployment

1. **Apache Configuration**
   ```apache
   # .htaccess in the project root
   RewriteEngine On
   RewriteBase /meddy_voting_system/
   RewriteCond %{REQUEST_FILENAME} !-f
   RewriteCond %{REQUEST_FILENAME} !-d
   RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]
   ```

2. **PHP Configuration**
   ```ini
   # php.ini settings
   upload_max_filesize = 10M
   post_max_size = 10M
   max_execution_time = 300
   memory_limit = 256M
   ```

3. **Database Configuration**
   ```sql
   -- Create a dedicated database user
   CREATE USER 'meddy_user'@'localhost' IDENTIFIED BY 'secure_password';
   GRANT ALL PRIVILEGES ON meddy_voting_system.* TO 'meddy_user'@'localhost';
   FLUSH PRIVILEGES;
   ```

### Running the Application

1. **First-time Setup**
   ```bash
   # 1. Access the application
   http://your-domain/meddy_voting_system
   
   # 2. Create admin account
   - Navigate to /register.php
   - Register as admin
   - Set up voting settings
   ```

2. **Regular Usage**
   ```bash
   # 1. Start the servers
   - Start Apache
   - Start MySQL
   
   # 2. Access the application
   http://your-domain/meddy_voting_system
   
   # 3. Admin tasks
   - Login to admin dashboard
   - Manage positions and candidates
   - Configure voting times
   
   # 4. Voter tasks
   - Register/Login
   - Cast votes
   - View results
   ```

3. **Maintenance**
   ```bash
   # 1. Regular backups
   mysqldump -u username -p meddy_voting_system > backup.sql
   
   # 2. Log monitoring
   tail -f /path/to/error.log
   
   # 3. Security updates
   - Update PHP
   - Update MySQL
   - Update dependencies
   ```

## Usage

### Admin Access
1. Login with admin credentials
2. Access the admin dashboard
3. Manage positions and candidates
4. Configure voting times
5. Monitor results

### Voter Access
1. Register/Login with voter account
2. View available positions and candidates
3. Cast votes during active voting period
4. View results (if enabled)

## Directory Structure

```
meddy_voting_system/
├── admin/              # Admin control panel
├── assets/            # CSS, JS, and images
├── config/            # Configuration files
├── includes/          # PHP includes
├── uploads/           # Uploaded images
└── vendor/            # Dependencies
```

## Security Considerations

- Keep PHP and MySQL updated
- Use HTTPS in production
- Regularly backup the database
- Monitor error logs
- Update passwords periodically
- Implement rate limiting
- Use prepared statements

## Known Issues

- Vote recording may require additional debugging
- Working on improving transaction handling
- Enhancing error reporting

## Future Improvements

- [ ] Enhanced vote verification
- [ ] Email notifications
- [ ] Two-factor authentication
- [ ] Improved analytics
- [ ] Ballot preview
- [ ] Vote receipt generation
- [ ] API integration
- [ ] Audit logging

## Support

For issues and support:
- Create an issue in the repository
- Contact system administrator
- Check error logs in the server

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Credits

Developed and maintained by [Your Organization/Name]

## Version History

- v1.0.0 - Initial release
  - Basic voting functionality
  - Admin dashboard
  - User authentication

## Contributing

1. Fork the repository
2. Create a feature branch
3. Commit changes
4. Push to the branch
5. Create a Pull Request 