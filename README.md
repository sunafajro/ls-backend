# Система учета для школы языков

Задайте cookie validation key в файле config/web.php, в виде строки из случайных символов:
```
'request' => [
    'cookieValidationKey' => '<secret random string goes here>',
]
```
Параметр режима работы в файле config/params.php, standalone или bitrix:
```
return [
    'appMode' => 'standalone',
]
```
Update your vendor packages
```
docker-compose run --rm php composer update --prefer-dist
```
Run the installation triggers (creating cookie validation code)
```
docker-compose run --rm php composer install    
```
Запуск контейнера
```
docker-compose up -d
```
Приложение доступно по адресу:
http://127.0.0.1:8000
