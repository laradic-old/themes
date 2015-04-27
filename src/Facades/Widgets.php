<?php namespace Laradic\Themes\Facades;

use Illuminate\Support\Facades\Facade;

class Widgets extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'themes.widgets';
    }
}
