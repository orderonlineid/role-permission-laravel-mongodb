<?php

namespace Orderonlineid\Permission\Traits;

use Illuminate\Database\Eloquent\Builder;

trait SoftDeleteTrait
{
    protected static $softdelete_key = 'is_deleted'; // Default key
    protected static $softdelete_value = true; // Default value for soft delete
    protected static $softdeleteConditions = []; // Conditions for soft delete

    protected static function boot()
    {
        parent::boot(); // Call the parent boot method if necessary
        static::bootSoftDeletes();
    }

    // Boot the trait
    protected static function bootSoftDeletes()
    {
        static::addGlobalScope('is_deleted', function (Builder $builder) {
            // Define default conditions if not set
            static::$softdeleteConditions = config('permission.softdelete') ?? false;
            if (static::$softdeleteConditions) {
                $builder->where([key(static::$softdeleteConditions) => !static::$softdeleteConditions[key(static::$softdeleteConditions)]]); // Use static properties
            }
        });
    }

    // Soft delete the model
    public function delete()
    {
        $this->{static::$softdelete_key} = static::$softdelete_value; // Set the dynamic property
        $this->save(); // Save the model
    }

    // Restore the model
    public function restore()
    {
        $this->{static::$softdelete_key} = false; // Restore the soft delete
        $this->save(); // Save the model
    }

    // Force delete the model
    public function forceDelete()
    {
        parent::forceDelete();
    }
}