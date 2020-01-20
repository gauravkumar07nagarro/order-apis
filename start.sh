#!/bin/bash
docker-compose down -v && docker-compose build --no-cache && docker-compose up -d
sleep 15
docker exec app php artisan migrate
docker exec app bash -c "chmod -R 777 storage"
docker exec app php artisan l5-swagger:generate
docker exec app php vendor/bin/phpunit tests/Unit/
docker exec app php vendor/bin/phpunit tests/Feature/
docker exec app bash -c "chmod -R 777 storage/"
