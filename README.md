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
3. [Admin Panel Access](#admin-panel-access)
   - Super admin access
   - Read only admin access
   - User access restrictions
   - CRUD operations for tasks
   - Creating new users
4. [API Authentication (Tokens)](#api-authentication-tokens)
   - Generating an API Token
   - Using the token
   - Validating the token
5. [API Documentation](#api-documentation)
6. [Testing](#testing)
7. [TroubleShooting](#troubleshooting)

## Project Setup

### Prerequisites

- Ensure you have **Docker** installed and running on your machine.  
  You can download and install Docker from [https://www.docker.com/products/docker-desktop](https://www.docker.com/products/docker-desktop).

### Clone the Repository

```bash
git clone https://github.com/your-username/dts-developer-challenge.git
cd dts-developer-challenge
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

**Note** You man need to wait until the entry-point script has completed before seeding the database.  You can check it's progress using **docker logs laravel_app**.

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

[localhost/api/documentation](http://localhost/api/documentation)

This documentation provides an overview of all available endpoints, the required parameters, and the response formats. It will help you to understand how to interact with the API, including details on authentication, data retrieval, and data manipulation.

## API Testing
You can run the unit tests but entering the following command:

```bash
docker compose run --rm app_test php artisan test
```

This command requires some extra composer packages and will install these before completing the unit tests.

## Troubleshooting
You can run the unit tests but entering the following command:

### 1. Port Conflicts

* If the application or database container tries to use a port that is already in use on the local machine, it could cause a conflict.

Solution:

* Users can either stop the conflicting service or modify the Docker docker-compose.yml file to map the container ports to unused ports on the host machine.

### 2. Docker Not Running

* If Docker isn’t running or the Docker daemon isn’t properly initialized, users will get errors related to container startup or management.

Solution:

* Make sure Docker Desktop (or Docker Engine) is running and accessible.*

### 3. Docker Network Issues

* If containers can't communicate with each other (e.g., the Laravel app cannot reach the PostgreSQL container due to network misconfigurations), the application might fail to start or connect to the database.

Solution:

* Verify that the containers are on the same Docker network. This should happen automatically if you’re using Docker Compose, but it's good to check the network settings.

### 4. File Permission Errors:

* Docker containers sometimes encounter permission issues, especially when trying to write to volumes or certain directories like storage or bootstrap/cache.

Solution:

* Running the correct permission commands (chmod and chown) inside the container as you've done should resolve this issue.

### 5. Missing or Incorrect .env Configuration:

* If users forget to configure certain environment variables, such as database connection details, it can cause errors when the app tries to connect to those services.

Solution:

* Ensure users copy the .env.example file to .env and configure all necessary environment variables.

### 6. PHP or Composer Issues Inside the Container:

* If there are issues with the PHP environment inside the container (e.g., missing PHP extensions, outdated packages), users might encounter errors.

Solution:

* Running docker-compose exec app php artisan --version or composer install from inside the container can help diagnose these issues.

### 7. App Key Generation:

* If the app key is missing or not generated properly, it can lead to errors related to encryption or sessions.

Solution:

* Ensure the app key is generated, either manually with php artisan key:generate or automatically by the container startup script.

### 8. Database Connection Issues:

* If the database container isn’t running or if the database user/password in the .env file is misconfigured, users will encounter database connection errors.

Solution:

* Check the logs with docker logs <container_name> to diagnose any database startup issues, and verify that the database configuration in .env is correct.

### 9. Cache Issues:

* Sometimes Laravel’s cache or configuration files can become stale or corrupted, especially if there have been changes to the environment or configuration files.

Solution:

* Running php artisan config:clear and php artisan cache:clear can help resolve these issues.

