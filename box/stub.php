#!/usr/bin/env php
<?php


if (class_exists('Phar')) {
    Phar::mapPhar('phpdocumentor.phar');
    if (is_string(getenv('ANSICON'))) {
        require 'phar://phpdocumentor.phar/.box/bin/check-requirements.php';
    }

    require 'phar://phpdocumentor.phar/bin/phpdoc';
}

__HALT_COMPILER();
