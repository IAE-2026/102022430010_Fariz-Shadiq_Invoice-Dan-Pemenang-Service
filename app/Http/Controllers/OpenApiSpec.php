<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "Invoice Winner API",
    description: "API Documentation untuk Invoice & Winner Service"
)]

#[OA\Server(
    url: "http://127.0.0.1:8000",
    description: "Local Server"
)]

#[OA\SecurityScheme(
    securityScheme: "ApiKeyAuth",
    type: "apiKey",
    name: "X-IAE-KEY",
    in: "header",
    description: "Sertakan API Key berupa NIM Anda (contoh: 102022430010) pada header X-IAE-KEY"
)]

// --- Response Components ---

#[OA\Response(
    response: "Unauthorized",
    description: "API Key tidak ditemukan atau tidak disertakan",
    content: new OA\JsonContent(
        properties: [
            new OA\Property(property: "status", type: "string", example: "error"),
            new OA\Property(property: "message", type: "string", example: "API Key tidak ditemukan. Sertakan header X-IAE-KEY."),
            new OA\Property(
                property: "errors",
                properties: [
                    new OA\Property(property: "header", type: "string", example: "X-IAE-KEY header is required.")
                ],
                type: "object"
            )
        ]
    )
)]

#[OA\Response(
    response: "Forbidden",
    description: "API Key tidak valid atau tidak memiliki akses",
    content: new OA\JsonContent(
        properties: [
            new OA\Property(property: "status", type: "string", example: "error"),
            new OA\Property(property: "message", type: "string", example: "API Key tidak valid atau tidak memiliki akses."),
            new OA\Property(
                property: "errors",
                properties: [
                    new OA\Property(property: "header", type: "string", example: "Invalid X-IAE-KEY value.")
                ],
                type: "object"
            )
        ]
    )
)]

#[OA\Response(
    response: "NotFound",
    description: "Resource tidak ditemukan",
    content: new OA\JsonContent(
        properties: [
            new OA\Property(property: "status", type: "string", example: "error"),
            new OA\Property(property: "message", type: "string", example: "Resource tidak ditemukan."),
            new OA\Property(property: "errors", type: "object", nullable: true, example: null)
        ]
    )
)]

// --- Schema Components ---

#[OA\Schema(
    schema: "Winner",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "auction_id", type: "string", example: "AUC-001"),
        new OA\Property(property: "item_id", type: "string", example: "ITM-001"),
        new OA\Property(property: "bidder_id", type: "string", example: "USR-001"),
        new OA\Property(property: "bidder_name", type: "string", example: "Budi Santoso"),
        new OA\Property(property: "bidder_email", type: "string", example: "budi@email.com"),
        new OA\Property(property: "item_name", type: "string", example: "Laptop Asus ROG"),
        new OA\Property(property: "winning_bid", type: "number", format: "float", example: 15000000.00),
        new OA\Property(property: "starting_price", type: "number", format: "float", example: 10000000.00),
        new OA\Property(property: "bid_id", type: "string", example: "BID-001"),
        new OA\Property(property: "status", type: "string", example: "invoiced"),
        new OA\Property(property: "auction_ended_at", type: "string", format: "date-time", example: "2024-01-15T12:00:00Z"),
        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2024-01-15T12:00:00Z"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2024-01-15T12:00:00Z")
    ]
)]

#[OA\Schema(
    schema: "WinnerDetail",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "auction_id", type: "string", example: "AUC-001"),
        new OA\Property(property: "item_id", type: "string", example: "ITM-001"),
        new OA\Property(property: "bidder_id", type: "string", example: "USR-001"),
        new OA\Property(property: "bidder_name", type: "string", example: "Budi Santoso"),
        new OA\Property(property: "bidder_email", type: "string", example: "budi@email.com"),
        new OA\Property(property: "item_name", type: "string", example: "Laptop Asus ROG"),
        new OA\Property(property: "winning_bid", type: "number", format: "float", example: 15000000.00),
        new OA\Property(property: "starting_price", type: "number", format: "float", example: 10000000.00),
        new OA\Property(property: "bid_id", type: "string", example: "BID-001"),
        new OA\Property(property: "status", type: "string", example: "invoiced"),
        new OA\Property(property: "auction_ended_at", type: "string", format: "date-time", example: "2024-01-15T12:00:00Z"),
        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2024-01-15T12:00:00Z"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2024-01-15T12:00:00Z"),
        new OA\Property(property: "invoice", ref: "#/components/schemas/Invoice")
    ]
)]

#[OA\Schema(
    schema: "Invoice",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "invoice_number", type: "string", example: "INV-2024-000001"),
        new OA\Property(property: "winner_id", type: "integer", example: 1),
        new OA\Property(property: "auction_id", type: "string", example: "AUC-001"),
        new OA\Property(property: "item_id", type: "string", example: "ITM-001"),
        new OA\Property(property: "bidder_id", type: "string", example: "USR-001"),
        new OA\Property(property: "bidder_name", type: "string", example: "Budi Santoso"),
        new OA\Property(property: "bidder_email", type: "string", example: "budi@email.com"),
        new OA\Property(property: "item_name", type: "string", example: "Laptop Asus ROG"),
        new OA\Property(property: "subtotal", type: "number", format: "float", example: 15000000.00),
        new OA\Property(property: "tax_amount", type: "number", format: "float", example: 1650000.00),
        new OA\Property(property: "admin_fee", type: "number", format: "float", example: 300000.00),
        new OA\Property(property: "total_amount", type: "number", format: "float", example: 16950000.00),
        new OA\Property(property: "status", type: "string", example: "unpaid"),
        new OA\Property(property: "issued_at", type: "string", format: "date-time", example: "2024-01-15T12:05:00Z"),
        new OA\Property(property: "due_date", type: "string", format: "date-time", example: "2024-01-22T12:05:00Z"),
        new OA\Property(property: "paid_at", type: "string", format: "date-time", nullable: true, example: null),
        new OA\Property(property: "notes", type: "string", example: "Invoice otomatis untuk pemenang lelang AUC-001"),
        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2024-01-15T12:05:00Z"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2024-01-15T12:05:00Z")
    ]
)]

#[OA\Schema(
    schema: "MetaResponse",
    properties: [
        new OA\Property(
            property: "meta",
            properties: [
                new OA\Property(property: "service_name", type: "string", example: "Invoice-Winner-Service"),
                new OA\Property(property: "api_version", type: "string", example: "v1")
            ],
            type: "object"
        )
    ]
)]

class OpenApiSpec
{
}