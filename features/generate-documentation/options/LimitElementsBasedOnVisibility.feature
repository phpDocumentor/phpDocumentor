Feature: Omit specific elements based on their visibility
  In order to be able to limit the information for a given group of consumers
  As an application maintainer
  I need to be able to hide, or omit, specific elements based on their visibility

  Background:
    Given a file named "test.php" with:
    """
      <?php

      abstract class A {
        public $public;
        protected $protected;
        private $private;

        /** @internal */
        public $internal;

        /** @ignore */
        public $ignore;

        abstract public function PublicMethod();
        abstract protected function ProtectedMethod();
        private function PrivateMethod() { }

        /** @internal */
        abstract public function InternalMethod();

        /** @ignore */
        abstract public function IgnoreMethod();
      }
    """

  Scenario: Show all elements if nothing is provided using the `--visibility` command-line option
    When I run "phpdoc -f test.php"
    Then the AST has a "property" at expression "project.getFiles()['test.php'].getClasses()['\\A'].getProperties()['public']"
    Then the AST has a "property" at expression "project.getFiles()['test.php'].getClasses()['\\A'].getProperties()['protected']"
    Then the AST has a "property" at expression "project.getFiles()['test.php'].getClasses()['\\A'].getProperties()['private']"
    Then the AST has no "property" at expression "project.getFiles()['test.php'].getClasses()['\\A'].getProperties()['internal']"
    Then the AST has no "property" at expression "project.getFiles()['test.php'].getClasses()['\\A'].getProperties()['ignore']"

  Scenario: Only show public elements if 'public' is provided using the `--visibility` command-line option
    When I run "phpdoc -f test.php --visibility=public"
    Then the AST has a "property" at expression "project.getFiles()['test.php'].getClasses()['\\A'].getProperties()['public']"
    Then the AST has no "property" at expression "project.getFiles()['test.php'].getClasses()['\\A'].getProperties()['protected']"
    Then the AST has no "property" at expression "project.getFiles()['test.php'].getClasses()['\\A'].getProperties()['private']"
    Then the AST has no "property" at expression "project.getFiles()['test.php'].getClasses()['\\A'].getProperties()['internal']"
    Then the AST has no "property" at expression "project.getFiles()['test.php'].getClasses()['\\A'].getProperties()['ignore']"

  Scenario: Only show protected elements if 'protected' is provided using the `--visibility` command-line option
    When I run "phpdoc -f test.php --visibility=protected"
    Then the AST has no "property" at expression "project.getFiles()['test.php'].getClasses()['\\A'].getProperties()['public']"
    Then the AST has a "property" at expression "project.getFiles()['test.php'].getClasses()['\\A'].getProperties()['protected']"
    Then the AST has no "property" at expression "project.getFiles()['test.php'].getClasses()['\\A'].getProperties()['private']"
    Then the AST has no "property" at expression "project.getFiles()['test.php'].getClasses()['\\A'].getProperties()['internal']"
    Then the AST has no "property" at expression "project.getFiles()['test.php'].getClasses()['\\A'].getProperties()['ignore']"

  Scenario: Only show private elements if 'private' is provided using the `--visibility` command-line option
    When I run "phpdoc -f test.php --visibility=private"
    Then the AST has no "property" at expression "project.getFiles()['test.php'].getClasses()['\\A'].getProperties()['public']"
    Then the AST has no "property" at expression "project.getFiles()['test.php'].getClasses()['\\A'].getProperties()['protected']"
    Then the AST has a "property" at expression "project.getFiles()['test.php'].getClasses()['\\A'].getProperties()['private']"
    Then the AST has no "property" at expression "project.getFiles()['test.php'].getClasses()['\\A'].getProperties()['internal']"
    Then the AST has no "property" at expression "project.getFiles()['test.php'].getClasses()['\\A'].getProperties()['ignore']"

  Scenario: Show public and protected elements if both 'public' and 'protected' is provided using the `--visibility` command-line option
    When I run "phpdoc -f test.php --visibility='public,protected'"
    Then the AST has a "property" at expression "project.getFiles()['test.php'].getClasses()['\\A'].getProperties()['public']"
    Then the AST has a "property" at expression "project.getFiles()['test.php'].getClasses()['\\A'].getProperties()['protected']"
    Then the AST has no "property" at expression "project.getFiles()['test.php'].getClasses()['\\A'].getProperties()['private']"
    Then the AST has no "property" at expression "project.getFiles()['test.php'].getClasses()['\\A'].getProperties()['internal']"
    Then the AST has no "property" at expression "project.getFiles()['test.php'].getClasses()['\\A'].getProperties()['ignore']"

  Scenario: Only show public elements if 'public' is provided using the `parser/visibility` configuration option
    Given a file named "custom-options.xml" with:
    """
<?xml version="1.0" encoding="UTF-8" ?>
<phpdocumentor>
    <parser><visibility>public</visibility></parser>
</phpdocumentor>
    """
    When I run "phpdoc -f test.php -c custom-options.xml"
    Then the AST has a "property" at expression "project.getFiles()['test.php'].getClasses()['\\A'].getProperties()['public']"
    Then the AST has no "property" at expression "project.getFiles()['test.php'].getClasses()['\\A'].getProperties()['protected']"
    Then the AST has no "property" at expression "project.getFiles()['test.php'].getClasses()['\\A'].getProperties()['private']"
    Then the AST has no "property" at expression "project.getFiles()['test.php'].getClasses()['\\A'].getProperties()['internal']"
    Then the AST has no "property" at expression "project.getFiles()['test.php'].getClasses()['\\A'].getProperties()['ignore']"

  Scenario: Only show protected elements if 'protected' is provided using the `parser/visibility` configuration option
    Given a file named "custom-options.xml" with:
    """
<?xml version="1.0" encoding="UTF-8" ?>
<phpdocumentor>
    <parser><visibility>protected</visibility></parser>
</phpdocumentor>
    """
    When I run "phpdoc -f test.php -c custom-options.xml"
    Then the AST has no "property" at expression "project.getFiles()['test.php'].getClasses()['\\A'].getProperties()['public']"
    Then the AST has a "property" at expression "project.getFiles()['test.php'].getClasses()['\\A'].getProperties()['protected']"
    Then the AST has no "property" at expression "project.getFiles()['test.php'].getClasses()['\\A'].getProperties()['private']"
    Then the AST has no "property" at expression "project.getFiles()['test.php'].getClasses()['\\A'].getProperties()['internal']"
    Then the AST has no "property" at expression "project.getFiles()['test.php'].getClasses()['\\A'].getProperties()['ignore']"

  Scenario: Only show private elements if 'private' is provided using the `parser/visibility` configuration option
    Given a file named "custom-options.xml" with:
    """
<?xml version="1.0" encoding="UTF-8" ?>
<phpdocumentor>
    <parser><visibility>private</visibility></parser>
</phpdocumentor>
    """
    When I run "phpdoc -f test.php -c custom-options.xml"
    Then the AST has no "property" at expression "project.getFiles()['test.php'].getClasses()['\\A'].getProperties()['public']"
    Then the AST has no "property" at expression "project.getFiles()['test.php'].getClasses()['\\A'].getProperties()['protected']"
    Then the AST has a "property" at expression "project.getFiles()['test.php'].getClasses()['\\A'].getProperties()['private']"
    Then the AST has no "property" at expression "project.getFiles()['test.php'].getClasses()['\\A'].getProperties()['internal']"
    Then the AST has no "property" at expression "project.getFiles()['test.php'].getClasses()['\\A'].getProperties()['ignore']"

  Scenario: Show public and protected elements if both 'public' and 'protected' is provided using the `parser/visibility` configuration option
    Given a file named "custom-options.xml" with:
    """
<?xml version="1.0" encoding="UTF-8" ?>
<phpdocumentor>
    <parser><visibility>public,protected</visibility></parser>
</phpdocumentor>
    """
    When I run "phpdoc -f test.php -c custom-options.xml"
    Then the AST has a "property" at expression "project.getFiles()['test.php'].getClasses()['\\A'].getProperties()['public']"
    Then the AST has a "property" at expression "project.getFiles()['test.php'].getClasses()['\\A'].getProperties()['protected']"
    Then the AST has no "property" at expression "project.getFiles()['test.php'].getClasses()['\\A'].getProperties()['private']"
    Then the AST has no "property" at expression "project.getFiles()['test.php'].getClasses()['\\A'].getProperties()['internal']"
    Then the AST has no "property" at expression "project.getFiles()['test.php'].getClasses()['\\A'].getProperties()['ignore']"

  Scenario: Elements marked with the `@internal` tag should be hidden by default
    When I run "phpdoc -f test.php"
    Then the AST has no "property" at expression "project.getFiles()['test.php'].getClasses()['\\A'].getProperties()['internal']"

  Scenario: Elements marked with the `@ignore` tag should be hidden by default
    When I run "phpdoc -f test.php"
    Then the AST has no "property" at expression "project.getFiles()['test.php'].getClasses()['\\A'].getProperties()['ignore']"

  Scenario: Elements marked with `@ignore` should still be hidden when the `--parseprivate` command-line option is provided
    When I run "phpdoc -f test.php --parseprivate"
    Then the AST has no "property" at expression "project.getFiles()['test.php'].getClasses()['\\A'].getProperties()['ignore']"

  Scenario: Elements marked with the `@internal` tag should be shown when the `--parseprivate` command-line option is provided
    When I run "phpdoc -f test.php --parseprivate"
    Then the AST has a "property" at expression "project.getFiles()['test.php'].getClasses()['\\A'].getProperties()['internal']"
