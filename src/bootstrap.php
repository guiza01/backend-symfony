<?php

spl_autoload_register(static function (string $class): void {
    $prefixes = [
        'App\\' => __DIR__ . '/',
        'Symfony\\Component\\HttpFoundation\\' => __DIR__ . '/Symfony/Component/HttpFoundation/',
        'Symfony\\Component\\Routing\\Attribute\\' => __DIR__ . '/Symfony/Component/Routing/Attribute/',
        'Symfony\\Contracts\\HttpClient\\' => __DIR__ . '/Symfony/Contracts/HttpClient/',
    ];

    foreach ($prefixes as $prefix => $baseDir) {
        if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
            continue;
        }

        $relativeClass = substr($class, strlen($prefix));
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

        if (is_file($file)) {
            require_once $file;
        }

        return;
    }
});
