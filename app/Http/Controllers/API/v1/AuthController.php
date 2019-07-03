<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\Token;
use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validated = $request->validate([
            'username'  => 'required',
            'password'  => 'required',
        ]);

        $body = [];
        $body['username'] = $validated['username'];
        $body['password'] = $validated['password'];
        $body['AUTHENTICATIONKEY'] = env('REBASE_AUTH_KEY');
        $body['AUTHENTICATIONPASSWORD'] = env('REBASE_AUTH_PASS');

        try {
            $client = new Client();
            $response = $client->post(env('REBASE_API_URL') . "/login",  ['form_params' => $body]);
            $data = json_decode($response->getBody());
        } catch (RequestException $e) {
            abort(500);
        }

        // Get user
        $user = User::firstOrCreate([
            'rebase_user_id' => $data->Login->UserID
        ]);

        // Create new token
        $token = Token::create([
            'user_id' => $user->id,
            'token' => Str::random(60) . time()
        ]);

        return new JsonResponse([
            'user_id' => $token->user_id,
            'token' => $token->token,
        ]);
    }
}
