# E-commerce Website

This is a fully functional e-commerce website built with PHP and MySQL. It includes features for both customers and administrators.

## Features

### Customer-facing
- User registration and login
- Product browsing and searching
- Product categorization
- Shopping cart functionality
- Wishlist for saving products
- Secure checkout process
- Order history and tracking
- User profile updates

### Admin Panel
- Admin authentication
- Dashboard with an overview of sales and products
- Product management (add, update, delete products)
- Order management
- User account management
- View customer messages

## Technologies Used

- **Frontend:** HTML, CSS, JavaScript
- **Backend:** PHP
- **Database:** MySQL
- **Server:** Apache (or any other web server that supports PHP)

## Setup and Installation

1.  **Clone the repository:**
    ```bash
    git clone https://github.com/sakshikarande26/virtual-assistant-enabled-e-commerce-web-app.git
    ```

2.  **Database Setup:**
    - Import the `shop_db.sql` file into your MySQL database.
    - Update the database connection details in `components/connect.php`.

3.  **Web Server:**
    - Place the project files in the root directory of your web server (e.g., `htdocs` for XAMPP, `www` for WAMP).

4.  **Access the website:**
    - Open your web browser and navigate to `http://localhost/your_project_folder/`.

## Usage

- **Admin Login:** Navigate to `/admin/admin_login.php` to access the admin panel.
- **User Login:** Users can register and log in from the main website.
