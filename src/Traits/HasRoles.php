<?php

namespace Orderonlineid\Permission\Traits;

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
		}
		return $role;
	}

	/**
	 * Revoke the given role from the model.
	 *
	 * @param array|string|Role ...$roles
	 *
	 * @return array|Role|string
	 */
	public function removeRole(...$roles)
	{
	   $roles = collect($this->roles)->whereNotIn('code', $roles)->toArray();
	   $this->roles = $roles;
	   $this->save();

		return $roles;
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
			$permissions = collect($this->permissions)->pluck('code')->merge($existingPermissions)->toArray();
		} else {
			$permissions = $this->permissions;
		}
		return $permissions;
	}
}
