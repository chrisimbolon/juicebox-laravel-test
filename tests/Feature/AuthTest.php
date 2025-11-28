<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_allows_user_registration(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Chris Ninja',
            'email' => 'chris@example.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'user' => ['id', 'name', 'email'],
                'token'
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'chris@example.com'
        ]);
    }

    #[Test]
    public function it_allows_user_login_and_returns_token(): void
    {
        $user = User::factory()->create([
            'email' => 'login@test.com',
            'password' => bcrypt('secret123')
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'login@test.com',
            'password' => 'secret123'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'token', 'user']);
    }

    #[Test]
    public function it_rejects_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'wrong@test.com',
            'password' => bcrypt('secret123')
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'wrong@test.com',
            'password' => 'not-correct'
        ]);

        $response->assertStatus(401)
            ->assertJson(['message' => 'Invalid credentials']);
    }

    #[Test]
    public function it_returns_authenticated_user_details(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('apitest')->plainTextToken;

        $response = $this->getJson('/api/user', [
            'Authorization' => "Bearer $token"
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['id', 'name', 'email']);
    }
}
