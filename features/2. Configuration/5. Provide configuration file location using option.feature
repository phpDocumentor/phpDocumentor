Feature:
  As a User
  I want to provide an alternative configuration location
  So that I can decide where to store the configuration file for phpDocumentor

  Background:
    # In phpDocumentor 2 an error is thrown if there are no files in the project
    Given a file named "index.php" with:
      """
      <?php
      """

  Scenario: If the '--config' option is provided will the configuration file be loaded that is provided
    Given a file named "config.xml" with:
    """
    <?xml version="1.0" encoding="UTF-8" ?>
    <phpdocumentor>
      <title>This is an example title</title>
    </phpdocumentor>
    """
    When I run "phpdoc --config=config.xml -d ."
    Then the project has the title "This is an example title"

  Scenario: The '--config' option takes precedence over configuration files in the current working directory
    Given a file named "phpdoc.xml" with:
    """
    <?xml version="1.0" encoding="UTF-8" ?>
    <phpdocumentor>
      <title>This is an example title</title>
    </phpdocumentor>
    """
      And a file named "config.xml" with:
      """
      <?xml version="1.0" encoding="UTF-8" ?>
      <phpdocumentor>
        <title>This is another example title</title>
      </phpdocumentor>
      """
    When I run "phpdoc --config=config.xml -d ."
    Then the project has the title "This is another example title"

  @roadmap-v3
  Scenario: If a folder is provided using the '--config' option phpDocumentor will search for a 'phpdoc.xml' file
    Given a directory "config"
      And a file named "config.xml" with:
      """
      <?xml version="1.0" encoding="UTF-8" ?>
      <phpdocumentor>
        <title>This is another example title</title>
      </phpdocumentor>
      """
    When I run "phpdoc --config=config -d ."
    Then the project has the title "This is another example title"

  @roadmap-v3
  Scenario: If an unknown file is provided using the '--config' option phpDocumentor will throw an error
    When I run "phpdoc --config=config.xml -d ."
    Then the exit code should be non-zero
