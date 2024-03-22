<?php

namespace Orderonlineid\Permission\Middlewares;

use Closure;
use Exception;

/**
 * Class PermissionMiddleware
 * @package Orderonlineid\Permission\Middlewares
 */
class PermissionMiddleware
{
	/**
	 * @param $request
	 * @param Closure $next
	 * @param $permission
	 *
	 * @return mixed
	 * @throws Exception
	 */
	public function handle($request, Closure $next, $permission): mixed
	{
		if (app('auth')->guest()) {
			throw new Exception('User not logged in', 403);
		}
		$permissions = is_array($permission) ? $permission : explode('|', $permission);

		if (!app('auth')->user()->hasAnyPermissions(...$permissions)) {
			throw new Exception('Unauthorized with permissions ' . implode(', ', $permissions), 403);
		}

		return $next($request);
	}
}
