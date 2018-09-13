# Cafet Manager - PHP API Server : v1.0.0-beta
Both REST API for http connections and PHP librairy. Manage Interactions between client/server-side applications and database

------
## Installing development environment
Clone the git repository
```
git clone git@git.nowtryz.net:cafet-app/php-api-server.git
cd php-api-server
```

Run development environment
```
docker-compose up --build
```



## Stand-alone installation requierments

### Apache
- Tested with Apache 2.4.33
- Extension mod_rewrite enabled

### PHP 7.1 or upper with PDO, tested with:
- Tested versions:
    - PHP 7.1.16
    - PHP 7.2.4
    - PHP 7.2.9
- Extension:
    - PDO
    - PDO_mysql

### MySQL or PostgreSQL
- tested with MySQL 5.7.21



## Future updates

- Work with composer
- Create docker images
- Implementation of gettext