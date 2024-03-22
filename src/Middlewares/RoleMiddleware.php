<?php

namespace Orderonlineid\Permission\Middlewares;

use Closure;
use Exception;

/**
 * Class RoleMiddleware
 * @package Orderonlineid\Permission\Middlewares
 */
class RoleMiddleware
{
	/**
	 * @param $request
	 * @param Closure $next
	 * @param $role
	 *
	 * @return mixed
	 * @throws Exception
	 */
	public function handle($request, Closure $next, $role): mixed
	{
		if (app('auth')->guest()) {
			throw new Exception('User not logged in', 403);
		}

		$roles = is_array($role) ? $role : explode('|', $role);

		if (!app('auth')->user()->hasRoles(...$roles)) {
			throw new Exception('Unauthorized with role ' . implode(', ', $roles), 403);
		}
		return $next($request);
	}
}
