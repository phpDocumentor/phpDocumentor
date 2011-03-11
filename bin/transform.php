#!/usr/bin/env php
<?php
passthru(dirname(__FILE__) . '/docblox.php project:transform ' . implode(' ', array_slice($argv, 1)));