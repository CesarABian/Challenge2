<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Testing\TestResponse;
use Laravel\Passport\Client;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    use RefreshDatabase;

    /**
     * @var string
     */
    protected string $uri = '';

    /**
     * @var string
     */
    protected string $resourceClass = '';

    /**
     * @var string
     */
    protected string $matchAttribute = 'id';

    /**
     * @var array
     */
    protected array $storeData = [];

    /**
     * @var array
     */
    protected array $updateData = [];
    
    /**
     * setUp
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('migrate',['-vvv' => true]);
        Artisan::call('passport:install',['-vvv' => true]);
        Artisan::call('db:seed',['-vvv' => true]);
    }

    /**
     * getOauthClient
     *
     * @return Client
     */
    protected function getOauthClient(mixed $oauthClientId = '2'): Client
    {
        return Client::findOrFail($oauthClientId);
    }
        
    /**
     * doOauthLogin
     *
     * @param  mixed $oauthClientId
     * @return TestResponse
     */
    protected function doOauthLogin($oauthClientId = '2'): TestResponse
    {
        $oauthClient = $this->getOauthClient($oauthClientId);

        $body = [
            'username' => 'admin@admin.com',
            'password' => 'admin',
            'client_id' => $oauthClient->id,
            'client_secret' => $oauthClient->secret,
            'grant_type' => 'password',
        ];
        return $this->json('POST','/oauth/token',$body,['Accept' => 'application/json']);
    }
    
    /**
     * doGetAccessToken
     *
     * @param  mixed $oauthClientId
     * @return TestResponse
     */
    protected function doGetAccessToken($oauthClientId = '2'): TestResponse
    {
        $oauthClient = $this->getOauthClient($oauthClientId);

        $loginResponse = $this->doOauthLogin($oauthClientId);
        $loginResponse = json_decode($loginResponse->getContent(), true);

        $body = [
            'refresh_token' => $loginResponse['refresh_token'],
            'client_id' => $oauthClient->id,
            'client_secret' => $oauthClient->secret,
            'grant_type' => 'refresh_token',
        ];
        return $this->json('POST','/oauth/token',$body,['Accept' => 'application/json']);
    }
    
    /**
     * getAuthHeader
     *
     * @param  mixed $oauthClientId
     * @return array
     */
    protected function getAuthHeader(mixed $oauthClientId = '2'): array
    {
        $loginResponse = $this->doOauthLogin($oauthClientId)->json();
        return ['Authorization' => 'Bearer ' . $loginResponse['access_token']];
    }
    
    /**
     * checkPaginatedWithData
     *
     * @param  TestResponse $response
     * @return void
     */
    protected function checkPaginatedWithData(TestResponse $response): void
    {
        $response
            ->assertStatus(200)
            ->assertJsonStructure(['data','links','meta'])
            ->assertJsonMissingExact(['data' => []]);
    }
    
    /**
     * testResourceStore
     *
     * @return void
     */
    protected function testResourceStore(): void
    {
        $response = $this->json('post', $this->uri, $this->storeData, $this->getAuthHeader());
        $response->assertSee($this->storeData);
    }
    
    /**
     * testResourceUpdate
     *
     * @return void
     */
    protected function testResourceUpdate(): void
    {
        $model = $this->resourceClass::where($this->matchAttribute, '=', $this->storeData[$this->matchAttribute])->first();
        if (!$model) {
            $model = $this->resourceClass::create($this->storeData);
        }
        $params = '/' . $model->id;
        $response = $this->json('put', $this->uri . $params, $this->updateData, $this->getAuthHeader());
        $response->assertSee($this->updateData);
    }
    
    /**
     * testResourceDelete
     *
     * @param  mixed $matchAttribute
     * @return void
     */
    protected function testResourceDelete(string $matchAttribute): void
    {
        $model = $this->resourceClass::create($this->storeData);
        $params = '/' . $model->id;
        $response = $this->json('delete', $this->uri . $params, [], $this->getAuthHeader());
        $model = $this->resourceClass::where($matchAttribute, '=', $model->$matchAttribute)->first();
        $response
            ->assertStatus(204);
        $this->assertNull($model);
    }
    
    /**
     * testResourceAll
     *
     * @return void
     */
    protected function testResourceAll(): void
    {
        $response = $this->json('get', $this->uri, [], $this->getAuthHeader());
        $this->checkPaginatedWithData($response);
    }
    
    /**
     * testResourceByRelationAttribute
     *
     * @param  mixed $relation
     * @param  mixed $attribute
     * @return void
     */
    protected function testResourceByRelationAttribute(string $relation, string $attribute): void
    {
        $model = $this->resourceClass::first();
        $params = '?' . $relation . '_' . $attribute . '=' . $model->$relation->$attribute;
        $response = $this->json('get', $this->uri . $params, [], $this->getAuthHeader());
        $this->checkPaginatedWithData($response);
    }
    
    /**
     * testResourceByAttribute
     *
     * @param  mixed $attribute
     * @return void
     */
    protected function testResourceByAttribute(string $attribute): void
    {
        $model = $this->resourceClass::first();
        $params = '?' . $attribute . '=' . $model->$attribute;
        $response = $this->json('get', $this->uri . $params, [], $this->getAuthHeader());
        $this->checkPaginatedWithData($response);
    }
    
    /**
     * testResourceByAttributeLikeXX
     *
     * @param  mixed $attribute
     * @return void
     */
    protected function testResourceByAttributeLikeXX(string $attribute): void
    {
        $model = $this->resourceClass::first();
        $params = '?' . $attribute . '=' . $model->$attribute . '&like=%%';
        $response = $this->json('get', $this->uri . $params, [], $this->getAuthHeader());
        $this->checkPaginatedWithData($response);
    }
    
    /**
     * testResourceByAttributeLikeX
     *
     * @param  mixed $attribute
     * @return void
     */
    protected function testResourceByAttributeLikeX(string $attribute): void
    {
        $model = $this->resourceClass::first();
        $params = '?' . $attribute . '=' . $model->$attribute . '&like=%';
        $response = $this->json('get', $this->uri . $params, [], $this->getAuthHeader());
        $this->checkPaginatedWithData($response);
    }
    
    /**
     * testResource
     *
     * @return void
     */
    protected function testResource(): void
    {
        $this->testResourceAll();
        $this->testResourceDelete($this->matchAttribute);
        $this->testResourceStore();
        $this->testResourceUpdate();
    }
    
    /**
     * getImageContent
     *
     * @return mixed
     */
    protected function getImageContent(): mixed
    {
        return file_get_contents('https://cdn.pixabay.com/photo/2015/04/23/22/00/tree-736885_1280.jpg');
    }
}
