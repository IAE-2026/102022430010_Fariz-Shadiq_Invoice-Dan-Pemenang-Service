<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Invoices",
    description: "Endpoint untuk mengelola data invoice"
)]
class InvoiceController extends Controller
{
    use BaseApiResponse;

    public function __construct(protected InvoiceService $invoiceService)
    {
    }

    #[OA\Get(
        path: "/api/v1/invoices",
        summary: "Daftar semua invoice",
        description: "Menampilkan daftar invoice dengan pagination.",
        operationId: "getInvoices",
        tags: ["Invoices"],
        security: [["ApiKeyAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "per_page",
                description: "Jumlah data per halaman (default: 10)",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "integer", default: 10)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Daftar invoice berhasil diambil",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "string", example: "success"),
                        new OA\Property(property: "message", type: "string", example: "Daftar invoice berhasil diambil."),
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(ref: "#/components/schemas/Invoice")
                        ),
                        new OA\Property(ref: "#/components/schemas/MetaResponse")
                    ]
                )
            ),
            new OA\Response(response: 401, ref: "#/components/responses/Unauthorized"),
            new OA\Response(response: 403, ref: "#/components/responses/Forbidden")
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $query = Invoice::with('winner')->latest();
        $perPage = min($request->get('per_page', 10), 100);
        $invoices = $query->paginate($perPage);

        return $this->paginatedResponse($invoices, 'Daftar invoice berhasil diambil.');
    }

    #[OA\Get(
        path: "/api/v1/invoices/{id}",
        summary: "Detail invoice berdasarkan ID",
        description: "Menampilkan detail data invoice beserta pemenang lelang.",
        operationId: "getInvoiceById",
        tags: ["Invoices"],
        security: [["ApiKeyAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "ID invoice",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer", example: 1)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Detail invoice berhasil diambil",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "string", example: "success"),
                        new OA\Property(property: "message", type: "string", example: "Detail invoice berhasil diambil."),
                        new OA\Property(property: "data", ref: "#/components/schemas/Invoice"),
                        new OA\Property(ref: "#/components/schemas/MetaResponse")
                    ]
                )
            ),
            new OA\Response(response: 404, ref: "#/components/responses/NotFound"),
            new OA\Response(response: 401, ref: "#/components/responses/Unauthorized")
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $invoice = Invoice::with('winner')->find($id);

        if (!$invoice) {
            return $this->notFoundResponse('Invoice');
        }

        return $this->successResponse($invoice, 'Detail invoice berhasil diambil.');
    }

    #[OA\Post(
        path: "/api/v1/invoices",
        summary: "Buat invoice baru",
        description: "Membuat invoice lelang baru berdasarkan data dari Service B atau data simulasi (mock).",
        operationId: "createInvoice",
        tags: ["Invoices"],
        security: [["ApiKeyAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["auction_id"],
                properties: [
                    new OA\Property(
                        property: "auction_id",
                        description: "ID lelang dari Service B",
                        type: "string",
                        example: "AUC-001"
                    ),
                    new OA\Property(
                        property: "use_mock",
                        description: "Gunakan data simulasi jika Service B tidak aktif",
                        type: "boolean",
                        example: true
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Invoice berhasil dibuat",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "string", example: "success"),
                        new OA\Property(property: "message", type: "string", example: "Invoice berhasil dibuat."),
                        new OA\Property(property: "data", ref: "#/components/schemas/Invoice"),
                        new OA\Property(ref: "#/components/schemas/MetaResponse")
                    ]
                )
            ),
            new OA\Response(response: 400, description: "Request tidak valid"),
            new OA\Response(response: 422, description: "Lelang belum berakhir atau gagal memproses data"),
            new OA\Response(response: 409, description: "Invoice untuk lelang tersebut sudah ada"),
            new OA\Response(response: 401, ref: "#/components/responses/Unauthorized"),
            new OA\Response(response: 403, ref: "#/components/responses/Forbidden")
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'auction_id' => 'required|string',
            'use_mock'   => 'nullable|boolean',
        ]);

        try {
            $invoice = $this->invoiceService->createInvoice(
                $request->input('auction_id'),
                (bool) $request->input('use_mock', false)
            );

            return $this->createdResponse($invoice, 'Invoice berhasil dibuat.');
        } catch (\Exception $e) {
            $code = $e->getCode();
            // Map common error codes or default to 422
            if ($code < 400 || $code > 599) {
                $code = 422;
            }
            return $this->errorResponse($e->getMessage(), null, $code);
        }
    }
}