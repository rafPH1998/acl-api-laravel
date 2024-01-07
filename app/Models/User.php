<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\DTO\Users\UserDTO;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    public function isSuperAdmin(): bool
    {
        return in_array($this->email, config('acl.super_admins'));
    }

    public function getAll(string $filter = '')
    {
        return $this->when(!empty($filter), fn($query) => $query->where('name', 'LIKE', "%{$filter}%"))
                ->with(['permissions'])
                ->paginate(10);
    }

    public function findById(string $userId): ?User
    {
        return $this->with('permissions')->find($userId);
    }

    public function createUser(array $data): User
    {
        $data['password'] = bcrypt($data['password']);
        return $this->create($data);
    }

    public function updateUser(array $data, string $userId): bool
    {
        if (!$user = $this->find($userId)) {
            return false;
        }
        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }
        return $user->update($data);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->where('email', $email)->first();
    }

    public function deleteUser(string $id): bool
    {
        if (!$user = $this->find($id)) {
            return false;
        }
        return $user->delete();
    }

    public function syncPermissions(string $userId, array $permissions): ?bool
    {
        if (!$user = $this->find($userId)) {
            return null;
        }
        $user->permissions()->sync($permissions);
        return true;
    }

    public function getPermissionsByUserId(string $user)
    {
        return $this->find($user)->permissions()->get();
    }

    public function hasPermissions(User $user): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
    
        $permissions = $user->permissions()->get();
    
        foreach ($permissions as $permission) {
            if ($user->permissions()
                ->where('name', $permission->name)
                ->where('description', $permission->description)
                ->exists()) {
                return true;
            }
        }
    
        return false;
    }
}
