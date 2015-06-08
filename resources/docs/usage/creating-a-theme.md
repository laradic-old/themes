<!---
title: Creating a theme
author: Robin Radic
icon: fa fa-legal
-->

By default, Laradic Themes will search your `public` and `public/themes` folder for themes, as defined in the config file. 
You can add paths in the config file or do it on the fly using `Themes::addPath('/path/to/dir')`.
  
  
Alternatively you can use the `themes:create {theme/slug} [path]` console command to create a theme.
  
  
#### Default theme folder structure
```js
- public
- - frontend
- - - default
- - - - packages
- - - - - {vendor-name}
- - - - - - {package-name}
- - - - - - - assets
- - - - - - - views
- - - - namespaces
- - - - - {namespace}
- - - - - - assets
- - - - - - views
- - - - assets
- - - - views
- - - - theme.php
- - - - composer.json
```
  
#### theme.php
A perfect place to manage your assets
  
```php
use Illuminate\Contracts\Foundation\Application;
use Laradic\Themes\Theme;

return [
    'parent'   => null, // backend/parent
    'name'     => 'Default theme',
    'slug'     => 'backend/admin',
    'version'  => '0.0.1',
    'register' => function (Application $app, Theme $theme)
    {
    },
    'boot'     => function (Application $app, Theme $theme)
    {
        Asset::group('base')
            ->add('jquery', 'scripts/plugins/jquery.js')
            ->add('bootstrap', 'scripts/plugins/bootstrap.custom.js', ['jquery'])
            ->add('bootstrap', 'scripts/plugins/bootstrap.custom.css')
            ->add('bootbox', 'scripts/plugins/bootbox.js', ['jquery', 'bootstrap'])
            ->add('bootbox3', 'scripts/plugins/bootbox.js', ['jquery', 'bootstrap', 'bootbox2'])
            ->add('bootbox2', 'scripts/plugins/bootbox.js', ['jquery', 'bootstrap', 'bootbox1'])
            ->add('bootbox1', 'scripts/plugins/bootbox.js', ['jquery', 'bootstrap', 'bootbox']);

        Asset::group('red')->add('bootbox', 'scripts/plugins/bootbox.js');
    }
];
```
