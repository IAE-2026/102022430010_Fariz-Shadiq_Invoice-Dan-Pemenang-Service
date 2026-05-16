<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Invoices",
    description: "Invoices API"
)]
class InvoiceController extends Controller
{
    #[OA\Get(
        path: "/api/v1/invoices",
        summary: "Daftar invoice",
        tags: ["Invoices"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Success"
            )
        ]
    )]
    public function index(): JsonResponse
    {
        return response()->json([
            'message' => 'Invoices endpoint works'
        ]);
    }

    #[OA\Get(
        path: "/api/v1/invoices/{id}",
        summary: "Detail invoice",
        tags: ["Invoices"],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "Invoice ID",
                in: "path",
                required: true
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Success"
            )
        ]
    )]
    public function show($id): JsonResponse
    {
        return response()->json([
            'message' => 'Detail invoice',
            'id' => $id
        ]);
    }

    #[OA\Post(
        path: "/api/v1/invoices",
        summary: "Buat invoice",
        tags: ["Invoices"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: "auction_id",
                        type: "string",
                        example: "AUC-001"
                    ),
                    new OA\Property(
                        property: "use_mock",
                        type: "boolean",
                        example: true
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Invoice created"
            )
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        return response()->json([
            'message' => 'Invoice created',
            'data' => $request->all()
        ], 201);
    }
}