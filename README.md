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
   make up
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
  "user": { ... }
}
```

### 2. Products

#### **List All Products**

* **Endpoint:** `GET /api/products`
* **Public:** Yes

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
      "name": "Logitech Mouse",
      "description": "Wireless",
      "price": 49.50,
      "quantity": 50
    }
  ]
}
```

### 3. Orders

#### **Create Order**

* **Endpoint:** `POST /api/orders`
* **Headers:** `Authorization: Bearer <your_token>`

**Request Body:**
```json
{
  "products": [
    { "id": 1, "quantity": 1 },
    { "id": 2, "quantity": 5 }
  ]
}
```

**Response (201 Created):**
```json
{
  "message": "Order created successfully",
  "order": {
    "id": 55,
    "order_number": "ORD-50017",
    "total_paid": 2247.49,
    "date": "2025-12-24T14:30:00+00:00",
    "items": [
      {
        "product_id": 1,
        "product_name": "MacBook Pro",
        "quantity": 1,
        "price_at_purchase": 1999.99,
        "total": 1999.99
      }
    ]
  }
}
```

#### **Get Order Info**

* **Endpoint:** `GET /api/orders/{id}`
* **Headers:** `Authorization: Bearer <your_token>`

**Response (200 OK):**
```json
{
  "data": {
    "id": 55,
    "order_number": "ORD-50017",
    "total": 2247.49,
    "date": "2025-12-24T14:30:00+00:00",
    "items": [...]
  }
}
```

---
