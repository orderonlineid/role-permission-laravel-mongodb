<?php

namespace Orderonlineid\Permission\Traits;

use MongoDB\BSON\ObjectId;
use MongoDB\Laravel\Eloquent\Model;
use Orderonlineid\Permission\Guard;
use Orderonlineid\Permission\Models\Permission;
use Orderonlineid\Permission\Models\Role;
use ReflectionException;
use Throwable;
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
		if ($this->getModelRole()->exists()) {
			$exceptPermissions = collect($this->getModelRole()->first()->permissions)
				->merge($this->permissions);
		} else {
			$exceptPermissions = collect($this->permissions);
		}
		$inputPermissions = collect($permissions)->map(function ($permission) {
			$dataPermission = $this->getStoredPermission($permission);
			return [
				'id' => new ObjectId($dataPermission->id),
				'code' => $permission
			];
		})->whereNotIn('code', $exceptPermissions->pluck('code'));

		if (!empty($inputPermissions)) {
			$this->permissions = $inputPermissions->toArray();
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
		if ($this->getModelRole()->exists()) {
			$exceptPermissions = collect($this->getModelRole()->first()->permissions);
		}

		$inputPermissions = collect($permissions)->map(function ($permission) {
			$dataPermission = $this->getStoredPermission($permission);
			return [
				'id' => new ObjectId($dataPermission->id),
				'code' => $permission
			];
		});
		if (!$this instanceof Role && !empty($this->role)) {
			$inputPermissions = $inputPermissions
				->merge($exceptPermissions)
				->unique();
		}
		$this->permissions = $inputPermissions->toArray();
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

	public function getEligiblePermission(...$permissions): int
	{
		if (is_array($permissions[0])) {
			$permissions = $permissions[0];
		}
		$currentPermissions = collect($this->permissions);
		if ($this->getModelRole()->exists()) {
			$currentPermissions = $currentPermissions
				->merge($this->getModelRole()->first()->permissions)
				->unique();
		}
		return $currentPermissions->whereIn('code', $permissions)->count();
	}

	/**
	 * Determine if the model has any of the given permissions.
	 *
	 * @param array ...$permissions
	 *
	 * @return bool
	 */
	public function hasAllPermissions(...$permissions): bool
	{
		$eligiblePermission = $this->getEligiblePermission($permissions);
		return count($permissions) === $eligiblePermission && $eligiblePermission > 0;
	}

	/**
	 * Determine if the model has any of the given permissions.
	 *
	 * @param array|string ...$permissions
	 *
	 * @return bool
	 */
	public function hasAnyPermissions(...$permissions): bool
	{
		$eligiblePermission = $this->getEligiblePermission($permissions);
		return $eligiblePermission > 0;
	}

	public function getModelRole()
	{
		return $this->hasOne(Role::class, 'code', 'role.code');
	}
}
