<?php

return array(

	/*
    |--------------------------------------------------------------------------
    | CSS path and CSS build path
    |--------------------------------------------------------------------------
    |
    | Minify is an extention that can minify your css files into one build file.
    | The css_path property is the location where your CSS files are located
    | The css_builds_path property is the location where the builded files are
    | stored.  THis is relative to the css_path property.
    |
    */

    'css_path' => '/css/',
    'css_build_path' => 'builds/',

    /*
    |--------------------------------------------------------------------------
    | CSS Files
    |--------------------------------------------------------------------------
    |
    | Load css files
    |
    */
    'css' => array(
        'default' => array(
            'css1.css',
            'css2.css',
            'css3.css'
        )
    ),

    'css3_browsers' => array(
            "Internet Explorer" => 9,
            "Mozilla Firefox"   =>  3.5,
            "Opera"     =>  9,
            "Apple Safari"    =>  4,
            "Google Chrome"    =>  4,
    ),

	/*
    |--------------------------------------------------------------------------
    | JS path and JS build path
    |--------------------------------------------------------------------------
    |
    | Minify is an extention that can minify your JS files into one build file.
    | The JS_path property is the location where your JS files are located
    | The JS_builds_path property is the location where the builded files are
    | stored.  THis is relative to the css_path property.
    |
    */

    'js_path' => '/js/',
    'js_build_path' => 'builds/',

);
