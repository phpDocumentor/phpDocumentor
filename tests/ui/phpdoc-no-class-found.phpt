--TEST--
phpdoc project:run -f tests/data/NoClass.php -t build
--FILE--
<?php
require_once 'tests/common/ui-include.php';
?>
--ARGS--
project:run -f tests/data/NoClass.php -t build --config=none
--EXPECTF--
Initializing parser and collecting files .. OK
Parsing files
  No page-level DocBlock was found in file %s
Storing structure.xml in "%sbuild/structure.xml" .. OK
Initializing transformer .. OK
Processing behaviours .. OK
Executing transformations