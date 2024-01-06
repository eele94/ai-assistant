<?php

namespace Eele94\Assistant\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Eele94\Assistant\Assistant
 */
class Assistant extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Eele94\Assistant\Assistant::class;
    }
}
