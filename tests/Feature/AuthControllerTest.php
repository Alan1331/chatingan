<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user registration with valid data.
     */
    public function test_register_with_valid_data()
    {
        $response = $this->postJson('/api/register', [
            'name'                  => 'John Doe',
            'email'                 => 'john.doe@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'address'               => '123 Main Street, Springfield',
            'gender'                => true, // Assuming true represents male
            'marital_status'        => 'single',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'user' => [
                         'id',
                         'name',
                         'email',
                         'address',
                         'gender',
                         'marital_status',
                         'created_at',
                         'updated_at',
                     ],
                     'token',
                 ]);

        // Assert the user was created in the database
        $this->assertDatabaseHas('users', [
            'email' => 'john.doe@example.com',
        ]);
    }

    /**
     * Test registration with invalid data (validation errors).
     */
    public function test_register_with_invalid_data()
    {
        $response = $this->postJson('/api/register', [
            'name'           => '', // Name is required
            'email'          => 'invalid-email', // Invalid email format
            'password'       => 'pass', // Too short
            'address'        => '',
            'gender'         => 'not-boolean', // Invalid boolean
            'marital_status' => 'complicated', // Invalid value
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors([
                     'name',
                     'email',
                     'password',
                     'address',
                     'gender',
                     'marital_status',
                 ]);
    }

    /**
     * Test user login with valid credentials.
     */
    public function test_login_with_valid_credentials()
    {
        // Create a user
        $user = User::factory()->create([
            'email'    => 'jane.doe@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email'    => 'jane.doe@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'message',
                     'user' => [
                         'id',
                         'name',
                         'email',
                         'address',
                         'gender',
                         'marital_status',
                         'created_at',
                         'updated_at',
                     ],
                     'token',
                 ]);
    }

    /**
     * Test login with invalid credentials.
     */
    public function test_login_with_invalid_credentials()
    {
        // Create a user
        $user = User::factory()->create([
            'email'    => 'jane.doe@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email'    => 'jane.doe@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
                 ->assertJson([
                     'error' => 'Invalid credentials',
                 ]);
    }

    /**
     * Test accessing the profile of an authenticated user.
     */
    public function test_get_profile_authenticated()
    {
        // Create and authenticate a user
        $user = User::factory()->create();

        $token = auth()->login($user);

        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->getJson('/api/users/me');

        $response->assertStatus(200)
                 ->assertJson([
                     'user' => [
                         'id'             => $user->id,
                         'name'           => $user->name,
                         'email'          => $user->email,
                         'address'        => $user->address,
                         'gender'         => $user->gender,
                         'marital_status' => $user->marital_status,
                         'created_at'     => $user->created_at->toISOString(),
                         'updated_at'     => $user->updated_at->toISOString(),
                     ],
                 ]);
    }

    /**
     * Test accessing the profile without authentication.
     */
    public function test_get_profile_unauthenticated()
    {
        $response = $this->getJson('/api/users/me');

        $response->assertStatus(401)
                 ->assertJson([
                     'message' => 'Token not provided',
                 ]);
    }

    /**
     * Test logout of an authenticated user.
     */
    public function test_logout_authenticated()
    {
        // Create and authenticate a user
        $user = User::factory()->create();

        $token = auth()->login($user);

        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->postJson('/api/logout');

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'User logged out successfully',
                 ]);

        // Attempt to access a protected route with the same token
        $profileResponse = $this->withHeader('Authorization', "Bearer $token")
                                ->getJson('/api/users/me');

        $profileResponse->assertStatus(401)
                        ->assertJson([
                            'message' => 'The token has been blacklisted',
                        ]);
    }

    /**
     * Test logout without authentication.
     */
    public function test_logout_unauthenticated()
    {
        $response = $this->postJson('/api/logout');

        $response->assertStatus(401)
                 ->assertJson([
                     'message' => 'Token not provided',
                 ]);
    }

    /**
     * Test updating the authenticated user's profile with valid data.
     */
    public function test_update_profile_with_valid_data()
    {
        // Create and authenticate a user
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'address' => '123 Main Street, Springfield',
            'gender' => true,
            'marital_status' => 'single',
        ]);

        $token = JWTAuth::fromUser($user);

        // New profile data
        $updatedData = [
            'name' => 'Jane Doe',
            'email' => 'jane.doe@example.com',
            'address' => '456 New Street, Springfield',
            'gender' => false,  // Female
            'marital_status' => 'married',
        ];

        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->putJson('/api/users/me', $updatedData);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Profile updated successfully',
                     'user' => [
                         'name' => 'Jane Doe',
                         'email' => 'jane.doe@example.com',
                         'address' => '456 New Street, Springfield',
                         'gender' => false,
                         'marital_status' => 'married',
                     ],
                 ]);

        // Ensure the user was updated in the database
        $this->assertDatabaseHas('users', [
            'email' => 'jane.doe@example.com',
            'name' => 'Jane Doe',
        ]);
    }

    /**
     * Test updating the authenticated user's profile with invalid data.
     */
    public function test_update_profile_with_invalid_data()
    {
        // Create and authenticate a user
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        // Invalid profile data
        $invalidData = [
            'email' => 'not-a-valid-email',  // Invalid email format
            'password' => 'short',           // Password too short
        ];

        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->putJson('/api/users/me', $invalidData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email', 'password']);
    }

    /**
     * Test updating the profile without authentication.
     */
    public function test_update_profile_unauthenticated()
    {
        // New profile data
        $updatedData = [
            'name' => 'Jane Doe',
            'email' => 'jane.doe@example.com',
        ];

        $response = $this->putJson('/api/users/me', $updatedData);

        $response->assertStatus(401)
                 ->assertJson([
                     'message' => 'Token not provided',
                 ]);
    }
}