<?php namespace Laradic\Themes\Facades;

use Illuminate\Support\Facades\Facade;

class Themes extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'themes';
    }
}
