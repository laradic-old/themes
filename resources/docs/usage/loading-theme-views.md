<!---
title: Loading theme views
author: Robin Radic
icon: fa fa-legal
-->

The active and default theme can be set in the configuration by altering the `active` and `default` keys.
`View::make` will return the first found view using the following order:

- public/{area}/{theme}/views/view-file.EXT 
- (parent theme views folder)
- resources/views/view-file.EXT
- (default theme views folder)
  
  
You can set the active theme on the fly by using `Theme::setActive('theme/slug')`.
  

```php
// public/{active/theme}/views/view-file.EXT
$view = View::make("view-file");

// public/{active/theme}/namespaces/my-namespace/views/view-file.EXT
$view = View::make("my-namespace::view-file");

// public/{active/theme}/packages/vendor-name/package-name/views/view-file.EXT
$view = View::make("vendor-name/package-name::view-file");

Themes::setActive("backend/admin");
$view = View::make("view-file"); // -> public/backend/admin/views/view-file.EXT
// etc
```
