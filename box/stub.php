#!/usr/bin/env php
<?php


if (class_exists('Phar')) {
    if (is_string(getenv('ANSICON'))) {
        require 'phar://' . __FILE__ . '/.box/bin/check-requirements.php';
    }

    require 'phar://' . __FILE__ . '/bin/phpdoc';
}

__HALT_COMPILER();
