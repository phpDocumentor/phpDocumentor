--TEST--
phpdoc project:transform
--FILE--
<?php
require_once 'tests/common/ui-include.php';
?>
--ARGS--
project:transform --config=none 2>&1
--EXPECTF--
Initializing transformer ..

%s
%s
%s
%s


project:transform [-s|--source[="..."]] [-t|--target[="..."]] [--template[="..."]] [--parseprivate] [-p|--progressbar] [-c|--config[="..."]]
