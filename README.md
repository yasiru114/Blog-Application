# Blog Application - IN2120 Web Programming

A full-featured blog application built with HTML, CSS, JavaScript, PHP, and MySQL.

## Features

- **User Authentication**: Register, Login, Logout
- **Blog Management**: Create, Read, Update, Delete blogs
- **Authorization**: Users can only edit/delete their own blogs
- **Markdown Editor**: Write blogs with Markdown support and live preview
- **Responsive Design**: Works on desktop, tablet, and mobile
- **Clean UI**: Modern, minimal interface

## Project Structure

```
blog-application/
├── index.php              # Home page - lists all blogs
├── login.php              # User login
├── register.php           # User registration
├── logout.php             # Logout handler
├── create.php             # Create new blog post
├── edit.php               # Edit existing blog post
├── delete.php             # Delete blog post handler
├── view.php               # View single blog post
├── config.php             # Database configuration
├── auth.php               # Authentication functions
├── style.css              # Main stylesheet
├── script.js              # JavaScript functionality
├── includes/
│   ├── header.php
│   └── footer.php
├── sql/
│   └── schema.sql         # Database schema
└── README.md
```

## Database Schema

### user table
| Column   | Type          | Description              |
|----------|---------------|--------------------------|
| id       | INT (PK)      | User ID                  |
| username | VARCHAR(50)   | Unique username          |
| email    | VARCHAR(100)  | Unique email             |
| password | VARCHAR(255)  | Hashed password          |
| role     | VARCHAR(20)   | User role (default: user)|

### blogPost table
| Column     | Type          | Description                    |
|------------|---------------|--------------------------------|
| id         | INT (PK)      | Blog post ID                   |
| user_id    | INT (FK)      | Author user ID                 |
| title      | VARCHAR(255)  | Blog title                     |
| content    | TEXT          | Blog content (Markdown)        |
| created_at | TIMESTAMP     | Creation timestamp             |
| updated_at | TIMESTAMP     | Last update timestamp          |

## Setup Instructions (Local Development)

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher (or MariaDB)
- Web server (Apache/Nginx) or use PHP's built-in server

### Steps

1. **Clone or download the project**
   ```bash
   git clone <your-repo-url>
   cd blog-application
   ```

2. **Create the database**
   ```sql
   CREATE DATABASE blog_app;
   ```

3. **Import the schema**
   ```bash
   mysql -u root -p blog_app < sql/schema.sql
   ```
   Or run the SQL commands manually in phpMyAdmin.

4. **Configure database connection**
   Edit `config.php` and update your database credentials:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', 'your_password');
   define('DB_NAME', 'blog_app');
   ```

5. **Start the development server**
   ```bash
   php -S localhost:8000
   ```

6. **Access the application**
   Open http://localhost:8000 in your browser

## Hosting Instructions

### InfinityFree (Recommended - Free)

1. Sign up at [InfinityFree](https://infinityfree.net/)
2. Create a new hosting account
3. Create a MySQL database from the control panel
4. Upload all files via FTP (FileZilla) to `htdocs/` folder
5. Update `config.php` with your InfinityFree database credentials:
   - Host: `sqlXXX.infinityfree.com`
   - Username: Your database username
   - Password: Your database password
   - Database name: Your database name
6. Import `sql/schema.sql` using phpMyAdmin

### Other Free Hosts
- 000WebHost
- AwardSpace
- Hostinger (free tier)

**Important**: Update `config.php` with the correct database credentials provided by your hosting service.

## Usage

### For Visitors
- View all blogs on the home page
- Click "Read More" to view a full blog post
- See author name and date on each post

### For Registered Users
1. **Register**: Create a new account with username, email, and password
2. **Login**: Sign in with your credentials
3. **Create Blog**: Click "New Post" to write a blog using Markdown
4. **Edit Blog**: Click "Edit" on your own posts only
5. **Delete Blog**: Click "Delete" on your own posts only
6. **Logout**: Sign out when done

### Markdown Support
The editor supports basic Markdown:
- **Bold**: `**text**`
- *Italic*: `*text*`
- Headers: `# Header 1`, `## Header 2`
- Lists: `- item` or `1. item`
- Links: `[text](url)`
- Code: `` `code` ``

Use the "Preview" tab to see rendered output before publishing.

## Security Features

- Passwords are hashed using `password_hash()`
- Session-based authentication
- Authorization checks before edit/delete operations
- SQL injection prevention using prepared statements
- Input validation and sanitization
- XSS prevention with `htmlspecialchars()`

## Browser Compatibility

- Chrome (recommended)
- Firefox
- Safari
- Edge

## File Descriptions

| File | Purpose |
|------|---------|
| `index.php` | Lists all blog posts with preview |
| `login.php` | Authenticates users |
| `register.php` | Creates new user accounts |
| `create.php` | Form to write new blog posts |
| `edit.php` | Form to update existing posts |
| `view.php` | Displays full blog post |
| `auth.php` | Helper functions for auth checks |
| `config.php` | Database connection settings |
| `style.css` | All styling and responsive rules |
| `script.js` | Markdown preview, form validation |

## Screenshots

*(Add screenshots of your running application here)*

## License

This project is for educational purposes (IN2120 - Web Programming, University of Moratuwa).

## Author

[Your Name] - [Your Student Index Number]

## Submission Checklist

- [ ] All source code pushed to GitHub
- [ ] Application hosted online with working URL
- [ ] PDF document created with:
  - [ ] GitHub repository link
  - [ ] Hosted website link
- [ ] Demonstration video recorded (3 minutes)
- [ ] Folder renamed with student index number
- [ ] All files zipped and ready for submission

## Notes

- Make sure to never commit real database credentials to GitHub
- Use environment variables or a separate config file not in version control for production
- Test all features before recording the demo video
- Ensure the hosted version has the same functionality as local

---

**Course**: IN2120 - Web Programming  
**Year**: 2026  
**University**: University of Moratuwa
