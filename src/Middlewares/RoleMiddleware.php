<?php

namespace Orderonlineid\Permission\Middlewares;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;

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
	public function handle($request, Closure $next,...$roles): mixed
	{
		if (app('auth')->guest()) {
			throw new AuthorizationException('User not logged in', 403);
		}

		if (!app('auth')->user()->hasRoles(...$roles)) {
			throw new AuthorizationException('Unauthorized with role ' . implode(', ', $roles), 403);
		}
		return $next($request);
	}
}
