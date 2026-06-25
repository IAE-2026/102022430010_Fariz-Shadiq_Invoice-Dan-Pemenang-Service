<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Winner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceWinnerApiTest extends TestCase
{
    use RefreshDatabase;

    protected string $apiKey;

    protected function setUp(): void
    {
        parent::setUp();
        // Get the API Key configured for tests
        $this->apiKey = config('app.iae_api_key') ?: '2021000000000';
    }

    /**
     * Test X-IAE-KEY header is required.
     */
    public function test_api_requires_key_header(): void
    {
        $response = $this->getJson('/api/v1/winners');

        $response->assertStatus(401);
        $response->assertJsonStructure([
            'status',
            'message',
            'errors' => ['header']
        ]);
        $this->assertEquals('error', $response->json('status'));
    }

    /**
     * Test wrong X-IAE-KEY returns 403 Forbidden.
     */
    public function test_api_rejects_invalid_key_header(): void
    {
        $response = $this->withHeaders([
            'X-IAE-KEY' => 'WRONG_API_KEY'
        ])->getJson('/api/v1/winners');

        $response->assertStatus(403);
        $response->assertJsonStructure([
            'status',
            'message',
            'errors' => ['header']
        ]);
    }

    /**
     * Test successful request to winners index.
     */
    public function test_get_winners_list_with_valid_key(): void
    {
        // Seed winner
        $winner = Winner::create([
            'auction_id'     => 'AUC-T1',
            'item_id'        => 'ITM-T1',
            'bidder_id'      => 'USR-T1',
            'bidder_name'    => 'Test Bidder',
            'winning_bid'    => 5000000,
            'starting_price' => 4000000,
            'status'         => 'pending'
        ]);

        $response = $this->withHeaders([
            'X-IAE-KEY' => $this->apiKey
        ])->getJson('/api/v1/winners');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');
        $response->assertJsonStructure([
            'status',
            'message',
            'data',
            'meta' => [
                'service_name',
                'api_version',
                'pagination' => [
                    'total',
                    'per_page',
                    'current_page',
                    'last_page'
                ]
            ]
        ]);
        $this->assertEquals('success', $response->json('status'));
        $this->assertCount(1, $response->json('data'));
    }

    /**
     * Test get invoices list.
     */
    public function test_get_invoices_list(): void
    {
        $response = $this->withHeaders([
            'X-IAE-KEY' => $this->apiKey
        ])->getJson('/api/v1/invoices');

        $response->assertStatus(200);
        $this->assertEquals('success', $response->json('status'));
    }

    /**
     * Test create invoice with mock data.
     */
    public function test_create_invoice_successfully(): void
    {
        $response = $this->withHeaders([
            'X-IAE-KEY' => $this->apiKey
        ])->postJson('/api/v1/invoices', [
            'auction_id' => 'AUC-T2',
            'use_mock'   => true
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'invoice_number',
                'auction_id',
                'total_amount',
                'status'
            ]
        ]);
        $this->assertEquals('success', $response->json('status'));
        $this->assertEquals('unpaid', $response->json('data.status'));
        
        // Assert record exists in database
        $this->assertDatabaseHas('invoices', [
            'auction_id' => 'AUC-T2'
        ]);
    }

    /**
     * Test GraphQL Query hello.
     */
    public function test_graphql_hello_query(): void
    {
        $query = '
            query {
                hello {
                    name
                    nim
                    message
                }
            }
        ';

        $response = $this->postJson('/graphql', [
            'query' => $query
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'hello' => [
                    'name' => 'Fariz Shadiq',
                    'nim' => '102022430010',
                    'message' => 'EAI MENYENANGKAN!'
                ]
            ]
        ]);
    }

    /**
     * Test GraphQL Mutation createInvoice.
     */
    public function test_graphql_create_invoice_mutation(): void
    {
        $mutation = '
            mutation {
                createInvoice(auction_id: "AUC-GQL", use_mock: true) {
                    invoice_number
                    auction_id
                    total_amount
                    status
                }
            }
        ';

        $response = $this->postJson('/graphql', [
            'query' => $mutation
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'createInvoice' => [
                    'invoice_number',
                    'auction_id',
                    'total_amount',
                    'status'
                ]
            ]
        ]);
        
        $this->assertEquals('AUC-GQL', $response->json('data.createInvoice.auction_id'));
        $this->assertEquals('unpaid', $response->json('data.createInvoice.status'));

        // Assert record exists in database
        $this->assertDatabaseHas('invoices', [
            'auction_id' => 'AUC-GQL'
        ]);
    }
}
