Feature:
  As a User
  I want to provide an default configuration that can be overridden by an individual developer
  So that I can provide a default config but still allow it to be overridden on a per location basis

  Background:
    # In phpDocumentor 2 an error is thrown if there are no files in the project
    Given a file named "index.php" with:
      """
      <?php
      """

  Scenario: phpDocumentor loads the distribution (.dist.xml) file
    Given a file named "phpdoc.dist.xml" with:
    """
    <?xml version="1.0" encoding="UTF-8" ?>
    <phpdocumentor>
      <title>This is an example title</title>
    </phpdocumentor>
    """
    When I run phpDocumentor against the directory "."
    Then the project has the title "This is an example title"

  Scenario: phpDocumentor loads the normal configuration if both a distribution and normal configuration is provided
    Given a file named "phpdoc.dist.xml" with:
    """
    <?xml version="1.0" encoding="UTF-8" ?>
    <phpdocumentor>
      <title>This is another example title</title>
    </phpdocumentor>
    """
    Given a file named "phpdoc.xml" with:
    """
    <?xml version="1.0" encoding="UTF-8" ?>
    <phpdocumentor>
      <title>This is an example title</title>
    </phpdocumentor>
    """
    When I run phpDocumentor against the directory "."
    Then the project has the title "This is an example title"

