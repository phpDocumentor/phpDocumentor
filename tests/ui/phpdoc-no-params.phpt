--TEST--
phpdoc project:run
--FILE--
<?php
require_once 'tests/common/ui-include.php';
?>
--ARGS--
project:run --config=none 2>&1
--EXPECTF--
Initializing parser and collecting files .. OK
Parsing files

%s
%s
%s
%s


project:run [-t|--target[="..."]] [-f|--filename[="..."]] [-d|--directory[="..."]] [-e|--extensions[="..."]] [-i|--ignore[="..."]] [--ignore-tags[="..."]] [--hidden] [--ignore-symlinks] [-m|--markers[="..."]] [--title[="..."]] [--force] [--validate] [--visibility[="..."]] [--defaultpackagename[="..."]] [--sourcecode] [-p|--progressbar] [--template[="..."]] [--parseprivate] [-c|--config[="..."]]
