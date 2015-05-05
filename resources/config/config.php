<?php

return array(
    /* debugging */
    'debug'           => true, // if true, disables all minify, chache and concenation etc
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
    )
);
