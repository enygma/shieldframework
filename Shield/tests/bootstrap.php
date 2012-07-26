<?php
//PHPunit bootstrap

function load($className)
{
    $path = __DIR__.'/../'.str_replace('Shield\\', '/', $className).'.php';
    if (is_file($path)) {
        include_once $path;
    } else {
        $this->_throwError('Could not load class: '.$className);
    }
}
spl_autoload_register('load');
?>