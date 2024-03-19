<?php

namespace Orderonlineid\Permission;

use Illuminate\Support\Collection;
use ReflectionClass;
use ReflectionException;
use function get_class;
use function is_object;

class Guard
{
    /**
     * Return Default Guard name
     *
     * @param $class
     *
     * @return string
     * @throws ReflectionException
     */
    public function getDefaultName(): string
    {
        return config('auth.defaults.guard');
    }
}
