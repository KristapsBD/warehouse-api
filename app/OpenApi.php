<?php

declare(strict_types=1);

namespace App;

use OpenApi\Attributes as OA;

/**
 * OpenAPI Documentation
 *
 * All API documentation is centralized here to keep controllers clean.
 */
#[OA\Info(
    version: '1.0.0',
    title: 'Warehouse API',
    description: 'REST API for warehouse management - products and orders',
    contact: new OA\Contact(email: 'support@warehouse.local')
)]
#[OA\Server(url: '/api', description: 'Warehouse API Server')]
#[OA\Tag(name: 'Authentication', description: 'User authentication endpoints')]
#[OA\Tag(name: 'Products', description: 'Product management endpoints')]
#[OA\Tag(name: 'Orders', description: 'Order management endpoints')]
class OpenApi
{
}

/**
 * Authentication Endpoints
 */
#[OA\Post(
    path: '/login',
    summary: 'User login',
    description: 'Authenticate user with email and password to receive an API token',
    tags: ['Authentication'],
)]
#[OA\RequestBody(
    required: true,
    content: new OA\JsonContent(
        required: ['email', 'password'],
        properties: [
            new OA\Property(property: 'email', type: 'string', format: 'email', example: 'test@example.com'),
            new OA\Property(property: 'password', type: 'string', format: 'password', example: 'password'),
        ]
    )
)]
#[OA\Response(
    response: 200,
    description: 'Successful login',
    content: new OA\JsonContent(
        properties: [
            new OA\Property(property: 'token', type: 'string', example: '1|abc123xyz...'),
            new OA\Property(
                property: 'user',
                type: 'object',
                properties: [
                    new OA\Property(property: 'id', type: 'integer', example: 1),
                    new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
                    new OA\Property(property: 'email', type: 'string', example: 'test@example.com'),
                ]
            ),
        ]
    )
)]
#[OA\Response(response: 401, description: 'Invalid credentials')]
#[OA\Response(response: 422, description: 'Validation error')]
class LoginEndpoint
{
}

#[OA\Post(
    path: '/logout',
    summary: 'User logout',
    description: 'Revoke the current API token',
    security: [['sanctum' => []]],
    tags: ['Authentication'],
)]
#[OA\Response(response: 200, description: 'Successfully logged out')]
#[OA\Response(response: 401, description: 'Unauthenticated')]
class LogoutEndpoint
{
}

/**
 * Product Endpoints
 */
#[OA\Get(
    path: '/products',
    summary: 'List all products',
    description: 'Get paginated list of all available products',
    tags: ['Products'],
)]
#[OA\Parameter(
    name: 'page',
    in: 'query',
    description: 'Page number for pagination',
    required: false,
    schema: new OA\Schema(type: 'integer', default: 1, minimum: 1)
)]
#[OA\Response(
    response: 200,
    description: 'Successful operation',
    content: new OA\JsonContent(
        properties: [
            new OA\Property(
                property: 'data',
                type: 'array',
                items: new OA\Items(ref: '#/components/schemas/Product')
            ),
            new OA\Property(property: 'links', ref: '#/components/schemas/PaginationLinks'),
            new OA\Property(property: 'meta', ref: '#/components/schemas/PaginationMeta'),
        ]
    )
)]
class ProductIndexEndpoint
{
}

/**
 * Order Endpoints
 */
#[OA\Get(
    path: '/orders/{order}',
    summary: 'Get order details',
    description: 'Retrieve a specific order by its ID',
    security: [['sanctum' => []]],
    tags: ['Orders'],
)]
#[OA\Parameter(
    name: 'order',
    in: 'path',
    description: 'Order ID',
    required: true,
    schema: new OA\Schema(type: 'integer', example: 1)
)]
#[OA\Response(
    response: 200,
    description: 'Successful operation',
    content: new OA\JsonContent(
        properties: [
            new OA\Property(property: 'data', ref: '#/components/schemas/Order'),
        ]
    )
)]
#[OA\Response(response: 401, description: 'Unauthenticated')]
#[OA\Response(response: 404, description: 'Order not found')]
class OrderShowEndpoint
{
}

#[OA\Post(
    path: '/orders',
    summary: 'Create a new order',
    description: 'Create a new order with specified products and quantities',
    security: [['sanctum' => []]],
    tags: ['Orders'],
)]
#[OA\RequestBody(
    required: true,
    content: new OA\JsonContent(
        required: ['products'],
        properties: [
            new OA\Property(
                property: 'products',
                type: 'array',
                minItems: 1,
                maxItems: 50,
                items: new OA\Items(
                    required: ['id', 'quantity'],
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', description: 'Product ID', example: 1),
                        new OA\Property(property: 'quantity', type: 'integer', minimum: 1, maximum: 1000, example: 2),
                    ]
                ),
                example: [['id' => 1, 'quantity' => 2], ['id' => 3, 'quantity' => 1]]
            ),
        ]
    )
)]
#[OA\Response(
    response: 201,
    description: 'Order created successfully',
    content: new OA\JsonContent(
        properties: [
            new OA\Property(property: 'message', type: 'string', example: 'Order created successfully'),
            new OA\Property(property: 'order', ref: '#/components/schemas/Order'),
        ]
    )
)]
#[OA\Response(response: 400, description: 'Order creation failed (e.g., insufficient stock)')]
#[OA\Response(response: 401, description: 'Unauthenticated')]
#[OA\Response(response: 422, description: 'Validation error')]
class OrderStoreEndpoint
{
}

/**
 * Reusable Schemas
 */
#[OA\Schema(
    schema: 'Product',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Widget Pro'),
        new OA\Property(property: 'description', type: 'string', example: 'A high-quality widget'),
        new OA\Property(property: 'price', type: 'number', format: 'float', example: 29.99),
        new OA\Property(property: 'quantity', type: 'integer', example: 100),
    ]
)]
class ProductSchema
{
}

#[OA\Schema(
    schema: 'Order',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'order_number', type: 'string', example: 'ORD-2025-001'),
        new OA\Property(property: 'total', type: 'number', format: 'float', example: 149.99),
        new OA\Property(property: 'date', type: 'string', format: 'date-time', example: '2025-01-04T10:30:00+00:00'),
        new OA\Property(
            property: 'items',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/OrderItem')
        ),
    ]
)]
class OrderSchema
{
}

#[OA\Schema(
    schema: 'OrderItem',
    properties: [
        new OA\Property(property: 'product_id', type: 'integer', example: 5),
        new OA\Property(property: 'quantity', type: 'integer', example: 2),
        new OA\Property(property: 'price', type: 'number', format: 'float', example: 29.99),
        new OA\Property(property: 'total', type: 'number', format: 'float', example: 59.98),
    ]
)]
class OrderItemSchema
{
}

#[OA\Schema(
    schema: 'PaginationLinks',
    properties: [
        new OA\Property(property: 'first', type: 'string', nullable: true),
        new OA\Property(property: 'last', type: 'string', nullable: true),
        new OA\Property(property: 'prev', type: 'string', nullable: true),
        new OA\Property(property: 'next', type: 'string', nullable: true),
    ]
)]
class PaginationLinksSchema
{
}

#[OA\Schema(
    schema: 'PaginationMeta',
    properties: [
        new OA\Property(property: 'current_page', type: 'integer', example: 1),
        new OA\Property(property: 'last_page', type: 'integer', example: 10),
        new OA\Property(property: 'per_page', type: 'integer', example: 100),
        new OA\Property(property: 'total', type: 'integer', example: 1000),
    ]
)]
class PaginationMetaSchema
{
}
