<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;


class PostsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_list_posts()
    {
        $user = User::factory()->create();
        Post::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->getJson('/api/posts');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'content', 'user_id', 'created_at', 'updated_at']
                ],
                'links',
                'meta'
            ]);
    }

    #[Test]
    public function it_requires_auth_to_create_a_post()
    {
        $response = $this->postJson('/api/posts', [
            'title' => 'Test Post',
            'content' => 'Sample content',
        ]);

        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }

    #[Test]
    public function it_can_create_a_post()
    {
        $user = User::factory()->create();
        $token = $user->createToken('apitest')->plainTextToken;

        $response = $this->postJson('/api/posts', [
            'title' => 'New Post',
            'content' => 'Post Content'
        ], [
            'Authorization' => "Bearer $token"
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'title' => 'New Post',
                'content' => 'Post Content'
            ]);

        $this->assertDatabaseHas('posts', ['title' => 'New Post']);
    }

    #[Test]
    public function it_validates_required_fields_when_creating()
    {
        $user = User::factory()->create();
        $token = $user->createToken('apitest')->plainTextToken;

        $response = $this->postJson('/api/posts', [], [
            'Authorization' => "Bearer $token"
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'content']);
    }

    #[Test]
    public function it_can_update_a_post()
    {
        $user = User::factory()->create();
        $token = $user->createToken('apitest')->plainTextToken;

        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->patchJson("/api/posts/{$post->id}", [
            'title' => 'Updated Title'
        ], [
            'Authorization' => "Bearer $token"
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Post updated successfully']);

        $this->assertDatabaseHas('posts', ['id' => $post->id, 'title' => 'Updated Title']);
    }

    #[Test]
    public function it_can_delete_a_post()
    {
        $user = User::factory()->create();
        $token = $user->createToken('apitest')->plainTextToken;

        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->deleteJson("/api/posts/{$post->id}", [], [
            'Authorization' => "Bearer $token"
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Post deleted successfully']);

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }
}
