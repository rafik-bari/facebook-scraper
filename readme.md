 

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/license.svg" alt="License"></a>
</p>


An API-based scraper to scrape emails of Facebook pages admins ( and groups )

## Installation
    composer install
    
and then migrate the databases and seed with demo access tokens and page fields:

    php artisan migrate
 
    php artisan db:seed
    
install supervisor:

    sudo apt-get install supervisor
    
Sign Up on the site and login to your account. 
