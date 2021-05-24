<?php

namespace App\Http\Middleware;

use App\Services\Response\JsonResponse;
use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if ($this->auth->guard($guard)->guest()) {
            return JsonResponse::send([], trans('messages.general_errors.unauthorized'), 401, 401);
        }

        return $next($request);
    }
}
