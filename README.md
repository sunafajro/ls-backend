# Система учета для школы языков
Установка:
```
calc2$ sudo docker build -t apache/php .
calc2$ sudo docker-compose -f docker-compose-lin.yml up -d
calc2$ sudo docker exec -it web /bin/bash
calc2# composer.phar install
calc2# composer require "yiisoft/yii2:~2.0.15" --update-with-dependencies
```