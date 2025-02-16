# For Using Docker : Clone source code, and build as Docker Compose

## Step 1: Install Dependencies

    composer install

## Step 2: Change Owner and Permission

You must change source code to user root:www-data before build as docker running; otherwise, it will not be running as Fail File Permission

    chown -R root:www-data .
    # OR
    chown -R www-data:www-data .
    
    # Optional
    chmod -R 775 .

## Step 3: Build and Run Docker Compose

    docker-compose up -d --build

## Step 4: Check Whether All Service Running - App-Laravel, Postgres, Nginx

    docker compose ps -a

## Step 5: Create Admin Role User for Login

    docker-compose exec app php artisan app:create-admin-user

## Step 6: Shutdown Docker for All Service

    docker-compose down -v

## * Be Aware: docker prune with -a can delete unused containers, networks, images (both dangling and unused)

    docker system prune -a

# For Development Running : What to do, after Clone project

In Laravel projects, it's common to see many files ignored by Git due to the default .gitignore file that Laravel includes. This file is designed to prevent unnecessary files and directories from being tracked by version control, ensuring that your repository stays clean and only contains important source code and configuration files.

Now, after clone this project, assume you already install composer and php (if not, please refer to document or GPT), you need:

## Step 1: Install Dependencies

    composer install

## Step 2: Change Configure .env To Your Need
You can make update .env with your database and application settings. For example, use SQLLite for Development first instead of postgres **OR** delete old and copy new one from .env.example

    nano .env

## Step 3: Generate App Key

    php artisan key:generate

## Step 4: Run Migrations (if using a database)
This step will prompt to create **database.sqlite**, just write **yes** to create first

    php artisan migrate

## Step 5: Run the Application

    php artisan serve

# I. Register and Login Email/Password with - admin OR user - role

To have admin role to access all function in this project, use command below:

    php artisan app:create-admin-user

- Enter username
- Enter email so that can use to login
- Enter password 

For user role, you can register in GUI

# II. Send Cuisine Data using API

You can send data (cuisine) to the API filename - **Online_Food.postman_collection.json** - and upload this file to any tool such as **Postman**, etc.

# III. Command for Laravel Project

## Run project

        php artisan serve

## Create project laravel
        
        composer create-project --prefer-dist laravel/laravel online_food

## After download this project. You need to reload image : 
- Delete storage located - public/storage - Otherwise, if not found, can just execute script below
- Then can execute below command

        php artisan storage:link

## Install routes api

        php artisan install:api

## Create controller, model, view (blade)

        php artisan make:controller CuisineController --resource
        php artisan make:model Cuisine -m
        php artisan make:view admins/cuisines/index

## Create migration and rollback

        php artisan make:migration add_role_to_user_table
        php artisan migrate:rollback

## Create super admin user and Run with - **app:create-admin-user** - you can also change this command in **CreateAdminUser** file
        php artisan make:command CreateAdminUser
        php artisan app:create-admin-user
