<?php

namespace App\Http\Middleware;

use App\Services\UserService;
use Closure;
use Illuminate\Http\Request;

class ScopeAdminMiddleware
{
    public function __construct(
        private UserService $userService
    ) { }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $this->userService->getRequest('get', 'scope/admin');
        if (!$response->ok()) {
            abort(401, 'unauthorized');
        }

        return $next($request);
    }
}
