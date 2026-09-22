# Trivia A1

A small PHP/MySQL trivia application with player registration, login,
credits, trivia games, scoring, result groups, and an admin dashboard.

## Features

- Player registration and login
- Password hashing with PHP `password_hash`
- CSRF protection on form actions
- Player credits
- Multiple trivia sets
- Ten-question games
- Score and group assignment
- Player game history
- Admin dashboard
- Admin credit management
- MySQL/MariaDB database
- Seed trivia data included

## Security change in this Git version

The original development copy contained a database password directly in
`config.php`.

This version does not store database credentials in the repository.
`config.php` reads them from environment variables:

```text
DB_HOST
DB_NAME
DB_USER
DB_PASS
```

If the old database password is still active, change it before using this
repository.

## Requirements

On Ubuntu 24.04 you can install the basic requirements with:

```bash
sudo apt update
sudo apt install -y apache2 mariadb-server php php-mysql git
```

For local development you can also use PHP's built-in web server.

## Database setup

Create a database and a dedicated database user. Replace the sample password
with a new strong password of your own.

For example, enter MariaDB:

```bash
sudo mariadb
```

Then run SQL similar to:

```sql
CREATE DATABASE triviadb
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

CREATE USER 'triviadb'@'localhost'
  IDENTIFIED BY 'REPLACE_WITH_A_NEW_PASSWORD';

GRANT ALL PRIVILEGES ON triviadb.*
  TO 'triviadb'@'localhost';

FLUSH PRIVILEGES;
EXIT;
```

Import the schema and trivia questions:

```bash
mysql -u triviadb -p triviadb < full_trivia_schema_seed.sql
```

## Configure the application

The repository includes `.env.example` as a reference:

```bash
cp .env.example .env
nano .env
```

Do not commit `.env`.

The PHP application intentionally reads real configuration from environment
variables rather than automatically reading `.env` from the public web
directory.

For a quick local test in a shell, load the variables and start PHP:

```bash
set -a
source .env
set +a

php -S 127.0.0.1:8000
```

Then visit:

```text
http://127.0.0.1:8000
```

For Apache or PHP-FPM production hosting, set the same variables in the
server/service configuration instead of placing secrets in Git.

## Creating an administrator

Normal registration creates a regular player account.

After registering the account that should become an administrator, update
its role in MariaDB:

```sql
USE triviadb;
UPDATE users
SET role = 'admin'
WHERE email = 'you@example.com';
```

Use your actual administrator email address.

## Project files

```text
admin.php
config.php
db.php
full_trivia_schema_seed.sql
functions.php
home.php
home_admin.php
home_player.php
images/
index.php
logout.php
play.php
register.php
results.php
start_game.php
style.css
```

## Before committing to Git

Check that your `.env` file is ignored:

```bash
git status --ignored
```

You should see `.env` under ignored files.

You can also search the project for the old database password or other
accidentally committed credentials before the first push.

## Initialize Git

```bash
git init -b main
git add .
git status
git commit -m "Initial release"
```

## Create a private GitHub repository

If this is a project you may commercialize, private is a reasonable default:

```bash
gh repo create trivia-a1 --private --source=. --remote=origin --push
```

## Create a public GitHub repository

If you intentionally want to share the source:

```bash
gh repo create trivia-a1 --public --source=. --remote=origin --push
```

## Future updates

```bash
git add .
git commit -m "Describe the change"
git push
```

## License

An MIT license template is included. Replace `YOUR NAME` before publishing if
you want to use the MIT license.

If you want this project to remain proprietary, remove the `LICENSE` file
before publishing or sharing the repository.
