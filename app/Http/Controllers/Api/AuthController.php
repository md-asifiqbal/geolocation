<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AuthRequest;
use App\Http\Resources\Auth\UserResource;
use Throwable;

class AuthController extends Controller
{
    public function signup(AuthRequest $request)
    {
        try {
            $user = User::create($request->validated());
            return response()->apiSuccess(__('User Registration Successfully'), UserResource::make($user));
        } catch (Throwable $e) {
            return response()->apiError($e->getMessage(), $e->getCode());
        }
    }

    public function login(AuthRequest $request)
    {
        try {
            if (!auth()->attempt($request->validated())) {
                return response()->apiError(__('Invalid Credentials'), 401);
            }
            $user = User::where('email', $request->email)->first();
            return response()->apiSuccess(__('Login Successfully'), [
                'token' => substr(strstr($user->createToken($request->email)->plainTextToken, '|'), 1),
                'user' => UserResource::make($user)
            ]);
        } catch (Throwable $e) {
            return response()->apiError($e->getMessage(), $e->getCode());
        }
    }
}
