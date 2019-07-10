<?php

namespace App\Providers;

use App\Models\Token;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Auth::viaRequest('custom-token', function ($request) {

            if(!$request->header('Authorization')){
                abort(400);
            }

            $tokens = Token::where('token', $request->bearerToken())->get();
            if ($tokens->count() < 1) {
                abort(401);
            }

//            $token = Token::where('token', explode(" ", $request->header('Authorization'))[1])->first();
            info('test');
            return $tokens->first()->user;
        });
    }
}
