## Laravel API Webshop

This is a Laravel-based API webshop project. It is dockerized for easy setup and testing.

## Prerequisites

Make sure you have Docker installed on your system

## Setup

1. Clone the repository:

    ```bash
    git clone https://github.com/sararzbn/api-webshop.git
    ```

2. Navigate to the project directory:

    ```bash
    cd api-webshop
    ```

3. Build and start the Docker containers:

    ```bash
    docker-compose up --build
    ```

   This command will build the Docker images and start the containers.


4. Access the application

   Open your web browser and go to [localhost:8001](localhost:8001).


5. Run Migrations

   In another terminal window, run the migrations to set up the database:

    ```bash
    docker-compose exec app php artisan migrate
    ```
6. Importing Masterdata

    The import is facilitated by the ImportMasterdata command, which leverages Laravel's Artisan Console to execute the
    import process. Here's an overview of the command:

    ```bash
    docker-compose exec app php artisan import:masterdata
    ```

## Testing

To run the tests, use the following command:

```bash
docker-compose exec app ./vendor/bin/phpunit
