<?php

return [
    'models' => [
        'permission' => Orderonlineid\Permission\Models\Permission::class,
        'role' => Orderonlineid\Permission\Models\Role::class,
    ],
    'collection_names' => [
        'roles' => 'roles',
        'permissions' => 'permissions',
    ],
    // uncomment here if you need soft delete
    // 'softdelete' => [
	// 	'is_deleted' => true,
	// ],
];
