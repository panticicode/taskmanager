<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About TaskManager

TaskManager is an intuitive task management application designed to streamline your workday. It simplifies the process of creating, tracking, and managing tasks, all within a single platform. Whether you're a student organizing homework assignments or a professional overseeing projects, TaskManager serves as your dependable companion for enhancing productivity.

### Setup

To begin, download the resource via the traditional zip file method or by using the clone command from the repository.

### Steps to install

Navigate to the directory where the repository was cloned and execute the following commands:

- sudo chmod -R 755 /var/www/html/tasks/
- cd /var/www/html/tasks.
- composer install.
- sudo mv .env.local .env
- sudo chmod -R 755 /var/www/html/tasks/
- php artisan migrate --seed (it will ask to create new db if not exist. Choose Yes)
- npm install && npm run build.

If all steps execute without errors, the application is ready for use. Proceed to the login screen to utilize the default user for task management. Alternatively, register a new user for task management purposes.

## Default User

- admin@tasks.local
- 123123123

## Enjoy!




# taskmanager
