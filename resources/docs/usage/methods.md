<!---
title: Methods
author: Robin Radic
icon: fa fa-legal
-->


```php
Themes::hello();

Asset::hello();



```

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
| `Themes::count()`             | int   | Get the number of themes |
| `Themes::count()`             | int   | Get the number of themes |
| `Themes::count()`             | int   | Get the number of themes |
| `Themes::count()`             | int   | Get the number of themes |
