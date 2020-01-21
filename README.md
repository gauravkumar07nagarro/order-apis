# Restful APIs for Orders
RESTful APIs  which have been used in Orders related tasks.

  - Get the list of orders
  - Create a new order
  - Update the status of an order

# Tools/Softwares

- [Docker](https://www.docker.com) as a container service.
- [Apache](https://httpd.apache.org/docs/2.4/) for webserver used to process incoming request for PHP application.
- [PHP](https://www.php.net) is used to create backend APIs.
- [Laravel Framework]() is a web application framework with expressive, elegant syntax and to ease the development of APIs.
- [MySQL](https://dev.mysql.com/doc/refman/5.7/en/) is used for database.


### Installation
Make sure docker is running in your system
```sh
$ git clone https://github.com/gauravkumar07nagarro/order-apis.git 
```
### Environment variables

API_SUBTYPE=myapp <br/>
API_PREFIX=/ <br/>
API_VERSION=v1 <br/>
API_STRICT=false <br/>
API_CONDITIONAL_REQUEST=false <br/>
API_DEFAULT_FORMAT=json <br/>
API_DEBUG=false <br/>
GOOGLE_MAP_API_KEY = "YOUR-GOOGLE-API-KEY" <br/>
GOOGLE_MAP_DISTANCE_API = "https://maps.googleapis.com/maps/api/distancematrix/json" <br/>
LISTING_LIMIT=10 <br/>
L5_SWAGGER_GENERATE_ALWAYS=false <br/>


### Change GOOGLE_MAP_API_KEY from .env

Please make sure that you have changed **GOOGLE_MAP_API_KEY** with your google Distance Matrix API Key

### Run Docker


```sh
$ cd order-apis
$ ./start.sh
```
Note : make sure **start.sh** is executable

Above file is used to setup your project in Docker container. It will perform below tasks:
- Creating dependencies used in project.
- Database migrations used for API operations.
- Generate swagger api documentation.
- Run all test cases.

### Swagger Documentation
    [http://localhost:8080/documentation] (API Demo)
List of APIs will be displayed

### API Documentation & Examples

Base Url http://localhost:8080/

| METHOD | Content-type |API Endpoint | Example |
| ------ | ------ | ------ | ------- |
| GET | application/json | /orders?page={page}&limit={limit} | http://localhost:8080/orders?page=1&limit=10 |
| POST | application/json | /orders | http://localhost:8080/orders |
| PATCH | application/json | /orders/{id} |  http://localhost:8080/orders/10 |

- ## API To Create A New Order
  Method : POST <br/>
  Url : http://localhost:8080/orders <br/>
  Body : 
    ```sh 
    { 
        "origin": ["28.459497", "77.026634"], 
        "destination": ["26.912434", "77.026634"]
    }
    ```
  Response : 
    ```sh
    {
        "total_distance": 226694,
        "status": "UNASSIGNED",
        "id": 2
    }
    ```
  Response Codes:
  - 200  :  OK
  - 422 : Unprocessable Entitiy

- ## API To Update Status Of An Order
  Method : PATCH <br/>
  Url : http://localhost:8080/orders/1 <br/>
  Body : 
    ```sh 
    { 
        "status": "TAKEN"
    }
    ```
  Response : 
    ```sh
    {
        "status": "SUCCESS"
    }
    ```
  Response Codes:
  - 200  :  OK
  - 422 : Unprocessable Entitiy

- ## API To List The Orders
  Method : GET <br/>
  Url : http://localhost:8080/orders?page=1&limit=10 <br/>
  Response : 
    ```sh 
      [
        {
            "id": 1,
            "total_distance": 170201,
            "status": "TAKEN"
        },
        {
            "id": 2,
            "total_distance": 226694,
            "status": "UNASSIGNED"
        }
      ]
    ```
  Response Codes:
  - 200 :  OK
  - 422 : Unprocessable Entitiy
