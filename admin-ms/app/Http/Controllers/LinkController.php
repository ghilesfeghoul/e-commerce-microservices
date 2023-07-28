<?php

namespace App\Http\Controllers;

use App\Http\Resources\LinkResource;
use App\Models\Link;

class LinkController extends Controller
{
    public function __construct(
        private UserService $userService
    ) { }

    public function index($id)
    {
        $user = $this->userService->get("users/{$id}");
        $links = Link::with('orders')->where('user_id', $user['id'])->get();

        return LinkResource::collection($links);
    }
}
