<?php

namespace Tests\Feature;

use App\Models\Cart;
use Tests\TestCase;

class CartTest extends TestCase
{
    /**
     * @var string
     */
    protected string $uri = '/api/cart';

    /**
     * @var string
     */
    protected string $resourceClass = Cart::class;

    /**
     * @var string
     */
    protected string $matchAttribute = 'user_id';

    /**
     * @var array
     */
    protected array $storeData = [
        'quantity' => 1,
        'user_id' => 2,
        'product_id' => 1,
    ];

    /**
     * @var array
     */
    protected array $updateData = [
        'quantity' => 2,
    ];
    
    /**
     * testCartResource
     *
     * @return void
     */
    public function testCartResource(): void
    {
        $response = $this->json('get', $this->uri, [], $this->getAuthHeader());
        $response->assertSee([
            'cart',
            'total',
        ]);
        $response->assertStatus(200);

        $this->testResourceDelete($this->matchAttribute);
        $this->testResourceStore();
        $this->testResourceUpdate();
    }
}