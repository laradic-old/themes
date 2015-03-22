<?php

return array(
    /* debugging */
    'debug'      => false, // if true, disables all preprocessing, minify, chache etc
    /* paths */
    'active'     => 'frontend/default',
    'default'    => 'frontend/default',
    /** @deprecated */
    'fallback'   => null,
    /* Class names */
    'assetClass' => '\\Laradic\\Themes\\Assets\\Asset',
    'themeClass' => '\\Laradic\\Themes\\Theme',
    'paths'      => array(
        'themes'     => array(
            public_path()
        ),
       'namespaces' => 'namespaces',
        // These paths are relative to the theme path defined above
        'packages' => 'packages',
        #'packages'   => 'packages',
        'views'      => 'views',    //default ex: public/themes/{area}/{theme}/views
        'assets'     => 'assets',
        'cache'      => 'cache'
    ),
    /* default output processing options */
    'assets'     => array(
        'preprocess' => true,
        'minify'     => true,
        'compress'   => true,
        'optimize'   => true
    )
);
