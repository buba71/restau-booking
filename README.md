# Restaurant booking application

## Installation

````
git clone https://github.com/buba71/restau-booking.git

cd restau-booking

composer install 

yarn install

yar run build

````

---

## Create Database

````
symfony console doctrine:database:create

symfony console doctrine:schema: update --force
````

---

## Tests

````
make tests
````

---

## Code quality

This run phpstan and phpinsights.

````
make analyse
````

---

## Run tests
