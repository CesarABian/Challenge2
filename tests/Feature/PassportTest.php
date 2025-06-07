<?php

namespace Tests\Feature;

use Tests\TestCase;

class PassportTest extends TestCase
{   
    /**
     * testOauthLogin
     *
     * @return void
     */
    protected function testOauthLogin(): void
    {
        $response = $this->doOauthLogin();
        $response
            ->assertStatus(200)
            ->assertJsonStructure(['token_type','expires_in','access_token','refresh_token']);
    }
    
    /**
     * testGetAccessToken
     *
     * @return void
     */
    protected function testGetAccessToken(): void
    {
        $response = $this->doGetAccessToken();
        $response
            ->assertStatus(200)
            ->assertJsonStructure(['token_type','expires_in','access_token','refresh_token']);
    }
    /**
     * testPassportAuth
     *
     * @return void
     */
    public function testPassportAuth(): void
    {
        $this->testOauthLogin();
        $this->testGetAccessToken();
    }
}