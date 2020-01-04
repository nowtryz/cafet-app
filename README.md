# Cafet Manager - PHP API Server : Beta 0.2.0

Both REST API for http connections and PHP librairy. Manage Interactions between client/server-side applications and
database

## Rest API

The server exposes an API to interact with it if you want to incorporate data with your website or whatever.
Documentation about the rest api can found on the project's [documentations](http://cafet-app.static.nowtryz.net/php-api-server/) or in [the openapi specifications](./openapi.yml)

## Stand-alone installation

### Binary Install

#### Requirements
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

1. Dowload the [latest version] and unpack the `app` folder where you want.
1. Create a virtual host pointing to this folder. e.g. `cofee.example.com`
1. Create a database.
1. Run SQL import scripts on your mysql server. (you can find them in the `sql_scripts` folder of the download archive)
1. Open and edit `path/to/app/cafetapi_content/config.php`.
1. Access `http://cofee.example.com` and your installation is now up and ready

[latest version]: https://gitlab.com/cafet-app/cafet-app/-/jobs/artifacts/master/download?job=deploy%3Aapp

### Docker
You can also use docker containers to run the application. For this solution, please refer to the steps bellow:

 1. Make sure to have `docker` and `docker-compose` install on your server
 1. To run the server in a docker container, just create a `docker-compose.yml` and paste the content of
    [this file](./docker-compose-prod.yml) in it.
 1. Log in to the gitlab's registry:
    ```shell
    echo DGNvKHoDSYNLxxbZe2cr | docker login -u docker-token --password-stdin registry.gitlab.com
    ```
 1. Then start `docker-compose`:
    ```shell
    docker-compose up -d
    ```

## Development environment installation

### Source code
Clone the git repository
```
git clone git@gitlab.com/cafet-app/cafet-app.git
cd cafet-app
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
- use [MiniCssExtractPlugin](https://webpack.js.org/plugins/mini-css-extract-plugin/)
