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
            $response = $client->request('GET', env('REBASE_API_URL') . "/AfspraakInfo", ['query' => $body]);
            $baseuser  = json_decode($response->getBody());
        } catch (RequestException $e) {
            abort(500);
        }

        $user = User::where('rebase_user_id', $baseuser->AfspraakInfo->Client_Uniek_ID)->first();

        if($baseuser->AfspraakInfo->Afspraakstatus === 1 && $user) {
            $user->tokens->map(function ($token) use ($baseuser) {
                (new SendMessage())->sendFCM('Er is een niewe afspraak op '. $baseuser->AfspraakInfo->Datum, 'Nieuwe afspraak', $token->push_token);
            });
            return new JsonResponse($request->get('key'));
        }

        $user->tokens->map(function ($token) use ($baseuser) {
            (new SendMessage())->sendFCM('Er is een een afspraak gewijzigd op '. $baseuser->AfspraakInfo->Datum, 'Gewijzigde afspraak', $token->push_token);
        });

        return new JsonResponse($request->get('key'));
    }
}
