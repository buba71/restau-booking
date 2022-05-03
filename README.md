# Restaurant booking application

## Installation

````
git clone https://github.com/buba71/restau-booking.git

cd restau-booking

composer install 

yarn install

yar run build

````

Create *public/uploads/images* directory(Restaurant images are stored in this directory).

````
chmod -R 777 public/uploads/images

````

---

## Create Database

````
symfony console doctrine:database:create

symfony console doctrine:schema: update --force
````

---

## Run tests

````
make test
````

---

## Code quality

This run phpstan and phpinsights.

````
make analyse
````

---
