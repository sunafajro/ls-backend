# Система учета для школы языков

## Установка:
  1. cd c:\
  2. mkdir c:\dev
  3. mkdir c:\dev\apache2
  4. mkdir c:\dev\calc2
  5. mkdir c:\dev\mysql
  6. cd c:\dev\calc2
  7. docker build -f ./dockerfile-php -t apache/php .
  8. docker-compose -f docker-compose.yml up -d