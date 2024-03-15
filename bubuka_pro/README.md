<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Stack:
#### Node version: 20.5.0
#### Npm version: 9.8.0
#### Php  version: 8.1

## Instruction:
1) Сделать **git clone** проекта.
2) Переименовать **.env.example** в **.env** и настроить под себя.
3) Зайти в **composer.json** и установить все зависимости, которые предлагаются.
4) Если будет нужно, то также сделать и для nodejs с помощью команды: **npm install**
5) Теперь можно запускать сервер через composer 

## SWAGGER
1) Для просмотра документации: 
[http://{localhost}/api/documentation](http://{localhost}/api/documentation)
или посмотреть файл `bubuka_pro/storage/api_docs/api-docs.json`
2) Команда для преобразований swagger аннотации в один файл: ` php artisan l5-swagger:generate `

## Tests
1) Команда для запуска тестов: ` php artisan test `
2) Команда для запуска тестов в определенной папке: ` php artisan test --filter 'Tests\\Feature\\Auth\\`
3) Команда для запуска тестов в определенной файле: ` php artisan test --filter 'Tests\\Feature\\Auth\\RegisterTest`
4) Команда для запуска определенного теста: ` php artisan test --filter 'Tests\\Feature\\Auth\\RegisterTest::test_create_new_user_by_admin` 

## Job
1) Команда для запуска очередей: `php artisan queue:work`
2) Изменения в .env: ` QUEUE_CONNECTION=database `

## Docker
1) Команда для сборки контейнера: `docker-compose up --build`
2) Команда для использования bash в системе: `docker exec -it apps_app bash`
