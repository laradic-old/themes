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
- **Template Languages** Works with PHP, Blade, Twig and any other engine. 
- **Cascading & Inheritance** Themes are able to inherit all options from other themes, providing a smart and intuitive way to extend existing themes.
- **Asset Management** Featuring: Dependable assets or asset groups, caching, minification, filters(scss, less, etc). Uses/extends kriswallsmith/assetic in the background.   
- **Navigation & Breadcrumbs** Extra navigation helper classes that can be used in your themes.
- **And so much more**...
  

[**Check the documentation for all features and options**](http://docs.radic.nl/themes/)


#### My other packages
| Package | Description | |
|----|----|----|
| [radic/blade-extensions](https://github.com/radic/blade-extensions) | A collection of usefull Laravel blade extensions, like $loop data in foreach, view partials, etc | [doc](http://docs.radic.nl/blade-extensions) |
| [laradic/extensions](https://github.com/laradic/extensions) | Modular and manageable approach to extending your app | [doc](http://docs.radic.nl/extensions) |
| [laradic/config](https://github.com/laradic/config) | Laravel 5 Config exras like namespaces, saving to db/file. yaml/php array parser etc | [doc](http://docs.radic.nl/config) |
| [laradic/docit](https://github.com/laradic/docit) | A documentation generator for your code, live preview [docs.radic.nl](http://docs.radic.nl/) | [doc](http://docs.radic.nl/docit) |
| [laradic/themes](https://github.com/laradic/themes) | Laravel 5 theme package | [doc](http://docs.radic.nl/themes) |
 
  
#### Installation  
###### Composer
```JSON
"laradic/themes": "~0.2"
```
###### Laravel
Add the ThemesServiceProvider to your config.
```php
'Laradic\Themes\ThemesServiceProvider'
```

Optionally, you can add any of the Facades below:
```php
array(
    'Themes' => 'Laradic\Themes\Facades\Themes',
    'Asset' => 'Laradic\Themes\Facades\Asset',
    'Navigation' => 'Laradic\Themes\Facades\Navigation'
);
```
##### Configuration
```sh
php artisan vendor:publish laradic/themes --tag="config"
```

```php

return array(
    /* debugging */
    'debug'           => false, // if true, disables all minify, chache and concenation etc
    /* paths */
    'active'          => 'frontend/default',
    'default'         => 'frontend/default',
    /** @deprecated */
    'fallback'        => null,
    /* Class names */
    'assetClass'      => '\\Laradic\\Themes\\Assets\\Asset',
    'assetGroupClass' => '\\Laradic\\Themes\\Assets\\AssetGroup',
    'themeClass'      => '\\Laradic\\Themes\\Theme',
    'paths'           => array(
        'themes'     => array(
            public_path('themes'),
            public_path()
        ),
        // These paths are relative to the theme path defined above
        'namespaces' => 'namespaces',
        'packages'   => 'packages',
        'views'      => 'views',    //default ex: public/themes/{area}/{theme}/views
        'assets'     => 'assets',
        // relative to public_path
        'cache'      => 'cache'
    ),
    'assets' => array(
        /* Assetic Filters that should be applied to all assets with the given extension
           Note that adding global filters can also be done by using Asset::addGlobalFilter('css', 'FilterFQClassName....') */
        'globalFilters' => array(
            'css' => array('Laradic\Themes\Assets\Filters\UriRewriteFilter'),
            'js' => array('Laradic\Themes\Assets\Filters\UriRewriteFilter'),
            'scss' => array('Assetic\Filter\ScssphpFilter', 'Laradic\Themes\Assets\Filters\UriRewriteFilter')
        )
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
A perfect place to define/manage your assets(groups)
  
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
        Asset::addGlobalFilter('css', 'Laradic\Themes\Assets\Filters\UriRewriteFilter')
            ->addGlobalFilter('js', 'Laradic\Themes\Assets\Filters\UriRewriteFilter')
            ->addGlobalFilter('scss', 'Laradic\Themes\Assets\Filters\UriRewriteFilter')
            ->addGlobalFilter('scss', 'Assetic\Filters\ScssphpFilter');
            
        Asset::group('base')
            ->addFilter('ts', 'Some\Random\FilterClass')
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


// Assets
Asset::addGlobalFilter('css', 'Laradic\Themes\Assets\Filters\UriRewriteFilter')
    ->addGlobalFilter('js', 'Laradic\Themes\Assets\Filters\UriRewriteFilter');

Asset::group('base')
    ->add('jquery', 'plugins/jquery/dist/jquery.min.js')
    ->add('bootstrap', 'plugins/bootstrap/dist/js/bootstrap.min.js', [ 'jquery' ])
    ->add('bootstrap', 'plugins/bootstrap/dist/css/bootstrap.min.css')
    ->add('bootbox', 'something::bootbox/bootbox.js', [ 'jquery', 'bootstrap' ])
    ->add('slimscroll', 'plugins/jquery-slimscroll/jquery.slimscroll.js', [ 'jquery' ])
    ->add('modernizr', 'plugins/modernizr/modernizr.js')
    ->add('moment', 'plugins/moment/moment.js')
    ->add('highlightjs', 'plugins/highlightjs/highlight.pack.js')
    ->add('highlightjs', 'plugins/highlightjs/styles/zenburn.css');

Asset::group('ie9')
    ->add('respond', 'plugins/respond/dest/respond.min.js')
    ->add('html5shiv', 'plugins/html5shiv/dist/html5shiv.js');
    
{!! Asset::group('base')->add('style', 'style.css')->render('styles') !!}

<!--[if lt IE 9]>
{!! Asset::group('ie9')->render('scripts') !!}
<![endif]-->
{!! Asset::group('base')->render('scripts') !!}


{!! Asset::script('something::bootbox/bootbox.js') !!}
<!--
Get the URL
{!! Asset::url('something::bootbox/bootbox.js') !!}

Get the URI
{!! Asset::uri('something::bootbox/bootbox.js') !!}

Dump the content
{!! Asset::make('bootbox', 'something::bootbox/bootbox.js')->dump() !!}

Dump some scss converted to css
@if(class_exists('Leafo\ScssPhp\Compiler'))
{!! Asset::make('sassStyle', 'sassStyle.scss')->dump() !!}
```
  
###### Console commands
```sh
php artisan themes:publishers ## List all available publishers
php artisan themes:publish <publisher>
```
  
And much, much more.. Check out the [**documentation**](http://docs.radic.nl/themes/).

### Copyright/License
Copyright 2015 [Robin Radic](https://github.com/RobinRadic) - [MIT Licensed](http://radic.mit-license.org)
