<?php

namespace App\Http\Middleware;

use App\Models\Token;
use Closure;

class CheckToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(!$request->header('Authorization')){
            abort(400);
        }

        $token = explode(" ", $request->header('Authorization'));

        if (count($token) < 2) {
            abort(400);
        }
        $tokens = Token::where('token', $token[1])->count();

        if ($tokens < 1) {
            abort(401);
        }

        return $next($request);
    }
}
