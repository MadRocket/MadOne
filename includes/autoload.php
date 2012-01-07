<?php
require __DIR__.'/../vendor/.composer/autoload.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();

$loader->registerPrefixes(array(
    // My
    'Madone_' => __DIR__.'/',
    'Model_'  => __DIR__.'/',
    'Storm_'  => __DIR__.'/',
    'Outer_'  => __DIR__.'/',
));

$loader->register();
