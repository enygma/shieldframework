<?php
//PHPunit bootstrap
ob_start();

function load($className)
{
    $path = __DIR__.'/../'.str_replace('Shield\\', '/', $className).'.php';
    if (is_file($path)) {
        include_once $path;
        return true;
    } else {
        return false;
    }
}
spl_autoload_register('load');
