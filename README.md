# Restaurant Order - Order Processing Backend System

Welcome to the Restaurant Order order processing system repository. This project is a REST API built with the Laravel framework to manage the order processing for a restaurant. This README will provide an overview of the project, setup and usage instructions, as well as guidelines for contributions.

## Prerequisites

Before getting started, make sure you have the following prerequisites installed:

-   PHP (8+ recommended)
-   Composer
-   Laravel (10.x)
-   Database (e.g., MySQL)
-   Web server (e.g., Apache, Nginx)

## Installation

1. Clone this repository to your local machine:

    ```bash
     git clone https://github.com/mthiagoalves/restaurant_API.git

2. Navigate to the project directory:

    ```bash
        cd restaurant_API

3. Install Composer dependencies:

    ```bash
        composer install

4. Create an environment file from the example file:

    ```bash
        cp .env.example .env

5. Configure the environment variables in the .env file to match your database configuration and other settings.

6. Generate an application key:

    ```bash
        php artisan key:generate

7. Run database migrations to create the necessary tables:

    ```bash
        php artisan migrate

8. Start the development server:

    ```bash
        php artisan serve

**The API is now available at 'http://localhost:8000/api/v1'


