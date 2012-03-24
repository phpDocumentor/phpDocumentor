--TEST--
phpdoc project:run -f dirname(__FILE__) . '/../data/DocBlockTestFixture.php' -t dirname(__FILE__) . '/../../build/' -q
--FILE--
<?php

$_SERVER['argc']    = 9;
$_SERVER['argv'][1] = 'project:run';
$_SERVER['argv'][2] = '-f';
$_SERVER['argv'][3] = dirname(__FILE__) . '/../data/DocBlockTestFixture.php';
$_SERVER['argv'][4] = '-t';
$_SERVER['argv'][5] = dirname(__FILE__) . '/../../build/';
$_SERVER['argv'][6] = '-q';
$_SERVER['argv'][7] = '--config';
$_SERVER['argv'][8] = 'none';
$_SERVER['argv'][9] = '--template';
$_SERVER['argv'][10] = 'stub';

require_once 'tests/common/ui-include.php';

?>
--EXPECTF--
