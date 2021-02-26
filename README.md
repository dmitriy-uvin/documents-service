Установка

```
git clone https://github.com/dmitriy-uvin/documents-service
cd documents-service
composer install
npm install && npm run dev
```
```
cp .env.example .env
Setup database configuration
Setup DBrain api tokens
php artisan key:generate
php artisan storage:link
```
```
php artisan migrate
php artisan db:seed
```
Будут созданы 4 пользователя с разными ролями:
- Developer
developer@developer.com
password
- Administrator
admin@gmail.com
123123123
- Manager
manager@gmail.com
123123123
- Worker
worker1@gmail.com
123123123
