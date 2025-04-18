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
   - Run initialisation commands
4. [Admin Panel Access](#admin-panel-access)
   - Admin User Roles
   - Admin Panel URL and Login Credentials
   - Changing Admin Credentials
5. [API Authentication (Laravel Sanctum)](#api-authentication-laravel-sanctum)
   - Generate API Token
   - Using Postman for API Requests
6. [API Documentation](#api-documentation)
7. [Running Migrations & Seeding the Database](#running-migrations--seeding-the-database)
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
* DB_CONNECTION: Configure your database connection (e.g., mysql).
* DB_PORT: Specify the database port (default: 3306).
* DB_DATABASE: Set your database name.
* DB_USERNAME: Set your database username.
* DB_PASSWORD: Set your database password.

**Note:** Be sure to leave DB_HOST as postgres because the PostgreSQL container will be running in the same Docker network.


**Note:** If you leave APP_KEY blank, an app key will be generated for you by the docker entry-point script. In a production environment this should be set manually with php artisan key:generate.

3. PGAdmin Configuration (Optional)

If you are using pgAdmin for managing your PostgreSQL database, make sure to configure the following in your .env file:

PGADMIN_USERNAME: Set the username for pgAdmin access.
PGADMIN_PASSWORD: Set the password for pgAdmin access.

4. **Save the .env file after making the necessary changes.**

### Running the Application Locally

1. Start the application using Docker

This command will build the Docker containers (if needed) and start them in the background.

```bash
docker compose up --build -d
```

2. Set the correct permissions

Once the containers are running, set the appropriate permissions for the storage and bootstrap/cache directories. These are required by Laravel to ensure logs and cached files can be written.

```bash
docker exec -it laravel_app chmod -R 775 storage bootstrap/cache
docker exec -it laravel_app chown -R www-data:www-data storage bootstrap/cache
```

3. Seed the database

This command will populate the database with initial data, including a default admin user and some dummy records for testing the API.

```bash
docker exec -it laravel_app php artisan db:seed
```

**The application should now be running and accessible at [http://localhost](http://localhost).**

## Loading the Apps Admin Panel
