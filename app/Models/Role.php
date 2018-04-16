<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasPermissions;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasPermissions;

    public static function create(array $attributes = []) {
        if (static::where('type', $attributes['type'])->first())
			throw new \Exception('Ce rôle existe déjà');

        return static::query()->create($attributes);
    }

	public static function find(int $id, string $only_for = null) {
		$roles = static::where('id', $id);

		if ($only_for !== null)
			$roles->where('only_for', $only_for);

		return $roles->first();
	}

	public static function findByType(string $type, string $only_for = null) {
		$roles = static::where('type', $type);

		if ($only_for !== null)
			$roles->where('only_for', $only_for);

		return $roles->first();
	}

	public static function getRole($role, string $only_for = null) {
        if (is_string($role))
            return static::findByType($role, $only_for);
        else if (is_int($role))
			return static::find($role, $only_for);
		else
			return null;
	}

	public static function getRoles($roles, string $only_for = 'users') {
		return static::whereIn('id', $roles)->orWhereIn('type', $roles)->where('only_for', $only_for)->get();
	}

    public function permissions(): BelongsToMany {
        return $this->belongsToMany(Permission::class, 'roles_permissions');
    }

    public function users(): BelongsToMany {
		return $this->belongsToMany(User::class, 'users_roles');
    }

    public function childRoles(): BelongsToMany {
		return $this->belongsToMany(Role::class, 'roles_parents', 'parent_id', 'role_id');
    }

    public function parentRoles(): BelongsToMany {
		return $this->belongsToMany(Role::class, 'roles_parents', 'role_id', 'parent_id');
    }

    public function hasPermissionTo($permission): bool {
        if (is_string($permission))
            $permission = Permission::findByType($permission);
        else if (is_int($permission))
            $permission = Permission::find($permission);

        return $this->permissions->contains('id', $permission->id);
    }

	public function givePermissionTo($permissions) {
		$this->permissions()->withTimestamps()->attach(Permission::getPermissions(stringToArray($permissions), $this->only_fr === 'users'));

		return $this;
	}

	public function assignParentRole($roles) {
		$this->parentRoles()->withTimestamps()->attach(static::getRoles(stringToArray($roles), $this->only_for));

		return $this;
	}
}
