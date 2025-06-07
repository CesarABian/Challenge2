<?php

namespace Tests\Feature;

use App\Models\File;
use App\Models\Product;
use App\Models\ProductDocument;
use App\Models\ProductImage;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ProductTest extends TestCase
{
    /**
     * @var string
     */
    protected string $uri = '/api/product';

    /**
     * @var string
     */
    protected string $resourceClass = Product::class;

    /**
     * @var string
     */
    protected string $matchAttribute = 'code';

    /**
     * @var array
     */
    protected array $storeData = [
        'code' => 1234,
        'title' => 1234,
        'description' => '1234',
        'price' => 1234,
        'price_unit' => 1234,
        'weight' => 1234,
        'weight_unit' => 1234,
        'width' => 1234,
        'height' => 1234,
        'dimension_unit' => 1234,
        'material' => 1234,
        'color' => 1234,
        'package_untis' => 1234,
        'stock' => 1234,
        'brand_id' => 1,
        'category_id' => 1,
    ];

    /**
     * @var array
     */
    protected array $updateData = [
        'code' => 987987,
    ];
    
    /**
     * testProductResource
     *
     * @return void
     */
    public function testProductResource(): void
    {
        $this->testResource();
        $this->testResourceByRelationAttribute('user', 'id');
        $this->testResourceByRelationAttribute('user', 'name');
        $this->testResourceByRelationAttribute('category', 'id');
        $this->testResourceByRelationAttribute('category', 'name');
        $this->testResourceByRelationAttribute('brand', 'id');
        $this->testResourceByRelationAttribute('brand', 'name');
        $this->testResourceByAttribute('code');
        $this->testResourceByAttributeLikeXX('code');
        $this->testResourceByAttributeLikeX('code');
        $this->testResourceByAttribute('title');
        $this->testResourceByAttributeLikeXX('title');
        $this->testResourceByAttributeLikeX('title');
        $content = $this->getImageContent();
        $this->testImageResource($content);
        $this->testDocumentResource($content);
    }
    
    /**
     * testDocumentResource
     *
     * @param  mixed $content
     * @return void
     */
    protected function testDocumentResource(mixed $content): void
    {
        $this->testUploadDocument($content);
        $this->testUpdateDocumentPosition();
        $this->testShowDocument();
        $this->testDeleteDocument();
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
     * @param  Product $model
     * @return ProductImage
     */
    protected function createImage(Product $model): ?ProductImage
    {
        $file = File::create([
            'name' => uniqid(),
            'path' => '',
            'real_path' => '',
        ]);
        $image = ProductImage::create([
            'product_id' => $model->id,
            'file_id' => $file->id,
        ]);
        return $image;
    }
    
    /**
     * createDocument
     *
     * @param  Product $model
     * @return ProductDocument
     */
    protected function createDocument(Product $model): ?ProductDocument
    {
        $file = File::create([
            'name' => uniqid(),
            'path' => '',
            'real_path' => '',
        ]);
        $document = ProductDocument::create([
            'product_id' => $model->id,
            'file_id' => $file->id,
        ]);
        return $document;
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
            "product_id" => $model->id,
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
        $model = Product::first();
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
        $model = Product::first();
        if (!$model) {
            $model = $this->resourceClass::create($this->storeData);
        }
        $image = $this->createImage($model);
        $imageData = [
            'id' => $image->id,
            'product_id' => $image->product_id,
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
        $model = Product::first();
        if (!$model) {
            $model = $this->resourceClass::create($this->storeData);
        }
        $image = $this->createImage($model);
        $data = [];
        $params = '/' . $model->id . '/image/' . $image->id;
        $response = $this->json('delete', $this->uri . $params, $data, $this->getAuthHeader());
        $response->assertSee($data);
    }
    
    /**
     * testUploadDocument
     *
     * @param  mixed $content
     * @return void
     */
    protected function testUploadDocument(mixed $content): void
    {
        $model = $this->resourceClass::first();
        $file = UploadedFile::fake()->createWithContent(
            'document.jpg', $content
        );
        $response = $this->post($this->uri . '/' . $model->id . '/document', ['document' => $file], $this->getAuthHeader());
        $response->assertSee([
            "product_id" => $model->id,
        ]);
        $response->assertStatus(201);
    }
    
    /**
     * testUpdateDocumentPosition
     *
     * @return void
     */
    protected function testUpdateDocumentPosition(): void
    {
        $model = Product::first();
        if (!$model) {
            $model = $this->resourceClass::create($this->storeData);
        }
        $updateData = [
            'position' => 5,
        ];
        $params = '/' . $model->id . '/document/' . $this->createDocument($model)->id;
        $response = $this->json('put', $this->uri . $params, $updateData, $this->getAuthHeader());
        $response->assertSee($updateData);
    }
    
    /**
     * testShowDocument
     *
     * @return void
     */
    protected function testShowDocument(): void
    {
        $model = Product::first();
        if (!$model) {
            $model = $this->resourceClass::create($this->storeData);
        }
        $document = $this->createDocument($model);
        $data = [
            'id' => $document->id,
            'product_id' => $document->product_id,
            'file_id' => $document->file_id,
            'position' => $document->position,
        ];
        $params = '/' . $model->id . '/document/' . $document->id;
        $response = $this->json('get', $this->uri . $params, $data, $this->getAuthHeader());
        $response->assertSee($data);
    }
    
    /**
     * testDeleteDocument
     *
     * @return void
     */
    protected function testDeleteDocument(): void
    {
        $model = Product::first();
        if (!$model) {
            $model = $this->resourceClass::create($this->storeData);
        }
        $document = $this->createDocument($model);
        $data = [];
        $params = '/' . $model->id . '/document/' . $document->id;
        $response = $this->json('delete', $this->uri . $params, $data, $this->getAuthHeader());
        $response->assertSee($data);
    }
}