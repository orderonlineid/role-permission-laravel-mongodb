<?php

namespace Orderonlineid\Permission\Models;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use MongoDB\Laravel\Eloquent\Model;
use Orderonlineid\Permission\Guard;
use Orderonlineid\Permission\Traits\HasPermissions;
use Orderonlineid\Permission\Traits\HasRoles;
use ReflectionException;

/**
 * Class Permission
 * @property string $_id
 * @package Orderonlineid\Permission\Models
 */
class Role extends Model
{
	use HasRoles, HasPermissions;
	public $guarded = ['id'];

	/**
	 * Permission constructor.
	 *
	 * @param array $attributes
	 *
	 * @throws ReflectionException
	 */
	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);
		$this->setTable(config('permission.collection_names.role'));
	}

	/**
	 * @param array $attributes
	 * @return Builder|Model
	 * @throws ReflectionException
	 */
	public static function create(array $attributes = []): Builder|Model
	{
		$attributes['guard_name'] = $attributes['guard_name'] ?? (new Guard())->getDefaultName();
		if (static::query()->where('name', $attributes['name'])->where('guard_name', $attributes['guard_name'])->first()) {
			throw new Exception('Role already exists');
		}

		return static::query()->create($attributes);
	}

	/**
	 * @param string $name
	 * @param string|null $guard_name
	 * @return Builder|Model
	 * @throws ReflectionException
	 */
	public static function findOrCreate(string $name, string $guard_name = null): Builder|Model
	{
		$guardName = $guardName ?? (new Guard())->getDefaultName();
		$role = static::query()
			->where('name', $name)
			->where('gurad_name', $guardName)
			->first();

		if (!$role) {
			$role = static::create(['name' => $name, 'guard_name' => $guardName]);
		}

		return $role;
	}

	/**
	 * @param string $name
	 * @param string|null $guardName
	 * @return Builder|Model
	 * @throws ReflectionException
	 * @throws Exception
	 */
	public static function findByName(string $name, string $guardName = null): Builder|Model
	{
		$guardName = (new Guard())->getDefaultName();

		$role = static::query()
			->where('name', $name)
			->where('guard_name', $guardName)
			->first();

		if (!$role) {
			throw new Exception('Role not exists');
		}

		return $role;
	}
}
