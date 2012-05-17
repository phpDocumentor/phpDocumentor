--TEST--
phpdoc project:run -f tests/data/NoPackageDocBlock.php -t build
--FILE--
<?php
require_once 'tests/common/ui-include.php';
?>
--ARGS--
project:run -f tests/data/NoPackageDocBlock.php -t build --config=none
--EXPECTF--
Initializing parser and collecting files .. OK
Parsing files
  Cannot have a @subpackage when a @package tag is not present
  Cannot have a @subpackage when a @package tag is not present
Storing structure.xml in "%sbuild/structure.xml" .. OK
Initializing transformer .. OK
Processing behaviours .. OK
Executing transformations