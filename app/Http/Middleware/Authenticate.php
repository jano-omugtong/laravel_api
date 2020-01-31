<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return api/auth/home list of users|unauthorized
     */
    protected function redirectTo($request)
    {
        if (!auth('api')->check()) {
            if ($request->path() == "api/auth/users"){
                return route('home', 1);
            }

            return route('home', 0);
        }
    }
}
