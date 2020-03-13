# Cafet Manager - PHP API Server : Beta 0.2.0

Both REST API for http connections and PHP librairy. Manage Interactions between client/server-side applications and database

## Rest API

Documentation about the rest api can found [here](http://cafet-app.static.nowtryz.net/php-api-server/) or in [the openapi specifications file](./openapi.yml)

## Stand-alone installation

### Docker

1. Make sure to have `docker` and `docker-compose` install on your server
1. To run the server in a docker container, just create a `docker-compose.yml` with the content bellow
    ```yaml
    version: "3"
    services:
        php:
            image: registry.gitlab.com/cafet-app/cafet-app/server:latest
            container_name: cafet-server
            environment:
                SERVER_DOMAIN: localhost
                MAILHUB: localhost
            ports:
                - "80:80"
            links:
                - db
            depends_on:
                - db
            volumes:
                - php-logs:/var/log
            networks:
                - cafet_net

        db:
            image: registry.gitlab.com/cafet-app/cafet-app/database:latest
            container_name: cafet-server-mysql
            restart: always
            ports:
                - '3306:3306'
            networks:
                - cafet_net

    volumes:
        php-logs:

    networks:
        cafet_net:
    ```
1. Then start `docker-compose`
    ```shell
    echo DGNvKHoDSYNLxxbZe2cr | docker login -u docker-token --password-stdin registry.gitlab.com
    docker-compose up -d
    ```

### Binary Install

#### Requierments
- Apache
    - Tested with Apache 2.4.33
    - Extension mod_rewrite enabled
- PHP 7.2 or upper with PDO, tested with:
    - Tested versions:
        - PHP 7.2.4
        - PHP 7.2.9
    - Extensions:
        - PDO
    - PDO_mysql
- MySQL 5.7 or upper
    - tested with MySQL 5.7.21

#### Installation

1. [Dowload the latest version](https://gitlab.com/cafet-app/cafet-app/-/jobs/artifacts/master/download?job=deploy:app) and unpack the `app` folder where you want.
1. Create a virtual host pointing to this folder. e.g. `cofee.example.com`
1. Create a database.
1. Run SQL import scripts that you can find [here](https://gitlab.com/cafet-app/cafet-app/-/jobs/artifacts/master/download?job=deploy:database_structure) on your mysql server.
1. Open and edit `path/to/app/cafetapi_content/config.php`.
1. Access `http://cofee.example.com` and your installation is now up and ready

## Development environment installation

### Source code
Clone the git repository
```
git clone git@git.nowtryz.net:cafet-app/php-api-server.git
cd php-api-server
```

Run development environment
```
docker-compose up --build
```

### Access app
Without any other docker port binding:
- The app is accessible from `{docker_host}:80/`
- phpMyAdmin is accessible from `{docker_host}:81/`

### Loging in app
Demo user is `Nowtryz <damien.djmb@gmail.com>` with password `admin`. Easy to keep in mind :wink:, let's enjoy!

## Future updates

- Work with composer
- Create docker images
- Implementation of gettext