<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Laravel Ambassador

This is a laravel project built with a monolithic approach. It contains such of Api endpoints, which use a different concepts, such as events, jwt tokens for authentication, CRUD, laravel commands, seeders, database factories and more. It uses also a set of tools as Stripe for checkout, Mailhog for catching emails locally and Redis for memory cache. 

It uses docker containers for local development and can also be deployed with this approach on whatever Cloud platform you want such as Google Cloud Platform, Amazon Web Services or Microsof Azure. For this purpose, some changes are needed to be performed.

## System Requirements

- PHP >= 7.4
- Composer
- Docker
- Git

## Installation guide

First, clone this repo with this command
    
    git clone https://github.com/ghilesfeghoul/ambassador.git

Then move to the ambassador directory and install all dependencies

    cd ambassador
    composer install

After that, create .env file

    mv .env.example .env

Make sure you have a Stripe account as a development mode and copy the secret to .env in a STRIPE_SECRET variable. 

Add also CHECKOUT_URL variable needed for the checkout side of the API. This variable is in the fact, the Frontend url, but by default, it can be set to your backend url while we have no frontend for now. 

    #.env file
    ...
    STRIPE_SECRET=<YOUR_STRIPE_SECRET_VARIABLE>
    CHECKOUT_URL=<FRONTEND_APP_URL>

After that, start all docker containers

    docker-compose up --build

Then, connect to the backend container and run these commands

    php artisan key:generate
    php artisan migrate
    php artisan db:seed

If you want to see if everything is running OK, you can retrieve some products record from database by executing this command inside docker container

    php artisan Tinker
    Product::all()

Then go to http://localhost:8000. You will see all things run correctly.



## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
