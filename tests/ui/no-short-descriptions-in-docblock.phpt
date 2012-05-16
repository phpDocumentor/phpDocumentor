--TEST--
phpdoc project:run -f tests/data/NoShortDescription.php -t build
--FILE--
<?php
require_once 'tests/common/ui-include.php';
?>
--ARGS--
project:run -f tests/data/NoShortDescription.php -t build --config=none
--EXPECTF--
Initializing parser and collecting files .. OK
Parsing files
  No short description for property %s
  No short description for method %s
  No short description for class %s
  No short description for file %s
Storing structure.xml in "%sbuild/structure.xml" .. OK
Initializing transformer .. OK
Processing behaviours .. OK
Executing transformations