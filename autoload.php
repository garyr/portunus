<?php
$autoload = null;
foreach (array(__DIR__ . '/../../autoload.php', __DIR__ . '/vendor/autoload.php') as $file) {
    if (file_exists($file)) {
        $autoload = $file;
        if (!defined('PORTUNUS_COMPOSER_VENDOR_DIR')) {
            define('PORTUNUS_COMPOSER_VENDOR_DIR', dirname($file));
        }
        break;
    }
}

unset($file);

if (!defined('PORTUNUS_COMPOSER_VENDOR_DIR')) {
    fwrite(STDERR,
        'You need to set up the project dependencies using the following commands:' . PHP_EOL .
        'wget http://getcomposer.org/composer.phar' . PHP_EOL .
        'php composer.phar install' . PHP_EOL
    );
    die(1);
}

require PORTUNUS_COMPOSER_VENDOR_DIR . '/autoload.php';