<?php

namespace App\Http\Controllers\API\v1;

use App\Models\ChatConversation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_conversations()
    {
        $user = User::factory()->create();

        $token = $this->apiLogin();

        $conversations = ChatConversation::factory()->count(2)->create(['client_user_id' => $user->id]);

        $message1 = $conversations[0]->messages()->create(['message' => 'Hello, world!', 'user_id' => $conversations[0]->client_user_id]);
        $message2 = $conversations[1]->messages()->create(['message' => 'Hi there!', 'user_id' => $conversations[1]->coach_user_id]);

        $response = $this->get('/api/v1/chat/conversations', ['Authorization' => 'Bearer ' . $token->token]);
        $response->assertOk();

        $response->assertJson([
            'data' => [
                [
                    'id' => $conversations[0]->id,
                    'last_message' => [
                        'id' => $message1->id,
                        'message' => $message1->message,
                    ],
                ],
                [
                    'id' => $conversations[1]->id,
                    'last_message' => [
                        'id' => $message2->id,
                        'message' => $message2->message,
                    ],
                ],
            ],
        ]);
    }

    public function test_it_returns_a_conversation()
    {
        $token = $this->apiLogin();
        $user = User::find($token->user_id);

        $conversation = ChatConversation::factory()->create([
            'coach_user_id' => $user->id,
            'client_user_id' => $user->id,
        ]);

        $response = $this->post('/api/v1/chat/conversations', [
            'coach_rebase_id' => $conversation->coachUser->rebase_user_id,
            'client_rebase_id' => $conversation->clientUser->rebase_user_id,
        ], ['Authorization' => 'Bearer ' . $token->token]);
        $response->assertOk();

        // Ensure the response contains the correct conversation
        $response->assertJson([
            'data' => [
                'id' => $conversation->id,
                'client' => [
                    'id' => $conversation->clientUser->id,
                ],
                'coach' => [
                    'id' => $conversation->coachUser->id,
                ],
            ],
        ]);
    }

    public function test_it_returns_messages_for_a_conversation()
    {
        $token = $this->apiLogin();
        $user = User::factory()->create();

        $conversation = ChatConversation::factory()->create();
        $conversation->messages()->createMany([
            ['message' => 'Hello, world!', 'user_id' => $conversation->coach_user_id],
            ['message' => 'Hi there!', 'user_id' => $conversation->client_user_id],
        ]);

        $response = $this->get("/api/v1/chat/messages/$conversation->id", ['Authorization' => "Bearer $token->token"]);
        $response->assertOk()->assertJsonCount(2, 'data');
    }
}
