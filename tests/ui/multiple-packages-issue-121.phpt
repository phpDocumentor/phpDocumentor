--TEST--
phpdoc project:run -f tests/data/MultiplePackagesDocBlock.php -t build
--FILE--
<?php
require_once 'tests/common/ui-include.php';
?>
--ARGS--
project:run -f tests/data/MultiplePackagesDocBlock.php -t build --config=none
--EXPECTF--
Initializing parser and collecting files .. OK
Parsing files
  Only one @package tag is allowed
Storing structure.xml in "%sbuild/structure.xml" .. OK
Initializing transformer .. OK
Processing behaviours .. OK
Executing transformations
