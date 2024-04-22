<?php

namespace Orderonlineid\Permission\Traits;

use Exception;
use MongoDB\BSON\ObjectId;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Query\Builder;
use Orderonlineid\Permission\Guard;
use Orderonlineid\Permission\Models\Role;
use ReflectionException;
use function collect;

/**
 * Trait HasRoles
 * @package Orderonlineid\Permission\Traits
 */
trait HasRoles
{
	use HasPermissions;

	public function assignRole(Role|Model|Builder $role)
	{
		if ($role->exists()) {
			$this->role_id = new ObjectId($role->id);
			$this->role = [
				'code' => $role->code,
				'name' => $role->name
			];
			$this->save();
		} else {
			throw new Exception('Role not defined', 422);
		}
		return $role;
	}

	/**
	 * Revoke the given role from the model.
	 *
	 * @param Role|Model|Builder $role
	 *
	 * @return array|Role|string
	 */
	public function removeRole(Role|Model|Builder $role)
	{
		if ($role->exists()) {
			$this->role = [];
			$this->save();
		} else {
			throw new Exception('Role not defined', 422);
		}
		return $role;
	}

	/**
	 * Return Role object
	 *
	 * @param String|Role $role role name
	 *
	 * @return Builder|Model
	 * @throws ReflectionException
	 */
	protected function getStoredRole(Role|string $role): Builder|Model
	{
		$guardName = (new Guard())->getDefaultName();
		if (is_string($role)) {
			return Role::findByCode($role, $guardName);
		}

		return $role;
	}

	/**
	 * Determine if the model has one of the given role.
	 *
	 * @param string|array|Role $roles
	 *
	 * @return bool
	 */
	public function hasRoles(...$roles): bool
	{
		$roles = collect($roles)->map(function ($role) {
			return $role instanceof Role ? $role->code : $role;
		});

		return isset($this->role['code']) && $roles->contains($this->role['code']);
	}

	public function getAllPermissions()
	{
		if ($this->getModelRole()->exists() && isset($this->permissions)) {
			$existingPermissions = collect($this->getModelRole()->first()->permissions)->pluck('code');
			$permissions = collect($this->permissions)
				->pluck('code')
				->merge($existingPermissions)
				->unique()
				->toArray();
		} else {
			$permissions = $this->permissions;
		}
		return $permissions;
	}
}
