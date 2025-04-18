# Meddy Voting System

A secure and user-friendly online voting system built with PHP and MySQL.

## Features

- User registration and authentication
- Secure voting system with one vote per user
- Real-time voting results
- Admin dashboard for managing candidates and viewing statistics
- Responsive design for mobile and desktop
- Security features including password hashing and SQL injection prevention

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache web server
- mod_rewrite enabled

## Installation

1. Clone the repository to your web server directory:
```bash
git clone https://github.com/yourusername/meddy_voting_system.git
```

2. Create a MySQL database and import the schema:
```bash
mysql -u root -p
CREATE DATABASE voting_system;
USE voting_system;
source config/schema.sql;
```

3. Configure the database connection:
- Copy `config/config.php` to `config/config.local.php`
- Update the database credentials in `config/config.local.php`

4. Set proper permissions:
```bash
chmod 755 -R /path/to/meddy_voting_system
chmod 777 -R /path/to/meddy_voting_system/images
```

5. Access the application through your web browser:
```
http://localhost/meddy_voting_system
```

## Default Admin Account

- Username: admin
- Password: admin123
- Email: admin@example.com

**Important:** Change the admin password after first login!

## Directory Structure

```
meddy_voting_system/
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
├── config/
│   ├── config.php
│   └── schema.sql
├── includes/
│   ├── functions.php
│   ├── header.php
│   └── footer.php
└── various PHP files
```

## Security Features

- Password hashing using PHP's password_hash()
- Prepared statements to prevent SQL injection
- CSRF token protection
- XSS prevention through input sanitization
- Session security measures
- Rate limiting on login attempts

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a new Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support, please open an issue in the GitHub repository or contact the maintainer. 