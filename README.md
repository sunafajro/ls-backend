# Система учета для школы языков

## Установка:

### Windows

  1. cd c:\
  2. mkdir c:\dev
  3. cd dev
  4. mkdir apache2
  5. copy calc.conf apache2/calc.conf
  5. git clone https://github.com/sunafajro/ls-backend.git calc2
  6. mkdir mysql
  7. cd calc2
  8. docker build -f ./dockerfile-php -t apache/php .
  9. docker-compose -f docker-compose.yml up -d
  10. docker exec -it db /bin/bash
  11. mysql -u root -p langschool
  12. source ./dbschema.sql;
  13. docker exec -it web /bin/bash
  14. composer.phar install

### Linux

  1. sudo mkdir /opt/apache2
  2. copy calc.conf /opt/apache2/calc.conf
  3. git clone https://github.com/sunafajro/ls-backend.git calc2
  4. sudo mv calc2 /opt/calc2
  5. sudo mkdir /opt/mysql
  6. cd /opt/calc2
  7. sudo chown :33 -R .
  8. sudo chmod 770 -R .
  9. sudo docker build -f ./dockerfile-php -t apache/php .
  10. sudo docker-compose -f docker-compose.yml up -d
  11. sudo docker exec -it db /bin/bash
  12. sudo mysql -u root -p langschool
  13. source ./dbschema.sql;
  14. sudo docker exec -it web /bin/bash
  15. composer.phar install

### calc.conf
```
<VirtualHost *:80>
  ServerAdmin webmaster@localhost
  DocumentRoot /var/www/calc2/web
  ErrorLog ${APACHE_LOG_DIR}/error.log
  CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```