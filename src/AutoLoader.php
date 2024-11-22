<?php

require_once __DIR__ . '/../autoload.php';

function find($class_name)
{
    global $sgAutoloadLocalNamespaces;

    $parts = explode('\\', $class_name);

    $namespace = array_shift($parts) . '\\';
    $class_file = array_pop($parts) . '.php';

    while (!array_key_exists($namespace, $sgAutoloadLocalNamespaces)) {
        if (isset($parts[0])) {
            $namespace = $namespace . array_shift($parts) . '\\';
        } else {
            return;
        }
    }

    $path = implode(DIRECTORY_SEPARATOR, $parts);
    $file = $sgAutoloadLocalNamespaces[$namespace] . DIRECTORY_SEPARATOR .
        $path . DIRECTORY_SEPARATOR .
        $class_file;

    if (!file_exists($file) && !class_exists($class_name)) {
        return;
    }

    return $file;
}

/**
 * autoload - take a class name and attempt to load it
 *
 * @param string $className Name of class we're looking for.
 */
function autoload($class_name)
{
    $file_name = find($class_name);

    if ($file_name !== null) {
        require_once $file_name;
    }
}

spl_autoload_register('autoload');

// Load composer's autoloader if present
if (is_readable(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} elseif (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    exit(__DIR__ . '/../vendor/autoload.php exists but is not readable');
}
