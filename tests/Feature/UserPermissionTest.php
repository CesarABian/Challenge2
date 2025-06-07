<?php

namespace Tests\Feature;

use App\Models\UserPermission;
use Tests\TestCase;

class UserPermissionTest extends TestCase
{
    /**
     * @var string
     */
    protected string $uri = '/api/user/permission';

    /**
     * @var string
     */
    protected string $resourceClass = UserPermission::class;

    /**
     * @var string
     */
    protected string $matchAttribute = 'section_id';

    /**
     * @var array
     */
    protected array $storeData = [
        'user_role_id' => 1,
        'section_id' => 2,
        'action' => 'list',
    ];

    /**
     * @var array
     */
    protected array $updateData = [
        'action' => 'show',
    ];
    
    /**
     * testUserPermissionResource
     *
     * @return void
     */
    public function testUserPermissionResource(): void
    {
        $this->testResource();
    }
}