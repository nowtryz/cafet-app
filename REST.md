# REST API
As the v0.2 of the server, the rest protocol will be implemented. As `web resources`, URIs and other API stufs can quickly become tricky, here are some explenations of the internal API functionment.

## Structure
If we consider end points pathes of `web resources` as a directory index, then the following would be this index.

```
v2
 ├─ cafet
 |   ├─ client
 |   |   ├─ %{id}
 |   |   |   ├─ reloads : Reload[]
 |   |   |   ├─ expenses : Expense[]
 |   |   |   └─ last_expenses : Expense[]
 |   |   |
 |   |   ├─ %{id} : Client
 |   |   ├─ list : Client[]
 |   |   ├─ new : Client[]
 |   |   └─ search
 |   |       └─ %{name} : Client[]
 |   |
 |   ├─ reload
 |   |   ├─ %{id} : Reload
 |   |   ├─ list : Reload[]
 |   |   └─ new : Reload
 |   |
 |   ├─ expense
 |   |   ├─ %{id}
 |   |   |   └─ details : ExpenseDetail[]
 |   |   └─ %{id} : Expense
 |   |
 |   ├─ product_bought
 |   |   ├─ list : ProductBought[]
 |   |   └─ %{id} : ProductBought
 |   |
 |   ├─ formula_bought
 |   |   └─ %{id}
 |   |   |   └─ products : ProductBought[]
 |   |   └─ %{id} : FormulaBought
 |   |
 |   ├─ group
 |   |   ├─ %{id}
 |   |   |   └─ products : Products[]
 |   |   ├─ %{id} : ProductGroup
 |   |   ├─ list : ProductGroup[]
 |   |   └─ new : ProductGroup
 |   |
 |   ├─ product
 |   |   ├─ %{id}
 |   |   |   └─ replenishments (never implemented)
 |   |   ├─ %{id} : Product
 |   |   ├─ list
 |   |   |   ├─ sellable : Product[]
 |   |   |   └─ all : Product[]
 |   |   └─ new : Product
 |   |
 |   ├─ formula
 |   |   ├─ %{id}
 |   |   |   ├─ choices : Choice[]
 |   |   |   └─ choice
 |   |   |       ├─ %{id} : Choice
 |   |   |       └─ add : Choice
 |   |   ├─ %{id} : Formula
 |   |   ├─ list
 |   |   |   ├─ sellable : Formula[]
 |   |   |   └─ all : Formula[]
 |   |   └─ new : Formula
 |   |
 |   └─ order : bool
 |
 |
 ├─ user
 |   ├─ login : Login
 |   ├─ logout : bool
 |   └─ current : User
 |
 |
 └─ server
     ├─ config : key/value
     └─ infos : key/value
```

End points follow a simple convention, so these are the methods  and request argulments to reach end points:

| Path                                                                                       | Methods                            | Request arguments |
|--------------------------------------------------------------------------------------------|------------------------------------|-------------------|
| `/api/v2/cafet/group/%{id}`                                                                | `GET` / `PUT` / `PATCH` / `DELETE` |
| `/api/v2/cafet/group/%{id}/products`                                                       | `GET`                              | `noimage` / `hidden`
| `/api/v2/cafet/product/%{id}`                                                              | `GET` / `PUT` / `PATCH` / `DELETE` |
| `/api/v2/cafet/formula/%{id}`                                                              | `GET` / `PUT` / `PATCH` / `DELETE` | `noimage`
| `/api/v2/cafet/formula/%{id}/choices`                                                      | `GET`                              | `noimage`
| `/api/v2/cafet/formula/%{id}/choice/%{id}`                                                 | `GET` / `PUT` / `PATCH` / `DELETE` | `noimage`
| `/api/v2/cafet/client/%{id}`                                                               | `GET`                              |
| `/api/v2/cafet/expense/%{id}`                                                              | `GET`                              |
| `/api/v2/cafet/product_bought/%{id}`                                                       | `GET`                              |
| `/api/v2/cafet/formula_bought/%{id}`                                                       | `GET`                              |
| `/api/v2/cafet/reload/%{id}`                                                               | `GET`                              |
| `/api/v2/cafet/product/list`                                                               | `GET`                              | `noimage` / `hidden`
| `/api/v2/cafet/formula/list`                                                               | `GET`                              | `noimage` / `hidden`
| arrays like `/api/v2/cafet/.../list` (and its child) or `/api/v2/cafet/.../%{id}` children | `GET`                              |
| `/api/v2/cafet/.../new` and `/api/v2/cafet/formula/%{id}/choice/add`                       | `POST`                             |
| `/api/v2/cafet/.../search`                                                                 | `GET`                              |
| `/api/v2/user/login` and `/api/v2/cafet/user/logout`                                       | `POST`                             |
| `/api/v2/user/current`                                                                     | `GET` / `PATCH`                    |
| `/api/v2/server/config`                                                                    | `GET` / `PUT` / `PATCH`            |
| `/api/v2/server/state`                                                                     | `GET`                              |
| `/api/v2/server/infos`                                                                     | `GET`                              |

Any end point can use the request argument `pretty` to format the output in a human readable way.

## Where the hell body parts have gone?
Every field excepte the "wanted resouce" have been modified to only keep this resource in the body. So usage of body parts changed and it may have changed a little.

For queries (no content difference):

| Field         | New usage
|---------------|----------
| `"origin"`    | Since namespaces have completly changed to cover queries from any origin and the calls have moved to `/api/`, the field is no longer needed
| `"version"`   | It's now part of the resource URI: `http://%{host}/api/%{version}/...`
| `"session_id"`| `User-Token` HTTP Header field
| `"action"`    | As anybody noticed, it IS the URI e.g. `http://%{host}/api/v2/client/47`
| `"arguments"` | It's now the HTTP Request body, but kept its JSON synthax

For reponses:

| Field        | New usage
|--------------|----------
| `"status"`   | On `OK`, the server simply responds with a `HTTP 200` code
|              | On `Error`, the server responds with an HTTP error code corresponding to the error occured
| `"computing"`| No more used
| `"result"`   | Now the HTTP body

## :warning: Errors

Errors are throwed with HTTP error codes as the following

### 2xx Success

| HTTP Code | Message      | Description
|-----------|:------------:|------------
| `200`     | `OK`         | The request has succeeded.
| `201`     | `Created`    | The request has been fulfilled and resulted in a new resource being created.
| `204`     | `No Content` | The request has been fulfilled but there is no need to return an entity-body.
    
    
### 4xx Client Error

| HTTP Code | Message              | Description
|-----------|:--------------------:|------------
| `400`     | `Bad Request`        | The request cannot be processed due to syntax error or failed semantic validation.
| `401`     | `Unauthorized`       | Missing or invalid User-token when needed. **MUST** be return with a `WWW-Authenticate` header field.
| `403`     | `Forbidden`          | The resource is unavailable for the current logged user.
| `404`     | `Resource Not Found` | The ressource cannot be found due to wrong id or malformed URI. *Should* be return with a `Reason` header field.
| `405`     | `Method Not Allowed` | The resource does not support method with which the request was made. **MUST** be return with a `Allow` header field.
| `409`     | `Conflict`           | Existing conflict, there is a  mismatch with the resource id, its type or with linked resources. *Should* be return with a `Reason` header field.
| `418`     | `I'm a teapot`       | The functionality is not implemented.
    
### 5xx Server Error
| HTTP Code | Message                 | Description
|-----------|:-----------------------:|------------
| `500`     | `Internal Server Error` | An unexpected error occurred while trying to fulfill the request.