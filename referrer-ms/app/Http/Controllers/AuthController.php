<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateInfoRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Services\UserService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Order;

class AuthController extends Controller
{
    public function __construct(
        private UserService $userService
    ) { }

    public function register(RegisterRequest $request)
    {
        $user = $this->userService->post('register',
            $request->only('first_name', 'last_name', 'email', 'password')
            + ['is_admin' => 0]
        );

        return response($user, Response::HTTP_CREATED);
    }

    public function login(Request $request)
    {
        $data = $request->only('email', 'password') + ['scope' => 'ambassador'];

        $response = $this->userService->post("login", $data);

        $cookie = cookie('jwt', $response['jwt'], 60 * 24); // 1 day

        return response([
            'message' => 'success'
        ])->withCookie($cookie);
    }

    public function user(Request $request)
    {
        $user = $this->userService->get('user');
        
        $orders = Order::where('user_id', $user['id'])->get();

        $user['revenue'] = $orders->sum(fn(Order $order) => $order->total);

        return $user;
    }

    public function logout()
    {
        $cookie = \Cookie::forget('jwt');

        $this->userService->post('logout', []);

        return response([
            'message' => 'success'
        ])->withCookie($cookie);
    }

    public function updateInfo(UpdateInfoRequest $request)
    {
        $user = $this->userService->put('users/info', $request->only('first_name', 'last_name', 'email'));

        return response($user, Response::HTTP_ACCEPTED);
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = $this->userService->put('users/password', $request->only('password'));

        return response($user, Response::HTTP_ACCEPTED);
    }
}
