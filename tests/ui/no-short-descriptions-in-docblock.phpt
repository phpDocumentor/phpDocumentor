--TEST--
phpdoc project:run -f tests/data/NoShortDescription.php -t build
--FILE--
<?php
require_once 'tests/common/ui-include.php';
?>
--ARGS--
project:run -f tests/data/NoShortDescription.php -t build --config=none
--EXPECTF--
phpDocumentor version %s

%s ERR (3): No short description for property %s
%s ERR (3): No short description for method %s
%s ERR (3): No short description for class %s
%s ERR (3): No short description for file %s
Starting transformation of files (this could take a while depending upon the size of your project)
Finished transformation in %s seconds
