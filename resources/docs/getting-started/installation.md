<!---
title: Installation
author: Robin Radic
icon: fa fa-legal
-->

###### Composer
```JSON
"laradic/themes": "~1.0"
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
