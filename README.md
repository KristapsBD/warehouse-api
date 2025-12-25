# Warehouse Management API

### Prerequisites
* Docker & Docker Compose
* Make (Optional for shortcuts)

### Installation
1. **Clone the repository:**
   ```bash
   git clone git@github.com:KristapsBD/warehouse-api.git
   cd warehouse-api
   ```

2. **Setup Environment:**
   ```bash
   cp .env.example .env
   ```

3. **Build & Run:**
   ```bash
   make setup
   ```

4. **Initialize Database:**
   ```bash
   make migrate-fresh
   ```

The API is now running at **http://localhost:8080**

---

## Useful commands

| Command | Description |
| :--- | :--- |
| `make test` | Run full PHPUnit tests |
| `make shell` | Enter PHP container terminal |
| `make db` | Enter DB container terminal |
| `make migrate-fresh` | Rerun all migrations and seed |
| `make logs` | View server logs |

---

## API Documentation

### 1. Authentication

#### **Login**
* **Endpoint:** `POST /api/login`
* **Public:** Yes
* **Rate Limit:** 5 requests per minute per IP

**Request Body:**
```json
{
  "email": "test@example.com",
  "password": "password"
}
```

**Response (200 OK):**
```json
{
  "token": "1|xurXVzYtNF8fdkQU9KVG8PqVz...",
  "user": {
    "id": 1,
    "name": "Test User",
    "email": "test@example.com"
  }
}
```

**Response (401 Unauthorized):**
```json
{
  "message": "Invalid credentials"
}
```

#### **Logout**
* **Endpoint:** `POST /api/logout`
* **Headers:** `Authorization: Bearer <your_token>`

**Response (200 OK):**
```json
{
  "message": "Logged out"
}
```

### 2. Products

#### **List All Products**

* **Endpoint:** `GET /api/products`
* **Public:** Yes
* **Pagination:** 100 items per page
* **Caching:** Results cached for 1 hour per page. Cache invalidates automatically on product changes.

**Query Parameters:**
* `page` (optional) - Page number (default: 1)

**Response (200 OK):**
```json
{
  "data": [
    {
      "id": 1,
      "name": "MacBook Pro",
      "description": "M3 Chip, 16GB RAM",
      "price": 1999.99,
      "quantity": 10
    },
    {
      "id": 2,
      "name": "GeForce RTX 5090 GPU",
      "description": "RTX 5090 Limited Edition",
      "price": 1599.00,
      "quantity": 2
    }
  ],
  "links": {
    "first": "http://localhost:8080/api/products?page=1",
    "last": "http://localhost:8080/api/products?page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "per_page": 100,
    "to": 2,
    "total": 2
  }
}
```

### 3. Orders

#### **Create Order**

* **Endpoint:** `POST /api/orders`
* **Headers:** `Authorization: Bearer <your_token>`
* **Rate Limit:** 60 requests per minute per user

**Request Body:**
```json
{
  "products": [
    { "id": 1, "quantity": 1 },
    { "id": 2, "quantity": 5 }
  ]
}
```

**Validation Rules:**
* `products` - Required, array, min 1 item, max 50 items
* `products.*.id` - Required, integer, must exist in products table, must be unique in request
* `products.*.quantity` - Required, integer, min 1, max 1000

**Response (201 Created):**
```json
{
  "message": "Order created successfully",
  "order": {
    "id": 55,
    "order_number": "ORD-50165",
    "total": 2247.49,
    "date": "2025-12-25T14:30:00+00:00",
    "items": [
      {
        "product_id": 1,
        "quantity": 1,
        "price_at_purchase": 1999.99,
        "total": 1999.99
      },
      {
        "product_id": 2,
        "quantity": 5,
        "price_at_purchase": 49.50,
        "total": 247.50
      }
    ]
  }
}
```

**Response (400 Bad Request - Insufficient Stock):**
```json
{
  "error": "Order failed",
  "message": "Product 'GeForce RTX 5090 GPU' does not have enough stock (Requested: 5, Available: 2)"
}
```

**Response (422 Unprocessable Entity - Validation Error):**
```json
{
  "message": "The products.0.id field must exist in the products table.",
  "errors": {
    "products.0.id": [
      "The products.0.id field must exist in the products table."
    ]
  }
}
```

#### **Get Order Details**

* **Endpoint:** `GET /api/orders/{id}`
* **Headers:** `Authorization: Bearer <your_token>`

**Response (200 OK):**
```json
{
    "data": {
        "id": 55,
        "order_number": "ORD-50017",
        "total": 523.77,
        "date": "2025-12-24T14:30:00+00:00",
        "items": [
            {
                "product_id": 1,
                "quantity": 2,
                "price": 1999.99,
                "total": 3999.98
            },
            ...
        ]
    }
}
```

**Response (404 Not Found):**
```json
{
  "error": "Resource not found",
  "message": "The requested entry does not exist."
}
```

---
