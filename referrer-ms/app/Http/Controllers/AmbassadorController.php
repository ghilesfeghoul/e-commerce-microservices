<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;

class AmbassadorController extends Controller
{
    public function __construct(
        private UserService $userService
    ) { }

    public function index()
    {
        $users = collect($this->userService->get('users'));

        return $users->filter(fn($user) => $user['is_admin'] === 0)->values();
    }
}
