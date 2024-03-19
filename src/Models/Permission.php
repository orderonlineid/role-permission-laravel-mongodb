<?php

namespace Orderonlineid\Permission\Models;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use MongoDB\Laravel\Eloquent\Model;
use Orderonlineid\Permission\Guard;
use ReflectionException;
use Throwable;

/**
 * Class Permission
 * @property string $_id
 * @package Orderonlineid\Permission\Models
 */
class Permission extends Model
{
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
		$this->setTable(config('permission.collection_names.permissions'));
	}

	/**
	 * @param array $attributes
	 * @return Builder|Model
	 * @throws ReflectionException
	 * @throws Exception
	 */
	public static function create(array $attributes = []): Builder|Model
	{
		$attributes['guard_name'] = $attributes['guard_name'] ?? (new Guard())->getDefaultName();

		if (static::query()->where('name', $attributes['name'])->where('guard_name', $attributes['guard_name'])->first()) {
			throw new Exception('Permission already exists');
		}

		return static::query()->create($attributes);
	}

	/**
	 * @param string $name
	 * @param string|null $guardName
	 * @return Model|Builder
	 * @throws ReflectionException
	 * @throws Throwable
	 */
	public static function findByName(string $name, string $guardName = null): Model|Builder
	{
		$guardName = $guardName ?? (new Guard())->getDefaultName();

		$permission = static::query()
			->where('name', $name)
			->where('guard_name', $guardName)
			->first();
		throw_if($permission === null, new Exception('Permission not found'));
		return $permission;
	}
}
