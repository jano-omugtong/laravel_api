
## About Project

This is a simple laravel application using passport oauth2 and RESTful api for users.

## Setup

clone this repo
cd to the repo folder
run
    ~/laravel_api$ composer install
    ~/laravel_api$ php artisan key:generate
create a .env file inside the project folder
modify the .env file:
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=database
    DB_USERNAME=user
    DB_PASSWORD=secret
run
    ~/laravel_api$ php artisan migrate
    ~/laravel_api$ php artisan passport:install
        or/if errors occur: php artisan passport:client --personal
run this to check the available routes
    ~/laravel_api$ php artisan route:list
run the routes (eg. Postman)


## Input Data

input data fields are the following
{
	"last_name": "Juan",
    "first_name": "Dela Cruz",
    "email": "jdc@something.com",
    "sex": 1,
    "civil_status": "S",
    "address": "Philippines",
    "nationality": "Filipino"
}


## Basis for sex and civil_status inputs

sex:
    The four codes specified in ISO/IEC 5218 are:
        0 = Not known,
        1 = male,
        2 = female,
        9 = not Applicable.
https://en.wikipedia.org/wiki/ISO/IEC_5218
        
 civil_status:
     An indicator to identify the legal marital status of a PERSON.
    National Codes:
        S	Single
        M	Married/Civil Partner
        D	Divorced/Person whose Civil Partnership has been dissolved
        W	Widowed/Surviving Civil Partner
        P	Separated
        N	Not disclosed
(https://www.datadictionary.nhs.uk/data_dictionary/attributes/p/person/person_marital_status_de.asp?shownav=1)
