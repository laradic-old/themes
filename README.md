<a name="top"></a>![Laravel logo](http://laravel.com/assets/img/laravel-logo.png)  Laradic Themes for Laravel 5
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
  

-----------
  
<a name="overview"></a>
### Overview <sub>[^](#top)</sub>
- [Features](#top)
- [Overview](#overview)
- [Installation](#installation)
- [First time usage guide](#first-time)
- [Views/Themes](#viewsthemes-)
- [Assets](#assets)
- [The theme.php file](#assets)
- [Navigation/Breadcrumbs](#navigation)
- [Console Commands](#console)
- [Todo](#todo)
- [Copyright/license](#copyright)
  
-----------
  
  

<a name="installation"></a>
### Installation  <sub>[^](#top)</sub>
###### Composer
```JSON
"laradic/themes": "~0.2"
```
###### Laravel
Add the ThemesServiceProvider to your config.
```php
'Laradic\Themes\ThemeServiceProvider'
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
php artisan vendor:publish --tag="config"
```


<a name="first-time"></a>
### First time usage guide and explenation <sub>[^](#top)</sub>

Open up the `laradic.config.php` file. **For first time use**, just change the theme folder path to your desired location.
```php
return array(
    // ...
    'paths'           => array(
        'themes'     => array(
            public_path('themes'), // change this to the desired location
            public_path()
        ),
        // ...
    )
);
```

Save it and run `php artisan themes:init`. This will generate a few folders and files matching the default config settings.
It only serves as an example, showing the directory structure for a theme and theme view inheritance.

Loading a view file is the same as normal.
```php
// The active theme is 'example/main'.
// It contains the index.blade.php file in the view folder.
// Which can be loaded like:
View::make('index');
```

#### Inheritance system basics

**Load priority**
[`Active Theme View Folder`]() **>** [`Parent Theme View Folder`(if set)]() **>** [`Default Theme View Folder`]() **>** [`Default Laravel View Folder`]()

If you understand that, skip these points. Otherwise, more details about this:
- If you open the `index.blade.php` file, you see it @extends layout.
- If there was a `layout.blade.php` file in the same folder, it would use that one (duhh).
- However, that's not the case right now. So the Theme manager will start looking in other theme directories if they have the file (with the same relative path).
- It will first check the parent theme of 'example/main', defined in the theme.php file (or not, its optional).
- If its not there either, it will check the default theme. Which in this case, has the layout.blade.php file.
- If by any chance, the default folder doesn't have that file either, it will lastly check the standard Laravel view folder for that file.

The same goes for loading Views, Assets, etc.

#### Cascade system basics
To put it simply, every theme can have "sub-themes". Inside a theme folder, you notice the `namespaces` and `packages` folder. 

##### To create a namespace  
For example: 
- create the `lingo` folder inside the `namespaces` folder of the `example/main` theme. 
- Inside that folder, create the `assets` and `views` folder.
- Create a `myview.blade.php` inside the `view` folder
```php
View::make('lingo::myview')
```

- Create a `subdir/otherview.blade.php` inside the `view` folder
```php
View::make('lingo::subdir.otherview')
```

##### To create package
A package need to be in 2 directories. 
- So create the `foo/bar` folder inside the `packages` folder of the `example/main` theme.
- Inside that folder, create the `assets` and `views` folder.
- Create a `hakker.blade.php` inside the `view` folder
```php
View::make('foo/bar::hakker')
```

- Create a `subdir/otherhakker.blade.php` inside the `view` folder
```php
View::make('foo/bar::subdir.otherhakker')
```

**The same goes for assets**

### Views/Themes <sub>[^](#top)</sub>

The active and default theme can be set in the configuration by altering the `active` and `default` keys.  
You can set the active theme on the fly by using `Theme::setActive('theme/slug')`.  
You can set the default theme on the fly by using `Theme::setDefault('theme/slug')`.  

```php
// public/themes/{active/theme}/views/view-file.EXT
$view = View::make("view-file");

// public/themes/{active/theme}/namespaces/my-namespace/views/view-file.EXT
$view = View::make("my-namespace::view-file");

// public/themes/{active/theme}/packages/vendor-name/package-name/views/view-file.EXT
$view = View::make("vendor-name/package-name::view-file");

Themes::setActive("backend/admin");
$view = View::make("view-file"); // -> public/backend/admin/views/view-file.EXT
// etc
```

#### Common methods overview
Check out the API documentation for the full list of methods.

##### Themes (Facade => ThemeFactory)
 
| Function call | Return type | Description |
|:--------------|:------------|:------------|
| `Themes::setActive($theme)`   | self  | Set the active theme, `$theme` can be a Theme instance or the slug string of that theme |
| `Themes::getActive()`         | Theme | Returns the active theme |
| `Themes::setDefault($theme)`  | self  | Set the default theme, `$theme` can be a Theme instance or the slug string of that theme |
| `Themes::getDefault()`        | Theme | Returns the default theme |
| `Themes::resolveTheme($slug)` | Theme | Resolve a theme using it's slug. It will check all theme paths for the required theme. |
| `Themes::all()`               | string[] | Returns all resolved theme slugs |
| `Themes::get($slug)`          | Theme | Returns the theme instance if the theme is found |
| `Themes::has($slug)`          | bool  | Check if a theme exists |
| `Themes::count()`             | int   | Get the number of themes |
| `Themes::addNamespace($name, $dirName)`      | self   | Add a namespace to the theme |
| `Themes::getPath($type)`      | string   | Get a path for the type (assets, views, namespaces, packages) |


##### Theme (instance of a theme)
 
| Function call | Return type | Description |
|:--------------|:------------|:------------|
| `Theme::getConfig()`          | array  | The array from `theme.php` |
| `Theme::getParentTheme()`     | Theme  | .. |
| `Theme::getParentSlug()`      | string  | .. |
| `Theme::hasParent()`          | bool  | .. |
| `Theme::getSlug()`            | string  | .. |
| `Theme::getSlugKey()`         | string  | .. |
| `Theme::getSlugProvider()`    | string  | .. |
| `Theme::getName()`            | string  | .. |
| `Theme::isActive()`           | bool  | .. |
| `Theme::isDefault()`          | bool  | .. |
| `Theme::isBooted()`           | bool  | .. |
| `Theme::boot()`               | void  | .. |
| `Theme::getVersion()`         | SemVer  | .. |
| `Theme::getPath()`            | string  | .. |
| `Theme::getCascadedPath()`    | string  | .. |


<a name="assets"></a>
### Assets <sub>[^](#top)</sub>
 
The `Asset` **Facade** links to `AssetFactory`. It should not be confused with the `Asset` class that `Asset::make` returns, which actually holds asset data.
  
**Note** `$path` is the same as with Views (namespaces, packages, etc)

| Function call | Return type | Description |
|:--------------|:------------|:------------|
| `Asset::make($path);` | [`Asset<FileAsset>`](blob/master/Assets/Asset.php) | Returns the asset instance |
| `Asset::url($path);` | string | Returns the asset URL |
| `Asset::uri($path);` | string | Returns the asset uri |
| `Asset::script($path, array $attr = [ ], $secure = false));` | string | Renders the asset in a `<script src="">` tag |
| `Asset::style($path, array $attr = [ ], $secure = false));` | string | Renders the asset in a `<link ..>` tag |
| `Asset::group($name);` | [`AssetGroup`](blob/master/Assets/AssetGroup.php) | Returns an AssetGroup, more details below |
| `Asset::addGlobalFilter($extension, $callback);` | void | Add global `Assetic` filter, to be applied on all assets with matching extension |
| `Asset::setCachePath($path);` | string | Returns the filesystem path to the asset file |
| `Asset::getCachePath();` | string | Returns the filesystem path to the asset file |
| `Asset::deleteAllCached();` | string | Returns the filesystem path to the asset file |
| `Asset::setAssetClass("Full\Class\Name");` | string | Returns the filesystem path to the asset file |
| `Asset::setAssetGroupClass("Full\Class\Name");` | string | Returns the filesystem path to the asset file |


##### AssetGroup
Is used to group assets. Has several features you could use:
- Depencency management
- Minifaction & concenation
- Caching

```php
// I would advice to do this in theme.php its boot closure!
Asset::group('base')
    ->add('jquery', 'plugins/jquery/dist/jquery.min.js')
    ->add('bootstrap', 'plugins/bootstrap/dist/js/bootstrap.min.js', [ 'jquery' ])
    ->add('bootstrap', 'plugins/bootstrap/dist/css/bootstrap.min.css')
    ->add('bootbox', 'something::bootbox/bootbox.js', [ 'jquery', 'bootstrap' ])
    ->add('slimscroll', 'plugins/jquery-slimscroll/jquery.slimscroll.js', [ 'jquery' ])
    ->add('modernizr', 'plugins/modernizr/modernizr.js')
    ->add('moment', 'plugins/moment/moment.js')
    ->add('highlightjs', 'plugins/highlightjs/highlight.pack.js')
    ->add('highlightjs', 'plugins/highlightjs/styles/zenburn.css')
    ->add('sassStyle', 'sassStyle.scss');

Asset::group('ie9')
    ->add('respond', 'plugins/respond/dest/respond.min.js')
    ->add('html5shiv', 'plugins/html5shiv/dist/html5shiv.js');
    
// And continue somewhere else 
Asset::group('base')
    ->add('name', 'path');
    
    
// Other functions
$group = Asset::group('base');

$group->addFilter('scss', 'Assetic\Filters\ScssphpFilter')
$group->render($type, $combine = true); // type can be either: 'scripts' or 'styles'
$group->getFilters($fileExtension);
$group->get($type, $handle); // $handle is the name of the asset, which u entered as first parameter with add()
$group->getSorted($type); // get all assets of $type sorted by dependency
$group->getAssets($type); // get all assets of $type
$group->getName(); // get the name of group ('base')
```


```php
// then in the view files you could do
<html>
<head>
    <link href='//fonts.googleapis.com/css?family=Lato:100' rel='stylesheet' type='text/css'>
    {!! Asset::group('base')->add('style', 'style.css')->render('styles') !!}

</head>
<body>
		<div class="container">
			<div class="content">
                <div class="info">
                    Using theme: {{ Themes::getActive()->getName() }}.
                    @if(Themes::getActive()->hasParent())
                        Using parent theme: {{ Themes::getActive()->getParentTheme()->getName() }}
                    @endif
                </div>
				<div class="title">
                    @section('content')
                        The layout
                    @show
                </div>
			</div>
		</div>

    <!--[if lt IE 9]>
    {!! Asset::group('ie9')->render('scripts') !!}
    <![endif]-->
    {!! Asset::group('base')->render('scripts') !!}
    {!! Asset::script('something::bootbox/bootbox.js') !!}
    
    <!-- Get the URL -->
    {!! Asset::url('something::bootbox/bootbox.js') !!}

    <!-- Get the URI -->
    {!! Asset::uri('something::bootbox/bootbox.js') !!}

    <!-- Dump the content -->
    Asset::make('bootbox', 'something::bootbox/bootbox.js')->dump()

    <!-- Dump some scss converted to css -->
    {!! Asset::make('sassStyle', 'sassStyle.scss')->dump() !!}
</body>
</html>
    
```

### The theme.php file <sub>[^](#top)</sub>
Beside the obvious fields, the boot field is rather important.
Use the **boot** field closure to register namespaces for your theme, define assets and asset groups, and other global stuff.
```php
use Illuminate\Contracts\Foundation\Application;
use Laradic\Themes\Theme;

return [
    'parent'   => null,
    'name'     => 'Example theme',
    'slug'     => 'example/theme',
    'version'  => '0.0.1',
    'register' => function (Application $app, Theme $theme)
    {
    },
    'boot'     => function (Application $app, Theme $theme)
    {
        Themes::addNamespace('something', 'something');
        
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
    }
];
```
<a name="navigation"></a>
### Navigation/Breadcrumbs <sub>[^](#top)</sub>

<a name="console"></a>
### Console Commands <sub>[^](#top)</sub>


###### List publishers
```sh
php artisan themes:publishers
```
  
###### Publish a theme 
```sh
php artisan themes:publish <publisher>
```
  
###### Creating a theme
```sh
php artisan themes:create <theme/slug> [path]
```
  
###### Creating the initial theme structure (ment as example)
```sh
php artisan themes:init
```



<a name="todo"></a>
### Todo <sub>[^](#top)</sub>
- [ ] Finishing Navigation & Breadcrumb helper
- [ ] Proper documentation (not this README.md file)
- [ ] Unit tests
  
  
<a name="copyright"></a>
### Copyright/License <sub>[^](#top)</sub>
Copyright 2015 [Robin Radic](https://github.com/RobinRadic) - [MIT Licensed](http://radic.mit-license.org)
