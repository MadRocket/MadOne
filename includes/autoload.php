<?php

require_once __DIR__.'/../vendor/symfony/class-loader/Symfony/Component/ClassLoader/UniversalClassLoader.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();

$loader->registerPrefixes(array(
    // My
    'Madone_' => __DIR__.'/',
    'Model_'  => __DIR__.'/',
    'Storm_'  => __DIR__.'/',
    'Outer_'  => __DIR__.'/',

    // Vendor
    'Twig_' => __DIR__.'/../vendor/twig/twig/lib/',
    'Twig_Extensions_' => __DIR__.'/../vendor/twig/extensions/lib/',
));

$loader->register();
