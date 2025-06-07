<?php

namespace Tests\Feature;

use App\Models\Section;
use Tests\TestCase;

class SectionTest extends TestCase
{
    /**
     * @var string
     */
    protected string $uri = '/api/section';

    /**
     * @var string
     */
    protected string $resourceClass = Section::class;

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
     * testSectionResource
     *
     * @return void
     */
    public function testSectionResource(): void
    {
        $this->testResource();
    }
}