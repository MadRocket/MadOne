<?php
require __DIR__ . '/vendor/.composer/autoload.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();

$loader->registerPrefixes(array(
    // My
    'Madone_' => __DIR__.'/includes/',
    'Model_'  => __DIR__.'/includes/',
    'Storm_'  => __DIR__.'/includes/',
    'Outer_'  => __DIR__.'/includes/',
));

$loader->register();
