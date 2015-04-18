<!---
title: Configuration
author: Robin Radic
icon: fa fa-legal
-->

#### Publish config
```sh
php artisan vendor:publish laradic/themes --tag="config"
```
  
#### Config preview
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
