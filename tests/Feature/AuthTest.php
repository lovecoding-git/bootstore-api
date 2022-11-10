<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AuthTest extends TestCase
{
    /**
     * Register Test
     *
     * @return void
     */
    public function test_register()
    {
        $response = $this->json('POST', '/api/register', [
            'name'  =>  'test',
            'username'  => 'test',
            'email'  =>  'test@test.com',
            'password'  => '12341234',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);


    }
    /**
     * Register Validation Test
     *
     * @return void
     */
    public function test_register_validation()
    {
        $this->json('POST', 'api/register')
            ->assertStatus(200)
            ->assertJson([
                'status' => 'false',
            ]);
    }

    /**
     * Login Test
     *
     * @return void
     */
    public function test_login()
    {
        $username = "test";
        $password = "12341234";

        // Creating Users
        $this->createTestUser($username, $password);


        // Simulated landing
        $response = $this->json('POST','/api/login',[
            'username' => $username,
            'password' => $password,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);

        // Delete users
        User::where('email','test@test.com')->delete();
    }
    /**
     * Login Validation
     *
     * @return void
     */
    public function test_login_validation()
    {
        $this->json('POST', 'api/login')
            ->assertStatus(200)
            ->assertJson([
                'status' => 'false',
            ]);

    }
    /**
     * Logout Test
     *
     * @return void
     */
    public function test_logout()
    {
        $username = "test";
        $password = "12341234";

        // Creating Users
        $this->createTestUser($username, $password);

        $token = Auth::attempt(['username' => $username, 'password'=>$password]);

        $headers = ['Authorization' => "Bearer $token"];
        $this->json('POST', 'api/logout', [], $headers)
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);

        User::where('username', $username)->delete();
    }
    protected function createTestUser($username = 'test', $password = '12341234')
    {
        return User::create([
            'name' => 'test',
            'username' => $username,
            'email'=> 'test@test.com',
            'password' => bcrypt($password)
        ]);
    }
}
