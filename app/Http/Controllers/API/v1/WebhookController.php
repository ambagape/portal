<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rebase\SendMessage;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public $user = null;

    public function notify(Request $request)
    {
        info($request->toArray());
        $request->validate([
            'key' => 'required'
        ]);

        $body = [];
        $body['TOKEN'] = '{D6B2B336-E153-4B03-BBD9-3C72238A76FF}';
        $body['KEY'] = $request->get('key');

        try {
            $client = new Client();
            $response = $client->post(env('REBASE_API_URL') . "/AfspraakInfo",  ['form_params' => $body]);
            $this->user = json_decode($response->getBody());
        } catch (RequestException $e) {
            abort(500);
        }

        $this->user = User::where('rebase_user_id', $this->user->AfspraakInfo->Client_Uniek_ID);

        if($this->user) {
            $this->user->tokens->map(function ($token) {
                (new SendMessage())->sendFCM('Er is een afspraak gewijzigd op'. Carbon::now()->toDateString(), 'Afspraak gewijzigd', $token->push_token);
            });

        }

        return new JsonResponse($request->get('key'));
    }
}
