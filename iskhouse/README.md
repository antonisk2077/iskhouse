# iskhouse
Hotel management system (PHP + MySQL).

## Run locally (Docker + PHP built-in server)
1) Start MySQL:
```bash
docker run --name iskhouse-mysql -e MYSQL_ROOT_PASSWORD=iskpass -e MYSQL_DATABASE=ams -p 3307:3306 -d mysql:8.0
```

2) Import database schema + seed:
```bash
docker exec -i iskhouse-mysql mysql -uroot -piskpass -h 127.0.0.1 ams < db/ams.sql
```

3) Update `config.php` to match your local paths and MySQL port:
```php
define('WEB_URL', 'http://localhost:8000/');
define('ROOT_PATH', '/absolute/path/to/iskhouse/');
define('DB_HOSTNAME', '127.0.0.1:3307');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'iskpass');
define('DB_DATABASE', 'ams');
```

4) Run the PHP server:
```bash
php -S localhost:8000 -t .
```

Open `http://localhost:8000`.

## Notes
- MySQL container can be stopped with `docker stop iskhouse-mysql`.
- If you already have a local MySQL/XAMPP setup, skip Docker and point `config.php` to your DB.
