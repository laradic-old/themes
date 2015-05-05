<?php

return array(
    /* debugging */
    'debug'        => true, // if true, disables all minify, chache and concenation etc
    /* paths */
    'active'       => 'frontend/default',
    'default'      => 'frontend/default',
    /** @deprecated */
    'fallback'     => null,
    /* Class names */
    'assetClass'   => '\\Laradic\\Themes\\Assets\\Asset',
    'themeClass'   => '\\Laradic\\Themes\\Theme',
    'widgetsClass' => '\\Laradic\\Themes\\Widgets',
    'paths'        => array(
        'themes'     => array(
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
