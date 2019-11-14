<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
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

        return new JsonResponse($request->get('key'));
    }
}
