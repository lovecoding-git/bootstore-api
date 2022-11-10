<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class BookTest extends TestCase
{
    public function createTestBook()
    {
        return Book::create([
            'user_id' => Auth::user()->id,
            'title' => 'test',
            'description' => 'description',
            'price' => '3.99',
        ]);
    }
    public function createTestUser($username = 'test', $password = '12341234')
    {
        return User::create([
            'name' => 'test',
            'username' => $username,
            'email'=> 'test@test.com',
            'password' => bcrypt($password)
        ]);
    }
    /**
     * Authenticate user.
     *
     * @return string
     */
    protected function authenticate()
    {
        $username = "test";
        $password = "12341234";

        // Creating Users
        $this->createTestUser($username, $password);

        return $token = Auth::attempt([
            'username' => $username,
            'password' => $password,
        ]);
    }
    /**
     * test create book.
     *
     * @return void
     */
    public function test_create_book()
    {
        $token = $this->authenticate();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
        ])->json('POST','api/books',[
            'title' => 'Test Product',
            'description' => 'Test Product',
            'price' => '29.99'
        ]);


        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);
    }
    /**
     * test update product.
     *
     * @return void
     */
    public function test_update_book()
    {
        $token = $this->authenticate();

        $book = $this->createTestBook();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
        ])->json('PUT',"api/books/$book->id",[
            'title' => 'Test Product',
            'description' => 'Test Product',
            'price' => '29.99'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);
    }
    /**
     * test find product.
     *
     * @return void
     */
    public function test_find_book()
    {
        $token = $this->authenticate();

        $book = $this->createTestBook();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
        ])->json('GET',"api/books/{$book->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);
    }
    /**
     * test get all products.
     *
     * @return void
     */
    public function test_get_all_books()
    {
        $response = $this->json('GET','api/books');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);
    }
    /**
     * test delete products.
     *
     * @return void
     */
    public function test_delete_book()
    {
        $token = $this->authenticate();

        $book = $this->createTestBook();


        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
        ])->json('DELETE',"api/books/$book->id");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);
    }
    /**
     * test delete products.
     *
     * @return void
     */
    public function test_unpublish_book()
    {
        $token = $this->authenticate();

        $book = $this->createTestBook();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
        ])->json('DELETE',"api/books/$book->id/unpublish");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);
    }
}
