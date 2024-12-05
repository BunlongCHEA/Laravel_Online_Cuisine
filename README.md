# I. Login Email and Password with - admin OR user - role

- You can login with this admin role

        admin@local.id  /  123456789

- Or login with this user role

        user@local.id  /  123456789

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
