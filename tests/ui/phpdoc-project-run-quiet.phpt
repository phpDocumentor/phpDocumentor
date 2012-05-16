--TEST--
phpdoc project:run -f dirname(__FILE__) . '/../data/DocBlockTestFixture.php' -t dirname(__FILE__) . '/../../build/' -q
--FILE--
<?php
require_once 'tests/common/ui-include.php';
?>
--ARGS--
project:run -f dirname(__FILE__) . '/../data/DocBlockTestFixture.php' -t dirname(__FILE__) . '/../../build/' -q
--EXPECTF--
