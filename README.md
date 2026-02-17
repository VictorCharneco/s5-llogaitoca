<p align="center"><img src="public/favicon.webp" width="250" alt="Project Logo"></p>

## Compàs - LLoga i Toca
REST API to manage **users**, **instruments**, **reservations**, and **meetings**.
Authentication with **Laravel Passport** (token based) + endpoints protected by role (**admin** / **user**).
Documentation with **Swagger (L5-Swagger)** and **tests (PHPUnit)**.

---

## Features

- Token auth (register / login / logout / me)
- Users (admin: list/show; user: delete own account; admin: delete any user)
- Instruments
  - admin: CRUD
  - user: list/show + reserve instrument
- Reservations
  - admin: list all
  - user: list mine + return + delete mine
- Meetings
  - user: create / join / quit / list my meetings
  - admin: list all / delete / update status

---

## Tech stack

- PHP + Laravel
- Laravel Passport
- L5-Swagger
- PHPUnit (Feature tests)

---

## Requirements

- PHP 8.x
- Composer
- MySQL (or SQLite)
- Node + npm (only if you want to build assets; for API only it is not required)

---

## Installation

1) Clone the repo and install dependencies:

```bash
git clone -b develop https://github.com/VictorCharneco/s5-llogaitoca.git
cd s5-llogaitoca
composer install
```

2) Create .env:
```bash
cp .env.example .env
php artisan key:generate
```

3) Configure database in .env (example MySQL with MAMP):

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=s5_llogaitoca
DB_USERNAME=root
DB_PASSWORD=root
```
If you use macOS MAMP, DB_PORT is usually 8889.


After editing .env, clear config cache:
```bash
php artisan config:clear
```

4) Run migrations:
```bash
php artisan migrate:fresh
```
If Laravel shows this message:
The database 'xxxx' does not exist... Would you like to create it?
Press Yes only if xxxx matches your DB_DATABASE value in .env.
If it shows laravel, your .env is not set correctly yet. Fix DB_DATABASE first and run again.

5) Install Passport (only the first time on a new DB):
```bash
php artisan passport:install
```
If Laravel asks:
Would you like to run all pending database migrations? (yes/no) [yes]
Type: no

6) Seeders:
```bash
php artisan db:seed
```
⚠️ migrate:fresh --seed drops all tables and recreates them. Use it only if you want to reset the DB.

Seeders create:

- demo users (including admin)
- demo instruments + images in public/demo/instruments
- demo reservations + demo meetings

### Admin demo credentials
Email: admin@llogaitoca.com

Password: password

### Run the project
```bash
php artisan serve
```

API URL: http://localhost:8000/api/...

### Postman colletion:
postman-files/llogaitoca_postman_collection.json

### Swagger (UI)

1) Generate docs:
```bash
php artisan l5-swagger:generate
```

2) Open Swagger UI:

http://localhost:8000/api/documentation


### Tests
This project includes Feature tests (TDD). 
Run:
```bash
php artisan test
```

The tests use in-memory SQLite (:memory:), so they do not touch your real database.


### Troubleshooting
#### Unknown database 'laravel'

Your .env is still using DB_DATABASE=laravel (default) or the DB does not exist.
- Set DB_DATABASE=s5_llogaitoca
- Run php artisan config:clear
- Run php artisan migrate:fresh again (and press Yes only if the prompted name is correct)

#### There are no commands defined in the "passport" namespace
Passport is not installed in this clone.
Run:
```bash
composer install
php artisan optimize:clear
php artisan passport:install
```