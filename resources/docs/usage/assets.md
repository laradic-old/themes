<!---
title: Assets
author: Robin Radic
icon: fa fa-code
-->

#### Defining assets
```sh
php artisan themes:publishers
```
  
#### Loading assets
You can load assets using the same syntax as loading theme views:
  
```php
// public/{active/theme}/assets/asset-file.EXT
$asset = Asset::make("asset-file");

// public/{active/theme}/namespaces/my-namespace/assets/asset-file.EXT
$asset = Asset::make("my-namespace::asset-file");

// public/{active/theme}/packages/vendor-name/package-name/assets/asset-file.EXT
$asset = Asset::make("vendor-name/package-name::asset-file");

Themes::setActive("backend/admin");
$asset = Asset::make("asset-file"); // -> public/backend/admin/assets/asset-file.EXT
// etc
```
  
As with the theme views, the assets will load using the following order:
  
- public/{area}/{theme}/assets/asset-file.EXT 
- (parent theme assets folder)
- resources/assets/asset-file.EXT
- (default theme assets folder)
  

#### Asset methods
<!---+ table table-hover table-condensed table-striped table-light +-->
| Method | Description |
|---|---|
| `Asset::make("asset-file");` | Returns the asset instance |
| `Asset::url("asset-file");` | Returns the asset URL |
| `Asset::uri("asset-file");` | Returns the asset uri |
| `Asset::script("asset-file");` | Renders the asset in a `<script src="">` tag |
| `Asset::style("asset-file");` | Renders the asset in a `<link ..>` tag |
| `Asset::path("asset-file");` | Returns the filesystem path to the asset file |
<!---+ /table +-->
