# CRM/Кабинет клиента/etc... все в одном для школы языков.

## Требования:

1. PHP 7.4
2. MySQL 8
3. Docker

##Развертывание:

1. Установить пакеты ```composer install```
2. Запустить ```php init``` для инициализации
3. Добавить в hosts файл запись
   ```
       127.0.0.1 school.local client.local exam.local
   ```
4. Настроить параметры БД в **common\config\main-local.php**
    ```
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=db;dbname=lsdb',
            'username' => 'lsuser',
            'password' => 'lsdbpass',
            'charset' => 'utf8',
        ],
    ```
5. Запустить контейнеры ```docker-compose up -d```
6. Произвести миграции бд таблиц ```php yii migrate```