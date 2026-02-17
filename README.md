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
composer install
```

2) Create .env:
```
cp .env.example .env
php artisan key:generate
```

3) Configure database in .env (example MySQL with MAMP):

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=s5_llogaitoca
DB_USERNAME=root
DB_PASSWORD=root
```
If you use macOS MAMP, DB_PORT is usually 8889.

4) Run migrations:
```
php artisan migrate
```

5) Install Passport (only the first time on a new DB):
```
php artisan passport:install
```

6) Seeders:
```
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
```
php artisan serve
```

API URL: http://localhost:8000/api/...

### Postman colletion:
postman-files/llogaitoca_postman_collection.json

### Swagger (UI)

1) Generate docs:
```
php artisan l5-swagger:generate
```

2) Open Swagger UI:

http://localhost:8000/api/documentation


### Tests
This project includes Feature tests (TDD). 
Run:
```
php artisan test
```

The tests use in-memory SQLite (:memory:), so they do not touch your real database.