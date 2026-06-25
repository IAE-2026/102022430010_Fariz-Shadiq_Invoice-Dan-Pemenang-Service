<?php

namespace App\GraphQL\Mutations;

use App\Services\InvoiceService;

class CreateInvoiceMutation
{
    public function __construct(protected InvoiceService $invoiceService)
    {
    }

    /**
     * Resolve the mutation.
     *
     * @param  mixed  $rootValue
     * @param  array  $args
     * @return array
     */
    public function __invoke(mixed $rootValue, array $args): array
    {
        $auctionId = $args['auction_id'];
        $useMock   = $args['use_mock'] ?? false;

        return $this->invoiceService->createInvoice($auctionId, $useMock);
    }
}
