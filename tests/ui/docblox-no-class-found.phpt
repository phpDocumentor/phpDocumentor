--TEST--
docblox project:run -f tests/data/NoClass.php -t build
--FILE--
<?php
$_SERVER['argc']    = 8;
$_SERVER['argv'][1] = 'project:run';
$_SERVER['argv'][2] = '-f';
$_SERVER['argv'][3] = dirname(__FILE__) . '/../data/NoClass.php';
$_SERVER['argv'][4] = '-t';
$_SERVER['argv'][5] = dirname(__FILE__) . '/../../build/';
$_SERVER['argv'][6] = '--config';
$_SERVER['argv'][7] = 'none';

require_once 'tests/common/ui-include.php';
?>
--EXPECTF--
DocBlox version %s

Starting transformation of files (this could take a while depending upon the size of your project)
Finished transformation in %s seconds
