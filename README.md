### Installation

- Install Docker

- Install PHP >=8.1

- Run command: composer install

- Run command: docker compose up -d

## For Windows
# Description
For window required additional configuration

# Install Scoop
- Run command: Set-ExecutionPolicy RemoteSigned -Scope CurrentUser # Optional: Needed to run a remote script the first time

- Run command: irm get.scoop.sh | iex

# Install Symfony CLI 
- Run command: scoop install symfony-cli

### CurrencyRatesCommand
## Description
- We can get the base of our currency in other currencies

## signature
- app:currency:rate currency currency currency

## Example
- Run commandL: php bin/console app:currency:rates EUR USD GBP