<?php

error_reporting(E_ALL | E_STRICT);

// register silently failing autoloader
spl_autoload_register(function($class){
        if (0 === strpos($class, 'ThreadWorker\\Tests\\')) {
            $path = __DIR__.'/'.strtr($class, '\\', DIRECTORY_SEPARATOR).'.php';
            if (is_file($path) && is_readable($path)) {
                require_once $path;
                return true;
            }
        }
        return false;
    });

return require_once __DIR__ . "/../vendor/autoload.php";
