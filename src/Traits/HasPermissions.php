<?php

namespace Orderonlineid\Permission\Traits;

use App\Models\User;
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
     * @param ...$permissions
     * @return User|Role|HasPermissions
     */
    public function givePermissionTo(...$permissions): self
	{
		$inputPermissions = collect($permissions)->map(function ($permission) {
			$dataPermission = $this->getStoredPermission($permission);
			return [
				'id' => new ObjectId($dataPermission->id),
				'name' => $permission
			];
		})->whereNotIn('name', collect($this->permissions)->pluck('name'));

		if (!$inputPermissions->isEmpty()) {
			$this->permissions = collect($this->permissions)->merge($inputPermissions->toArray())->toArray();
			$this->save();
		}

		return $this;
	}

	public function revokePermissionTo(...$permissions): self
	{
		$this->permissions = collect($this->permissions ?? [])
			->whereNotIn('name', $permissions)
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
			return Permission::findByName($permission, $guard);
		}

		return $permission;
	}
}
