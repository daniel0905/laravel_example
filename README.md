## Postman collection
   laravel_example/Laravel example.postman_collection.json
## Description
1. Create the api module to order books of the store
2. This source code is not using on the fact
3. I just try to show my coding style and how I use the laravel
4. Many function is not completed
5. The api which is implemented:
- For authentication
  - Login
  - Logout
  - Register
  - Change password
  - Refresh
- For order Books
  - Search order
  - Get order
  - Create order
  - Update order
  - Delete order

## Requirement
- PHP 8.0
- MySql 5
- Composer

## Getting Started
1. Download the source code
2. Change the database setting at \.env  file
   ```
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE={Database name}
    DB_USERNAME=root
    DB_PASSWORD={Database password}
    ```
3. Go to the root folder of project and run composer command:
    ```
   composer install
    ```
3. Run migration and import the example data by run the command below:
    ```
   php artisan migrate --seed
    ```
4. Start the server:
    ```
   php artisan serve
    ```
