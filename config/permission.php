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
];
