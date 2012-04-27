--TEST--
phpdoc project:run -f tests/data/MultiplePackagesDocBlock.php -t build
--FILE--
<?php
require_once 'tests/common/ui-include.php';
?>
--ARGS--
project:run -f tests/data/MultiplePackagesDocBlock.php -t build --config=none
--EXPECTF--
phpDocumentor version %s

%s ERR (3): Only one @package tag is allowed
Starting transformation of files (this could take a while depending upon the size of your project)
Finished transformation in %s seconds
