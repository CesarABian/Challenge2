<?php

namespace Tests\Feature;

use App\Models\Payment;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    /**
     * @var string
     */
    protected string $uri = '/api/payment';

    /**
     * @var string
     */
    protected string $resourceClass = Payment::class;

    /**
     * @var string
     */
    protected string $matchAttribute = 'status';

    /**
     * @var array
     */
    protected array $storeData = [
        "payment_provider_id" => 1,
        "status" => null,
    ];

    /**
     * @var array
     */
    protected array $updateData = [
        "status" => 'waiting',
    ];
    
    /**
     * testPaymentResource
     *
     * @return void
     */
    public function testPaymentResource(): void
    {
        $response = $this->json('post', '/api/payment_provider', [
            "name" => "MercadoPago",
            "data" => [
                "client_id" => "46gh5g4de68sf46r8gsrtg6854g6s86dsg",
                "client_secret" => "46gh5g4de68sf46r8gsrtg6854g6s86dsg",
            ],
        ], $this->getAuthHeader());
        $this->testResourceStore();
        $this->testResourceUpdate();
        $this->testResourceAll();
        $this->testResourceDelete($this->matchAttribute);
    }
}