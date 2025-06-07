<?php

namespace Tests\Feature;

use App\Models\UserRole;
use Tests\TestCase;

class UserRoleTest extends TestCase
{
    /**
     * @var string
     */
    protected string $uri = '/api/user/role';

    /**
     * @var string
     */
    protected string $resourceClass = UserRole::class;

    /**
     * @var string
     */
    protected string $matchAttribute = 'name';

    /**
     * @var array
     */
    protected array $storeData = [
        'name' => 'asdasda',
    ];

    /**
     * @var array
     */
    protected array $updateData = [
        'name' => 'hgf45gg',
    ];
    
    /**
     * testUserRoleResource
     *
     * @return void
     */
    public function testUserRoleResource(): void
    {
        $this->testResource();
    }
}