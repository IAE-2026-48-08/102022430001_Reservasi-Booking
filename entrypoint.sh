
set -e

if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    php artisan key:generate --force
fi

echo "Menunggu MySQL..."
until php -r "
    \$conn = @new mysqli(getenv('DB_HOST'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'), getenv('DB_DATABASE'), getenv('DB_PORT') ?: 3306);
    if (\$conn->connect_error) { exit(1); }
    exit(0);
" 2>/dev/null; do
    sleep 2
done
echo "MySQL siap."

php artisan migrate --force --seed
php artisan l5-swagger:generate

apache2-foreground