if [ -e ".env" ]; then
    echo
else
    echo "    cp .env.example .env ..."
    cp .env.example .env
fi
docker compose down --remove-orphans
docker compose up -d
sudo chown -R 82:82 public storage vendor node_modules cache bootstrap composer.json composer.lock
docker compose exec php-fpm composer install --ignore-platform-reqs
docker compose exec php-fpm php artisan migrate:refresh --seed