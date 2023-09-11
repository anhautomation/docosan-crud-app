# symnofy 6 
# php 8.2

Go to source code

Database run: docker-compose up --build -d

Database initialization: php bin/console make:migration4

Database creation: php bin/console doctrine:migrations:migrate

Data seed: php bin/console doctrine:fixtures:load     

Run: php -S localhost:8000 -t public