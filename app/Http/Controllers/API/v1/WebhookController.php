<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\Token;
use App\Models\User;
use App\Rebase\SendMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class WebhookController extends Controller
{
    public function notify(Request $request)
    {
        info($request->toArray());
        $request->validate([
            'key' => 'required',
        ]);

        $body = [];
        $body['TOKEN'] = '{D6B2B336-E153-4B03-BBD9-3C72238A76FF}';
        $body['KEY'] = $request->get('key');

        $response = Http::get(config('rebase.api_url') . '/AfspraakInfo', $body);

        if ($response->failed()) {
            abort(500);
        }

        $baseuser = $response->json();

        /** @var User $user */
        $user = User::where('rebase_user_id', Arr::get($baseuser, 'AfspraakInfo.Client_Uniek_ID'))->first();

        if (Arr::get($baseuser, 'AfspraakInfo.Afspraakstatus') === 1 && $user) {
            $user->tokens->map(function (Token $token) use ($baseuser) {
                (new SendMessage())->sendFCM('Er is een nieuwe afspraak op ' . Arr::get($baseuser, 'AfspraakInfo.Datum'), 'Nieuwe afspraak', $token->push_token, 0);
            });

            return new JsonResponse($request->get('key'));
        }

        $user->tokens->map(function (Token $token) use ($baseuser) {
            (new SendMessage())->sendFCM('Er is een een afspraak gewijzigd op ' . Arr::get($baseuser, 'AfspraakInfo.Datum'), 'Gewijzigde afspraak', $token->push_token, 0);
        });

        return new JsonResponse($request->get('key'));
    }
}
