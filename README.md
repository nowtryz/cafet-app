# Cafet Manager - PHP API Server : v1.0.0-beta
Both REST API for http connections and PHP librairy. Manage Interactions between client/server-side applications and database

## Usage

### API calls
API call are made through the `{docker_host}:80/index.php` or even `{docker_host}:80/`. *Maybe `{docker_host}:80/api/` in future versions.*

### Queries
Calls must be made with the following json POST *(or at least with all root objects as POST vars)*.
```json
{
    "origin": "cafet_app",
    "version": "1.0.0-beta",
    "session_id": "some_id",
    "action": "function_name",
    "arguments": {
        "1rst_arg": "1rst_value",
        "2nd_arg": "2nd_value",
        "3rd_arg": "3rd_value"
    }
}
```

Here is an example for the `login` call, **note that it's the only action which dosn't need a `session_id`**:
```json
{
    "origin": "cafet_app",
    "version": "1.0.0-beta",
    "action": "login",
    "arguments": {
        "email": "test@localhost.loc",
        "password": "pass"
    }
}
```

#### Save order
The `save order` action has a very particular structure because its arguments must follow a specific structure too. So here is an example of a `save order` action:
```json
{
    "origin": "cafet_app",
    "version": "1.0",
    "session_id": "some_id",
    "action": "saveOrder",
    "arguments": {
        "client_id": 256,
        "order": [
            {
                "type": "product",
                "id": 65,
                "amount": 4
            },
            {
                "type": "formula",
                "id": 2,
                "amount": 1,
                "products": [
                    2,
                    16,
                    69
                ]
            }
        ]
    }
}
```

### Responses
Responses are also json object following the structure as shown bellow.
```json
{
"status": "ok|error",
"result": "the_json_object",
"computing": "the time to compute in milis as a float"
}
```

Here is an example for an error result:
```json
{
    "status": "error",
    "result": {
        "error_code": "01-001",
        "error_type": "the error type",
        "error_message": "some message",
        "additional_message": "additional_message"
    },
    "computing": 0.01244
}
```

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



## Stand-alone installation requierments

### Apache
- Tested with Apache 2.4.33
- Extension mod_rewrite enabled

### PHP 7.1 or upper with PDO, tested with:
- Tested versions:
    - PHP 7.1.16
    - PHP 7.2.4
    - PHP 7.2.9
- Extensions:
    - PDO
    - PDO_mysql

### MySQL or PostgreSQL
- tested with MySQL 5.7.21



## Future updates

- Work with composer
- Create docker images
- Implementation of gettext