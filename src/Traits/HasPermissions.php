<?php

namespace Orderonlineid\Permission\Traits;

use MongoDB\BSON\ObjectId;
use MongoDB\Laravel\Eloquent\Model;
use Orderonlineid\Permission\Guard;
use Orderonlineid\Permission\Models\Permission;
use Orderonlineid\Permission\Models\Role;
use ReflectionException;
use function collect;
use function is_array;
use function is_string;

/**
 * Trait HasPermissions
 * @package Maklad\Permission\Traits
 */
trait HasPermissions
{
	/**
	 * Grant the given permission(s) to a role.
	 *
	 * @param ...$permissions
	 * @return Role|HasPermissions
	 * @throws ReflectionException
	 */
	public function givePermissionTo(...$permissions): self
	{
		$inputPermissions = collect($permissions)->map(function ($permission) {
			$dataPermission = $this->getStoredPermission($permission);
			return [
				'id' => new ObjectId($dataPermission->id),
				'code' => $permission
			];
		})->whereNotIn('code', collect($this->permissions)->pluck('code'))
			->toArray();

		if (!empty($inputPermissions)) {
			$this->permissions = collect($this->permissions)->merge($inputPermissions)->toArray();
			$this->save();
		}

		return $this;
	}

	/**
	 * Remove all current permissions and set the given ones.
	 *
	 * @param ...$permissions
	 * @return Role|HasPermissions
	 * @throws ReflectionException
	 */
	public function syncPermissions(...$permissions): self
	{
		$inputPermissions = collect($permissions)->flatten()->map(function ($permission) {
			$dataPermission = $this->getStoredPermission($permission);
			return [
				'id' => new ObjectId($dataPermission->id),
				'code' => $permission
			];
		})->toArray();

		$this->permissions = collect($this->permissions)->merge($inputPermissions)->toArray();
		$this->save();

		return $this;
	}

	/**
	 * Revoke the given permission.
	 *
	 * @param ...$permissions
	 * @return Role|HasPermissions
	 */
	public function revokePermissionTo(...$permissions): self
	{
		$this->permissions = collect($this->permissions ?? [])
			->whereNotIn('code', $permissions)
			->all();
		$this->save();
		return $this;
	}

	/**
	 * @param $permission
	 * @return \Illuminate\Database\Eloquent\Builder|mixed|Model
	 * @throws ReflectionException
	 */
	protected function getStoredPermission($permission): mixed
	{
		$guard = (new Guard())->getDefaultName();
		if (is_string($permission)) {
			return Permission::findByCode($permission, $guard);
		}

		return $permission;
	}
}
