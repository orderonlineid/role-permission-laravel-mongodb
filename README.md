# Role & Permission for Laravel MongoDB
This package provides role and permission management for Laravel applications using MongoDB as the database.

# Installation
To install this package, run the following Composer command:

```
composer require orderonlineid/role-permission-laravel-mongodb
```
After the installation is complete, you need to publish the configuration file and migrations by running the following command:
```
php artisan vendor:publish --provider="OrderOnlineId\RolePermissionMongoDb\RolePermissionMongoDbServiceProvider"
```

# Usage
Setting Up Roles and Permissions
To set up roles and permissions, you can use the Role and Permission models provided by this package.

```
use OrderOnlineId\RolePermissionMongoDb\Models\Role;
use OrderOnlineId\RolePermissionMongoDb\Models\Permission;

// Create a new role
$role = Role::create(['name' => 'admin']);

// Create a new permission
$permission = Permission::create(['code' => 'create','name' => 'Create']);

// Assign the permission to the role
$role->givePermissionTo($permission);
```

# Assigning Roles and Permissions to Users
To assign roles and permissions to users, you can use the HasRoles and HasPermissions traits provided by this package.

```
use App\Models\User;
use OrderOnlineId\RolePermissionMongoDb\Traits\HasRoles;
use OrderOnlineId\RolePermissionMongoDb\Traits\HasPermissions;

class User extends Authenticatable
{
    use HasRoles, HasPermissions;

    // ...
}
```
After that, you can assign roles and permissions to users like this:

```
$user = User::find(1);

$permission = Permission::create(['code' => 'create','name' => 'Create']);

// Assign a role
$user->assignRole($permission);

// Grant a permission
$user->givePermissionTo('view', 'users');
```
# Checking User Roles and Permissions
You can check if a user has a specific role or permission like this:

```
$user = User::find(1);

// Check for a role
if ($user->hasRole('admin')) {
    // ...
}

// Check for a permission
if ($user->hasAllPermission('view', 'users')) {
    // ...
}
```
For more information on using this package, please refer to the documentation or the configuration file.
