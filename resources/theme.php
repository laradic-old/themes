<?php

use Illuminate\Contracts\Foundation\Application;
use Laradic\Themes\Theme;

return [
    'parent'   => null,
    'name'     => 'Default theme',
    'slug'     => 'frontend/default',
    'version'  => '0.0.1',
    'register' => function (Application $app, Theme $theme)
    {
    },
    'boot'     => function (Application $app, Theme $theme)
    {
    }
];
