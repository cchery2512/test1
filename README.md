### Before run the app you going to need

- Install Docker

- Install PHP >=8.1

- Install the symfony-cli

- Run command: composer install

- Run command: docker-compose up -d (for up the mysql and redis services)

- For get the port numbers of the mysql and redis services you can put the command: docker-compose ps

- Run command: symfony server:start

### CurrencyRatesCommand
## Description
- We can get the base of our currency in other currencies

## signature
- app:currency:rate [base_currency] [target_currency_1] [target_currency_2] ... [target_currency_n]

## Example
- Run command: php bin/console app:currency:rates EUR USD GBP

## How the cron job was defined
- First we open the crontab file for editing with the command: crontab -e
- And then we write: 0 1 * * * cd [PROJECT_ROUTE] php bin/console app:currency:rates [base_currency] [target_currency_1] [target_currency_2] ... [target_currency_n] >> chmod +w [PROJECT_ROUTE]/log/cron.log 2>&1
- The values ​​of: ['PROJECT_ROUTE'], ['base_currency'] and ['target_currency_#'] must be replaced by those corresponding to their respective equipment or needs

### Run the 'exchange-rates' API

## Description
- We can get the base of our currency in other currencies fron the database or redis/cache

## signature
- GET /api/exchange-rates=[base_currency]&target_currencies=[target_currency_1,target_currency_2,...,target_currency_n]

## Example
- Put in you postman: http://127.0.0.1:8000/api/exchange-rates?base_currency=EUR&target_currencies=USD,GBP in GET

### Before run unit test you going to need
## create the test database
- php bin/console --env=test doctrine:database:create

## create the tables/columns in the test database
- php bin/console --env=test doctrine:schema:create

- Empty the database and reload all the fixture classes with: php bin/console --env=test doctrine:fixtures:load

## Run all unit test of the application
- php bin/phpunit