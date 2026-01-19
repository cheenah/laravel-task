Стек и инструкция по запуску
Технологии:
Backend: PHP 8.2, Laravel 12
Database: PostgreSQL 15
Frontend: Vue 3, Vue-router, Vite, Tailwind CSS
Auth: Passport (OAuth2), Spatie Permission

Инструкция по разворачиванию Docker:

1) В целевой папке создаем отдельную папку (например test-task)
cd ~
mkdir test-task
2) Переходим в папку и клонируем в нее репозиторий  
cd test-task
git clone https://github.com/cheenah/laravel-task.git
3) Создаем в папке файл docker-compose.yml
nano docker-compose.yml
4) Вставляем в него следующее

version: '3.8'

services:
app:
build:
context: ./laravel-task
ports:
- "8000:8000"
volumes:
- ./laravel-task:/var/www/html
environment:
APP_ENV: local
VITE_DEV_SERVER_URL: http://localhost:5173
DB_CONNECTION: pgsql
DB_HOST: db
DB_PORT: 5432
DB_DATABASE: laravel
DB_USERNAME: laravel
DB_PASSWORD: secret
depends_on:
- db

vite:
image: node:20
working_dir: /var/www/html
ports:
- "5173:5173"
volumes:
- ./laravel-task:/var/www/html
command: sh -c "npm install && npm run dev"

db:
image: postgres:15
environment:
POSTGRES_DB: laravel
POSTGRES_USER: laravel
POSTGRES_PASSWORD: secret
ports:
- "5432:5432"
volumes:
- pgdata:/var/lib/postgresql/data

volumes:
pgdata:

5) Сохраняем файл и запускаем контейнеры командой
   docker compose up -d
