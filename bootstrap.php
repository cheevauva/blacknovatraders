<?php

spl_autoload_register(function ($className) {
    $fullPath = __DIR__ . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php';

    if (file_exists($fullPath)) {
        require $fullPath;
        return;
    }
});
