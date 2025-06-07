<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\File;
use App\Models\Brand;
use App\Models\BrandLogo;
use Illuminate\Http\UploadedFile;

class BrandTest extends TestCase
{
    /**
     * @var string
     */
    protected string $uri = '/api/brand';

    /**
     * @var string
     */
    protected string $resourceClass = Brand::class;

    /**
     * @var string
     */
    protected string $matchAttribute = 'name';

    /**
     * @var array
     */
    protected array $storeData = [
        'name' => 'asdasda',
        'logo' => '',
    ];

    /**
     * @var array
     */
    protected array $updateData = [
        'name' => 'hgf45gg',
    ];
    
    /**
     * testBrandResource
     *
     * @return void
     */
    public function testBrandResource(): void
    {
        $this->testResource();
        $content = $this->getImageContent();
        $this->testImageResource($content);
    }
    
    /**
     * testImageResource
     *
     * @param  mixed $content
     * @return void
     */
    protected function testImageResource(mixed $content): void
    {
        $this->testUploadImage($content);
        $this->testShowImage();
        $this->testDeleteImage();
    }
    
    /**
     * createImage
     *
     * @param  Brand $model
     * @return BrandLogo
     */
    protected function createImage(Brand $model): ?BrandLogo
    {
        $file = File::create([
            'name' => uniqid(),
            'path' => '',
            'real_path' => '',
        ]);
        $image = BrandLogo::create([
            'brand_id' => $model->id,
            'file_id' => $file->id,
        ]);
        return $image;
    }
    
    /**
     * testUploadImage
     *
     * @param  mixed $content
     * @return void
     */
    protected function testUploadImage(mixed $content): void
    {
        $model = $this->resourceClass::first();
        $file = UploadedFile::fake()->createWithContent(
            'image.jpg', $content
        );
        $response = $this->post($this->uri . '/' . $model->id . '/logo', ['logo' => $file], $this->getAuthHeader());
        $response->assertSee([
            "brand_id" => $model->id,
        ]);
        $response->assertStatus(201);
    }
    
    /**
     * testShowImage
     *
     * @return void
     */
    protected function testShowImage(): void
    {
        $model = Brand::first();
        if (!$model) {
            $model = $this->resourceClass::create($this->storeData);
        }
        $image = $this->createImage($model);
        $imageData = [
            'id' => $image->id,
            'brand_id' => $image->brand_id,
            'file_id' => $image->file_id,
            'position' => $image->position,
        ];
        $params = '/' . $model->id . '/logo/' . $image->id;
        $response = $this->json('get', $this->uri . $params, $imageData, $this->getAuthHeader());
        $response->assertSee($imageData);
    }
    
    /**
     * testDeleteImage
     *
     * @return void
     */
    protected function testDeleteImage(): void
    {
        $model = Brand::first();
        if (!$model) {
            $model = $this->resourceClass::create($this->storeData);
        }
        $image = $this->createImage($model);
        $data = [];
        $params = '/' . $model->id . '/logo/' . $image->id;
        $response = $this->json('delete', $this->uri . $params, $data, $this->getAuthHeader());
        $response->assertSee($data);
    }
}