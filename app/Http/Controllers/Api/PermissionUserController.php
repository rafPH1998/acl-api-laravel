<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PermissionUserController extends Controller
{
    public function __construct(private User $user)
    {
    }

    public function syncPermissionsOfUser(string $userId, Request $request)
    {
      /*   $perm = collect($request->permissions);

        $permIds = $perm->filter(fn($p) => $p === 'on')->keys();
        dd($perm);
        exit; */
        $response = $this->user->syncPermissions($userId, $request->permissions);
        if (!$response) {
            return response()->json(['message' => 'user not found'], Response::HTTP_NOT_FOUND);
        }
        return response()->json(['message' => 'ok'], Response::HTTP_OK);
    }

    public function getPermissionsOfUser(string $userId)
    {
        if (!$this->user->find($userId)) {
            return response()->json(['message' => 'user not found'], Response::HTTP_NOT_FOUND);
        }
        $permissions = $this->user->getPermissionsByUserId($userId);
        return PermissionResource::collection($permissions);
    }
}
