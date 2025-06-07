<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\CategoryImage;
use App\Models\File;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    /**
     * @var string
     */
    protected string $uri = '/api/category';

    /**
     * @var string
     */
    protected string $resourceClass = Category::class;

    /**
     * @var string
     */
    protected string $matchAttribute = 'name';

    /**
     * @var array
     */
    protected array $storeData = [
        'name' => 'asdasda',
        'icon' => '',
        'parent_id' => '',
    ];

    /**
     * @var array
     */
    protected array $updateData = [
        'name' => 'hgf45gg',
    ];
    
    /**
     * testCategoryResource
     *
     * @return void
     */
    public function testCategoryResource(): void
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
        $this->testUpdateImagePosition();
        $this->testShowImage();
        $this->testDeleteImage();
    }
    
    /**
     * createImage
     *
     * @param  Category $model
     * @return CategoryImage
     */
    protected function createImage(Category $model): ?CategoryImage
    {
        $file = File::create([
            'name' => uniqid(),
            'path' => '',
            'real_path' => '',
        ]);
        $image = CategoryImage::create([
            'category_id' => $model->id,
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
        $response = $this->post($this->uri . '/' . $model->id . '/image', ['image' => $file], $this->getAuthHeader());
        $response->assertSee([
            "category_id" => $model->id,
        ]);
        $response->assertStatus(201);
    }
    
    /**
     * testUpdateImagePosition
     *
     * @return void
     */
    protected function testUpdateImagePosition(): void
    {
        $model = Category::first();
        if (!$model) {
            $model = $this->resourceClass::create($this->storeData);
        }
        $updateData = [
            'position' => 5,
        ];
        $params = '/' . $model->id . '/image/' . $this->createImage($model)->id;
        $response = $this->json('put', $this->uri . $params, $updateData, $this->getAuthHeader());
        $response->assertSee($updateData);
    }
    
    /**
     * testShowImage
     *
     * @return void
     */
    protected function testShowImage(): void
    {
        $model = Category::first();
        if (!$model) {
            $model = $this->resourceClass::create($this->storeData);
        }
        $image = $this->createImage($model);
        $imageData = [
            'id' => $image->id,
            'category_id' => $image->category_id,
            'file_id' => $image->file_id,
            'position' => $image->position,
        ];
        $params = '/' . $model->id . '/image/' . $image->id;
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
        $model = Category::first();
        if (!$model) {
            $model = $this->resourceClass::create($this->storeData);
        }
        $image = $this->createImage($model);
        $data = [];
        $params = '/' . $model->id . '/image/' . $image->id;
        $response = $this->json('delete', $this->uri . $params, $data, $this->getAuthHeader());
        $response->assertSee($data);
    }
}