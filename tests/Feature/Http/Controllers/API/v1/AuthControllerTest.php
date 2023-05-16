<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\API\v1\AuthController;
use App\Models\Token;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function testLogin()
    {
        // Mock the HTTP response for the login request
        Http::fake([
            config('rebase.api_url') . '/login' => Http::response([
                'Login' => [
                    'UserID' => 111,
                ],
            ], 200),
        ]);

        // Set up the request data
        $requestData = [
            'username' => 'mradmin',
            'password' => 'password',
            'full_name' => 'Mr admin',
        ];
        $request = new Request($requestData);

        // Make the login request
        $response = (new AuthController())->login($request);

        $testResponse = new TestResponse($response);

        // Check that the response is a JSON response with the correct data
        $testResponse->assertJson([
            'user_id' => Token::first()->user_id,
        ]);

    }

    public function testRegisterToken()
    {
        // Set up the database with a user and token
        $user = User::factory()->create();

        $token = Token::factory()->create(['user_id' => $user->id]);

        // Set up the request data
        $requestData = [
            'push_token' => Str::random(10),
        ];
        $request = new Request($requestData);
        $request->headers->set('Authorization', 'Bearer ' . $token->token);

        // Make the register token request
        $response = (new AuthController())->registerToken($request);

        // Check that the token's push token was updated
        $this->assertDatabaseHas('tokens', [
            'id' => $token->id,
            'push_token' => $requestData['push_token'],
        ]);
    }
}
