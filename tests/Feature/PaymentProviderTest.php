<?php

namespace Tests\Feature;

use App\Models\PaymentProvider;
use Tests\TestCase;

class PaymentProviderTest extends TestCase
{
    /**
     * @var string
     */
    protected string $uri = '/api/payment_provider';

    /**
     * @var string
     */
    protected string $resourceClass = PaymentProvider::class;

    /**
     * @var string
     */
    protected string $matchAttribute = 'name';

    /**
     * @var array
     */
    protected array $storeData = [
        "name" => "MercadoPago",
        "data" => [
            "client_id" => "46gh5g4de68sf46r8gsrtg6854g6s86dsg",
            "client_secret" => "46gh5g4de68sf46r8gsrtg6854g6s86dsg",
        ],
    ];

    /**
     * @var array
     */
    protected array $updateData = [
        "name" => "mercadopago",
        "data" => [
            "client_id" => "46gh5g4d54g6s86dsg",
            "client_secret" => "46gh5g4de68sf46r8gsrtg6854g6s86dsg",
        ],
    ];
    
    /**
     * testPaymentProviderResource
     *
     * @return void
     */
    public function testPaymentProviderResource(): void
    {
        $this->testResourceStore();
        $this->testResourceUpdate();
        $this->testResourceDelete($this->matchAttribute);
        $this->testResourceAll();
    }
    
    /**
     * testResourceStore
     *
     * @return void
     */
    protected function testResourceStore(): void
    {
        $response = $this->json('post', $this->uri, $this->storeData, $this->getAuthHeader());
        $response->assertSee(['name' => 'MercadoPago']);
    }
    
    /**
     * testResourceUpdate
     *
     * @return void
     */
    protected function testResourceUpdate(): void
    {
        $model = $this->resourceClass::where($this->matchAttribute, '=', $this->storeData[$this->matchAttribute])->first();
        $params = '/' . $model->id;
        $response = $this->json('put', $this->uri . $params, $this->updateData, $this->getAuthHeader());
        $response->assertSee(['name' => 'mercadopago']);
    }
}