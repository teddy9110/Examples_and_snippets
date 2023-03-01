<?php

namespace App\Facades;

use App\Http\Interfaces\ExternalLogServiceInterface;
use Illuminate\Support\Facades\Facade;

/**
 * @method static ExternalLogServiceInterface driver(string $driver = null)
 */
class ExternalLog extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return ExternalLogServiceInterface::class;
    }
}
