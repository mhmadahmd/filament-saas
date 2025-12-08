<?php

namespace Mhmadahmd\FilamentSaas\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Mhmadahmd\FilamentSaas\FilamentSaas
 */
class FilamentSaas extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Mhmadahmd\FilamentSaas\FilamentSaas::class;
    }
}
