<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class MessageControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test retrieving messages between authenticated user and a contact.
     */
    public function test_get_messages_between_users()
    {
        // Create two users
        $user1 = User::factory()->create(['name' => 'John Doe']);
        $user2 = User::factory()->create(['name' => 'Jane Doe']);

        // Create some messages between the users
        Message::factory()->create(['body' => 'Hello Jane', 'sender' => $user1->id, 'receiver' => $user2->id]);
        Message::factory()->create(['body' => 'Hi John', 'sender' => $user2->id, 'receiver' => $user1->id]);

        // Authenticate the first user
        $token = JWTAuth::fromUser($user1);

        // Fetch the messages between user1 and user2
        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->getJson("/api/messages/{$user2->id}");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'John Doe',
                     'Jane Doe',
                 ]);
    }

    /**
     * Test sending a message from authenticated user to another user.
     */
    public function test_send_message()
    {
        // Create two users
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        // Authenticate the sender
        $token = JWTAuth::fromUser($sender);

        // Send a message
        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->postJson('/api/messages', [
                             'receiver' => $receiver->id,
                             'body' => 'Hello there!',
                         ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'message' => 'Message sent successfully',
                     'data' => [
                         'body' => 'Hello there!',
                         'sender' => $sender->id,
                         'receiver' => $receiver->id,
                     ],
                 ]);

        // Assert the message is saved in the database
        $this->assertDatabaseHas('messages', [
            'sender' => $sender->id,
            'receiver' => $receiver->id,
            'body' => 'Hello there!',
        ]);
    }

    /**
     * Test updating a message sent by the authenticated user.
     */
    public function test_update_message()
    {
        // Create a user and a message
        $user = User::factory()->create();
        $message = Message::factory()->create(['sender' => $user->id, 'body' => 'Initial message']);

        // Authenticate the user
        $token = JWTAuth::fromUser($user);

        // Update the message
        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->putJson("/api/messages/{$message->id}", [
                             'body' => 'Updated message',
                         ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Message updated successfully',
                     'data' => [
                         'body' => 'Updated message',
                     ],
                 ]);

        // Assert the message is updated in the database
        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
            'body' => 'Updated message',
        ]);
    }

    /**
     * Test trying to update a message that wasn't sent by the authenticated user.
     */
    public function test_cannot_update_other_users_message()
    {
        // Create two users and a message
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $message = Message::factory()->create(['sender' => $user2->id, 'body' => 'User2 message']);

        // Authenticate the first user
        $token = JWTAuth::fromUser($user1);

        // Try to update a message sent by user2
        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->putJson("/api/messages/{$message->id}", [
                             'body' => 'User1 trying to update',
                         ]);

        $response->assertStatus(403)
                 ->assertJson([
                     'error' => 'Unauthorized',
                 ]);
    }

    /**
     * Test deleting a message sent by the authenticated user.
     */
    public function test_delete_message()
    {
        // Create a user and a message
        $user = User::factory()->create();
        $message = Message::factory()->create(['sender' => $user->id]);

        // Authenticate the user
        $token = JWTAuth::fromUser($user);

        // Delete the message
        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->deleteJson("/api/messages/{$message->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Message deleted successfully',
                 ]);

        // Assert the message is deleted from the database
        $this->assertDatabaseMissing('messages', [
            'id' => $message->id,
        ]);
    }

    /**
     * Test trying to delete a message that wasn't sent by the authenticated user.
     */
    public function test_cannot_delete_other_users_message()
    {
        // Create two users and a message
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $message = Message::factory()->create(['sender' => $user2->id]);

        // Authenticate the first user
        $token = JWTAuth::fromUser($user1);

        // Try to delete a message sent by user2
        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->deleteJson("/api/messages/{$message->id}");

        $response->assertStatus(403)
                 ->assertJson([
                     'error' => 'Unauthorized',
                 ]);
    }
}
