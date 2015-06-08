<!---
title: Configuration
author: Robin Radic
icon: fa fa-legal
-->

  
##### Configuration
```sh
php artisan vendor:publish laradic/themes --tag="config"
```

```php

return array(
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
        // full path to cache folder, requires to be public
        'cache'      => public_path('cache')
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
  
