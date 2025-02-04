# SOK Test App

This is a sample PHP application (with Node-based Gulp build) running in Docker containers using **Nginx**, **PHP-FPM**, and **MySQL**. The project includes:

- **PHP 8.3-FPM** (with pdo_mysql and Composer installed)  
- **Nginx** (serving the application on port 8080)  
- **MySQL** (port 13306 to avoid conflicts, with automatic database import from `docker/db/dump.sql` on first run)  
- **Node** container for installing JS libraries and running Gulp

## Requirements

- [Docker](https://www.docker.com/)  
- [Docker Compose](https://docs.docker.com/compose/)  
  or use the integrated `docker compose` command (Docker 20.10+).

No other local installation of PHP, MySQL, or Nginx is required.

## Getting Started

1. **Clone** the repository:

```bash
git clone https://github.com/<user>/<repo>.git
cd <repo>
```

2. **Check the `docker-compose.yml`**  
   Make sure the ports `8080:80` (for Nginx) and `13306:3306` (for MySQL) do not conflict with your local setup.

3. **Build and start** the containers:

```bash
docker compose up -d --build
```

   - The first time, it will pull base images (php:8.3-fpm, nginx:latest, mysql:8.0, node:16, etc.).
   - MySQL will automatically create a `soktestapp` database using credentials from the `environment` section.
   - The dump file (`docker/db/dump.sql`) will be imported if `/var/lib/mysql` is empty (i.e., on the first run).

4. **Check the Node build**  
   By default, a Node container will run `npm install && npm run build` at startup (if configured in the `docker-compose.yml`).  
   Alternatively, you can manually execute:

```bash
docker compose exec node bash
npm install
npm run build
```

   to rebuild JS/CSS if needed.

5. **Open the app**:

   - Go to [http://localhost:8080](http://localhost:8080/) in your browser.
   - The PHP code is served by Nginx, hitting the `php` container for PHP-FPM.

## Usage

### Composer
If you need to install PHP dependencies (e.g., `composer install`):
```bash
docker compose exec php bash
composer install
```
(assuming there's a `composer.json` in the project).

### Database 
- By default, the database is named `soktestapp`, user `soktestapp` with password `soktestapp`. 
- The MySQL container listens on port `13306` on the host, so you can connect with your local SQL client:
  
  ```
  Host: localhost
  Port: 13306
  User: soktestapp
  Password: soktestapp
  Database: soktestapp
  ```

### Logs
- Check logs:
```bash
docker compose logs -f nginx
docker compose logs -f php
docker compose logs -f db
docker compose logs -f node
```
  
### Stopping
To stop and remove containers:
```bash
docker compose down
```
If you want to remove volumes (including database data), use:
```bash
docker compose down --volumes
```

## License

[MIT](LICENSE)

## Author

**Vladislavs Valenieks**
