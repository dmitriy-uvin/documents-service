<?php

namespace App\Http\Middleware;

use App\Exceptions\Api\ApiKeyRequiredException;
use App\Exceptions\Api\WrongApiKeyException;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class CanUseApiMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->apiKey) {
            throw new ApiKeyRequiredException();
        }

        $user = User::where('api_key', '=', $request->apiKey)->get()->first();
        if (!$user) {
            throw new WrongApiKeyException();
        }
        return $next($request);
    }
}
