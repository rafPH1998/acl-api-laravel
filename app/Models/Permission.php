<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['name', 'description'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function getAll(string $filter = '')
    {
        return $this->when(!empty($filter), fn($query) => $query->where('name', 'LIKE', "%{$filter}%"))
                ->paginate(10);
    }

    public function findById(string $permissionId): ?Permission
    {
        return $this->find($permissionId);
    }

    public function createPermission(array $data): Permission
    {
        return $this->create($data);
    }

    public function updatePermission(array $data, string $permissionId): bool
    {
        if (!$perm = $this->find($permissionId)) {
            return false;
        }
        return $perm->update($data);
    }

    public function deletePermission(string $id): bool
    {
        if (!$perm = $this->find($id)) {
            return false;
        }
        return $perm->delete();
    }
}
