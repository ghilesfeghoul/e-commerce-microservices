<?php

namespace App\Http\Middleware;

use App\Services\UserService;
use Closure;
use Illuminate\Http\Request;

class ScopeAmbassadorMiddleware
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
        $response = $this->userService->getRequest('get', 'scope/ambassador');

        if (!$response->ok() || $response->getBody()->getContents() !== "1") {
            abort(401, 'unauthorized');
        }

        return $next($request);
    }
}
