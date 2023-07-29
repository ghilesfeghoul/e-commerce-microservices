<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Services\UserService;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    public function __construct(
        private UserService $userService
    ) { }

    public function show($code)
    {
        $link = Link::with('products')->where('code', $code)->first();
        $link['users'] = collect($this->userService->get('users'))->filter(fn($user) => $user['id'] === $link->user_id);

        return $link;
    }
}
