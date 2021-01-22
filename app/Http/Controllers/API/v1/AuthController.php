<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\Token;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validated = $request->validate([
            'username'  => 'required',
            'password'  => 'required',
            'full_name'  => 'required',
        ]);

        $body = [];
        $body['username'] = $validated['username'];
        $body['password'] = $validated['password'];
        $body['AUTHENTICATIONKEY'] = env('REBASE_AUTH_KEY');
        $body['AUTHENTICATIONPASSWORD'] = env('REBASE_AUTH_PASS');

        $response = Http::asForm()->post(env('REBASE_API_URL') . '/login', $body);

        if ($response->failed()) {
            abort(500);
        }

        $data = (object) $response->json();

        // Get user
        $user = User::updateOrCreate([
            'rebase_user_id' => $data->Login->UserID,
        ], [
            'full_name' => $validated['full_name'],
        ]);

        // Delete other tokens
        Token::where('user_id', $user->id)->delete();

        // Create new token
        $token = Token::create([
            'user_id' => $user->id,
            'token' => Str::random(60) . time(),
        ]);

        return new JsonResponse([
            'user_id' => $token->user_id,
            'token' => $token->token,
        ]);
    }

    public function registerToken(Request $request)
    {
        $validated = $request->validate([
            'push_token'  => 'required',
        ]);

        // Remove push tokens from other keys
        Token::where('push_token', $validated['push_token'])->update(['push_token' => null]);

        // Set Push token
        Token::query()
            ->where('token', $request->bearerToken())
            ->update(['push_token' => $validated['push_token']]);

        return response()->json(['success' => 'success'], 200);
    }
}
