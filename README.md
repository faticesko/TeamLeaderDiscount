# Teamleader CRM - Discount assignment

## Setup

Install composer:
```shell
$ composer install
```

Run project:
```shell
$ php -S localhost:8080 -t public 
```

Order endpoint:
```shell
POST {base_url}/order 
```

Payload example:
```shell
{
    "id": "1",
    "customer-id": "1",
    "items": [
        {
            "product-id": "B102",
            "quantity": "10",
            "unit-price": "4.99",
            "total": "49.90"
        }
    ],
    "total": "49.90"
}
```
