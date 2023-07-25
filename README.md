# Backend Task: E-commerce API

## Prerequisites

1.  [Composer](https://getcomposer.org/) installed
2.  PHP 8.2 installed

## Installation

1.  Clone the repository
2.  Install dependencies using `composer install`
3.  Copy `.env.example` to `.env` using `cp .env.example .env`
4.  Generate app key using `php artisan key:generate`
5.  Add database credentials on `.env` file
    ```
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=ecommerce-api
    DB_USERNAME=newuser
    DB_PASSWORD=password
    ```
6.  Run migrations using `php artisan migrate`
7.  Run the server using `php artisan serve`

## API Documentation

### Product Endpoints

#### Get all products

`GET /api/products`

##### Query Parameters

| Parameter | Type   | Description                                         |
| --------- | ------ | --------------------------------------------------- |
| page      | number | The page number to return. Default: 1               |
| perPage   | number | The number of items to return per page. Default: 10 |

Returns a list of [products resource](https://github.com/subhamchbty/ecommerce-api/blob/main/product-resource.md) with pagination.

#### Get a product

`GET /api/products/{id}`

Returns a [product resource](https://github.com/subhamchbty/ecommerce-api/blob/main/product-resource.md).

#### Create a product

`POST /api/products`

##### Request Body

```json
{
    "name": "Product iphone 11",
    "description": "medium text product 6 desc",
    "base_price": 450,
    "is_active": true,
    "variants": [
        {
            "name": "Green",
            "additional_cost": 0,
            "stock_count": 10
        },
        {
            "name": "Navy Blue",
            "additional_cost": 10,
            "stock_count": 10
        }
    ]
}
```

Returns newly created [product resource](https://github.com/subhamchbty/ecommerce-api/blob/main/product-resource.md).

#### Update a product

`PUT /api/products/{id}`

##### Request Body

```json
{
    "name": "Product 6",
    "description": "medium text product 6 desc",
    "base_price": 450,
    "is_active": true,
    "variants": [
        {
            "id": 21,
            "name": "Green",
            "additional_cost": 5,
            "stock_count": 10
        },
        {
            "name": "Space Black",
            "additional_cost": 10,
            "stock_count": 20
        }
    ]
}
```

Returns updated [product resource](https://github.com/subhamchbty/ecommerce-api/blob/main/product-resource.md).

#### Delete a product

`DELETE /api/products/{id}`

##### Response Body

```json
{
    "message": "Product deleted successfully"
}
```

#### Search products

`GET /api/products/search`

##### Query Parameters

| Parameter | Type   | Description     |
| --------- | ------ | --------------- |
| term      | string | The search term |

Returns a list of [products resource](https://github.com/subhamchbty/ecommerce-api/blob/main/product-resource.md) matching the search term.

#### Error Responses

##### 404 Not Found

```json
{
    "message": "No query results for model [App\\Models\\Product] 1"
}
```

##### 422 Unprocessable Entity

```json
{
    "message": "The given data was invalid.",
    "errors": {
        "name": ["The name field is required."],
        "description": ["The description field is required."],
        "base_price": ["The base price field is required."],
        "variants": ["The variants field is required."]
    }
}
```

## Testing

Run tests using `php artisan test`

## Assumptions

1. A product can have multiple variants.
2. A product cannot be created without variants.
3. Product variants are synced with the product. If a variant is not present in the update request, it will be deleted from the database.

## Architectural Decisions

1. Used Laravel framework to build the API.
2. Used Laravel's API Resource to transform the data.
3. Used Laravel's validation to validate the request data.
4. Used Laravel's pagination to paginate the data.
5. Used Laravel's database migrations to create the database schema.
