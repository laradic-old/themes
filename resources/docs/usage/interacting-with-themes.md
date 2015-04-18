<!---
title: Interacting with themes
author: Robin Radic
icon: fa fa-legal
-->

```php
// Getting themes
$theme = Themes::getActive();
$theme = Themes::getDefault();
$theme  = Themes::get('backend/admin');

// Theme methods
$exists = Theme::has('backend/admin');  // -> returns bool
$themeSlugs = Themes::all();            // -> returns array with theme slugs eg: ['frontend/default', 'backend/admin']
$name = $theme->getName();              // -> returns string
$slug = $theme->getSlug();              // -> returns string
$hasParent = $theme->hasParent();       // -> returns bool
$theme = $theme->getParentTheme();      // -> returns instance of Theme
$parentSlug = $theme->getParentSlug();  // -> returns string

// Theme configuration
$theme->getConfig();                    // -> Returns array with the complete theme.php config
$theme['config.key'];                   // -> Access theme.php config keys using dot notation

// To distribute themes in namespaces/packages using themes:publish 
// you can use the following methods (usually in a service provider of a seperate package)
Themes::addNamespace('');
Themes::addNamespacePublisher('');
Themes::addPackagePublisher('');
```
