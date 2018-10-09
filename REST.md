# REST API
As the v2 of the server, the rest protocol will be implemented. As `web resources` URIs and other API stufs can quickly become tricky, here are some explenations of the internal API functionment.

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
 |   |   |   └─ choices : Choice[]
 |   |   ├─ %{id} : Formula
 |   |   ├─ list
 |   |   |   ├─ sellable : Formula[]
 |   |   |   └─ all : Formula[]
 |   |   └─ new : Formula
 |   |
 |   ├─ choice
 |   |   ├─ %{id} : Choice
 |   |   ├─ list : Choice[]
 |   |   └─ new : Choice
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
| `/api/v2/cafet/product/%{id}`                                                              | `GET` / `PUT` / `PATCH` / `DELETE` |
| `/api/v2/cafet/formula/%{id}`                                                              | `GET` / `PUT` / `PATCH` / `DELETE` | `noimage`
| `/api/v2/cafet/choice/%{id}`                                                               | `GET` / `PUT` / `PATCH` / `DELETE` | `noimage`
| `/api/v2/cafet/client/%{id}`                                                               | `GET`                              |
| `/api/v2/cafet/expense/%{id}`                                                              | `GET`                              |
| `/api/v2/cafet/product_bought/%{id}`                                                       | `GET`                              |
| `/api/v2/cafet/formula_bought/%{id}`                                                       | `GET`                              |
| `/api/v2/cafet/reload/%{id}`                                                               | `GET`                              |
| `/api/v2/cafet/product/list`                                                               | `GET`                              | `noimage` / `hidden`
| arrays like `/api/v2/cafet/.../list` (and its child) or `/api/v2/cafet/.../%{id}` children | `GET`                              |
| `/api/v2/cafet/.../new`                                                                    | `POST`                             |
| `/api/v2/cafet/.../search`                                                                 | `GET`                              |
| `/api/v2/user/login` and `/api/v2/cafet/user/logout`                                       | `POST`                             |
| `/api/v2/user/current`                                                                     | `GET` / `PATCH`                    |
| `/api/v2/server/config`                                                                    | `GET` / `PUT` / `PATCH`            |
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
| `"computing"`| `Computing` HTTP Header fiel
| `"result"`   | It's now the HTTP body

## Errors
:construction: Work in progress on error binding.