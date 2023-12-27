<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\AuthApiRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthApiController extends Controller
{
    public function __construct(private User $user)
    { }

    public function auth(AuthApiRequest $req)
    {
        $user = $this->user->findByEmail($req->email);
        if (!$user || !Hash::check($req->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais estão incorretas.'],
            ]);
        }
        $user->tokens()->delete();
        $token = $user->createToken($req->device_name)->plainTextToken;
        return response()->json(['token' => $token]);
    }

    public function me()
    {
        $user = Auth::user();
        $user->load('permissions');
        return new UserResource($user);
    }

    public function logout()
    {
        Auth::user()->tokens()->delete();
        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
