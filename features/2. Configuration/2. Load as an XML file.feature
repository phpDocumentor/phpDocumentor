Feature:
  As a User
  I want to provide a configuration file using XML as format
  So that I get to provide it in my favorite format

  Background:
    # In phpDocumentor 2 an error is thrown if there are no files in the project
    Given a file named "index.php" with:
      """
      <?php
      """

  Scenario: phpDocumentor loads the option file in an XML format
    Given a file named "phpdoc.xml" with:
      """
      <?xml version="1.0" encoding="UTF-8" ?>
      <phpdocumentor>
        <title>This is an example title</title>
      </phpdocumentor>
      """
    When I run phpDocumentor against the directory "."
    Then the project has the title "This is an example title"
