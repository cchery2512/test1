### Before run the app you going to need

- Install Docker

## For run the application write this command in the console:
- docker-compose build && docker-compose up -d

## Important: the port of the application is the 14000 => http://127.0.0.1:14000

### CurrencyRatesCommand
## Description
- We can get the base of our currency in other currencies

## signature
- app:currency:rate [base_currency] [target_currency_1] [target_currency_2] ... [target_currency_n]

## Example
- Run command: docker-compose exec web php bin/console app:currency:rates EUR USD GBP ARS MXN PAB COL CRC COP AED BRL

## The CRON JOB is defined in the line 31 of the Dockerfile

### Run the 'exchange-rates' API

## **UPDATE**
- If the input values ​​are correct but the data is not found in the database, it will proceed to consult directly from the https://openexchangerates.org api and once the information is obtained it will be stored in the database. 

## Description
- We can get the base of our currency in other currencies fron the database or redis/cache

- In the event that the input values ​​are incorrect (for example, they enter a target_currency that does not exist) a BAD REQUEST error message will be returned.

## signature
- GET /api/exchange-rates=[base_currency]&target_currencies=[target_currency_1,target_currency_2,...,target_currency_n]

## Example
- Put in you postman: http://127.0.0.1:14000/api/exchange-rates?base_currency=EUR&target_currencies=USD,GBP in GET

## Run all unit test of the application
- docker-compose exec web php bin/phpunit

### Connection to the database outside of docker (with fixed ports)

## The credentials are:
- user:     root
- pasword:  password
- database: main
- host:     localhost
- port:     3307

## Note:
- If you have problems with the connections permisions you can add this param at the connection URL: ?allowPublicKeyRetrieval=true&useSSL=false