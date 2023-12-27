<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreUserRequest;
use App\Http\Requests\Api\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    public function __construct(
        private User $user,
    ){ }

    public function index(Request $request)
    {
        $users = $this->user->getAll($request->filter ?? '');
        return UserResource::collection($users);
    }

    public function store(StoreUserRequest $request)
    {
        $user = $this->user->createUser($request->validated());
        return new UserResource($user);
    }

    public function show(string $id)
    {
        if (!$user = $this->user->findById($id)) {
            return response()->json(['message' => 'user not found'], Response::HTTP_NOT_FOUND);
        }
        return new UserResource($user);
    }

    public function update(UpdateUserRequest $request, string $id)
    {
        $response = $this->user->updateUser($request->validated(), $id);
        if (!$response) {
            return response()->json(['message' => 'user not found'], Response::HTTP_NOT_FOUND);
        }
        return response()->json(['message' => 'user updated with success']);
    }


    public function destroy(string $id)
    {
        if (!$this->user->deleteUser($id)) {
            return response()->json(['message' => 'user not found'], Response::HTTP_NOT_FOUND);
        }
        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
