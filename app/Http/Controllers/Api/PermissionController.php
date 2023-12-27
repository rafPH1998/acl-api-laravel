<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StorePermissionRequest;
use App\Http\Requests\Api\UpdatePermissionRequest;
use App\Http\Resources\PermissionResource;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PermissionController extends Controller
{
    public function __construct(private Permission $permissions)
    { }

    public function index(Request $request)
    {
        $permissions = $this->permissions->getAll($request->filter ?? '');
        return PermissionResource::collection($permissions);
    }

    public function store(StorePermissionRequest $request)
    {
        $permission = $this->permissions->createPermission($request->validated());
        return new PermissionResource($permission);
    }

    public function show(string $id)
    {
        if (!$permission = $this->permissions->findById($id)) {
            return response()->json(['message' => 'permission not found'], Response::HTTP_NOT_FOUND);
        }
        return new PermissionResource($permission);
    }

    public function update(UpdatePermissionRequest $request, string $id)
    {
        $response = $this->permissions->updatePermission($request->validated(), $id);
        if (!$response) {
            return response()->json(['message' => 'permission not found'], Response::HTTP_NOT_FOUND);
        }
        return response()->json(['message' => 'permission updated with success']);
    }

    public function destroy(string $id)
    {
        if (!$this->permissions->deletePermission($id)) {
            return response()->json(['message' => 'permission not found'], Response::HTTP_NOT_FOUND);
        }
        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
