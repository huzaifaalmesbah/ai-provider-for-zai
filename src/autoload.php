<?php

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * PSR-4 autoloader for AI Provider for Z.AI package.
 *
 * @since 1.0.0
 *
 * @package Huzaifa\AiProviderForZAI
 */

spl_autoload_register(static function (string $class): void {
    $prefix = 'Huzaifa\\AiProviderForZAI\\';
    $baseDir = __DIR__ . '/';

    $len = strlen($prefix);

    if (strncmp($class, $prefix, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});
