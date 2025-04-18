# DTS Developer Challenge API

This is the API for the DTS Developer Challenge, built using Laravel 12 and Filament 3. The application provides a RESTful API with authentication powered by Laravel Sanctum, and includes an admin panel for managing users and roles. The API is documented using Swagger for easy integration and interaction.

---

## Table of Contents

1. [Project Setup](#project-setup)
   - Prerequisites
   - Clone the Repository
   - Environment Configuration
2. [Running the Application Locally](#running-the-application-locally)
   - Start the application using docker
   - Set the correct permissions
   - Seed the database
4. [Admin Panel Access](#admin-panel-access)
   - Super admin access
   - Read only admin access
   - User access restrictions
   - CRUD operations for tasks
   - Creating new users
5. [API Authentication (Tokens)](#api-authentication-tokens)
   - Generating an API Token
   - Using the token
   - Validating the token
6. [API Documentation](#api-documentation)
8. [Testing](#testing)
9. [Deployment](#deployment)
10. [Troubleshooting & FAQ](#troubleshooting--faq)
11. [Contributing](#contributing)
12. [License](#license)

## Project Setup

### Prerequisites

- Ensure you have **Docker** installed and running on your machine.  
  You can download and install Docker from [https://www.docker.com/products/docker-desktop](https://www.docker.com/products/docker-desktop).

### Clone the Repository

```bash
git clone https://github.com/your-username/dts-developer-challenge-api.git
cd dts-developer-challenge-api 
```

### Environment Configuration

1. Copy the .env.example file to .env

```bash
cp .env.example .env
```

2. Open the .env file and configure the following environment variables according to your preferences:

* APP_NAME: Set the application name (e.g., DTS Developer Challenge API).
* DB_DATABASE: Set your database name.
* DB_USERNAME: Set your database username.
* DB_PASSWORD: Set your database password.

**Note:** Be sure to leave DB_HOST as postgres because the PostgreSQL container will be running in the same Docker network.

**Note:** If you leave APP_KEY blank, an app key will be generated for you by the docker entry-point script. In a production environment this should be set manually with php artisan key:generate.

3. PGAdmin Configuration (Optional)

If you are using pgAdmin for managing your PostgreSQL database, make sure to configure the following in your .env file:

* PGADMIN_USERNAME: Set the username for pgAdmin access.
* PGADMIN_PASSWORD: Set the password for pgAdmin access.

4. **Save the .env file after making the necessary changes.**

## Running the Application Locally

### 1. Start the application using Docker

This command will build the Docker containers (if needed) and start them in the background.

```bash
docker compose up --build -d
```

### 2. Set the correct permissions

Once the containers are running, set the appropriate permissions for the storage and bootstrap/cache directories. These are required by Laravel to ensure logs and cached files can be written.

```bash
docker exec -it laravel_app chmod -R 775 storage bootstrap/cache
docker exec -it laravel_app chown -R www-data:www-data storage bootstrap/cache
```

### 3. Seed the database

This command will populate the database with initial data, including a default admin user and some dummy records for testing the API.

```bash
docker exec -it laravel_app php artisan db:seed
```

**The application should now be running and accessible at [http://localhost](http://localhost).**

## Admin Panel Access

The admin panel allows super admin users to manage application data, users, and roles. Only users with the correct credentials will be able to access the admin panel.

### 1. Super Admin Access
A seeded super admin user is provided with the following credentials to access the admin panel:

```bash
Email: super@super.com
Password: Admin123
```

Super admin users have full access to the application, including the ability to create, update, and delete users and roles, as well as modify application settings and data.

### 2. Read-Only Admin Access
A read-only admin user is provided with the following credentials to access the admin panel:

```bash
Email: admin@admin.com
Password: Admin123
```

Read-only admin users have limited access and can view data but cannot make changes. They do not have permission to create or modify users, roles or tasks.

### 3. User Access Restrictions
Only super admin and admin users can log in to the admin panel. Other users of the application will not have access to this feature.

Be cautious when deleting super admin users. Ensure that there is at least one super admin user in the system to maintain access to the admin panel.

Super admin users can change the details of any users and their passwords, including the seeded users (super@super.com, admin@admin.com), or create new super admin users before deleting the seeded ones. This ensures that the admin panel remains accessible after making changes.

### 4. CRUD Operations for Tasks
In the admin panel, super admin users will have the ability to perform CRUD (Create, Read, Update, Delete) operations on seeded "task" data.

This allows super admin users to manage the task data easily, adding, editing, or removing tasks as needed.

### 5. Creating New Users
Super admin users can create new users directly from the admin panel. To create a new user:

Log in as a super admin user.

Navigate to the Users section in the admin panel.

Click Create User and fill in the required details.

## API Authentication (Tokens)

To authenticate API requests, the application uses token-based authentication. Below are the steps to obtain an API token and use it in Postman for testing the endpoints.

### 1. Generating an API Token

To generate an API token, you need to log in as a user. You can use any the seeded users including the super admin or admin. Once logged in, you can request an API token.

* Make a post request to the following endpoint to authenticate:
```bash
POST /api/login
```

* Body (JSON)
```json
{
  "email": "super@super.com",
  "password": "Admin123"
}
```

* The response will contain an API token, which will look like this:
```json
{
  "token": "access token here",
  "user": "{...}"
}
```

### Using the token

To use the token in Postman (or similar) to authenticate requests:

1. Open Postman.
2. Go to the Authorization tab for the request you want to make.
3. Select Bearer Token from the dropdown.
4. Paste the API token you generated into the Token field.
5. Send the request, and the API will authenticate using the provided token.

### Validating the token

Once the token is included in the request, the application will validate it and process the request. If the token is invalid or expired, you will receive an authentication error.

## API Documentation
You can access the API documentation at the following URL:

[localhost/api/documentation] (http://localhost/api/documentation)

This documentation provides an overview of all available endpoints, the required parameters, and the response formats. It will help you to understand how to interact with the API, including details on authentication, data retrieval, and data manipulation.

## API Testing
You can access the API documentation at the following URL:

[localhost/api/documentation] (http://localhost/api/documentation)

This documentation provides an overview of all available endpoints, the required parameters, and the response formats. It will help you to understand how to interact with the API, including details on authentication, data retrieval, and data manipulation.