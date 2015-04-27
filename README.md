![Laravel logo](http://laravel.com/assets/img/laravel-logo.png)  Laradic Themes for Laravel 5
========================

[![Build Status](https://img.shields.io/travis/laradic/themes.svg?branch=master&style=flat-square)](https://travis-ci.org/laradic/themes)
[![GitHub Version](https://img.shields.io/github/tag/laradic/themes.svg?style=flat-square&label=version)](http://badge.fury.io/gh/laradic%2Fthemes)
[![Code Coverage](https://img.shields.io/badge/coverage-100%-green.svg?style=flat-square)](http://radic.nl:8080/job/laradic-themes/cloverphp)
[![Total Downloads](https://img.shields.io/packagist/dt/laradic/themes.svg?style=flat-square)](https://packagist.org/packages/laradic/themes)
[![License](http://img.shields.io/badge/license-MIT-ff69b4.svg?style=flat-square)](http://radic.mit-license.org)

[![Goto Documentation](http://img.shields.io/badge/goto-docs-orange.svg?style=flat-square)](http://docs.radic.nl/themes)
[![Goto API Documentation](https://img.shields.io/badge/goto-api--docs-orange.svg?style=flat-square)](http://radic.nl:8080/job/laradic-themes/PHPDOX_Documentation/)
[![Goto Repository](http://img.shields.io/badge/goto-repo-orange.svg?style=flat-square)](https://github.com/laradic/themes)


Version 0.2.0
-----------

**Laravel 5** package providing multi-theme inherited cascading support.

- **Easy and worry free setup** Laradic Themes does not conflict with the standard Laravel view system. 
- **Cascading & Inheritance** Themes are able to inherit all options from other themes, providing a smart and intuitive way to extend existing themes. 
- **Composer installer** Themes can be distributed using composer
- **Asset manager** Creating view partials and blocks. Nest them, extend them, render them.
- **And so much more**...
  

[**Check the documentation for all features and options**](http://docs.radic.nl/themes/)


#### My other packages
| Package | Description | |
|----|----|----|
| [laradic/extensions](https://github.com/laradic/extensions) | Modular and manageable approach to extending your app | [doc](http://docs.radic.nl/extensions) |
| [laradic/config](https://github.com/laradic/config) | Laravel 5 Config exras like namespaces, saving to db/file. yaml/php array parser etc | [doc](http://docs.radic.nl/config) |
| [laradic/docit](https://github.com/laradic/docit) | A documentation generator for your code, live preview [docs.radic.nl](http://docs.radic.nl/) | [doc](http://docs.radic.nl/docit) |
| [laradic/themes](https://github.com/laradic/themes) | Laravel 5 theme package | [doc](http://docs.radic.nl/themes) |
| [radic/blade-extensions](https://github.com/radic/blade-extensions) | A collection of usefull Laravel blade extensions, like $loop data in foreach, view partials, etc | [doc](http://docs.radic.nl/blade-extensions) |
  
  
#### Installation  
###### Composer
```JSON
"laradic/themes": "~0.2"
```
###### Laravel
```php
'Laradic\Themes\ThemesServiceProvider'
```

##### Configuration
```sh
php artisan vendor:publish laradic/themes --tag="config"
```

```php
return array(
    'debug'      => env('APP_DEBUG', false), // if true, disables all preprocessing, minify, chache etc
    'active'     => 'frontend/default',
    'default'    => 'frontend/default',    
    // Class names, if you want to extend/override
    'assetClass'   => '\\Laradic\\Themes\\Assets\\Asset',
    'themeClass'   => '\\Laradic\\Themes\\Theme',
    'widgetsClass' => '\\Laradic\\Themes\\Widgets',
    'paths'        => array(        
        'themes'     => array(
            public_path() // Add paths that will be searched for themes            
        ),
        // These paths are relative to the theme path defined above
        'namespaces' => 'namespaces', //ex: public/themes/{area}/{theme}/namespaces/{namespace}/views
        'packages'   => 'packages', 
        'views'      => 'views',      //ex: public/themes/{area}/{theme}/views
        'assets'     => 'assets'
    ),
    'assets'     => array(
        'preprocess' => true,
        'minify'     => true,
        'compress'   => true,
        'optimize'   => true
    )
);
```
  

#### Some examples
These are just some basic operations, there's a lot more stuff you can do which is covered in the [**documentation**](http://docs.radic.nl/themes/).
  
By default, Laradic Themes will search your `public` folder for themes. 
You can add paths in the config file or do it on the fly using `Themes::addPath('/path/to/dir')`.
  
###### Default theme folder structure
```
- public (as defined in config paths.themes)
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
  
###### theme.php
A perfect place to manage your assets
  
```php

use Illuminate\Contracts\Foundation\Application;
use Laradic\Themes\Assets\AssetGroup;
use Laradic\Themes\Theme;

return [
    'parent'   => null,
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

###### Loading theme views
The active and default theme can be set in the configuration by altering the `active` and `default` keys.
`View::make` will return the first found view using the following order:

- public/{area}/{theme}/views/view-file.EXT 
- (parent theme views folder)
- resources/views/view-file.EXT
- (default theme views folder)
    
You can set the active theme on the fly by using `Theme::setActive('theme/slug')`
  
```php
// public/{active/theme}/views/view-file.EXT
$view = View::make('view-file');  

// public/{area}/{theme}/namespaces/my-namespace/views/view-file.EXT
$view = View::make('my-namespace::view-file'); 

// public/{area}/{theme}/packages/vendor-name/package-name/views/view-file.EXT
$view = View::make('vendor-name/package-name::view-file'); 

Themes::setActive('backend/admin');
$view = View::make('view-file'); // -> public/backend/admin/views/view-file.EXT
// etc
```

###### Getting, setting and interacting with the themes
  
```php
// Getting themes
$theme  = Themes::getActive(); // -> returns instance of Theme
$theme  = Themes::getDefault();
$exists = Theme::has('backend/admin'); // -> returns bool
$theme  = Themes::get('backend/admin');
$themeSlugs = Themes::all(); // -> returns array with theme slugs eg: ['frontend/default', 'backend/admin']

// Theme methods
$theme->getName();
$theme->getSlug();
$theme->hasParent();
$theme->getParentTheme();
$theme->getParentSlug();
$theme->getConfig();


// To distribute themes in namespaces/packages using themes:publish 
// you can use the following methods (usually in a service provider of a seperate package)
Themes::addNamespace('');
Themes::addNamespacePublisher('');
Themes::addPackagePublisher('');
```
  
###### Console commands
```sh
php artisan themes:publishers ## List all available publishers
php artisan themes:publish <publisher>
```
  
And much, much more.. Check out the [**documentation**](http://docs.radic.nl/themes/).

### Copyright/License
Copyright 2015 [Robin Radic](https://github.com/RobinRadic) - [MIT Licensed](http://radic.mit-license.org)
