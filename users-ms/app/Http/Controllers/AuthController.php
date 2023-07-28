<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        return User::create(
            $request->only('first_name', 'last_name', 'email', 'is_admin')
            + ['password' => \Hash::make($request->input('password'))]
        );
    }

    public function login(Request $request)
    {
        if (!\Auth::attempt($request->only('email', 'password'))) {
            return response([
                'error' => 'invalid credentials'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = \Auth::user();

        $jwt = $user->createToken('token', [$request->input('scope')])->plainTextToken;

        return compact('jwt');
    }

    public function user(Request $request)
    {
        return $request->user();
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response([
            'message' => 'success'
        ]);
    }

    public function updateInfo(Request $request)
    {
        $user = $request->user();

        $user->update($request->only('first_name', 'last_name', 'email'));

        return response($user, Response::HTTP_ACCEPTED);
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $user->update([
            'password' => \Hash::make($request->input('password'))
        ]);

        return response($user, Response::HTTP_ACCEPTED);
    }

    public function users()
    {
        return User::all();
    }

    public function canScope(Request $request, string $scope)
    {
        return $request->user()->tokenCan($scope);
    }

    public function getUser(string $id)
    {
        return User::find($id);
    }
}
