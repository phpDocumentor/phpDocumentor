#!/usr/bin/env php
<?php
passthru(dirname(__FILE__).'/docblox.php project:parse '.implode(' ', array_slice($argv, 1)));