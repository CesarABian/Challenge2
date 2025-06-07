<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * @var string
     */
    protected string $uri = '/api/user';

    /**
     * @var string
     */
    protected string $resourceClass = User::class;

    /**
     * @var string
     */
    protected string $matchAttribute = 'email';

    /**
     * @var array
     */
    protected array $storeData = [
        'name' => 'asdasda',
        'email' => 'asdasda@asdasda.com',
        'password' => 'asdasda',
        'user_role_id' => '1',
    ];

    /**
     * @var array
     */
    protected array $updateData = [
        'name' => 'hgf45gg',
    ];
    
    /**
     * testGetAuthenticatedUser
     *
     * @return void
     */
    protected function testGetAuthenticatedUser(): void
    {
        $params = '?info=all';
        $response = $this->json('get', $this->uri . $params, [], $this->getAuthHeader());
        $response
            ->assertStatus(200)
            ->assertJsonStructure(['data','links','meta'])
            ->assertJsonMissingExact(['data' => []]);
    }
    
    /**
     * testUserResource
     *
     * @return void
     */
    public function testUserResource(): void
    {
        $this->testResource();
        $this->testGetAuthenticatedUser();
        $this->testResourceByRelationAttribute('role', 'id');
        $this->testResourceByRelationAttribute('role', 'name');
        $this->testResourceByAttribute('email');
        $this->testResourceByAttributeLikeXX('email');
        $this->testResourceByAttributeLikeX('email');
    }
}