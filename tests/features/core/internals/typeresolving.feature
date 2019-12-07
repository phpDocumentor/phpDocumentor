Feature: Phpdocumentor should correctly handle type resolving

  @github-562
  Scenario: phpdocumentor should conflate duplicated types
    Given A single file named "test.php" based on "internals/conflate-types.php"
    When I run "phpdoc -f test.php"
    Then class "\My\FZZNamespace\A" has a method "test" with returntype 'self|\My\FZZNamespace\A' without description
